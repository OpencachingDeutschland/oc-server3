<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 * queries if there are open reports for one ore more geocaches
 ***************************************************************************/

require __DIR__ . '/../lib2/web.inc.php';

$API_VERSION = 1;

header('Content-type: text/plain; charset=utf-8');

if (isset($_REQUEST['key']) &&
    $opt['logic']['api']['cache_reports']['key'] &&
    $opt['logic']['api']['cache_reports']['key'] == $_REQUEST['key'] &&
    isset($_REQUEST['caches'])
) {
    $caches = explode('|', $_REQUEST['caches']);
    $caches_sql = "'" . implode("','", array_map('sql_escape', $caches)) . "'";
    $rs = sql(
        "SELECT DISTINCT `wp_oc`
         FROM `caches`
         JOIN `cache_reports` `cr` ON `cr`.`cacheid`=`caches`.`cache_id`
         WHERE `wp_oc` IN (" . $caches_sql . ") AND `cr`.`status` IN (1,2)"
    );
    $caches = sql_fetch_column($rs);
    echo $API_VERSION . ':' . implode('|', $caches);
}
