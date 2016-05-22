<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'admins';
$tpl->menuitem = MNU_ADMIN_ADMINS;

$error = 0;

$login->verify();
if ($login->userid == 0) {
    $tpl->redirect_login();
}

if ($login->admin == 0) {
    $tpl->error(ERROR_NO_ACCESS);
}

$rs = sql("SELECT `user_id`,`username`,`admin` FROM `user` WHERE `admin` ORDER BY username");
while ($record = sql_fetch_assoc($rs)) {
    $admin['id'] = $record['user_id'];
    $admin['name'] = $record['username'];
    $rights = array();
    if ($record['admin'] & ADMIN_TRANSLATE) {
        $rights[] = "translate";
    }
    if ($record['admin'] & ADMIN_MAINTAINANCE) {
        $rights[] = "dbmaint";
    }
    if ($record['admin'] & ADMIN_USER) {
        $rights[] = "user/caches";
    }
    if ($record['admin'] & ADMIN_RESTORE) {
        $rights[] = "vand.restore";
    }
    if ($record['admin'] & 128) {
        $rights[] = "root";
    }
    if ($record['admin'] & ADMIN_LISTING) {
        $rights[] = "listing";
    }
    $admin['rights'] = implode(", ", $rights);
    $admins[] = $admin;
}
sql_free_result($rs);

$tpl->assign('admins', $admins);
$tpl->display();
