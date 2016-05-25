<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Tests for consistency of gk_item_waypoint und gk_move_waypoint tables.
 *  See http://redmine.opencaching.de/issues/18.
 *
 *  Data import functions are in util2/cron/modules/geokrety.class.php.
 ***************************************************************************/

$opt['rootpath'] = __DIR__ . '/../../';
require_once $opt['rootpath'] . 'lib2/cli.inc.php';

$itemwps = [];

// Get item waypoints
$rs = sql("SELECT `id` AS `itemid`, `wp` FROM `gk_item_waypoint`");
while ($r = sql_fetch_assoc($rs)) {
    $itemwps[$r['itemid']][$r['wp']]['itemwp'] = true;
}
sql_free_result($rs);

// Get move waypoints
$rsItems = sql("SELECT DISTINCT `itemid` AS `id` FROM `gk_move`");
while ($rItem = sql_fetch_assoc($rsItems)) {
    $lastmove = sql_value(
        "SELECT `id` FROM `gk_move`
        WHERE `itemid`='&1' AND `logtypeid`<>2
        ORDER BY `datemoved` DESC, `id` DESC
        LIMIT 1",
        0,
        $rItem['id']
    );

    $rsWp = sql(
        "SELECT `wp` FROM `gk_move_waypoint`
        LEFT JOIN `gk_move` ON `gk_move`.`id`=`gk_move_waypoint`.`id`
        WHERE `gk_move`.`id`='&1' AND `logtypeid` IN (0,3)",
        $lastmove
    );
    while ($rWp = sql_fetch_assoc($rsWp)) {
        $itemwps[$rItem['id']][$rWp['wp']]['movewp'] = true;
    }
    sql_free_result($rsWp);
}
sql_free_result($rsItems);

// test for missing waypoints
ksort($itemwps);
foreach ($itemwps as $itemid => $wps) {
    foreach ($wps as $wp => $flags) {
        if (isset($flags['itemwp']) && !isset($flags['movewp'])) {
            if (sql_value(
                "SELECT COUNT(*) FROM `gk_move`, `gk_move_waypoint`
                 WHERE
                    `gk_move`.`itemid`='&1'
                    AND `gk_move`.`logtypeid`<>2
                    AND `gk_move_waypoint`.`id`=`gk_move`.`id`
                    AND `gk_move_waypoint`.`wp`='&2'",
                0,
                $itemid,
                $wp
            ) == 0) {
                echo "item " . $itemid . ": " . $wp . " is missing in gk_move_waypoint\n";
            } else {
                echo "item " . $itemid . ": " . $wp . " is not the current wp in gk_move_waypoint\n";
            }
        } elseif (isset($flags['movewp']) && !isset($flags['itemwp'])) {
            echo "item " . $itemid . ": " . $wp . " is missing in gk_item_waypoint\n";
        }
    }
}
