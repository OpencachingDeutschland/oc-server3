<?php
/****************************************************************************
 * for license information see LICENSE.md
 *  edit a cache listing
 *  used template(s): editcache
 *  GET/POST Parameter: cacheid
 *****************************************************************************/

use Oc\GeoCache\StatisticPicture;
use Oc\Libse\ChildWp\HandlerChildWp;
use Oc\Libse\Coordinate\FormatterCoordinate;

require_once __DIR__ . '/lib/consts.inc.php';
$opt['gui'] = GUI_HTML;
require_once __DIR__ . '/lib/common.inc.php';

function getWaypoints($cacheId)
{
    global $waypointline;
    global $waypointlines;
    global $nowaypoints;

    $wpHandler = new HandlerChildWp();
    $wayPoints = $wpHandler->getChildWps($cacheId);
    $ret = '';

    if (!empty($wayPoints)) {
        $formatter = new FormatterCoordinate();

        foreach ($wayPoints as $wayPoint) {
            $tmpLine = $waypointline;

            $tmpLine = mb_ereg_replace(
                '{wp_image}',
                htmlspecialchars($wayPoint['image'], ENT_COMPAT, 'UTF-8'),
                $tmpLine
            );
            $tmpLine = mb_ereg_replace('{wp_type}', htmlspecialchars($wayPoint['name'], ENT_COMPAT, 'UTF-8'), $tmpLine);
            $htmlCoordinate = $formatter->formatHtml(
                $wayPoint['coordinate'],
                '</td></tr><tr><td style="white-space:nowrap">'
            );
            $tmpLine = mb_ereg_replace('{wp_coordinate}', $htmlCoordinate, $tmpLine);
            $tmpLine = mb_ereg_replace(
                '{wp_description}',
                htmlspecialchars(trim($wayPoint['description']), ENT_COMPAT, 'UTF-8'),
                $tmpLine
            );
            $tmpLine = mb_ereg_replace(
                '{wp_show_description}',
                mb_ereg_replace('\r\n', '<br />', htmlspecialchars($wayPoint['description'], ENT_COMPAT, 'UTF-8')),
                $tmpLine
            );
            $tmpLine = mb_ereg_replace('{cacheid}', htmlspecialchars($cacheId, ENT_COMPAT, 'UTF-8'), $tmpLine);
            $tmpLine = mb_ereg_replace(
                '{childid}',
                htmlspecialchars($wayPoint['childid'], ENT_COMPAT, 'UTF-8'),
                $tmpLine
            );

            $ret .= $tmpLine;
        }

        $ret = mb_ereg_replace('{lines}', $ret, $waypointlines);

        return $ret;
    }

    return $nowaypoints;
}

