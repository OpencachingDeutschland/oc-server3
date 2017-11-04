<?php
/***************************************************************************
 *    For license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

header('Content-type: text/html; charset=utf-8');

$opt['rootpath'] = '../../';
require __DIR__ . '/../../lib2/web.inc.php';

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

$users = $connection->fetchAll(
    'SELECT @curRow := @curRow + 1 AS rank, `user`.`username`, `stat_user`.`found`
     FROM `stat_user`
     INNER JOIN `user` ON `stat_user`.`user_id`=`user`.`user_id`
     INNER JOIN (SELECT @curRow := 0) r
     WHERE `user`.`is_active_flag`=1
     ORDER BY `stat_user`.`found` DESC
     LIMIT 100'
);

foreach ($users as $user) {
    echo $user['rank'] . ' ' . $user['username'] . ': ' . $user['found'] . "\n";
}
