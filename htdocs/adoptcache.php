<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'adoptcache';
$tpl->menuitem = MNU_CACHES_ADOPT;

$login->verify();
if ($login->userid == 0) {
    $tpl->redirect_login();
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'listbyuser';
$tpl->assign('action', $action);
$tpl->assign('error', '');
if (isset($_REQUEST['cacheid'])) {
    $tpl->assign('cacheid', $_REQUEST['cacheid'] + 0);
}

if ($action == 'listbycache') {
    $cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : 0;
    listRequestsByCacheId($cacheid);
} elseif ($action == 'add') {
    $tpl->assign('action', 'listbycache');

    $cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : 0;
    $tou = isset($_REQUEST['tou']) ? $_REQUEST['tou'] + 0 : 0;
    $submit = isset($_REQUEST['submit']) ? $_REQUEST['submit'] + 0 : 0;

    $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
    $tpl->assign('adoptusername', $username);

    if ($submit == 1) {
        $userid = sql_value("SELECT `user_id` FROM `user` WHERE `username`='&1'", 0, $username);
        if ($userid == 0) {
            $tpl->assign('error', 'userunknown');
        } elseif ($tou != 1) {
            $tpl->assign('error', 'tou');
        } else {
            addRequest($cacheid, $userid);
        }
    }

    listRequestsByCacheId($cacheid);
} elseif ($action == 'cancel') {
    $cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : 0;
    $userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] + 0 : 0;
    cancelRequest($cacheid, $userid);
} elseif ($action == 'commit') {
    $cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : 0;
    $submit = isset($_REQUEST['submit']) ? $_REQUEST['submit'] + 0 : 0;
    $tou = isset($_REQUEST['tou']) ? $_REQUEST['tou'] + 0 : 0;

    if ($submit == 1 && $tou == 1) {
        commitRequest($cacheid);
    } else {
        showAdoptScreen($cacheid, $submit);
    }
} else {
    $tpl->assign('action', 'listbyuser');
    listRequestsByUserId();
}

$tpl->error(ERROR_UNKNOWN);

function showAdoptScreen($cacheid, $touerror)
{
    global $tpl, $login;

    $rs = sql(
        "SELECT `caches`.`name`, `user`.`username`, `cache_adoption`.`date_created`
         FROM `caches`
         INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
         INNER JOIN `cache_adoption` ON `caches`.`cache_id`=`cache_adoption`.`cache_id`
         WHERE `caches`.`cache_id`='&1'
         AND `cache_adoption`.`user_id`='&2'",
        $cacheid,
        $login->userid
    );
    $r = sql_fetch_assoc($rs);
    if ($r === false) {
        $tpl->error(ERROR_NO_ACCESS);
    }

    $tpl->assign('cache', $r);
    sql_free_result($rs);

    if ($touerror != 0) {
        $tpl->assign('error', 'tou');
    }

    $tpl->display();
}

function listRequestsByCacheId($cacheid)
{
    global $tpl, $login;

    // cache exists?
    $cache = new cache($cacheid);
    if ($cache->exist() == false) {
        $tpl->error(ERROR_CACHE_NOT_EXISTS);
    }

    // is the current user the owner of the cache?
    if ($cache->getUserId() != $login->userid) {
        $tpl->error(ERROR_NO_ACCESS);
    }

    $rs = sql(
        "SELECT
             `caches`.`cache_id` AS `id`,
             `user`.`user_id` AS `userid`,
             `user`.`username` AS `username`,
             `cache_adoption`.`date_created`
         FROM `caches`
         INNER JOIN `cache_adoption`
             ON `caches`.`cache_id` = `cache_adoption`.`cache_id`
         INNER JOIN `user`
             ON `cache_adoption`.`user_id`=`user`.`user_id`
         WHERE `caches`.`cache_id`='&1'",
        $cacheid
    );
    $tpl->assign_rs('adoptions', $rs);
    sql_free_result($rs);

    $tpl->assign('cachename', $cache->getName());

    $tpl->display();
}

function listRequestsByUserId()
{
    global $tpl, $login;

    $tpl->menuitem = MNU_MYPROFILE_ADOPT;

    $rs = sql(
        "SELECT
             `caches`.`cache_id` AS `id`,
             `caches`.`name` AS `cachename`,
             `user`.`user_id` AS `ownerid`,
             `user`.`username` AS `ownername`,
             `cache_adoption`.`date_created`
         FROM `caches`
         INNER JOIN `cache_adoption`
             ON `caches`.`cache_id` = `cache_adoption`.`cache_id`
         INNER JOIN `user`
             ON `caches`.`user_id`=`user`.`user_id`
         WHERE `cache_adoption`.`user_id`='&1'",
        $login->userid
    );
    $tpl->assign_rs('adoptions', $rs);
    sql_free_result($rs);

    $tpl->display();
}

function addRequest($cacheid, $userid)
{
    global $tpl;

    // cache exists?
    $cache = new cache($cacheid);
    if ($cache->exist() == false) {
        $tpl->error(ERROR_CACHE_NOT_EXISTS);
    }

    $adopt_result = $cache->addAdoption($userid);
    if ($adopt_result === true) {
        $tpl->redirect('adoptcache.php?action=listbycache&cacheid=' . $cacheid);
    } else {
        $tpl->assign('error', $adopt_result);
        listRequestsByCacheId($cacheid);
    }
}

function commitRequest($cacheid)
{
    global $tpl, $login;

    // cache exists?
    $cache = new cache($cacheid);
    if ($cache->exist() == false) {
        $tpl->error(ERROR_CACHE_NOT_EXISTS);
    }

    if ($cache->commitAdoption($login->userid) == false) {
        $tpl->error(ERROR_UNKNOWN);
    }

    $tpl->redirect('viewcache.php?cacheid=' . $cacheid);
}

function cancelRequest($cacheid, $userid)
{
    global $tpl, $login;

    // cache exists?
    $cache = new cache($cacheid);
    if ($cache->exist() == false) {
        $tpl->error(ERROR_CACHE_NOT_EXISTS);
    }

    if ($cache->allowEdit() == false && $login->userid != $userid) {
        $tpl->error(ERROR_NO_ACCESS);
    }

    if ($cache->cancelAdoption($userid) == false) {
        $tpl->error(ERROR_UNKNOWN);
    }

    if ($userid == $login->userid) {
        $tpl->redirect('adoptcache.php');
    } else {
        $tpl->redirect('adoptcache.php?action=listbycache&cacheid=' . $cacheid);
    }
}
