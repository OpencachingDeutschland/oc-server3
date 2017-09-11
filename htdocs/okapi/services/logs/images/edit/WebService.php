<?php

namespace okapi\services\logs\images\edit;

use Exception;
use okapi\core\Db;
use okapi\core\Exception\BadRequest;
use okapi\core\Exception\InvalidParam;
use okapi\core\Okapi;
use okapi\core\Request\OkapiRequest;
use okapi\services\logs\images\LogImagesCommon;
use okapi\Settings;

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
     * Edit an log entry image and return its (new) position.
     * Throws CannotPublishException or BadRequest on errors.
     */

    private static function _call(OkapiRequest $request)
    {
        # Developers! Please notice the fundamental difference between throwing
        # CannotPublishException and the "standard" BadRequest/InvalidParam
        # exceptions. CannotPublishException will be caught by the service's
        # call() function and returns a message to be displayed to the user.

        # validate the 'image_uuid' parameter

        list($image_uuid, $log_internal_id) = LogImagesCommon::validate_image_uuid($request);

        # validate the 'caption', 'is_spoiler' and 'position' parameters

        $caption = $request->get_parameter('caption');
        if ($caption !== null && $caption == '') {
            throw new CannotPublishException(sprintf(
                _("%s requires all images to have captions. Please provide one."),
                Okapi::get_normalized_site_name()
            ));
        }

        $is_spoiler = $request->get_parameter('is_spoiler');
        if ($is_spoiler !== null) {
            if (!in_array($is_spoiler, array('true', 'false')))
                throw new InvalidParam('is_spoiler');
        }

        $position = LogImagesCommon::validate_position($request);

        if ($caption === null && $is_spoiler === null && $position === null) {
            # If no-params were allowed, what would be the success message?
            # It's more reasonable to assume that this was a developer's error.
            throw new BadRequest(
                "At least one of the parameters 'caption', 'is_spoiler' and 'position' must be supplied"
            );
        }

        $image_uuid_escaped = Db::escape_string($image_uuid);
        $log_entry_modified = false;

        # update caption
        if ($caption !== null) {
            Db::execute("
                update pictures
                set title = '".Db::escape_string($caption)."'
                where uuid = '".$image_uuid_escaped."'
            ");
            $log_entry_modified = true;
        }

        # update spoiler flag
        if ($is_spoiler !== null) {
            Db::execute("
                update pictures
                set spoiler = ".($is_spoiler == 'true' ? 1 : 0)."
                where uuid = '".$image_uuid_escaped."'
            ");
            $log_entry_modified = true;
        }

        # update position
        if ($position !== null)
        {
            if (Settings::get('OC_BRANCH') == 'oc.pl')
            {
                # OCPL as no arbitrary log picture ordering => ignore position parameter
                # and return the picture's current position.

                $image_uuids = Db::select_column("
                    select uuid from pictures
                    where object_type = 1 and object_id = '".Db::escape_string($log_internal_id)."'
                    order by date_created
                ");
                $position = array_search($image_uuid, $image_uuids);
            }
            else
            {
                list($position, $seq) = LogImagesCommon::prepare_position(
                    $log_internal_id,
                    $position,
                    0
                );
                # For OCDE the pictures table is write locked now.

                $old_seq = DB::select_value("
                    select seq from pictures where uuid = '".$image_uuid_escaped."'
                ");

                if ($seq != $old_seq)
                {
                    # First move the edited picture to the end, to make space for rotating.
                    # Remember that we have no transactions at OC.de. If something goes wrong,
                    # the image will stay at the end of the list.

                    $max_seq = Db::select_value("
                        select max(seq)
                        from pictures
                        where object_type = 1 and object_id = '".Db::escape_string($log_internal_id)."'
                    ");

                    Db::query("
                        update pictures
                        set seq = '".Db::escape_string($max_seq + 1)."'
                        where uuid = '".$image_uuid_escaped."'
                    ");

                    # now move the pictures inbetween
                    if ($seq < $old_seq) {
                        Db::execute("
                            update pictures
                            set seq = seq + 1
                            where
                                object_type = 1
                                and object_id = '".Db::escape_string($log_internal_id)."'
                                and seq >= '".Db::escape_string($seq)."'
                                and seq < '".Db::escape_string($old_seq)."'
                            order by seq desc
                        ");
                    } else {
                        Db::execute("
                            update pictures
                            set seq = seq - 1
                            where
                                object_type = 1
                                and object_id = '".Db::escape_string($log_internal_id)."'
                                and seq <= '".Db::escape_string($seq)."'
                                and seq > '".Db::escape_string($old_seq)."'
                            order by seq asc
                        ");
                    }

                    # and finally move the edited picture into place
                    Db::query("
                        update pictures
                        set seq = '".Db::escape_string($seq)."'
                        where uuid = '".$image_uuid_escaped."'
                    ");
                }

                Db::execute('unlock tables');
                $log_entry_modified = true;
            }
        }

        if (Settings::get('OC_BRANCH') == 'oc.pl' && $log_entry_modified) {
            # OCDE touches the log entry via trigger, OCPL needs an explicit update.
            # This will also update okapi_syncbase.

            Db::query("
                update cache_logs
                set last_modified = NOW()
                where id = '".Db::escape_string($log_internal_id)."'
            ");

            # OCPL code currently does not update pictures.last_modified when
            # editing, but that is a bug, see
            # https://github.com/opencaching/opencaching-pl/issues/341.
        }

        return $position;
    }

    public static function call(OkapiRequest $request)
    {
        # This is the "real" entry point. A wrapper for the _call method.

        $langpref = $request->get_parameter('langpref');
        if (!$langpref) $langpref = "en";
        $langprefs = explode("|", $langpref);
        Okapi::gettext_domain_init($langprefs);

        try
        {
            $position = self::_call($request);
            $result = array(
                'success' => true,
                'message' => _("Image properties have been successfully updated."),
                'position' => $position
            );
            Okapi::gettext_domain_restore();
        }
        catch (CannotPublishException $e)
        {
            Okapi::gettext_domain_restore();
            $result = array(
                'success' => false,
                'message' => $e->getMessage(),
                'position' => null
            );
        }

        Okapi::update_user_activity($request);
        return Okapi::formatted_response($request, $result);
    }

}
