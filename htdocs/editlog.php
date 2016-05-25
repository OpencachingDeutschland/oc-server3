<?php
/****************************************************************************
 *  For license information see doc/license.txt
 *
 *  edit a cache log
 *
 *  used template(s): editlog
 *  GET/POST Parameter: logid
 *
 *  Note: when changing recommendation, the last_modified-date of log-record
 *        has to be updated to trigger resync via xml-interface
 *
 *  Unicode Reminder メモ
 *****************************************************************************/

require_once __DIR__ . '/lib/consts.inc.php';
$opt['gui'] = GUI_HTML;
require_once __DIR__ . '/lib/common.inc.php';
require_once __DIR__ . '/lib2/logic/logtypes.inc.php';
require_once __DIR__ . '/lib/recommendation.inc.php';
require_once __DIR__ . '/lib2/edithelper.inc.php';

//Preprocessing
if ($error == false) {
    //logid
    $log_id = 0;
    if (isset($_REQUEST['logid'])) { // Ocprop
        $log_id = $_REQUEST['logid'];
    }

    if ($usr === false) {
        $tplname = 'login';

        tpl_set_var('username', '');
        tpl_set_var('message_start', '');
        tpl_set_var('message_end', '');
        tpl_set_var('target', 'editlog.php?logid=' . urlencode($log_id));
        tpl_set_var('message', $login_required);
        tpl_set_var('helplink', helppagelink('login'));
    } else {
        $useradmin = ($login->admin & ADMIN_USER) ? 1 : 0;

        //does log with this logid exist?
        $log_rs = sql(
            "
                SELECT
                    `cache_logs`.`id` AS `log_id`,
                    `cache_logs`.`cache_id` AS `cache_id`,
                    `cache_logs`.`node` AS `node`,
                    `cache_logs`.`text` AS `text`,
                    `cache_logs`.`date` AS `date`,
                    `cache_logs`.`user_id` AS `user_id`,
                    `cache_logs`.`type` AS `logtype`,
                    `cache_logs`.`oc_team_comment` AS `oc_team_comment`,
                    `cache_logs`.`text_html` AS `text_html`,
                    `cache_logs`.`text_htmledit` AS `text_htmledit`,
                    `caches`.`name` AS `cachename`,
                    `caches`.`type` AS `cachetype`,
                    `caches`.`user_id` AS `cache_user_id`,
                    `caches`.`logpw` AS `logpw`,
                    `caches`.`status` AS `status`,
                    `log_types`.`cache_status` > 0 AS `is_status_log`
                FROM `cache_logs`
                JOIN `log_types` ON `log_types`.`id`=`cache_logs`.`type`
                INNER JOIN `caches` ON `caches`.`cache_id`=`cache_logs`.`cache_id`
                WHERE `cache_logs`.`id`='&1'",
            $log_id
        );
        $log_record = sql_fetch_array($log_rs);
        sql_free_result($log_rs);

        if (($log_record !== false && (($log_record['status'] != 6) || ($log_record['cache_user_id'] == $login->userid && $log_record['user_id'] == $login->userid)) &&
                $log_record['status'] != 7) || $useradmin
        ) {
            require $stylepath . '/editlog.inc.php';
            require $stylepath . '/rating.inc.php';

            if ($log_record['node'] != $oc_nodeid) {
                tpl_errorMsg('editlog', $error_wrong_node);
                exit;
            }

            //is this log from this user?
            if ($log_record['user_id'] == $usr['userid']) {
                $tplname = 'editlog';

                //load settings
                $cache_name = $log_record['cachename'];
                $cache_type = $log_record['cachetype'];
                $cache_user_id = $log_record['cache_user_id'];

                // Ocprop:
                //  logtype, logday, logmonth, logyear, rating, submitform

                $log_type = isset($_POST['logtype']) ? $_POST['logtype'] : $log_record['logtype'];
                $log_date_day = isset($_POST['logday']) ? trim($_POST['logday']) : date(
                    'd',
                    strtotime($log_record['date'])
                );
                $log_date_month = isset($_POST['logmonth']) ? trim($_POST['logmonth']) : date(
                    'm',
                    strtotime(
                        $log_record['date']
                    )
                );
                $log_date_year = isset($_POST['logyear']) ? trim($_POST['logyear']) : date(
                    'Y',
                    strtotime(
                        $log_record['date']
                    )
                );
                $log_time_hour = isset($_POST['loghour']) ? trim($_POST['loghour']) : (substr(
                    $log_record['date'],
                    11
                ) == "00:00:00" ? "" : date('H', strtotime($log_record['date'])));
                $log_time_minute = isset($_POST['logminute']) ? trim($_POST['logminute']) : (substr(
                    $log_record['date'],
                    11
                ) == "00:00:00" ? "" : date('i', strtotime($log_record['date'])));
                $top_option = isset($_POST['ratingoption']) ? $_POST['ratingoption'] + 0 : 0;
                $top_cache = isset($_POST['rating']) ? $_POST['rating'] + 0 : 0;
                $oc_team_comment = isset($_POST['submitform']) ? @$_POST['teamcomment'] + 0 : ($log_record['oc_team_comment'] == 1);

                $log_pw = '';
                $use_log_pw = (($log_record['logpw'] == null) || ($log_record['logpw'] == '')) ? false : true;
                if (($use_log_pw) && $log_record['logtype'] == 1) {
                    $use_log_pw = false;
                }

                if ($use_log_pw) {
                    $log_pw = $log_record['logpw'];
                }

                // check if user has exceeded his top 10% limit
                $is_top = sqlValue(
                    "SELECT COUNT(`cache_id`) FROM `cache_rating` WHERE `user_id`='" . sql_escape(
                        $usr['userid']
                    ) . "' AND `cache_id`='" . sql_escape($log_record['cache_id']) . "'",
                    0
                );
                $user_founds = sqlValue(
                    "SELECT IFNULL(`found`, 0)
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

                $rating_percentage = $opt['logic']['rating']['percentageOfFounds'];
                if ($is_top || ($user_tops < floor($user_founds * $rating_percentage / 100))) {
                    $rating_msg = mb_ereg_replace(
                        '{chk_sel}',
                        ($is_top ? 'checked' : ''),
                        $rating_allowed . '<br />' . $rating_stat
                    );
                    $rating_msg = mb_ereg_replace('{max}', floor($user_founds * $rating_percentage / 100), $rating_msg);
                    $rating_msg = mb_ereg_replace('{curr}', $user_tops, $rating_msg);
                } else {
                    $anzahl = ($user_tops + 1 - ($user_founds * $rating_percentage / 100)) / ($rating_percentage / 100);
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

                if (isset($_POST['descMode'])) {
                    $descMode = $_POST['descMode'] + 0;  // Ocprop: 2
                    if (($descMode < 1) || ($descMode > 3)) {
                        $descMode = 3;
                    }
                    if (isset($_POST['oldDescMode'])) {
                        $oldDescMode = $_POST['oldDescMode'];
                        if (($oldDescMode < 1) || ($oldDescMode > 3)) {
                            $oldDescMode = $descMode;
                        }
                    } else {
                        $oldDescMode = $descMode;
                    }
                } else {
                    if ($log_record['text_html'] == 1) {
                        if ($log_record['text_htmledit'] == 1) {
                            $descMode = 3;
                        } else {
                            $descMode = 2;
                        }
                    } else {
                        $descMode = 1;
                    }

                    $oldDescMode = $descMode;
                }

                // fuer alte Versionen von OCProp
                if (isset($_POST['submit']) && !isset($_POST['version2'])) {
                    $descMode = 1;
                    $_POST['submitform'] = $_POST['submit'];
                }

                // Text from textarea; Ocprop
                if (isset($_POST['logtext'])) {
                    $log_text = $_POST['logtext'];
                } else {
                    $log_text = $log_record['text'];
                    if ($descMode == 1) {
                        $oldDescMode = 0;
                    }   // plain text with encoded HTML entities
                }

                // fuer alte Versionen von OCProp
                if ($descMode != 1 && isset($_POST['submit']) && !isset($_POST['version2'])) {
                    $log_text = iconv("ISO-8859-1", "UTF-8", $log_text);
                }

                $log_text = processEditorInput($oldDescMode, $descMode, $log_text);

                //validate date
                $date_ok = false;
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
                            $checkMkTime = mktime(
                                $log_time_hour + 0,
                                $log_time_minute + 0,
                                0,
                                $log_date_month,
                                $log_date_day,
                                $log_date_year
                            );
                            if ($checkMkTime >= time()) {
                                $date_ok = false;
                            }
                        }
                    }
                }

                $logtype_ok = logtype_ok($log_record['cache_id'], $log_type, $log_record['logtype']);

                // not a found log? then ignore the rating
                if ($log_type != 1 && $log_type != 7) {
                    $top_option = 0;
                }

                $pw_ok = true;
                if ($use_log_pw && $log_type == 1) {
                    if (!isset($_POST['log_pw']) || mb_strtolower($log_pw) != mb_strtolower($_POST['log_pw'])) {
                        $pw_ok = false;
                        $all_ok = false;
                    }
                }

                // ignore unauthorized team comments
                if (!teamcomment_allowed($log_record['cache_id'], $log_type, $log_record['oc_team_comment'])) {
                    $oc_team_comment = 0;
                }

                //store?
                if ($date_ok && $logtype_ok && $pw_ok && isset($_POST['submitform'])) { // Ocprop
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

                    // evtl. discard cache recommendation if the log type was changed from
                    // 'found' or 'attended' to something else
                    if (!$top_option) {
                        discard_recommendation($log_id);
                    }

                    // store changed data
                    sql(
                        "UPDATE `cache_logs`
                         SET `type`='&1',
                             `oc_team_comment`='&2',
                             `date`='&3',
                             `text`='&4',
                             `text_html`='&5',
                             `text_htmledit`='&6'
                         WHERE `id`='&7'",
                        $log_type,
                        $oc_team_comment,
                        $log_date,
                        $log_text,
                        (($descMode != 1) ? 1 : 0),
                        (($descMode == 3) ? 1 : 0),
                        $log_id
                    );

                    // update cache status if changed by logtype
                    if (is_latest_log($log_record['cache_id'], $log_record['log_id'])) {
                        $newstatus = sqlValue(
                            "SELECT `cache_status` FROM `log_types`
                             WHERE `id`='" . sql_escape($log_type) . "'",
                            false
                        );
                        if ($newstatus && $newstatus != $log_record['status']) {
                            sql("SET @STATUS_CHANGE_USER_ID='&1'", $login->userid);
                            sql(
                                "UPDATE `caches` SET `status`='" . sql_escape($newstatus) . "'
                                 WHERE `cache_id`='" . sql_escape($log_record['cache_id']) . "'"
                            );
                        }
                    }

                    //update user-stat if type changed
                    if ($log_record['logtype'] != $log_type) {
                        //call eventhandler
                        require_once $opt['rootpath'] . 'lib/eventhandler.inc.php';
                        event_change_log_type($log_record['cache_id'], $usr['userid'] + 0);
                    }

                    // update top-list
                    if ($top_option) {
                        if ($top_cache) {
                            sql(
                                "INSERT INTO `cache_rating` (`user_id`, `cache_id`, `rating_date`)
                                 VALUES('&1','&2','&3')
                                 ON DUPLICATE KEY UPDATE `rating_date`='&3'",
                                $usr['userid'],
                                $log_record['cache_id'],
                                $log_date
                            );
                            // cache_rating.rating_date is updated when it already exists, so that
                            // it stays consistent with cache_logs.date when editing a log date.

                            // When editing one of multiple found logs, this will move rating_date
                            // to the last edited record. While this may not always be what the user
                            // expects, it makes sense for two reasons:
                            //   1. It is a safeguard for the case that the log date and rating_date
                            //      have gotten out of sync for some reason (which has happend in the
                            //      past, probably due to a log-deletion related bug).
                            //   2. It can be used as a tweak to control which log's date is relevant
                            //      for the rating, e.g. when logging a second found on a recycled or
                            //      renewed cache [listing].
                        } else {
                            sql(
                                "DELETE FROM `cache_rating` WHERE `user_id`='&1' AND `cache_id`='&2'",
                                $usr['userid'],
                                $log_record['cache_id']
                            );
                        }
                    }

                    // do not use slave server for the next time ...
                    db_slave_exclude();

                    //display cache page
                    tpl_redirect(
                        'viewcache.php?cacheid=' . urlencode($log_record['cache_id']) . '&log=A#log' . urlencode(
                            $log_id
                        )
                    );
                    exit;
                }

                // build logtype options
                $disable_statuschange = (
                    $log_record['cache_user_id'] == $login->userid
                    && !is_latest_log($log_record['cache_id'], $log_record['log_id'])
                );

                $logtype_names = get_logtype_names();
                $allowed_logtypes = get_cache_log_types(
                    $log_record['cache_id'],
                    $log_record['logtype'],
                    !$disable_statuschange
                );
                $logtypeoptions = '';
                foreach ($allowed_logtypes as $logtype) {
                    $selected = ($log_record['logtype'] == $logtype ? ' selected="selected"' : '');
                    $logtypeoptions .= '<option value="' . $logtype . '"' . $selected . '>';
                    $logtypeoptions .= htmlspecialchars($logtype_names[$logtype], ENT_COMPAT, 'UTF-8');
                    $logtypeoptions .= '</option>' . "\n";
                }
                $disable_typechange = $disable_statuschange && $log_record['is_status_log'];
                tpl_set_var('type_edit_disabled', $disable_typechange ? $type_edit_disabled : '');

                // TODO: Enforce the 'diables' when processing the posted data.
                // It's not that urgent, because nothing can be broken by changing
                // past status log types (it was even allowed up to OC 3.0.17);
                // just the log history may look weird.

                if (teamcomment_allowed($log_record['cache_id'], 3, $log_record['oc_team_comment'])) {
                    tpl_set_var(
                        'teamcommentoption',
                        mb_ereg_replace('{chk_sel}', ($oc_team_comment ? 'checked' : ''), $teamcomment_field)
                    );
                } else {
                    tpl_set_var('teamcommentoption', '');
                }

                //set template vars
                tpl_set_var('cachename', htmlspecialchars($cache_name, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('logtypeoptions', $logtypeoptions);
                tpl_set_var('logday', htmlspecialchars($log_date_day, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('logmonth', htmlspecialchars($log_date_month, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('logyear', htmlspecialchars($log_date_year, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('loghour', htmlspecialchars($log_time_hour, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('logminute', htmlspecialchars($log_time_minute, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('cachename', htmlspecialchars($cache_name, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('cacheid', $log_record['cache_id']);
                tpl_set_var('reset', $reset);  // obsolete
                tpl_set_var('submit', $submit);
                tpl_set_var('logid', $log_id);
                tpl_set_var('date_message', !$date_ok ? $date_message : '');

                if ($descMode != 1) {
                    tpl_set_var('logtext', htmlspecialchars($log_text, ENT_COMPAT, 'UTF-8'), true);
                } else {
                    tpl_set_var('logtext', $log_text);
                }

                // Text / normal HTML / HTML editor
                tpl_set_var('use_tinymce', (($descMode == 3) ? 1 : 0));

                $headers = tpl_get_var('htmlheaders') . "\n";
                if ($descMode == 1) {
                    tpl_set_var('descMode', 1);
                } else {
                    if ($descMode == 2) {
                        tpl_set_var('descMode', 2);
                    } else {
                        // TinyMCE
                        $headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/tiny_mce_gzip.js"></script>' . "\n";
                        $headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/config/log.js.php?logid=0&lang=' .
                            strtolower(
                                $locale
                            ) . '"></script>' . "\n";
                        tpl_set_var('descMode', 3);
                    }
                }
                $headers .= '<script language="javascript" type="text/javascript" src="' . editorJsPath() . '"></script>' . "\n";
                tpl_set_var('htmlheaders', $headers);

                if ($use_log_pw == true && $log_pw != '') {
                    if (!$pw_ok && isset($_POST['submitform'])) {
                        tpl_set_var('log_pw_field', $log_pw_field_pw_not_ok);
                    } else {
                        tpl_set_var('log_pw_field', $log_pw_field);
                    }
                } else {
                    tpl_set_var('log_pw_field', '');
                }

                // build smilies
                $smilies = '';
                if ($descMode != 3) {
                    for ($i = 0; $i < count($smiley['show']); $i++) {
                        if ($smiley['show'][$i] == '1') {
                            $tmp_smiley = $smiley_link;
                            $tmp_smiley = mb_ereg_replace('{smiley_image}', $smiley['image'][$i], $tmp_smiley);
                            $tmp_smiley = mb_ereg_replace('{smiley_symbol}', $smiley['text'][$i], $tmp_smiley);
                            $smilies = $smilies . '&nbsp;' .
                                mb_ereg_replace(
                                    '{smiley_file}',
                                    $smiley['file'][$i],
                                    $tmp_smiley
                                ) . '&nbsp;';
                        }
                    }
                    tpl_set_var('smileypath', $opt['template']['smiley']);
                }
                tpl_set_var('smilies', $smilies);
            } else {
                //TODO: show error
            }
        } else {
            //TODO: show error
        }
    }
}

tpl_set_var('scrollposx', isset($_REQUEST['scrollposx']) ? $_REQUEST['scrollposx'] + 0 : 0);
tpl_set_var('scrollposy', isset($_REQUEST['scrollposy']) ? $_REQUEST['scrollposy'] + 0 : 0);

//make the template and send it out
tpl_BuildTemplate();


function is_latest_log($cache_id, $log_id)
{
    $lastest_log_id = sqlValue(
        "
        SELECT `id` FROM `cache_logs`
        WHERE `cache_id`='" . sql_escape($cache_id) . "'
        ORDER BY `order_date` DESC, `date_created` DESC, `id` DESC
        LIMIT 1",
        0,
        $cache_id
    );

    return ($log_id == $lastest_log_id);
}
