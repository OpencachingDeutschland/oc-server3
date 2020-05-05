<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/lib2/web.inc.php';

/** @var Doctrine\DBAL\Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

$tpl->name = 'usertops';
$tpl->menuitem = MNU_CACHES_USERTOPS;

$userId = (int) isset($_REQUEST['userid']) ? $_REQUEST['userid']: 0;
$ocOnly = isset($_REQUEST['oconly']) && $_REQUEST['oconly'];

$sUsername = $connection->fetchColumn('SELECT `username` FROM `user` WHERE `user_id` = :userId',['userId' => $userId]);
if ($sUsername == null) {
    $tpl->error(ERROR_USER_NOT_EXISTS);
}

$tpl->assign('userid', $userId);
$tpl->assign('username', $sUsername);
$tpl->assign('oconly', $ocOnly);

$rs = $connection->fetchAll(
    'SELECT `cache_rating`.`cache_id` AS `cacheid`,
            `caches`.`name` AS `cachename`,
            `user`.`username` AS `ownername`,
            `caches`.`type` AS `type`,
            `caches`.`status` AS `status`,
            `ca`.`attrib_id` IS NOT NULL AS `oconly`,
            `stat_caches`.`toprating` AS `countrating`
     FROM `cache_rating`
     INNER JOIN `caches`
       ON `cache_rating`.`cache_id` = `caches`.`cache_id`
     INNER JOIN `user`
       ON `caches`.`user_id`=`user`.`user_id`
     INNER JOIN `cache_status`
       ON `caches`.`status`=`cache_status`.`id`
     LEFT JOIN `stat_caches`
       ON `stat_caches`.`cache_id`=`cache_rating`.`cache_id`
     LEFT JOIN `caches_attributes` `ca`
       ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6
     WHERE `cache_status`.`allow_user_view`=1
       AND `cache_rating`.`user_id`= :userId
       AND (NOT :ocOnly OR `ca`.`attrib_id` IS NOT NULL)
     ORDER BY `caches`.`name` ASC',
    [
        'userId' => $userId,
        'ocOnly' => $ocOnly ? 1 : 0,
    ]
);

$tpl->assign('ratings', $rs);

$tpl->display();
