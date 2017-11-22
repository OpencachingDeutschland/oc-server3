<?php
/***************************************************************************
 * for license information see LICENSE.md
 *  Returns a list of all caches which have been hidden after publish.
 *  This allows an easier synchronization of this information on a
 *  replicated system than the XML interface.
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/../lib2/web.inc.php';

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

header('Content-type: text/plain; charset=utf-8');

$wpOcs = $connection
    ->executeQuery(
    'SELECT `wp_oc`
     FROM `caches`
     JOIN `cache_status` ON `cache_status`.`id`=`caches`.`status`
     WHERE `cache_status`.`allow_user_view`= 0
     AND `caches`.`status` != 5
     ORDER BY `cache_id`'
    )
    ->fetchAll(PDO::FETCH_COLUMN);

foreach ($wpOcs as $wp) {
    echo $wp . "\n";
}
