<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'adminhistory';
$tpl->menuitem = MNU_ADMIN_HISTORY;

$login->verify();
if ($login->userid === 0) {
    $tpl->redirect_login();
}

if (($login->admin & ADMIN_USER) != ADMIN_USER) {
    $tpl->error(ERROR_NO_ACCESS);
}

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

if (isset($_REQUEST['wp'])) {
    $cacheId = $connection->fetchColumn(
        'SELECT `cache_id` FROM `caches` WHERE `wp_oc`=:wp',
        [':wp' => $_REQUEST['wp']]
    );
} else {
    $cacheId = isset($_REQUEST['cacheid']) ? (int) $_REQUEST['cacheid'] : -1;
}

$showHistory = false;
$error = '';

if ($cacheId >= 0 &&
    $connection->fetchColumn('SELECT COUNT(*) FROM `caches` WHERE `cache_id`=:id',[':id' => $cacheId]) <> 1)
{
    $error = $translate->t('Cache not found', '', '', 0);
} elseif ($cacheId > 0) {
    $showHistory = true;
    $cache = new cache($cacheId);
    $cache->setTplHistoryData(0);
}

$tpl->assign('showhistory', $showHistory);
$tpl->assign('error', $error);
$tpl->display();
