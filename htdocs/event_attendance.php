<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'event_attendance';
$tpl->popup = true;

// id gesetzt?
$cache_id = isset($_REQUEST['id']) ? $_REQUEST['id'] + 0 : 0;
if ($cache_id != 0) {
    $rs = sql("SELECT `caches`.`name`, `user`.`username`, `caches`.`date_hidden`
               FROM `caches`
               INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
               INNER JOIN `user` ON `user`.`user_id`=`caches`.`user_id`
               WHERE `cache_status`.`allow_user_view`=1 AND
                     `caches`.`cache_id`='&1'", $cache_id);
    if ($r = sql_fetch_assoc($rs)) {
        $tpl->assign('owner', $r['username']);
        $tpl->assign('cachename', $r['name']);
        $tpl->assign('event_date', $r['date_hidden']);
    }
    sql_free_result($rs);

    $rs = sql(
        "SELECT DISTINCT `user`.`username`
        FROM `cache_logs`
        INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
        INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
        INNER JOIN `user` ON `user`.`user_id`=`cache_logs`.`user_id`
        WHERE `cache_status`.`allow_user_view` = 1
        AND `cache_logs`.`type` = 8
        AND `cache_logs`.`cache_id`='&1'
        ORDER BY `user`.`username`",
        $cache_id
    );
    $tpl->assign_rs('willattend', $rs);
    sql_free_result($rs);

    $rs = sql(
        "SELECT DISTINCT `user`.`username`
        FROM `cache_logs`
        INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
        INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
        INNER JOIN `user` ON `user`.`user_id`=`cache_logs`.`user_id`
        WHERE `cache_status`.`allow_user_view` = 1
        AND `cache_logs`.`type` = 7
        AND `cache_logs`.`cache_id`='&1'
        ORDER BY `user`.`username`",
        $cache_id
    );
    $tpl->assign_rs('attended', $rs);
    sql_free_result($rs);
}

$tpl->display();
