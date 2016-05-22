<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'recommendations';
$tpl->menuitem = MNU_CACHES_SEARCH_RECOMMENDATIONS;

$cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : 0;

$rs = sql(
    "SELECT
        `caches`.`cache_id` AS `id`,
        `caches`.`wp_oc` AS `wp`,
        `caches`.`user_id` AS `userid`,
        `caches`.`name` AS `name`,
        `user`.`username` AS `username`
    FROM `caches`
    INNER JOIN `cache_status`
        ON `caches`.`status`=`cache_status`.`id`
    INNER JOIN `user`
        ON `caches`.`user_id`=`user`.`user_id`
    WHERE (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`='&1')
    AND `caches`.`cache_id`='&2'",
    $login->userid,
    $cacheid
);
$rCache = sql_fetch_assoc($rs);
$tpl->assign('cache', $rCache);
sql_free_result($rs);

if ($rCache === false) {
    $tpl->error(ERROR_CACHE_NOT_EXISTS);
}

$rs = sql(
    "SELECT
        COUNT(`caches`.`cache_id`) / (SELECT `toprating` FROM `stat_caches` WHERE `cache_id`='&1')*100 AS `quote`,
        `caches`.`cache_id` AS `cacheid`,
         `caches`.`wp_oc` AS `wp`,
         `caches`.`name` AS `name`,
         `user`.`user_id` AS `cacheuserid`,
         `user`.`username` AS `cacheusername`,
         `caches`.`status`
    FROM `cache_rating` AS `r1`
    INNER JOIN `cache_rating` AS `r2`
        ON`r1`.`user_id`=`r2`.`user_id`
    INNER JOIN `caches`
        ON `r2`.`cache_id`=`caches`.`cache_id`
    INNER JOIN `user`
        ON `caches`.`user_id`=`user`.`user_id`
    INNER JOIN `cache_status`
        ON `caches`.`status`=`cache_status`.`id`
    WHERE `r1`.`cache_id`='&1'
    AND `r2`.`cache_id`!='&1'
    AND (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`='&1')
    GROUP BY `caches`.`cache_id`
    ORDER BY `quote` DESC, `caches`.`name` ASC LIMIT 25",
    $cacheid
);
$tpl->assign_rs('cacheRatings', $rs);
sql_free_result($rs);

$tpl->display();
