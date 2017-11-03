<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

// ATTN: This page is requested by Cronjobs::enabled().
use Doctrine\DBAL\Connection;

require __DIR__ . '/../lib2/web.inc.php';
/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);
header('Content-type: text/plain; charset=utf-8');
echo $connection->fetchColumn('SELECT NOW()');
