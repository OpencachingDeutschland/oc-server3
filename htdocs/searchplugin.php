<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Search cache listing by waypoint
 *  Server part of Mozilla search plugin for OC;
 *  also used for waypoint search field.
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'searchplugin';

// initialize
$keyword_name = 'name';
$keyword_finder = 'finder';
$keyword_owner = 'besitzer';
$keyword_town = 'ort';
$keyword_zipcode = 'plz';
$keyword_cacheid = 'id';
$keyword_wp = 'wp';

$searchurl = 'search.php';

// get parameter from URL
$userinput = isset($_REQUEST['userinput']) ? mb_trim($_REQUEST['userinput']) : '';
$sourceid = isset($_REQUEST['sourceid']) ? $_REQUEST['sourceid'] : 0;

if (($sourceid == 'waypoint-search') && ($userinput != '')) {
    $sourceid = 'mozilla-search';
    $userinput = 'wp:' . $userinput;
}

if (($sourceid == 'mozilla-search') && ($userinput != '')) {
    $params = mb_split(':', $userinput);
    if ($params !== false) {
        if (count($params) == 1) {
            $searchto = 'name';
            $searchfor = urlencode($params[0]);
        } else {
            $searchto = $params[0];
            array_splice($params, 0, 1);
            $searchfor = urlencode(implode(':', $params));
        }
        unset($params);

        // for zipcode/town-search: if logged in, sort by distance
        if ($login->userid != 0) {
            $order = 'byname';
        } else {
            $order = 'bydistance';
        }

        $targeturl = 'search.php?showresult=1&expert=0&output=HTML&f_userowner=0&f_userfound=0';
        switch ($searchto) {
            case $keyword_name:
                $targeturl .= '&utf8=1&sort=byname&searchbyname=1&f_inactive=1&cachename=' . $searchfor;
                break;
            case $keyword_finder:
                $targeturl .= '&utf8=1&sort=byname&searchbyfinder=1&f_inactive=0&finder=' . $searchfor;
                break;
            case $keyword_owner:
                $targeturl .= '&utf8=1&sort=byname&searchbyowner=1&f_inactive=0&owner=' . $searchfor;
                break;
            case $keyword_town:
                $targeturl .= '&utf8=1&searchbyort=1&f_inactive=1&ort=' . $searchfor . '&sort=' . $order;
                break;
            case $keyword_zipcode:
                $targeturl .= '&utf8=1&sort=bydistance&searchbyplz=1&f_inactive=1&plz=' . $searchfor . '&sort=' . $order;
                break;
            case $keyword_cacheid:
                $targeturl .= '&utf8=1&sort=byname&searchbycacheid=1&f_inactive=1&cacheid=' . $searchfor;
                break;
            case $keyword_wp:
                $targeturl = 'index.php';
                $searchfor = mb_trim($searchfor);
                $target = mb_strtolower(mb_substr($searchfor, 0, 2));
                if (mb_substr($target, 0, 1) == 'n') {
                    $target = 'nc';
                }
                if (mb_strpos($opt['logic']['ocprefixes'], $target) != false) {
                    $target = 'oc';
                }

                if ((($target == 'oc') || ($target == 'gc'))
                    && mb_ereg_match(
                        '((' . $opt['logic']['ocprefixes'] . '|gc)([a-z0-9]){4,5}|n([a-f0-9]){5,5})$',
                        mb_strtolower($searchfor)
                    )
                ) {
                    if ($target == 'gc') {
                        $wpfield = "IF(`wp_gc_maintained`='',`wp_gc`,`wp_gc_maintained`)";
                    } else {
                        $wpfield = "`wp_oc`";
                    }
                    // get cache_id from DB
                    // GC/NC waypoints can be duplicates -> return first match with least status number
                    $rs = sql(
                        "SELECT `cache_id` FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`='&1') AND " . $wpfield . "='&2' ORDER BY `caches`.`status`,`caches`.`cache_id` LIMIT 0,1",
                        $login->userid,
                        $searchfor
                    );
                    if (sql_num_rows($rs)) {
                        $record = sql_fetch_array($rs);
                        $targeturl = 'viewcache.php?cacheid=' . $record['cache_id'];
                        unset($record);
                    } else {
                        $tpl->error(ERROR_SEARCHPLUGIN_WAYPOINT_NOTFOUND, $searchfor);
                    }
                    sql_free_result($rs);
                } else {
                    // wrong waypoint format
                    $tpl->error(ERROR_SEARCHPLUGIN_WAYPOINT_FORMAT);
                    exit;
                }
                break;
        }
        $tpl->redirect($targeturl);
    }
}

$tpl->redirect('index.php');
