<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Regenerates gk_item_waypoint from gk_move_waypoint.
 *  See http://redmine.opencaching.de/issues/18.
 *
 *  Data import functions are in util2/cron/modules/geokrety.class.php.
 ***************************************************************************/

$opt['rootpath'] = __DIR__ . '/../../';
require_once $opt['rootpath'] . 'lib2/cli.inc.php';

if (count($argv) != 2 || $argv[1] != 'run') {
    die("\n" .
        "Please verify that runcron.php is disabled in crontab;\n" .
        "then run this script with\n" .
        "\n" .
        "   php fix-waypoints.php run\n" .
        "\n" .
        "Don't forget to re-enable cronjobs afterwards!\n" .
        "\n");
}

sql("DELETE FROM `gk_item_waypoint`");

$rsItems = sql("SELECT DISTINCT `itemid` AS `id` FROM `gk_move`");
while ($rItem = sql_fetch_assoc($rsItems)) {
    $lastmove = sql_value(
        "SELECT `id` FROM `gk_move`
        WHERE `itemid`='&1' AND `logtypeid`<>2
        /* TODO: How does Geokrety.org order moves with same date? We assume by ID: */
        ORDER BY `datemoved` DESC, `id` DESC
        LIMIT 1",
        0,
        $rItem['id']
    );

    $rsWp = sql(
        "INSERT INTO `gk_item_waypoint`
        (SELECT `gk_move`.`itemid`, `gk_move_waypoint`.`wp`
         FROM `gk_move_waypoint`
         LEFT JOIN `gk_move` ON `gk_move`.`id`=`gk_move_waypoint`.`id`
         WHERE `gk_move`.`id`='&1' AND `logtypeid` IN (0,3) AND `wp`<>''
        )",
        $lastmove
    );
}
sql_free_result($rsItems);
