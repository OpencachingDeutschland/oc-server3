<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use Doctrine\DBAL\Connection;

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'admins';
$tpl->menuitem = MNU_ADMIN_ADMINS;

$error = 0;

$login->verify();
if ($login->userid === 0) {
    $tpl->redirect_login();
}

if ($login->admin == 0) {
    $tpl->error(ERROR_NO_ACCESS);
}

/** @var Connection $connection */
$connection = AppKernel::Container()->get(Connection::class);

$admins = $connection->fetchAll(
    'SELECT `user_id` as id, `username` as name, `admin`
     FROM `user`
     WHERE `admin`
     ORDER BY username'
);

foreach($admins as &$admin) {
    $rights = [];

    if ($admin['admin'] & ADMIN_TRANSLATE) {
        $rights[] = 'translate';
    }
    if ($admin['admin'] & ADMIN_MAINTAINANCE) {
        $rights[] = 'dbmaint';
    }
    if ($admin['admin'] & ADMIN_USER) {
        $rights[] = 'user/caches';
    }
    if ($admin['admin'] & ADMIN_RESTORE) {
        $rights[] = 'vand.restore';
    }
    if ($admin['admin'] & 128) {
        $rights[] = 'root';
    }
    if ($admin['admin'] & ADMIN_LISTING) {
        $rights[] = 'listing';
    }
    $admin['rights'] = implode(', ', $rights);
}

$tpl->assign('admins', $admins);
$tpl->display();
