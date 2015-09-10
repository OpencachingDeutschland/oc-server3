<?php

namespace okapi\services\logs\submit;

use Exception;
use okapi\Okapi;
use okapi\Db;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\OkapiAccessToken;
use okapi\Settings;
use okapi\services\caches\search\SearchAssistant;
use okapi\BadRequest;


/**
 * This exception is thrown by WebService::_call method, when error is detected in
 * user-supplied data. It is not a BadRequest exception - it does not imply that
 * the Consumer did anything wrong (it's the user who did). This exception shouldn't
 * be used outside of this file.
 */
class CannotPublishException extends Exception {}

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 3
        );
    }

    /**
     * Publish a new log entry and return log entry uuid. Throws
     * CannotPublishException or BadRequest on errors.
     */
    private static function _call(OkapiRequest $request)
    {
        # Developers! Please notice the fundamental difference between throwing
        # CannotPublishException and standard BadRequest/InvalidParam exceptions!
        # Notice, that this is "_call" method, not the usual "call" (see below
        # for "call").

        $cache_code = $request->get_parameter('cache_code');
        if (!$cache_code) throw new ParamMissing('cache_code');

        $logtype = $request->get_parameter('logtype');
        if (!$logtype) throw new ParamMissing('logtype');
        if (!in_array($logtype, array('Found it', "Didn't find it", 'Comment', 'Will attend', 'Attended')))
            throw new InvalidParam('logtype', "'$logtype' in not a valid logtype code.");

        $comment = $request->get_parameter('comment');
        if (!$comment) $comment = "";

        $comment_format = $request->get_parameter('comment_format');
        if (!$comment_format) $comment_format = "auto";
        if (!in_array($comment_format, array('auto', 'html', 'plaintext')))
            throw new InvalidParam('comment_format', $comment_format);

        $tmp = $request->get_parameter('when');
        if ($tmp)
        {
            $when = strtotime($tmp);
            if ($when < 1)
                throw new InvalidParam('when', "'$tmp' is not in a valid format or is not a valid date.");
            if ($when > time() + 5*60)
                throw new CannotPublishException(_("You are trying to publish a log entry with a date in future. ".
                    "Cache log entries are allowed to be published in the past, but NOT in the future."));
        }
        else
            $when = time();

        $on_duplicate = $request->get_parameter('on_duplicate');
        if (!$on_duplicate) $on_duplicate = "silent_success";
        if (!in_array($on_duplicate, array('silent_success', 'user_error', 'continue')))
            throw new InvalidParam('on_duplicate', "Unknown option: '$on_duplicate'.");

        $rating = $request->get_parameter('rating');
        if ($rating !== null && (!in_array($rating, array(1,2,3,4,5))))
            throw new InvalidParam('rating', "If present, it must be an integer in the 1..5 scale.");
        if ($rating && $logtype != 'Found it' && $logtype != 'Attended')
            throw new BadRequest("Rating is allowed only for 'Found it' and 'Attended' logtypes.");
        if ($rating !== null && (Settings::get('OC_BRANCH') == 'oc.de'))
        {
            # We will remove the rating request and change the success message
            # (which will be returned IF the rest of the query will meet all the
            # requirements).

            self::$success_message .= " ".sprintf(_("However, your cache rating was ignored, because %s does not have a rating system."),
                Okapi::get_normalized_site_name());
            $rating = null;
        }

        $recommend = $request->get_parameter('recommend');
        if (!$recommend) $recommend = 'false';
        if (!in_array($recommend, array('true', 'false')))
            throw new InvalidParam('recommend', "Unknown option: '$recommend'.");
        $recommend = ($recommend == 'true');
        if ($recommend && $logtype != 'Found it')
        {
            if ($logtype != 'Attended')
                throw new BadRequest("Recommending is allowed only for 'Found it' and 'Attended' logs.");
            else if (Settings::get('OC_BRANCH') == 'oc.pl')
            {
                # We will remove the recommendation request and change the success message
                # (which will be returned IF the rest of the query will meet all the
                # requirements).
                self::$success_message .= " ".sprintf(_("However, your cache recommendation was ignored, because %s does not allow recommending event caches."),
                    Okapi::get_normalized_site_name());
                $recommend = null;
            }
        }

        $needs_maintenance = $request->get_parameter('needs_maintenance');
        if (!$needs_maintenance) $needs_maintenance = 'false';
        if (!in_array($needs_maintenance, array('true', 'false')))
            throw new InvalidParam('needs_maintenance', "Unknown option: '$needs_maintenance'.");
        $needs_maintenance = ($needs_maintenance == 'true');
        if ($needs_maintenance && (!Settings::get('SUPPORTS_LOGTYPE_NEEDS_MAINTENANCE')))
        {
            # If not supported, just ignore it.
            self::$success_message .= " ".sprintf(_("However, your \"needs maintenance\" flag was ignored, because %s does not support this feature."),
                Okapi::get_normalized_site_name());
            $needs_maintenance = false;
        }

        # Check if cache exists and retrieve cache internal ID (this will throw
        # a proper exception on invalid cache_code). Also, get the user object.

        $cache = OkapiServiceRunner::call('services/caches/geocache', new OkapiInternalRequest(
            $request->consumer, null, array('cache_code' => $cache_code,
            'fields' => 'internal_id|status|owner|type|req_passwd')));
        $user = OkapiServiceRunner::call('services/users/by_internal_id', new OkapiInternalRequest(
            $request->consumer, $request->token, array('internal_id' => $request->token->user_id,
            'fields' => 'is_admin|uuid|internal_id|caches_found|rcmds_given')));

        # Various integrity checks.

        if ($cache['type'] == 'Event')
        {
            if (!in_array($logtype, array('Will attend', 'Attended', 'Comment')))
                throw new CannotPublishException(_('This cache is an Event cache. You cannot "Find" it (but you can attend it, or comment on it)!'));
        }
        else  # type != event
        {
            if (in_array($logtype, array('Will attend', 'Attended')))
                throw new CannotPublishException(_('This cache is NOT an Event cache. You cannot "Attend" it (but you can find it, or comment on it)!'));
            else if (!in_array($logtype, array('Found it', "Didn't find it", 'Comment')))
                throw new Exception("Unknown log entry - should be documented here.");
        }
        if ($logtype == 'Comment' && strlen(trim($comment)) == 0)
            throw new CannotPublishException(_("Your have to supply some text for your comment."));

        # Password check.

        if (($logtype == 'Found it' || $logtype == 'Attended') && $cache['req_passwd'])
        {
            $valid_password = Db::select_value("
                select logpw
                from caches
                where cache_id = '".mysql_real_escape_string($cache['internal_id'])."'
            ");
            $supplied_password = $request->get_parameter('password');
            if (!$supplied_password)
                throw new CannotPublishException(_("This cache requires a password. You didn't provide one!"));
            if (strtolower($supplied_password) != strtolower($valid_password))
                throw new CannotPublishException(_("Invalid password!"));
        }

        # Prepare our comment to be inserted into the database. This may require
        # some reformatting which depends on the current OC installation.

        if (Settings::get('OC_BRANCH') == 'oc.de')
        {
            # OCDE stores all comments in HTML format, while the 'text_html' field
            # indicates their *original* format as delivered by the user. This
            # allows processing the 'text' field contents without caring about the
            # original format, while still being able to re-create the comment in
            # its original form. It requires us to HTML-encode plaintext comments
            # and to indicate this by setting 'html_text' to FALSE.
            #
            # For user-supplied HTML comments, OCDE requires us to do additional
            # HTML purification prior to the insertion into the database.

            if ($comment_format == 'plaintext')
            {
                # This code is identical to the plaintext processing in OCDE code,
                # including a space handling bug: Multiple consecutive spaces will
                # get semantically lost in the generated HTML.

                $formatted_comment = htmlspecialchars($comment, ENT_COMPAT);
                $formatted_comment = nl2br($formatted_comment);
                $value_for_text_html_field = 0;
            }
            else
            {
                if ($comment_format == 'auto')
                {
                    # 'Auto' is for backward compatibility. Before the "comment_format"
                    # was introduced, OKAPI used a weird format in between (it allowed
                    # HTML, but applied nl2br too).

                    $formatted_comment = nl2br($comment);
                }
                else
                    $formatted_comment = $comment;

                # NOTICE: We are including EXTERNAL OCDE library here! This
                # code does not belong to OKAPI!

                $opt['rootpath'] = $GLOBALS['rootpath'];
                $opt['html_purifier'] = Settings::get('OCDE_HTML_PURIFIER_SETTINGS');
                require_once($GLOBALS['rootpath'] . 'lib2/OcHTMLPurifier.class.php');

                $purifier = new \OcHTMLPurifier($opt);
                $formatted_comment = $purifier->purify($formatted_comment);
                $value_for_text_html_field = 1;
            }
        }
        else
        {
            # OCPL is even weirder. It also stores HTML-lized comments in the database
            # (it doesn't really matter if 'text_html' field is set to FALSE). OKAPI must
            # save it in HTML either way. However, escaping plain-text doesn't work!
            # If we put "&lt;b&gt;" in, it still gets converted to "<b>" before display!
            # NONE of this process is documented within OCPL code. OKAPI uses a dirty
            # "hack" to save PLAINTEXT comments (let us hope the hack will remain valid).
            #
            # OCPL doesn't require HTML purification prior to the database insertion.
            # HTML seems to be purified dynamically, before it is displayed.

            if ($comment_format == 'plaintext')
            {
                $formatted_comment = htmlspecialchars($comment, ENT_QUOTES);
                $formatted_comment = nl2br($formatted_comment);
                $formatted_comment = str_replace("&amp;", "&amp;#38;", $formatted_comment);
                $formatted_comment = str_replace("&lt;", "&amp;#60;", $formatted_comment);
                $formatted_comment = str_replace("&gt;", "&amp;#62;", $formatted_comment);
                $value_for_text_html_field = 0; // WRTODO: get rid of
            }
            elseif ($comment_format == 'auto')
            {
                $formatted_comment = nl2br($comment);
                $value_for_text_html_field = 1;
            }
            else
            {
                $formatted_comment = $comment;
                $value_for_text_html_field = 1;
            }
        }
        unset($comment);

        # Duplicate detection.

        if ($on_duplicate != 'continue')
        {
            # Attempt to find a log entry made by the same user, for the same cache, with
            # the same date, type, comment, etc. Note, that these are not ALL the fields
            # we could check, but should work ok in most cases. Also note, that we
            # DO NOT guarantee that duplicate detection will succeed. If it doesn't,
            # nothing bad happens (user will just post two similar log entries).
            # Keep this simple!

            $duplicate_uuid = Db::select_value("
                select uuid
                from cache_logs
                where
                    user_id = '".mysql_real_escape_string($request->token->user_id)."'
                    and cache_id = '".mysql_real_escape_string($cache['internal_id'])."'
                    and type = '".mysql_real_escape_string(Okapi::logtypename2id($logtype))."'
                    and date = from_unixtime('".mysql_real_escape_string($when)."')
                    and text = '".mysql_real_escape_string($formatted_comment)."'
                    ".((Settings::get('OC_BRANCH') == 'oc.pl') ? "and deleted = 0" : "")."
                limit 1
            ");
            if ($duplicate_uuid != null)
            {
                if ($on_duplicate == 'silent_success')
                {
                    # Act as if the log has been submitted successfully.
                    return $duplicate_uuid;
                }
                elseif ($on_duplicate == 'user_error')
                {
                    throw new CannotPublishException(_("You have already submitted a log entry with exactly the same contents."));
                }
            }
        }

        # Check if already found it (and make sure the user is not the owner).
        #
        # OCPL forbids logging 'Found it' or "Didn't find" for an already found cache,
        # while OCDE allows all kinds of duplicate logs.

        if (Settings::get('OC_BRANCH') == 'oc.pl'
            && (($logtype == 'Found it') || ($logtype == "Didn't find it")))
        {
            $has_already_found_it = Db::select_value("
                select 1
                from cache_logs
                where
                    user_id = '".mysql_real_escape_string($user['internal_id'])."'
                    and cache_id = '".mysql_real_escape_string($cache['internal_id'])."'
                    and type = '".mysql_real_escape_string(Okapi::logtypename2id("Found it"))."'
                    and ".((Settings::get('OC_BRANCH') == 'oc.pl') ? "deleted = 0" : "true")."
            ");
            if ($has_already_found_it)
                throw new CannotPublishException(_("You have already submitted a \"Found it\" log entry once. Now you may submit \"Comments\" only!"));
            if ($user['uuid'] == $cache['owner']['uuid'])
                throw new CannotPublishException(_("You are the owner of this cache. You may submit \"Comments\" only!"));
        }

        # Check if the user has already rated the cache. BTW: I don't get this one.
        # If we already know, that the cache was NOT found yet, then HOW could the
        # user submit a rating for it? Anyway, I will stick to the procedure
        # found in log.php. On the bright side, it's fail-safe.

        if ($rating)
        {
            $has_already_rated = Db::select_value("
                select 1
                from scores
                where
                    user_id = '".mysql_real_escape_string($user['internal_id'])."'
                    and cache_id = '".mysql_real_escape_string($cache['internal_id'])."'
            ");
            if ($has_already_rated)
                throw new CannotPublishException(_("You have already rated this cache once. Your rating cannot be changed."));
        }

        # If user wants to recommend...

        if ($recommend)
        {
            # Do the same "fail-safety" check as we did for the rating.

            $already_recommended = Db::select_value("
                select 1
                from cache_rating
                where
                    user_id = '".mysql_real_escape_string($user['internal_id'])."'
                    and cache_id = '".mysql_real_escape_string($cache['internal_id'])."'
            ");
            if ($already_recommended)
                throw new CannotPublishException(_("You have already recommended this cache once."));

            # Check the number of recommendations.

            $founds = $user['caches_found'] + 1;  // +1, because he'll find THIS ONE in a moment, right?
                # Note: caches_found includes event attendance on both, OCDE and OCPL.
                # Though OCPL does not allow recommending events, for each 10 event
                # attendances the user may recommend a non-event cache.
            $rcmds_left = floor($founds / 10.0) - $user['rcmds_given'];
            if ($rcmds_left <= 0)
                throw new CannotPublishException(_("You don't have any recommendations to give. Find more caches first!"));
        }

        # If user checked the "needs_maintenance" flag, we will shuffle things a little...

        if ($needs_maintenance)
        {
            # If we're here, then we also know that the "Needs maintenance" log type is supported
            # by this OC site. However, it's a separate log type, so we might have to submit
            # two log types together:

            if ($logtype == 'Comment')
            {
                # If user submits a "Comment", we'll just change its type to "Needs maintenance".
                # Only one log entry will be issued.

                $logtype = 'Needs maintenance';
                $second_logtype = null;
                $second_formatted_comment = null;
            }
            elseif ($logtype == 'Found it')
            {
                # If "Found it", then we'll issue two log entries: one "Found it" with the
                # original comment, and second one "Needs maintenance" with empty comment.

                $second_logtype = 'Needs maintenance';
                $second_formatted_comment = "";
            }
            elseif ($logtype == "Didn't find it")
            {
                # If "Didn't find it", then we'll issue two log entries, but this time
                # we'll do this the other way around. The first "Didn't find it" entry
                # will have an empty comment. We will move the comment to the second
                # "Needs maintenance" log entry. (It's okay for this behavior to change
                # in the future, but it seems natural to me.)

                $second_logtype = 'Needs maintenance';
                $second_formatted_comment = $formatted_comment;
                $formatted_comment = "";
            }
            else if ($logtype == 'Will attend' || $logtype == 'Attended')
            {
                # OC branches which know maintenance logs do not allow them on event caches.
                throw new CannotPublishException(_("Event caches cannot \"need maintenance\"."));
            }
            else
                throw new Exception();
        }
        else
        {
            # User didn't check the "Needs maintenance" flag OR "Needs maintenance" log type
            # isn't supported by this server.

            $second_logtype = null;
            $second_formatted_comment = null;
        }

        # Finally! Insert the rows into the log entries table. Update
        # cache stats and user stats.

        $log_uuid = self::insert_log_row(
            $request->consumer->key, $cache['internal_id'], $user['internal_id'], $logtype,
            $when, $formatted_comment, $value_for_text_html_field);
        self::increment_cache_stats($cache['internal_id'], $when, $logtype);
        self::increment_user_stats($user['internal_id'], $logtype);
        if ($second_logtype != null)
        {
            # Reminder: This will never be called while SUPPORTS_LOGTYPE_NEEDS_MAINTENANCE is off.

            self::insert_log_row(
                $request->consumer->key, $cache['internal_id'], $user['internal_id'], $second_logtype,
                $when + 1, $second_formatted_comment, $value_for_text_html_field);
            self::increment_cache_stats($cache['internal_id'], $when + 1, $second_logtype);
            self::increment_user_stats($user['internal_id'], $second_logtype);
        }

        # Save the rating.

        if ($rating)
        {
            # This code will be called for OCPL branch only. Earlier, we made sure,
            # to set $rating to null, if we're running on OCDE.

            # OCPL has a little strange way of storing cumulative rating. Instead
            # of storing the sum of all ratings, OCPL stores the computed average
            # and update it using multiple floating-point operations. Moreover,
            # the "score" field in the database is on the -3..3 scale (NOT 1..5),
            # and the translation made at retrieval time is DIFFERENT than the
            # one made here (both of them are non-linear). Also, once submitted,
            # the rating can never be changed. It surely feels quite inconsistent,
            # but presumably has some deep logic into it. See also here (Polish):
            # http://wiki.opencaching.pl/index.php/Oceny_skrzynek

            switch ($rating)
            {
                case 1: $db_score = -2.0; break;
                case 2: $db_score = -0.5; break;
                case 3: $db_score = 0.7; break;
                case 4: $db_score = 1.7; break;
                case 5: $db_score = 3.0; break;
                default: throw new Exception();
            }
            Db::execute("
                update caches
                set
                    score = (score*votes + '".mysql_real_escape_string($db_score)."')/(votes + 1),
                    votes = votes + 1
                where cache_id = '".mysql_real_escape_string($cache['internal_id'])."'
            ");
            Db::execute("
                insert into scores (user_id, cache_id, score)
                values (
                    '".mysql_real_escape_string($user['internal_id'])."',
                    '".mysql_real_escape_string($cache['internal_id'])."',
                    '".mysql_real_escape_string($db_score)."'
                );
            ");
        }

        # Save recommendation.

        if ($recommend)
        {
            if (Db::field_exists('cache_rating', 'rating_date'))
            {
                Db::execute("
                    insert into cache_rating (user_id, cache_id, rating_date)
                    values (
                        '".mysql_real_escape_string($user['internal_id'])."',
                        '".mysql_real_escape_string($cache['internal_id'])."',
                        from_unixtime('".mysql_real_escape_string($when)."')
                    );
                ");
            }
            else
            {
                Db::execute("
                    insert into cache_rating (user_id, cache_id)
                    values (
                        '".mysql_real_escape_string($user['internal_id'])."',
                        '".mysql_real_escape_string($cache['internal_id'])."'
                    );
                ");
            }
        }

        # We need to delete the copy of stats-picture for this user. Otherwise,
        # the legacy OC code won't detect that the picture needs to be refreshed.

        $filepath = Okapi::get_var_dir().'/images/statpics/statpic'.$user['internal_id'].'.jpg';
        if (file_exists($filepath))
            unlink($filepath);

        # Success. Return the uuid.

        return $log_uuid;
    }

    private static $success_message = null;
    public static function call(OkapiRequest $request)
    {
        # This is the "real" entry point. A wrapper for the _call method.

        $langpref = $request->get_parameter('langpref');
        if (!$langpref) $langpref = "en";

        # Error messages thrown via CannotPublishException exceptions should be localized.
        # They will be delivered for end user to display in his language.

        Okapi::gettext_domain_init(explode("|", $langpref));
        try
        {
            # If appropriate, $success_message might be changed inside the _call.
            self::$success_message = _("Your cache log entry was posted successfully.");
            $log_uuid = self::_call($request);
            $result = array(
                'success' => true,
                'message' => self::$success_message,
                'log_uuid' => $log_uuid
            );
            Okapi::gettext_domain_restore();
        }
        catch (CannotPublishException $e)
        {
            Okapi::gettext_domain_restore();
            $result = array(
                'success' => false,
                'message' => $e->getMessage(),
                'log_uuid' => null
            );
        }

        return Okapi::formatted_response($request, $result);
    }

    private static function increment_cache_stats($cache_internal_id, $when, $logtype)
    {
        if (Settings::get('OC_BRANCH') == 'oc.de')
        {
            # OCDE handles cache stats updates using triggers. So, they are already
            # incremented properly.
        }
        else
        {
            # OCPL doesn't use triggers for this. We need to update manually.

            if ($logtype == 'Found it')
            {
                Db::execute("
                    update caches
                    set
                        founds = founds + 1,
                        last_found = greatest(ifnull(last_found, 0), from_unixtime('".mysql_real_escape_string($when)."'))
                    where cache_id = '".mysql_real_escape_string($cache_internal_id)."'
                ");
            }
            elseif ($logtype == "Didn't find it")
            {
                Db::execute("
                    update caches
                    set notfounds = notfounds + 1
                    where cache_id = '".mysql_real_escape_string($cache_internal_id)."'
                ");
            }
            elseif ($logtype == 'Comment')
            {
                Db::execute("
                    update caches
                    set notes = notes + 1
                    where cache_id = '".mysql_real_escape_string($cache_internal_id)."'
                ");
            }
            else
            {
                # This log type is not represented in cache stats.
            }
        }
    }

    private static function increment_user_stats($user_internal_id, $logtype)
    {
        if (Settings::get('OC_BRANCH') == 'oc.de')
        {
            # OCDE handles cache stats updates using triggers. So, they are already
            # incremented properly.
        }
        else
        {
            # OCPL doesn't have triggers for this. We need to update manually.

            switch ($logtype)
            {
                case 'Found it': $field_to_increment = 'founds_count'; break;
                case "Didn't find it": $field_to_increment = 'notfounds_count'; break;
                case 'Comment': $field_to_increment = 'log_notes_count'; break;
                default:
                    # This log type is not represented in user stats.
                    return;
            }
            Db::execute("
                update user
                set $field_to_increment = $field_to_increment + 1
                where user_id = '".mysql_real_escape_string($user_internal_id)."'
            ");
        }
    }

    private static function insert_log_row(
        $consumer_key, $cache_internal_id, $user_internal_id, $logtype, $when,
        $formatted_comment, $text_html
    )
    {
        $log_uuid = Okapi::create_uuid();
        Db::execute("
            insert into cache_logs (uuid, cache_id, user_id, type, date, text, text_html, last_modified, date_created, node)
            values (
                '".mysql_real_escape_string($log_uuid)."',
                '".mysql_real_escape_string($cache_internal_id)."',
                '".mysql_real_escape_string($user_internal_id)."',
                '".mysql_real_escape_string(Okapi::logtypename2id($logtype))."',
                from_unixtime('".mysql_real_escape_string($when)."'),
                '".mysql_real_escape_string($formatted_comment)."',
                '".mysql_real_escape_string($text_html)."',
                now(),
                now(),
                '".mysql_real_escape_string(Settings::get('OC_NODE_ID'))."'
            );
        ");
        $log_internal_id = Db::last_insert_id();

        # Store additional information on consumer_key which have created this log entry.
        # (Maybe we'll want to display this somewhere later.)

        Db::execute("
            insert into okapi_cache_logs (log_id, consumer_key)
            values (
                '".mysql_real_escape_string($log_internal_id)."',
                '".mysql_real_escape_string($consumer_key)."'
            );
        ");

        return $log_uuid;
    }
}
