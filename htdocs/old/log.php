<?php
/***************************************************************************
 * ./log.php
 * -------------------
 * begin                : July 4 2004
 *
 * For license information see doc/license.txt
 *****************************************************************************/

/****************************************************************************
 *
 * Unicode Reminder メモ
 *
 * log a cache visit
 *
 * used template(s): log
 *
 * GET Parameter: cacheid
 ****************************************************************************/

//prepare the templates and include all neccessary
require_once('./lib/common.inc.php');
require_once('./lib/logtypes.inc.php');
require($stylepath . '/smilies.inc.php');
require_once($opt['rootpath'] . '../lib/htmlpurifier-4.2.0/library/HTMLPurifier.auto.php');

$no_tpl_build = false;
//Preprocessing
if ($error == false) {
    //cacheid
    $cache_id = 0;
    if (isset($_REQUEST['wp'])) {
        $cache_id = sqlValue("SELECT `cache_id` FROM `caches` WHERE `wp_oc`='" . sql_escape($_REQUEST['wp']) . "'", 0);
    } else {
        if (isset($_REQUEST['cacheid'])) {
            $cache_id = $_REQUEST['cacheid'];
        }
    }

    if ($usr === false) {
        $tplname = 'login';

        tpl_set_var('username', '');
        tpl_set_var('target', 'log.php?cacheid=' . urlencode($cache_id));
        tpl_set_var('message', $login_required);
        tpl_set_var('message_start', '');
        tpl_set_var('message_end', '');
    } else {
        //set here the template to process
        $tplname = 'log_cache';

        require($stylepath . '/log_cache.inc.php');
        require($stylepath . '/rating.inc.php');

        $useradmin = ($login->admin & ADMIN_USER) ? 1 : 0;
        $adminlog = false;

        $cachename = '';
        if ($cache_id != 0) {
            //get cachename
            $rs = sql(
                "SELECT `caches`.`name`, `caches`.`user_id`, `caches`.`logpw`,
				                  `caches`.`wp_gc`, `caches`.`wp_nc`, `caches`.`type`,
				                  `caches`.`status`,
				                  `cache_status`.`allow_user_log`=1 OR `caches`.`user_id`='&1' AS `log_allowed`
				             FROM `caches`
				       INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
							      WHERE (`cache_status`.`allow_user_log`=1 OR `caches`.`user_id`='&1' OR '&3')
										      AND `caches`.`cache_id`='&2'",
                $usr['userid'],
                $cache_id,
                $useradmin
            );

            if (mysql_num_rows($rs) == 0) {
                $cache_id = 0;
            } else {
                $record = sql_fetch_array($rs);
                $adminlog = !($record['log_allowed']) && $useradmin;

                // only the owner and admins are allowed to make logs to not published caches
                if ($record['user_id'] == $usr['userid'] || $record['status'] != 5 || $useradmin) {
                    $cachename = $record['name'];
                    $cache_user_id = $record['user_id'];
                    $use_log_pw = (($record['logpw'] == null) || ($record['logpw'] == '')) ? false : true;
                    if ($use_log_pw) {
                        $log_pw = $record['logpw'];
                    }
                    $wp_gc = $record['wp_gc'];
                    $wp_nc = $record['wp_nc'];
                    $cache_type = $record['type'];
                } else {
                    $cache_id = 0;
                }
            }

            sql_free_result($rs);
        }

        if ($cache_id != 0) {
            $all_ok = false;
            $log_text = isset($_POST['logtext']) ? ($_POST['logtext']) : '';
            $log_type = isset($_POST['logtype']) ? ($_POST['logtype'] + 0) : null;
            $log_date_day = isset($_POST['logday']) ? trim($_POST['logday']) : date('d');
            $log_date_month = isset($_POST['logmonth']) ? trim($_POST['logmonth']) : date('m');
            $log_date_year = isset($_POST['logyear']) ? trim($_POST['logyear']) : date('Y');
            $log_time_hour = isset($_POST['loghour']) ? trim($_POST['loghour']) : "";
            $log_time_minute = isset($_POST['logminute']) ? trim($_POST['logminute']) : "";
            $top_option = isset($_POST['ratingoption']) ? $_POST['ratingoption'] + 0 : 0;
            $top_cache = isset($_POST['rating']) ? $_POST['rating'] + 0 : 0;
            $oc_team_comment = isset($_POST['teamcomment']) ? ($_POST['teamcomment'] != 0) : 0;

            // check if user has exceeded his top5% limit
            $user_founds = sqlValue(
                "SELECT IFNULL(`stat_user`.`found`, 0)
                FROM `user`
                LEFT JOIN `stat_user` ON `user`.`user_id`=`stat_user`.`user_id`
                WHERE `user`.`user_id`='" . sql_escape(
                    $usr['userid']
                ) . "'",
                0
            );
            $user_tops = sqlValue(
                "SELECT COUNT(`user_id`) FROM `cache_rating` WHERE `user_id`='" . sql_escape($usr['userid']) . "'",
                0
            );

            if ($user_tops < floor($user_founds * rating_percentage / 100)) {
                // initialize checkbox with value of past recommandation for this cache (if one exists)
                $recommended = sqlValue(
                    "SELECT COUNT(`user_id`) FROM `cache_rating` WHERE `user_id`='" . sql_escape(
                        $usr['userid']
                    ) . "' AND `cache_id`='" . sql_escape($cache_id) . "'",
                    0
                );
                $rating_msg = mb_ereg_replace(
                    '{chk_sel}',
                    $recommended ? 'checked' : '',
                    $rating_allowed . '<br />' . $rating_stat
                );
                $rating_msg = mb_ereg_replace('{max}', floor($user_founds * rating_percentage / 100), $rating_msg);
                $rating_msg = mb_ereg_replace('{curr}', $user_tops, $rating_msg);
            } else {
                $anzahl = ($user_tops + 1 - ($user_founds * rating_percentage / 100)) / (rating_percentage / 100);
                if ($anzahl > 1) {
                    $rating_msg = mb_ereg_replace('{anzahl}', $anzahl, $rating_too_few_founds);
                } else {
                    $rating_msg = mb_ereg_replace('{anzahl}', $anzahl, $rating_too_few_founds);
                }
                if ($user_tops) {
                    $rating_msg .= '<br />' . $rating_maywithdraw;
                }
            }
            tpl_set_var('rating_message', mb_ereg_replace('{rating_msg}', $rating_msg, $rating_tpl));

            // descMode auslesen, falls nicht gesetzt aus dem Profil laden
            if (isset($_POST['descMode'])) {
                $descMode = $_POST['descMode'] + 0;
            } else {
                if (sqlValue(
                        "SELECT `no_htmledit_flag` FROM `user` WHERE `user_id`='" . sql_escape($usr['userid']) . "'",
                        1
                    ) == 1
                ) {
                    $descMode = 1;
                } else {
                    $descMode = 3;
                }
            }
            if (($descMode < 1) || ($descMode > 3)) {
                $descMode = 3;
            }

            // fuer alte Versionen von OCProp
            if ((isset($_POST['submit']) || isset($_POST['submitform'])) && !isset($_POST['version3'])) {
                die('Your client may be outdated!');
            }

            if ($descMode != 1) {
                // Filter Input
                $purifier = new HTMLPurifier();
                $log_text = $purifier->purify($log_text);
            } else {
                // escape text
                $log_text = nl2br(htmlspecialchars($log_text, ENT_COMPAT, 'UTF-8'));
            }

            // ignore unauthorized team comments
            if (!teamcomment_allowed($cache_id, $log_type)) {
                $oc_team_comment = 0;
            }

            // validate data
            if (is_numeric($log_date_month) && is_numeric($log_date_day) && is_numeric($log_date_year) &&
                ("$log_time_hour$log_time_minute" == "" || is_numeric($log_time_hour)) &&
                ($log_time_minute == "" || is_numeric($log_time_minute))
            ) {
                $date_ok = checkdate($log_date_month, $log_date_day, $log_date_year)
                    && ($log_date_year >= 2000)
                    && ($log_time_hour >= 0) && ($log_time_hour <= 23)
                    && ($log_time_minute >= 0) && ($log_time_minute <= 59);
                if ($date_ok) {
                    if (isset($_POST['submitform'])) {
                        if (mktime(
                                $log_time_hour + 0,
                                $log_time_minute + 0,
                                0,
                                $log_date_month,
                                $log_date_day,
                                $log_date_year
                            ) >= mktime()
                        ) {
                            $date_ok = false;
                        }
                    }
                }
            } else {
                $date_ok = false;
            }

            $logtype_ok = logtype_ok($cache_id, $log_type, 0);  // depends on userid

            // not a found log? then ignore the rating
            if ($log_type != 1 && $log_type != 7) {
                $top_option = 0;
            }

            $pw_ok = true;
            if (isset($_POST['submitform'])) {
                $all_ok = $date_ok && $logtype_ok;

                if ($all_ok && $use_log_pw && ($log_type == 1 || $log_type == 7)) {
                    if (!isset($_POST['log_pw']) ||
                        mb_strtolower($log_pw) != mb_strtolower($_POST['log_pw'])
                    ) {
                        $pw_ok = false;
                        $all_ok = false;
                    }
                }
            }

            if (isset($_POST['submitform']) && ($all_ok == true)) {
                // 00:00:01 = "00:00 was logged"
                // 00:00:00 = "no time was logged"
                if ("$log_time_hour$log_time_minute" != "" &&
                    $log_time_hour == 0 && $log_time_minute == 0
                ) {
                    $log_time_second = 1;
                } else {
                    $log_time_second = 0;
                }

                $log_date = date(
                    'Y-m-d H:i:s',
                    mktime(
                        $log_time_hour + 0,
                        $log_time_minute + 0,
                        $log_time_second,
                        $log_date_month,
                        $log_date_day,
                        $log_date_year
                    )
                );

                // add logentry to db if not already exists (e.g. by multiple sending the form
                // or by ocprop errors)
                $rs = sql(
                    "SELECT `id`
                    FROM `cache_logs`
					WHERE `cache_id`='&1'
					AND `user_id`='&2'
					AND `type`='&3'
					AND `date`='&4'
					AND `text`='&5'",
                    $cache_id,
                    $usr['userid'],
                    $log_type,
                    $log_date,
                    $log_text
                );
                $already_exists = (sql_fetch_row($rs) !== false);
                sql_free_result($rs);

                if (!$already_exists) {
                    // update cache_status
                    $new_cache_status = sqlValue(
                        "SELECT `cache_status` FROM `log_types` WHERE `id`='" . sql_escape($log_type) . "'",
                        0
                    );
                    if ($new_cache_status > 0) {
                        sql("SET @STATUS_CHANGE_USER_ID='&1'", $usr['userid']);
                        $rs = sql(
                            "UPDATE `caches` SET `status`='&1' WHERE `cache_id`='&2'",
                            $new_cache_status,
                            $cache_id
                        );
                    }

                    // insert log
                    sql(
                        "INSERT INTO `cache_logs` (`id`, `cache_id`, `user_id`, `type`, `oc_team_comment`, `date`, `text`, `text_html`, `text_htmledit`, `node`)
						         VALUES ('', '&1', '&2', '&3', '&4', '&5', '&6', '&7', '&8', '&9')",
                        $cache_id,
                        $usr['userid'],
                        $log_type,
                        $oc_team_comment,
                        $log_date,
                        $log_text,
                        (($descMode != 1) ? 1 : 0),
                        (($descMode == 3) ? 1 : 0),
                        $oc_nodeid
                    );

                    // do not use slave server for the next time ...
                    db_slave_exclude();

                    // update top-list
                    if ($top_option) {
                        if ($top_cache) {
                            sql(
                                "INSERT IGNORE INTO `cache_rating` (`user_id`, `cache_id`, `rating_date`)
                                VALUES('&1', '&2', '&3')",
                                $usr['userid'],
                                $cache_id,
                                $log_date
                            );
                        } else {
                            sql(
                                "DELETE FROM `cache_rating` WHERE `user_id`='&1' AND `cache_id`='&2'",
                                $usr['userid'],
                                $cache_id
                            );
                        }
                    }

                    //call eventhandler
                    require_once($rootpath . 'lib/eventhandler.inc.php');
                    event_new_log($cache_id, $usr['userid'] + 0);
                }

                //redirect to viewcache
                $no_tpl_build = true;
                //include('viewcache.php');
                tpl_redirect('viewcache.php?cacheid=' . $cache_id);
            } else {
                // build logtype options
                $logtype_names = get_logtype_names();
                $allowed_logtypes = get_cache_log_types($cache_id, 0);
                $default_log_type = isset($_REQUEST['logtype']) ? $_REQUEST['logtype'] + 0 : 0;
                if (!logtype_ok($cache_id, $default_log_type, 0)) {
                    $default_log_type = $allowed_logtypes[0];
                }

                $logtypeoptions = '';
                foreach ($allowed_logtypes as $logtype) {
                    $selected = ($logtype == $default_log_type ? ' selected="selected"' : '');
                    $logtypeoptions .= '<option value="' . $logtype . '"' . $selected . '>' . htmlspecialchars(
                            $logtype_names[$logtype],
                            ENT_COMPAT,
                            'UTF-8'
                        ) . '</option>' . "\n";
                }

                //set tpl vars
                tpl_set_var('cachename', htmlspecialchars($cachename, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('cacheid', htmlspecialchars($cache_id, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('logday', htmlspecialchars($log_date_day, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('logmonth', htmlspecialchars($log_date_month, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('logyear', htmlspecialchars($log_date_year, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('loghour', htmlspecialchars($log_time_hour, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('logminute', htmlspecialchars($log_time_minute, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('logtypeoptions', $logtypeoptions);
                tpl_set_var('reset', $reset);  // obsolete
                tpl_set_var('submit', $submit);
                tpl_set_var('date_message', '');

                tpl_set_var('adminlog', $adminlog);

                $oc_team_comment = isset($_REQUEST['teamcomment']) ? ($_REQUEST['teamcomment'] != 0) : 0;
                if (teamcomment_allowed($cache_id, 3)) {
                    $tco = mb_ereg_replace(
                        '{chk_sel}',
                        ($oc_team_comment || $adminlog ? 'checked' : ''),
                        $teamcomment_field
                    );
                    $tco = mb_ereg_replace('{textclass}', ($adminlog ? 'redtext' : ''), $tco);
                    tpl_set_var('teamcommentoption', $tco);
                } else {
                    tpl_set_var('teamcommentoption', '');
                }

                // Text / normal HTML / HTML editor
                tpl_set_var('use_tinymce', (($descMode == 3) ? 1 : 0));

                if ($descMode == 1) {
                    tpl_set_var('descMode', 1);
                } else {
                    if ($descMode == 2) {
                        tpl_set_var('descMode', 2);
                    } else {
                        // TinyMCE
                        $headers = tpl_get_var('htmlheaders') . "\n";
                        $headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/tiny_mce_gzip.js"></script>' . "\n";
                        $headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/config/log.js.php?logid=0&lang=' . strtolower(
                                $locale
                            ) . '"></script>' . "\n";
                        tpl_set_var('htmlheaders', $headers);

                        tpl_set_var('descMode', 3);
                    }
                }

                if ($descMode != 1) {
                    tpl_set_var('logtext', htmlspecialchars($log_text, ENT_COMPAT, 'UTF-8'), true);
                } else {
                    tpl_set_var('logtext', $log_text);
                }

                $listed_on = [];
                if ($wp_gc > "") {
                    $listed_on[] = '<a href="http://www.geocaching.com/seek/cache_details.aspx?wp=' . $wp_gc . '"  target="_blank">geocaching.com</a> <a href="http://www.geocaching.com/seek/log.aspx?wp=' . $wp_gc . '" target="_blank">(loggen)</a>';
                }
                if ($wp_nc > "") {
                    $listed_on[] = 'navicache.com';
                }

                if (sizeof($listed_on)) {
                    tpl_set_var('listed_start', "");
                    tpl_set_var('listed_end', "");
                    tpl_set_var('listed_on', sizeof($listed_on) == 0 ? $listed_only_oc : implode(", ", $listed_on));
                } else {
                    tpl_set_var('listed_start', "<!--");
                    tpl_set_var('listed_end', "-->");
                }

                if ($use_log_pw == true) {
                    if (!$pw_ok == true) {
                        tpl_set_var('log_pw_field', $log_pw_field_pw_not_ok);
                    } else {
                        if ($cache_type == 6) {
                            tpl_set_var('log_pw_field', $event_log_pw_field);
                        } else {
                            tpl_set_var('log_pw_field', $other_log_pw_field);
                        }
                    }
                } else {
                    tpl_set_var('log_pw_field', '');
                }

                if (!$date_ok) {
                    tpl_set_var('date_message', $date_message);
                }

                // build smilies
                $smilies = '';
                if ($descMode != 3) {
                    for ($i = 0; $i < count($smileyshow); $i ++) {
                        if ($smileyshow[$i] == '1') {
                            $tmp_smiley = $smiley_link;
                            $tmp_smiley = mb_ereg_replace('{smiley_image}', $smileyimage[$i], $tmp_smiley);
                            $smilies = $smilies . mb_ereg_replace(
                                    '{smiley_text}',
                                    ' ' . $smileytext[$i] . ' ',
                                    $tmp_smiley
                                ) . '&nbsp;';
                        }
                    }
                }
                tpl_set_var('smilies', $smilies);
            }
        } else {
            // no cache found
            $no_tpl_build = true;
        }
    }
}

if ($no_tpl_build == false) {
    //make the template and send it out
    tpl_BuildTemplate(false);
}
