<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/../lib2/web.inc.php';

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

header('Content-type: text/plain; charset=utf-8');

if (isset($_REQUEST['key'], $opt['logic']['api']['email_problems']['key']) &&
    $opt['logic']['api']['email_problems']['key'] === $_REQUEST['key']
) {
    $rs = $connection->fetchAssoc('SELECT `user_id`, `email_problems` FROM `user` WHERE `email_problems`');
    foreach ($rs as $r) {
        echo $r['user_id'] . ' ' . $r['email_problems'] . "\n";
    }
}
