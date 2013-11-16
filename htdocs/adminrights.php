<?php

/* * *************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 * ************************************************************************* */

require('./lib2/web.inc.php');
require_once('./lib2/logic/user.class.php');

$tpl->name = 'adminrights';
$tpl->menuitem = MNU_ADMIN_RIGHTS;

$error = 0;

$login->verify();
if ($login->userid == 0)
    $tpl->redirect_login();

if (!($login->admin & 128))
    $tpl->error(ERROR_NO_ACCESS);

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'display';
$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid'] + 0 : 0;
$permid = isset($_REQUEST['permid']) ? $_REQUEST['permid'] + 0 : 0;


if ($action == 'searchuser') {
    searchUser();
} else if ($action == 'removeperms') {
    removePerms();
    searchUser();

    $tpl->display();
} else if ($action == 'addperm') {
    addPerms();
    searchUser();

    $tpl->display();
} else {
    $tpl->display();
}

function searchUser() {
    global $tpl, $opt, $userid;

    $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';





    $rs = sql("SELECT `user_id`, `username`, `admin` FROM `user` WHERE `username`='&1' OR `email`='&1' OR `user_id`='&2'", $username, $userid);
    $r = sql_fetch_assoc($rs);
    sql_free_result($rs);
    if ($r == false) {
        $tpl->assign('error', 'userunknown');
        $tpl->display();
    }
    $tpl->assign('haveallperms', false);
    $tpl->assign('haveno', false);
    //Rights the User have
    $rights = array();
    $norights = array();
    if ($r['admin'] & ADMIN_TRANSLATE) {
        $rights[ADMIN_TRANSLATE] = "translate";
    } else {
        $norights[ADMIN_TRANSLATE] = "translate";
    }
    if ($r['admin'] & ADMIN_MAINTAINANCE) {
        $rights[ADMIN_MAINTAINANCE] = "dbmaint";
    } else {
        $norights[ADMIN_MAINTAINANCE] = "dbmaint";
    }
    if ($r['admin'] & ADMIN_USER) {
        $rights[ADMIN_USER] = "user/caches";
    } else {
        $norights[ADMIN_USER] = "user/caches";
    }
    if ($r['admin'] & ADMIN_NEWS) {
        $rights[ADMIN_NEWS] = "newsapprove";
    } else {
        $norights[ADMIN_NEWS] = "newsapprove";
    }
    if ($r['admin'] & ADMIN_RESTORE) {
        $rights[ADMIN_RESTORE] = "vand.restore";
    } else {
        $norights[ADMIN_RESTORE] = "vand.restore";
    }
    if ($r['admin'] & 128) {
        $rights[128] = "root";
    } else {
        $norights[128] = "root";
    }
    if ($r['admin'] & ADMIN_LISTING) {
        $rights[ADMIN_LISTING] = "listing";
    } else {
        $norights[ADMIN_LISTING] = "listing";
    }


    $r['rights'] = $rights;
    if (!empty($norights)) {
        $r['norights'] = $norights;
    } else {
        $tpl->assign('haveallperms', true);
    }

    $tpl->assign('showdetails', true);
    $tpl->assign('username', $r['username']);


    $tpl->assign('user', $r);

    $user = new user($r['user_id']);
    if (!$user->exist())
        $tpl->error(ERROR_UNKNOWN);

    $tpl->display();
}

function removePerms() {
    global $userid, $permid, $tpl;

    if ($permid != ADMIN_TRANSLATE && $permid != ADMIN_MAINTAINANCE && $permid != ADMIN_USER && $permid != ADMIN_NEWS && $permid != ADMIN_RESTORE && $permid != ADMIN_LISTING && $permid != 128 && $permid != ADMIN_TRANSLATE) {
        $tpl->error(ERROR_NO_ACCESS);
    }
    $rs = sql("SELECT `admin` FROM `user` WHERE `user_id`='&1'", $userid);
    $r = sql_fetch_assoc($rs);
    sql_free_result($rs);
    if ($r == false) {
        $tpl->assign('error', 'userunknown');
        $tpl->display();
    }
    if ($r['admin'] & $permid) {
        $perm = $r['admin'] - $permid;
        sql("UPDATE `user` SET `admin`='&1' WHERE `user_id`='&2'", $perm, $userid);
    }
}

function addPerms() {
    global $userid, $permid, $tpl;

    if ($permid != ADMIN_TRANSLATE && $permid != ADMIN_MAINTAINANCE && $permid != ADMIN_USER && $permid != ADMIN_NEWS && $permid != ADMIN_RESTORE && $permid != ADMIN_LISTING && $permid != 128 && $permid != ADMIN_TRANSLATE) {
        $tpl->error(ERROR_NO_ACCESS);
    }
    $rs = sql("SELECT `admin` FROM `user` WHERE `user_id`='&1'", $userid);
    $r = sql_fetch_assoc($rs);
    sql_free_result($rs);
    if ($r == false) {
        $tpl->assign('error', 'userunknown');
        $tpl->display();
    }
    if (!($r['admin'] & $permid)) {
        $perm = $r['admin'] + $permid;
        sql("UPDATE `user` SET `admin`='&1' WHERE `user_id`='&2'", $perm, $userid);
    }
}

?>
