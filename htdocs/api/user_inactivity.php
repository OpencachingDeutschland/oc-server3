<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/../lib2/web.inc.php';

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

header('Content-type: text/plain; charset=utf-8');

if (isset($_REQUEST['key'], $opt['logic']['api']['user_inactivity']['key'], $_REQUEST['userid'])
    && $opt['logic']['api']['user_inactivity']['key'] === $_REQUEST['key']
) {
    $loginLag = $connection->fetchColumn(
        'SELECT DATEDIFF(NOW(),`last_login`)
         FROM `user`
         WHERE `user_id`= :userId',
        [':userId' => $_REQUEST['userid']]
    );

    if ($loginLag !== null) {
        echo floor($loginLag / 30.5);
    } else {
        echo 'unknown';
    }
}
