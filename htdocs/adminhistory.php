<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'adminhistory';
$tpl->menuitem = MNU_ADMIN_HISTORY;

$login->verify();
if ($login->userid == 0) {
    $tpl->redirect_login();
}

if (($login->admin & ADMIN_USER) != ADMIN_USER) {
    $tpl->error(ERROR_NO_ACCESS);
}

if (isset($_REQUEST['wp'])) {
    $cache_id = sql_value("SELECT `cache_id` FROM `caches` WHERE `wp_oc`='&1'", 0, $_REQUEST['wp']);
} else {
    $cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : - 1;
}

$showhistory = false;
$error = '';

if ($cache_id >= 0 && sql_value("SELECT COUNT(*) FROM `caches` WHERE `cache_id`='&1'", 0, $cache_id) <> 1) {
    $error = $translate->t('Cache not found', '', '', 0);
} elseif ($cache_id > 0) {
    $showhistory = true;
    $cache = new cache($cache_id);
    $cache->setTplHistoryData(0);
}

$tpl->assign('showhistory', $showhistory);
$tpl->assign('error', $error);
$tpl->display();