//Preprocessing
if ($error == false) {
    $cache_id = 0;
    if (isset($_REQUEST['cacheid'])) {
        $cache_id = (int) $_REQUEST['cacheid'];
    }

    if ($usr === false) {
        $tplname = 'login';

        tpl_set_var('username', '');
        tpl_set_var('target', 'editcache.php?cacheid=' . urlencode($cache_id));
        tpl_set_var('message_start', '');
        tpl_set_var('message_end', '');
        tpl_set_var('message', $login_required);
        tpl_set_var('helplink', helppagelink('login'));
    } else {
        $cache_rs = sql(
            "
                SELECT
                    `caches`.`uuid`,
                    `caches`.`user_id`,
                    `caches`.`name`,
                    `caches`.`type`,
                    `caches`.`size`,
                    `caches`.`date_created`,
                    `caches`.`date_hidden`,
                    `caches`.`date_activate`,
                    `caches`.`longitude`,
                    `caches`.`latitude`,
                    `caches`.`country`,
                    `caches`.`terrain`,
                    `caches`.`difficulty`,
                    `caches`.`desc_languages`,
                    `caches`.`status`,
                    `caches`.`search_time`,
                    `caches`.`way_length`,
                    `caches`.`logpw`,
                    `caches`.`wp_oc`,
                    `caches`.`wp_gc`,
                    `caches`.`show_cachelists`,
                    `caches`.`protect_old_coords`,
                    `caches`.`node`,
                    `user`.`username`,
                    `stat_caches`.`picture`
                FROM `caches`
                INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
                LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id`
                WHERE `caches`.`cache_id`='&1'",
            $cache_id
        );
        $cache_record = sql_fetch_array($cache_rs);
        sql_free_result($cache_rs);

        if ($cache_record !== false) {
            if ($cache_record['user_id'] == $usr['userid'] || $login->listingAdmin()) {
                $tplname = 'editcache';
                tpl_acceptsAndPurifiesHtmlInput();

                require $stylepath . '/editcache.inc.php';

                if ($cache_record['node'] != $oc_nodeid) {
                    tpl_errorMsg('editcache', $error_wrong_node);
                    exit;
                }

                //here we read all used information from the form if submitted, otherwise from DB
                $cache_name = trim(isset($_POST['name']) ? trim($_POST['name']) : $cache_record['name']);  // Ocprop
                $cache_type = isset($_POST['type']) ? $_POST['type'] : $cache_record['type'];
                if (!isset($_POST['size'])) {
                    if ($cache_type == 4 || $cache_type == 5) {
                        $sel_size = 7;
                    } else {
                        $sel_size = $cache_record['size'];
                    }
                } else {
                    $sel_size = isset($_POST['size']) ? $_POST['size'] : $cache_record['size'];
                }
                $cache_hidden_day = isset($_POST['hidden_day']) ? $_POST['hidden_day'] : date(
                    'd',
                    strtotime($cache_record['date_hidden'])
                );  // Ocprop
                $cache_hidden_month = isset($_POST['hidden_month']) ? $_POST['hidden_month'] : date(
                    'm',
                    strtotime($cache_record['date_hidden'])
                );  // Ocprop
                $cache_hidden_year = isset($_POST['hidden_year']) ? $_POST['hidden_year'] : date(
                    'Y',
                    strtotime($cache_record['date_hidden'])
                );  // Ocprop

                if (is_null($cache_record['date_activate'])) {
                    $cache_activate_day = isset($_POST['activate_day']) ? $_POST['activate_day'] : date('d');
                    $cache_activate_month = isset($_POST['activate_month']) ? $_POST['activate_month'] : date('m');
                    $cache_activate_year = isset($_POST['activate_year']) ? $_POST['activate_year'] : date('Y');
                    $cache_activate_hour = isset($_POST['activate_hour']) ? $_POST['activate_hour'] : date('H');
                } else {
                    $cache_activate_day = isset($_POST['activate_day']) ? $_POST['activate_day'] : date(
                        'd',
                        strtotime($cache_record['date_activate'])
                    );
                    $cache_activate_month = isset($_POST['activate_month']) ? $_POST['activate_month'] : date(
                        'm',
                        strtotime($cache_record['date_activate'])
                    );
                    $cache_activate_year = isset($_POST['activate_year']) ? $_POST['activate_year'] : date(
                        'Y',
                        strtotime($cache_record['date_activate'])
                    );
                    $cache_activate_hour = isset($_POST['activate_hour']) ? $_POST['activate_hour'] : date(
                        'H',
                        strtotime($cache_record['date_activate'])
                    );
                }

                $cache_difficulty = isset($_POST['difficulty']) ? $_POST['difficulty'] : $cache_record['difficulty'];  // Ocprop
                $cache_terrain = isset($_POST['terrain']) ? $_POST['terrain'] : $cache_record['terrain'];  // Ocprop
                $cache_country = isset($_POST['country']) ? $_POST['country'] : $cache_record['country'];  // Ocprop
                $show_all_countries = isset($_POST['show_all_countries']) ? $_POST['show_all_countries'] : 0;
                $listing_modified = isset($_POST['listing_modified']) ? $_POST['listing_modified'] + 0 : 0;
                $status = isset($_POST['status']) ? $_POST['status'] : $cache_record['status'];  // Ocprop
                $status_old = $cache_record['status'];
                $search_time = isset($_POST['search_time']) ? trim($_POST['search_time']) : $cache_record['search_time'];
                $way_length = isset($_POST['way_length']) ? trim($_POST['way_length']) : $cache_record['way_length'];

                if ($status_old == 5 && $status == 5) {
                    if (isset($_REQUEST['publish'])) {  // Ocprop; see also res_state_warning.tpl
                        $publish = $_REQUEST['publish'];
                        if (!($publish == 'now' || $publish == 'later' || $publish == 'notnow')) {
                            // somebody messed up the POST-data, so we do not publish the cache,
                            // since he isn't published right now (status=5)
                            $publish = 'notnow';
                        }
                        if ($publish == 'now') {
                            $status = 1;
                        }
                    } else {
                        if (is_null($cache_record['date_activate'])) {
                            $publish = 'notnow';
                        } else {
                            $publish = 'later';
                        }
                    }
                } else {
                    $publish = isset($_POST['publish']) ? $_POST['publish'] : 'now';
                    if (!($publish == 'now' || $publish == 'later' || $publish == 'notnow')) {
                        // somebody messed up the POST-data, so the cache has to be published (status<5)
                        $publish = 'now';
                    }
                }

                $bAdmin = sqlValue("SELECT `admin` FROM `user` WHERE `user_id` = &1", 0, $usr['userid']);

                if ($status == 7 && ($bAdmin & ADMIN_USER) != ADMIN_USER) {
                    $status = $status_old;
                }

                if ($status_old == 7) {  // cache is locked
                    // only admins can change status of locked caches
                    if (($bAdmin & ADMIN_USER) != ADMIN_USER) {
                        // no status change allowed for normal user
                        $status = $status_old;
                    }
                }

                $search_time = mb_ereg_replace(',', '.', $search_time);
                $way_length = mb_ereg_replace(',', '.', $way_length);

                if (mb_strpos($search_time, ':') == mb_strlen($search_time) - 3) {
                    $st_hours = mb_substr($search_time, 0, mb_strpos($search_time, ':'));
                    $st_minutes = mb_substr($search_time, mb_strlen($st_hours) + 1);

                    if (is_numeric($st_hours) && is_numeric($st_minutes)) {
                        if (($st_minutes >= 0) && ($st_minutes < 60)) {
                            $search_time = $st_hours + $st_minutes / 60;
                        }
                    }
                }

                $log_pw = isset($_POST['log_pw']) ? mb_substr($_POST['log_pw'], 0, 20) : $cache_record['logpw'];
                // fix #4356: gc waypoints are frequently copy&pasted with leading spaces
                $wp_gc = isset($_POST['wp_gc']) ? strtoupper(trim($_POST['wp_gc'])) : $cache_record['wp_gc'];  // Ocprop
                $showlists = isset($_POST['showlists']) ? 1 : $cache_record['show_cachelists'] + 0;
                $protect_old_coords = isset($_POST['protect_old_coords']) ? 1 : $cache_record['protect_old_coords'] + 0;

                // name
                $name_not_ok = false;
                if ($cache_name == "") {
                    $name_not_ok = true;
                }

                if (isset($_POST['latNS'])) {
                    //get coords from post-form
                    $coords_latNS = $_POST['latNS'];  // Ocprop
                    $coords_lonEW = $_POST['lonEW'];  // Ocprop
                    $coords_lat_h = trim($_POST['lat_h']);  // Ocprop
                    $coords_lon_h = trim($_POST['lon_h']);  // Ocprop
                    $coords_lat_min = trim($_POST['lat_min']);  // Ocprop
                    $coords_lon_min = trim($_POST['lon_min']);  // Ocprop
                } else {
                    //get coords from DB
                    $coords_lon = $cache_record['longitude'];
                    $coords_lat = $cache_record['latitude'];

                    if ($coords_lon < 0) {
                        $coords_lonEW = 'W';
                        $coords_lon = -$coords_lon;
                    } else {
                        $coords_lonEW = 'E';
                    }

                    if ($coords_lat < 0) {
                        $coords_latNS = 'S';
                        $coords_lat = -$coords_lat;
                    } else {
                        $coords_latNS = 'N';
                    }

                    $coords_lat_h = floor($coords_lat);
                    $coords_lon_h = floor($coords_lon);

                    $coords_lat_min = sprintf("%02.3f", round(($coords_lat - $coords_lat_h) * 60, 3));
                    $coords_lon_min = sprintf("%02.3f", round(($coords_lon - $coords_lon_h) * 60, 3));
                }

                //here we validate the data

                //coords
                $lon_not_ok = false;

                if (!mb_ereg_match('^[0-9]{1,3}$', $coords_lon_h)) {
                    $lon_not_ok = true;
                } else {
                    $lon_not_ok = (($coords_lon_h >= 0) && ($coords_lon_h < 180)) ? false : true;
                }

                if (is_numeric($coords_lon_min)) {
                    // important: use here |=
                    $lon_not_ok |= (($coords_lon_min >= 0) && ($coords_lon_min < 60)) ? false : true;
                } else {
                    $lon_not_ok = true;
                }

                //same with lat
                $lat_not_ok = false;

                if (!mb_ereg_match('^[0-9]{1,3}$', $coords_lat_h)) {
                    $lat_not_ok = true;
                } else {
                    $lat_not_ok = (($coords_lat_h >= 0) && ($coords_lat_h < 180)) ? false : true;
                }

                if (is_numeric($coords_lat_min)) {
                    // important: use here |=
                    $lat_not_ok |= (($coords_lat_min >= 0) && ($coords_lat_min < 60)) ? false : true;
                } else {
                    $lat_not_ok = true;
                }

                //check effort
                $time_not_ok = true;
                tpl_set_var('effort_message', '');
                if (is_numeric($search_time) || ($search_time == '')) {
                    $time_not_ok = false;
                }
                if ($time_not_ok) {
                    tpl_set_var('effort_message', $time_not_ok_message);
                    $error = true;
                }
                $way_length_not_ok = true;
                if (is_numeric($way_length) || ($way_length == '')) {
                    $way_length_not_ok = false;
                }
                if ($way_length_not_ok) {
                    tpl_set_var('effort_message', $way_length_not_ok_message);
                    $error = true;
                }

                //check GC waypoint
                $wpgc_not_ok = $wp_gc != '' && !preg_match("/^(?:GC|CX)[0-9A-Z]{3,6}$/", $wp_gc);
                if ($wpgc_not_ok) {
                    $error = true;
                }

                //check hidden_since
                $hidden_date_not_ok = true;
                $hidden_date_mismatch = false;
                if (is_numeric($cache_hidden_day) && is_numeric($cache_hidden_month) &&
                    is_numeric($cache_hidden_year)
                ) {
                    $hidden_date_not_ok =
                        (checkdate($cache_hidden_month, $cache_hidden_day, $cache_hidden_year) == false);
                }
                if ($hidden_date_not_ok == false && $publish != 'notnow') {
                    $hidden_date = mktime(
                        0,
                        0,
                        0,
                        $cache_hidden_month,
                        $cache_hidden_day,
                        $cache_hidden_year
                    );
                    if ($status_old != 5) {
                        // the cache has already been published
                        $publish_date = strtotime(substr($cache_record['date_created'], 0, 10));
                    } elseif ($publish == 'later') {
                        // Activation hour can be ignored here. This simplifies checking event dates.
                        $publish_date = mktime(
                            0,
                            0,
                            0,
                            $cache_activate_month,
                            $cache_activate_day,
                            $cache_activate_year
                        );
                    } else {
                        // the cache is to be published now
                        $publish_date = time();
                    }
                    if (($cache_type == 6 && $hidden_date < $publish_date) ||
                        ($cache_type != 6 && $hidden_date > $publish_date)) {
                        $hidden_date_mismatch = true;
                    }
                }

                //check date_activate
                if ($status == 5) {
                    $activate_date_not_ok = true;
                    if (is_numeric($cache_activate_day) && is_numeric($cache_activate_month) &&
                        is_numeric($cache_activate_year) && is_numeric($cache_activate_hour)
                    ) {
                        $activate_date_not_ok =
                            checkdate(
                                $cache_activate_month,
                                $cache_activate_day,
                                $cache_activate_year
                            ) == false
                            || $cache_activate_hour < 0
                            || $cache_activate_hour > 23;
                    }
                } else {
                    $activate_date_not_ok = false;
                }

                //check status and publish options
                if (($status == 5 && $publish == 'now') || ($status != 5 && ($publish == 'later' || $publish == 'notnow'))) {
                    tpl_set_var('status_message', $status_message);
                    $status_not_ok = true;
                } else {
                    tpl_set_var('status_message', '');
                    $status_not_ok = false;
                }

                //check cache size
                $size_not_ok = false;
                if ($sel_size != 7 && ($cache_type == 4 || $cache_type == 5)) {
                    $error = true;
                    $size_not_ok = true;
                }

                //difficulty / terrain
                $diff_not_ok = false;
                tpl_set_var('diff_message', '');
                if ($cache_difficulty < 2 || $cache_difficulty > 10 || $cache_terrain < 2 || $cache_terrain > 10) {
                    tpl_set_var('diff_message', $diff_not_ok_message);
                    $error = true;
                    $diff_not_ok = true;
                }

                // cache-attributes
                $attribs_not_ok = false;
                if (isset($_POST['cache_attribs'])) {
                    $cache_attribs = mb_split(';', $_POST['cache_attribs']);
                } else {
                    // get attribs for this cache from db
                    $rs = sql("SELECT `attrib_id` FROM `caches_attributes` WHERE `cache_id`='&1'", $cache_id);
                    if (mysqli_num_rows($rs) > 0) {
                        unset($cache_attribs);
                        while ($record = sql_fetch_array($rs)) {
                            $cache_attribs[] = $record['attrib_id'];
                        }
                        unset($record);
                    } else {
                        $cache_attribs = [];
                    }
                    sql_free_result($rs);
                }

                if (in_array(ATTRIB_ID_SAFARI, $cache_attribs) && $cache_type != 4) {
                    tpl_set_var('safari_message', $safari_not_allowed_message);
                    $error = true;
                    $attribs_not_ok = true;
                } else {
                    tpl_set_var('safari_message', '');
                }

                //try to save to DB?
                if (isset($_POST['submit'])) {  // Ocprop
                    // all validations ok?
                    if (!(
                        $hidden_date_not_ok || $hidden_date_mismatch ||
                        $lat_not_ok || $lon_not_ok || $name_not_ok ||
                        $time_not_ok || $way_length_not_ok || $size_not_ok ||
                        $activate_date_not_ok || $status_not_ok || $diff_not_ok ||
                        $attribs_not_ok || $wpgc_not_ok
                    )
                    ) {
                        $cache_lat = $coords_lat_h + $coords_lat_min / 60;
                        if ($coords_latNS == 'S') {
                            $cache_lat = -$cache_lat;
                        }

                        $cache_lon = $coords_lon_h + $coords_lon_min / 60;
                        if ($coords_lonEW == 'W') {
                            $cache_lon = -$cache_lon;
                        }

                        if ($publish == 'now') {
                            $activation_date = 'NULL';
                        } elseif ($publish == 'later') {
                            $status = 5;
                            $activation_date =
                                "'" . sql_escape(
                                    date(
                                        'Y-m-d H:i:s',
                                        mktime(
                                            $cache_activate_hour,
                                            0,
                                            0,
                                            $cache_activate_month,
                                            $cache_activate_day,
                                            $cache_activate_year
                                        )
                                    )
                                ) . "'";
                        } elseif ($publish == 'notnow') {
                            $status = 5;
                            $activation_date = 'NULL';
                        } else {
                            // should never happen
                            $activation_date = 'NULL';
                        }

                        // check for Ocprop data to ignore
                        if ($ocpropping) {
                            $rs = sql("SELECT `type`, `size` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
                            if ($r = sql_fetch_assoc($rs)) {
                                if ($r['type'] == 8 && $cache_type == 7) {
                                    $cache_type = 8;
                                }
                                if ($r['type'] == 10 && $cache_type == 2) {
                                    $cache_type = 10;
                                }
                                if ($r['size'] == 8 && ($sel_size == 1 || $sel_size == 2)) {
                                    $sel_size = 8;
                                }
                            }
                            sql_free_result($rs);
                        }

                        // fix showlists setting
                        if (!isset($_POST['showlists'])) {
                            $showlists = 0;
                        }
                        if (!isset($_POST['protect_old_coords'])) {
                            $protect_old_coords = 0;
                        }

                        // save to DB
                        // Status update will trigger touching the last_modified date of all depending records.
                        // Status change via editcache.php is no longer available via the user interface,
                        // but still used by Ocprop and maybe other tools.
                        sql("SET @STATUS_CHANGE_USER_ID='&1'", $usr['userid']);
                        sql(
                            "UPDATE `caches` SET `name`='&1', `longitude`='&2', `latitude`='&3', `type`='&4', `date_hidden`='&5', `country`='&6', `size`='&7', `difficulty`='&8', `terrain`='&9', `status`='&10', `search_time`='&11', `way_length`='&12', `logpw`='&13', `wp_gc`='&14', `show_cachelists`='&15', `protect_old_coords`='&16', `date_activate` = $activation_date WHERE `cache_id`='&17'",
                            $cache_name,
                            $cache_lon,
                            $cache_lat,
                            $cache_type,
                            date('Y-m-d', mktime(0, 0, 0, $cache_hidden_month, $cache_hidden_day, $cache_hidden_year)),
                            $cache_country,
                            $sel_size,
                            $cache_difficulty,
                            $cache_terrain,
                            $status,
                            $search_time,
                            $way_length,
                            $log_pw,
                            $wp_gc,
                            $showlists,
                            $protect_old_coords,
                            $cache_id
                        );

                        // send notification on admin intervention
                        if ($cache_record['user_id'] != $usr['userid'] &&
                            $opt['logic']['admin']['listingadmin_notification'] != ''
                        ) {
                            mail(
                                $opt['logic']['admin']['listingadmin_notification'],
                                mb_ereg_replace(
                                    '{occode}',
                                    $cache_record['wp_oc'],
                                    mb_ereg_replace(
                                        '{username}',
                                        $usr['username'],
                                        t('Cache listing {occode} has been modified by {username}')
                                    )
                                ),
                                t('The modifications can be checked via vandalism restore function.')
                            );
                        }

                        // generate status-change log
                        if ($status != $status_old && $status_old != 5) {
                            switch ($status) {
                                case 1:
                                    $logtype = 10;
                                    break;
                                case 2:
                                    $logtype = 11;
                                    break;
                                case 3:
                                    $logtype = 9;
                                    break;
                                case 6:
                                    $logtype = 13;
                                    break;
                                default:
                                    $logtype = 0;  // ???
                            }
                            if ($logtype > 0) {
                                sql(
                                    "INSERT INTO `cache_logs` (`node`, `cache_id`, `user_id`, `type`, `date`)
                                         VALUES ('&1','&2','&3','&4','&5')",
                                    $oc_nodeid,
                                    $cache_id,
                                    $usr['userid'],
                                    $logtype,
                                    date('Y-m-d')
                                );
                                // notifications will be automatically generated
                            }
                        }

                        // update cache attributes
                        $attriblist = '999';
                        $countCacheAttrIbs = count($cache_attribs);
                        for ($i = 0; $i < $countCacheAttrIbs; $i++) {
                            if ($cache_attribs[$i] + 0 > 0) {
                                sql(
                                    "INSERT IGNORE INTO `caches_attributes` (`cache_id`, `attrib_id`)
                                     VALUES('&1', '&2')",
                                    $cache_id,
                                    $cache_attribs[$i] + 0
                                );
                                $attriblist .= "," . ($cache_attribs[$i] + 0);
                            }
                        }

                        sql(
                            "DELETE FROM `caches_attributes`
                             WHERE `cache_id`='&1'
                             AND `attrib_id`
                             NOT IN (" . $attriblist . ')',
                            // SQL injections in $attriblist prevented by adding 0 above
                            $cache_id
                        );

                        StatisticPicture::deleteStatisticPicture($usr['userid']);

                        //display cache-page
                        tpl_redirect('viewcache.php?cacheid=' . urlencode($cache_id));
                        // Ocprop: Location:\s*$viewcacheUrl\?cacheid=([0-9]+)
                        // (s.a. tpl_redirect() in common.inc.php
                        exit;
                    }
                } elseif (isset($_POST['show_all_countries_submit'])) {
                    $show_all_countries = 1;
                }

                //here we only set up the template variables

                //build countrylist
                $countriesoptions = '';

                //check if selected country is in list_default
                if ($show_all_countries == 0) {
                    $rs = sql(
                        "SELECT `show` FROM `countries_list_default` WHERE `show`='&1' AND `lang`='&2'",
                        $cache_country,
                        $locale
                    );
                    if (mysqli_num_rows($rs) == 0) {
                        $show_all_countries = 1;
                    }
                    sql_free_result($rs);
                }

                //get the record
                if ($show_all_countries == 0) {
                    $rs = sql(
                        "SELECT `countries`.`short`, IFNULL(`sys_trans_text`.`text`, `countries`.`name`) AS `name`
                         FROM `countries`
                         INNER JOIN `countries_list_default`
                           ON `countries_list_default`.`show`=`countries`.`short`
                         LEFT JOIN `sys_trans`
                           ON `countries`.`trans_id`=`sys_trans`.`id`
                         LEFT JOIN `sys_trans_text`
                           ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                           AND `sys_trans_text`.`lang`='&1'
                         WHERE `countries_list_default`.`lang`='&1'
                         ORDER BY `name` ASC",
                        $locale
                    );
                } else {
                    $rs = sql(
                        "SELECT `countries`.`short`, IFNULL(`sys_trans_text`.`text`, `countries`.`name`) AS `name`
                         FROM `countries`
                         LEFT JOIN `sys_trans`
                           ON `countries`.`trans_id`=`sys_trans`.`id`
                         LEFT JOIN `sys_trans_text`
                           ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                         AND `sys_trans_text`.`lang`='&1'
                         ORDER BY `name` ASC",
                        $locale
                    );
                }

                while ($record = sql_fetch_assoc($rs)) {
                    $sSelected = ($record['short'] == $cache_country) ? ' selected="selected"' : '';
                    $countriesoptions .=
                        '<option value="'
                        . htmlspecialchars($record['short'], ENT_COMPAT, 'UTF-8')
                        . '"' . $sSelected . '>'
                        . htmlspecialchars($record['name'], ENT_COMPAT, 'UTF-8')
                        . '</option>' . "\n";
                }
                tpl_set_var('countryoptions', $countriesoptions);
                sql_free_result($rs);

                // cache-attributes
                $bBeginLine = true;
                $nPrevLineAttrCount = 0;
                $nLineAttrCount = 0;

                $cache_attrib_list = '';
                $cache_attrib_array = '';
                $cache_attribs_string = '';

                $rsAttrGroup = sql(
                    "SELECT `attribute_groups`.`id`,
                            IFNULL(`sys_trans_text`.`text`,
                            `attribute_groups`.`name`) AS `name`,
                            `attribute_categories`.`color`
                     FROM `attribute_groups`
                     INNER JOIN `attribute_categories`
                       ON `attribute_groups`.`category_id`=`attribute_categories`.`id`
                     LEFT JOIN `sys_trans`
                       ON `attribute_groups`.`trans_id`=`sys_trans`.`id`
                     LEFT JOIN `sys_trans_text`
                       ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                       AND `sys_trans_text`.`lang`='&1'
                     ORDER BY `attribute_groups`.`category_id` ASC, `attribute_groups`.`id` ASC",
                    $locale
                );
                while ($rAttrGroup = sql_fetch_assoc($rsAttrGroup)) {
                    $group_line = '';

                    $rs = sql(
                        "SELECT `cache_attrib`.`id`,
                                IFNULL(`ttname`.`text`, `cache_attrib`.`name`) AS `name`,
                                `cache_attrib`.`icon_undef`,
                                `cache_attrib`.`icon_large`,
                                IFNULL(`ttdesc`.`text`, `cache_attrib`.`html_desc`) AS `html_desc`
                         FROM `cache_attrib`
                         LEFT JOIN `caches_attributes`
                           ON `cache_attrib`.`id`=`caches_attributes`.`attrib_id`
                           AND `caches_attributes`.`cache_id`='&2'
                         LEFT JOIN `sys_trans` AS `tname`
                           ON `cache_attrib`.`trans_id`=`tname`.`id`
                           AND `cache_attrib`.`name`=`tname`.`text`
                         LEFT JOIN `sys_trans_text` AS `ttname`
                           ON `tname`.`id`=`ttname`.`trans_id`
                           AND `ttname`.`lang`='&1'
                         LEFT JOIN `sys_trans` AS `tdesc`
                           ON `cache_attrib`.`html_desc_trans_id`=`tdesc`.`id`
                           AND `cache_attrib`.`html_desc`=`tdesc`.`text`
                         LEFT JOIN `sys_trans_text` AS `ttdesc`
                           ON `tdesc`.`id`=`ttdesc`.`trans_id`
                           AND `ttdesc`.`lang`='&1'
                         WHERE `cache_attrib`.`group_id`='&3'
                         AND NOT IFNULL(`cache_attrib`.`hidden`, 0) = 1
                         AND (`cache_attrib`.`selectable`!=0 OR `caches_attributes`.`cache_id`='&2')
                         ORDER BY `cache_attrib`.`group_id` ASC, `cache_attrib`.`id` ASC",
                        $locale,
                        $cache_id,
                        $rAttrGroup['id']
                    );
                    while ($record = sql_fetch_array($rs)) {
                        $line = $cache_attrib_pic;

                        $line = mb_ereg_replace('{attrib_id}', $record['id'], $line);
                        $line = mb_ereg_replace('{attrib_text}', escape_javascript($record['name']), $line);
                        if (in_array($record['id'], $cache_attribs)) {
                            $line = mb_ereg_replace('{attrib_pic}', $record['icon_large'], $line);
                        } else {
                            $line = mb_ereg_replace('{attrib_pic}', $record['icon_undef'], $line);
                        }
                        $line = mb_ereg_replace('{html_desc}', escape_javascript($record['html_desc']), $line);
                        $line = mb_ereg_replace('{name}', escape_javascript($record['name']), $line);
                        $line = mb_ereg_replace('{color}', $rAttrGroup['color'], $line);
                        $group_line .= $line;
                        $nLineAttrCount++;

                        $line = $cache_attrib_js;
                        $line = mb_ereg_replace('{id}', $record['id'], $line);
                        if (in_array($record['id'], $cache_attribs)) {
                            $line = mb_ereg_replace('{selected}', 1, $line);
                        } else {
                            $line = mb_ereg_replace('{selected}', 0, $line);
                        }
                        $line = mb_ereg_replace('{img_undef}', $record['icon_undef'], $line);
                        $line = mb_ereg_replace('{img_large}', $record['icon_large'], $line);
                        $line = mb_ereg_replace(
                            '{conflicting_attribs}',
                            implode(',', attribute::getConflictingAttribIds($record['id'])),
                            $line
                        );
                        if ($cache_attrib_array != '') {
                            $cache_attrib_array .= ',';
                        }
                        $cache_attrib_array .= $line;

                        if (in_array($record['id'], $cache_attribs)) {
                            if ($cache_attribs_string != '') {
                                $cache_attribs_string .= ';';
                            }
                            $cache_attribs_string .= $record['id'];
                        }
                    }
                    sql_free_result($rs);

                    if ($group_line != '') {
                        $group_img = $cache_attrib_group;
                        $group_img = mb_ereg_replace('{color}', $rAttrGroup['color'], $group_img);
                        $group_img = mb_ereg_replace('{attribs}', $group_line, $group_img);
                        $group_img = mb_ereg_replace(
                            '{name}',
                            htmlspecialchars($rAttrGroup['name'], ENT_COMPAT, 'UTF-8'),
                            $group_img
                        );

                        if ($bBeginLine == true) {
                            $cache_attrib_list .= '<div class="attribswide">';
                            $bBeginLine = false;
                        }

                        $cache_attrib_list .= $group_img;
                        $nPrevLineAttrCount += $nLineAttrCount;

                        $nLineAttrCount = 0;
                    }
                }
                sql_free_result($rsAttrGroup);
                if ($bBeginLine == false) {
                    $cache_attrib_list .= '</div>';
                }

                tpl_set_var('cache_attrib_list', $cache_attrib_list);
                tpl_set_var('jsattributes_array', $cache_attrib_array);
                tpl_set_var('cache_attribs', $cache_attribs_string);

                //difficulty
                $difficulty_options = '';
                for ($i = 2; $i <= 10; $i++) {
                    if ($cache_difficulty == $i) {
                        $difficulty_options .= '<option value="' . $i . '" selected="selected">' . $i / 2 . '</option>';
                    } else {
                        $difficulty_options .= '<option value="' . $i . '">' . $i / 2 . '</option>';
                    }
                    $difficulty_options .= "\n";
                }
                tpl_set_var('difficultyoptions', $difficulty_options);

                //build terrain options
                $terrain_options = '';
                for ($i = 2; $i <= 10; $i++) {
                    if ($cache_terrain == $i) {
                        $terrain_options .= '<option value="' . $i . '" selected="selected">' . $i / 2 . '</option>';
                    } else {
                        $terrain_options .= '<option value="' . $i . '">' . $i / 2 . '</option>';
                    }
                    $terrain_options .= "\n";
                }
                tpl_set_var('terrainoptions', $terrain_options);

                //build typeoptions
                $types = '';
                $rsTypes = sql(
                    "SELECT `cache_type`.`id`,
                            IFNULL(`sys_trans_text`.`text`,
                            `cache_type`.`en`) AS `name`
                     FROM `cache_type`
                     LEFT JOIN `sys_trans`
                       ON `cache_type`.`trans_id`=`sys_trans`.`id`
                     LEFT JOIN `sys_trans_text`
                       ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                       AND `sys_trans_text`.`lang`='" . sql_escape($locale) . "'
                     ORDER BY `cache_type`.`ordinal` ASC"
                );
                while ($rType = sql_fetch_assoc($rsTypes)) {
                    $sSelected = ($rType['id'] == $cache_type) ? ' selected="selected"' : '';
                    $types .=
                        '<option value="' . $rType['id'] . '"' . $sSelected . '>'
                        . htmlspecialchars($rType['name'], ENT_COMPAT, 'UTF-8')
                        . '</option>';
                }
                sql_free_result($rsTypes);
                tpl_set_var('typeoptions', $types);

                //build sizeoptions
                $sizes = '';
                $rsSizes = sql(
                    "SELECT `cache_size`.`id`,
                            IFNULL(`sys_trans_text`.`text`, `cache_size`.`name`) AS `name`
                     FROM `cache_size`
                     LEFT JOIN `sys_trans`
                       ON `cache_size`.`trans_id`=`sys_trans`.`id`
                     LEFT JOIN `sys_trans_text`
                       ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                       AND `sys_trans_text`.`lang`='" . sql_escape($locale) . "'
                     ORDER BY `cache_size`.`ordinal` ASC"
                );
                while ($rSize = sql_fetch_assoc($rsSizes)) {
                    $sSelected = ($rSize['id'] == $sel_size) ? ' selected="selected"' : '';
                    $sizes .=
                        '<option value="' . $rSize['id'] . '"' . $sSelected . '>'
                        . htmlspecialchars($rSize['name'], ENT_COMPAT, 'UTF-8')
                        . '</option>';
                }
                sql_free_result($rsSizes);
                tpl_set_var('sizeoptions', $sizes);

                //Cachedescs
                $desclangs = mb_split(',', $cache_record['desc_languages']);
                $cache_descs = '';
                $gc_com_refs = false;
                foreach ($desclangs as $desclang) {
                    if (count($desclangs) > 1) {
                        $remove_url =
                            'removedesc.php?cacheid=' . urlencode($cache_id)
                            . '&desclang=' . urlencode($desclang);
                        $removedesc =
                            '&nbsp;[<a href="'
                            . htmlspecialchars($remove_url, ENT_COMPAT, 'UTF-8')
                            . '" onclick="testListingModified(this)" >' . $remove . '</a>]';
                    } else {
                        $removedesc = '';
                    }

                    $resp = sql(
                        "SELECT `desc` FROM `cache_desc` WHERE `cache_id`='&1' AND `language`='&2'",
                        $cache_id,
                        $desclang
                    );
                    $row = sql_fetch_array($resp);
                    if (mb_strpos($row['desc'], "http://img.groundspeak.com/") !== false) {
                        $gc_com_refs = true;
                    }
                    sql_free_result($resp);

                    $edit_url = 'editdesc.php?cacheid=' . urlencode($cache_id) . '&desclang=' . urlencode($desclang);

                    $cache_descs .=
                        '<tr><td colspan="2">'
                        . htmlspecialchars(db_LanguageFromShort($desclang), ENT_COMPAT, 'UTF-8')
                        . ' [<a href="' . htmlspecialchars($edit_url, ENT_COMPAT, 'UTF-8')
                        . '" onclick="testListingModified(this)" >' . $edit . '</a>]'
                        . $removedesc . '</td></tr>';
                }
                tpl_set_var('cache_descs', $cache_descs);

                if ($gc_com_refs) {
                    tpl_set_var('gc_com_refs_start', '');
                    tpl_set_var('gc_com_refs_end', '');
                } else {
                    tpl_set_var('gc_com_refs_start', '<!--');
                    tpl_set_var('gc_com_refs_end', '-->');
                }

                //Status
                $statusoptions = '';
                if ($status_old != 7) {
                    $rsStatus = sql(
                        "SELECT `cache_status`.`id`,
                                IFNULL(`sys_trans_text`.`text`,
                                `cache_status`.`name`) AS `name`
                         FROM `cache_status`
                         LEFT JOIN `sys_trans`
                           ON `cache_status`.`trans_id`=`sys_trans`.`id`
                         LEFT JOIN `sys_trans_text`
                           ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                           AND `sys_trans_text`.`lang`='" . sql_escape($locale) . "'
                         WHERE `cache_status`.`id` NOT IN (4, 5, 7)
                           OR `cache_status`.`id`='" . sql_escape($status_old + 0) . "'
                         ORDER BY `cache_status`.`id` ASC"
                    );
                    while ($rStatus = sql_fetch_assoc($rsStatus)) {
                        $sSelected = ($rStatus['id'] == $status) ? ' selected="selected"' : '';
                        if ($sSelected != '' || $status_old == 5) {
                            $statusoptions .=
                                '<option value="'
                                . htmlspecialchars($rStatus['id'], ENT_COMPAT, 'UTF-8')
                                . '"' . $sSelected . '>'
                                . htmlspecialchars($rStatus['name'], ENT_COMPAT, 'UTF-8')
                                . '</option>';
                        }
                    }
                    sql_free_result($rsStatus);
                } else {
                    $statusoptions .=
                        '<option value="7" selected="selected">'
                        . htmlspecialchars(t("Locked, invisible"), ENT_COMPAT, 'UTF-8')
                        . '</option>';
                }
                tpl_set_var('statusoptions', $statusoptions);
                $statuschange_a_msg =  mb_ereg_replace('%1', $cache_id, $status_change_a);
                $statuschange_msg =  mb_ereg_replace('{a}', $statuschange_a_msg, $status_change);
                tpl_set_var('statuschange', $status_old == 5 ? '' : $statuschange_msg);

                // show activation form?
                if ($status_old == 5) {  // status = not yet published
                    $tmp = $activation_form;

                    $tmp = mb_ereg_replace(
                        '{activate_day}',
                        htmlspecialchars($cache_activate_day, ENT_COMPAT, 'UTF-8'),
                        $tmp
                    );
                    $tmp = mb_ereg_replace(
                        '{activate_month}',
                        htmlspecialchars($cache_activate_month, ENT_COMPAT, 'UTF-8'),
                        $tmp
                    );
                    $tmp = mb_ereg_replace(
                        '{activate_year}',
                        htmlspecialchars($cache_activate_year, ENT_COMPAT, 'UTF-8'),
                        $tmp
                    );
                    $tmp = mb_ereg_replace('{publish_now_checked}', ($publish == 'now') ? 'checked' : '', $tmp);
                    $tmp = mb_ereg_replace('{publish_later_checked}', ($publish == 'later') ? 'checked' : '', $tmp);
                    $tmp = mb_ereg_replace('{publish_notnow_checked}', ($publish == 'notnow') ? 'checked' : '', $tmp);

                    $activation_hours = '';
                    for ($i = 0; $i <= 23; $i++) {
                        if ($cache_activate_hour == $i) {
                            $activation_hours .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
                        } else {
                            $activation_hours .= '<option value="' . $i . '">' . $i . '</option>';
                        }
                        $activation_hours .= "\n";
                    }
                    $tmp = mb_ereg_replace('{activation_hours}', $activation_hours, $tmp);

                    if ($activate_date_not_ok) {
                        $tmp = mb_ereg_replace('{activate_on_message}', $date_message, $tmp);
                    } else {
                        $tmp = mb_ereg_replace('{activate_on_message}', '', $tmp);
                    }

                    tpl_set_var('activation_form', $tmp);
                } else {
                    tpl_set_var('activation_form', '');
                }

                if ($cache_record['picture'] > 0) {
                    $pictures = '';
                    $rspictures = sql(
                        "SELECT `url`, `title`, `uuid`
                         FROM `pictures`
                         WHERE `object_id` = '&1'
                         AND `object_type` = 2
                         ORDER BY `seq`",
                        $cache_id
                    );

                    $countRsPictures = mysqli_num_rows($rspictures);
                    for ($i = 0; $i < $countRsPictures; $i++) {
                        $tmpline = ($i == 0 ? $pictureline0 : $pictureline);
                        $pic_record = sql_fetch_array($rspictures);

                        $tmpline = mb_ereg_replace(
                            '{link}',
                            htmlspecialchars($pic_record['url'], ENT_COMPAT, 'UTF-8'),
                            $tmpline
                        );
                        $tmpline = mb_ereg_replace(
                            '{title}',
                            htmlspecialchars($pic_record['title'], ENT_COMPAT, 'UTF-8'),
                            $tmpline
                        );
                        $tmpline = mb_ereg_replace(
                            '{uuid}',
                            htmlspecialchars($pic_record['uuid'], ENT_COMPAT, 'UTF-8'),
                            $tmpline
                        );

                        $pictures .= $tmpline;
                    }

                    $pictures = mb_ereg_replace('{lines}', $pictures, $picturelines);
                    mysqli_free_result($rspictures);
                    tpl_set_var('pictures', $pictures);
                } else {
                    tpl_set_var('pictures', $nopictures);
                }
                tpl_set_var('gc_com_msg2', mb_ereg_replace('%1', $opt['page']['sitename'], $gc_com_msg2));

                tpl_set_var('waypoints', getWaypoints($cache_id));

                tpl_set_var('cacheid', htmlspecialchars($cache_id, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('name', htmlspecialchars($cache_name, ENT_COMPAT, 'UTF-8'));

                tpl_set_var('ownername', htmlspecialchars($cache_record['username'], ENT_COMPAT, 'UTF-8'));

                tpl_set_var('date_day', htmlspecialchars($cache_hidden_day, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('date_month', htmlspecialchars($cache_hidden_month, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('date_year', htmlspecialchars($cache_hidden_year, ENT_COMPAT, 'UTF-8'));

                tpl_set_var('selLatN', ($coords_latNS == 'N') ? ' selected="selected"' : '');
                tpl_set_var('selLatS', ($coords_latNS == 'S') ? ' selected="selected"' : '');
                tpl_set_var('selLonE', ($coords_lonEW == 'E') ? ' selected="selected"' : '');
                tpl_set_var('selLonW', ($coords_lonEW == 'W') ? ' selected="selected"' : '');
                tpl_set_var('lat_h', htmlspecialchars($coords_lat_h, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('lat_min', htmlspecialchars($coords_lat_min, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('lon_h', htmlspecialchars($coords_lon_h, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('lon_min', htmlspecialchars($coords_lon_min, ENT_COMPAT, 'UTF-8'));

                tpl_set_var('name_message', ($name_not_ok == true) ? $name_message : '');
                tpl_set_var('lon_message', ($lon_not_ok == true) ? $coords_message : '');
                tpl_set_var('lat_message', ($lat_not_ok == true) ? $coords_message : '');
                if ($hidden_date_mismatch == true) {
                    if ($cache_type == 6) {
                        tpl_set_var('date_message', $event_before_publish_message);
                    } else {
                        tpl_set_var('date_message', $hide_after_publish_message);
                    }
                } else {
                    tpl_set_var('date_message', ($hidden_date_not_ok == true) ? $date_message : '');
                }
                tpl_set_var('size_message', ($size_not_ok == true) ? $sizemismatch_message : '');
                tpl_set_var('wpgc_message', ($wpgc_not_ok == true) ? $bad_wpgc_message : '');

                if ($lon_not_ok || $lat_not_ok || $hidden_date_not_ok || $name_not_ok) {
                    tpl_set_var('general_message', $error_general);
                } else {
                    tpl_set_var('general_message', '');
                }

                tpl_set_var('cacheid_urlencode', htmlspecialchars(urlencode($cache_id), ENT_COMPAT, 'UTF-8'));
                tpl_set_var(
                    'cacheuuid_urlencode',
                    htmlspecialchars(urlencode($cache_record['uuid']), ENT_COMPAT, 'UTF-8')
                );
                tpl_set_var('show_all_countries', $show_all_countries);
                tpl_set_var('show_all_countries_submit', ($show_all_countries == 0) ? $all_countries_submit : '');
                tpl_set_var('listing_modified', $listing_modified);
                tpl_set_var('savealert', $savealert);

                $st_hours = floor($search_time);
                $st_minutes = sprintf('%02.0F', ($search_time - $st_hours) * 60);

                tpl_set_var('search_time', $st_hours . ':' . $st_minutes);

                tpl_set_var('way_length', $way_length);
                tpl_set_var('log_pw', htmlspecialchars($log_pw, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('wp_gc', htmlspecialchars($wp_gc, ENT_COMPAT, 'UTF-8'));
                tpl_set_var('showlists_checked', $showlists ? 'checked="checked"' : '');
                tpl_set_var('protectcoords_checked', $protect_old_coords ? 'checked="checked"' : '');

                tpl_set_var('reset', $reset);  // obsolete
                tpl_set_var('submit', $submit);
            }
        }
    }
}

//make the template and send it out
tpl_BuildTemplate();
