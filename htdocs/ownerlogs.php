<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'ownerlogs';
$tpl->menuitem = MNU_MYPROFILE_OWNERLOGS;
$login->verify();

if (isset($_REQUEST['userid']) && $login->hasAdminPriv(ADMIN_USER)) {
    $ownerid = $_REQUEST['userid'] + 0;
} elseif ($login->userid == 0) {
    $tpl->redirect('login.php?target=ownerlogs.php');
} else {
    $ownerid = $login->userid;
}

$ownername = sql_value("SELECT `username` FROM `user` WHERE `user_id`='&1'", false, $ownerid);
if (!sql_value("SELECT `user_id` FROM `user` WHERE `user_id`='&1'", false, $ownerid)) {
    $tpl->error(ERROR_USER_NOT_EXISTS);
}
$tpl->assign('ownername', $ownername);
$tpl->assign('ownerid', $ownerid);

if ($ownerid != $login->userid) {
    $show_own_logs = true;
} else {
    $show_own_logs = isset($_REQUEST['ownlogs']) && $_REQUEST['ownlogs'];
}
$tpl->assign('show_own_logs', $show_own_logs);
$tpl->assign('ownlogs', $ownerid == $login->userid);

$tpl->assign(
    'total_found',
    sql_value(
        "SELECT COUNT(*)
        FROM `cache_logs`
        LEFT JOIN `caches` ON `caches`.`cache_id`=`cache_logs`.`cache_id`
        WHERE `cache_logs`.`type`=1 AND `caches`.`user_id`='&1'",
        0,
        $login->userid
    )
);
$tpl->assign(
    'total_attended',
    sql_value(
        "SELECT COUNT(*)
        FROM `cache_logs`
        LEFT JOIN `caches`
            ON `caches`.`cache_id`=`cache_logs`.`cache_id`
        WHERE `cache_logs`.`type`=7
        AND `caches`.`user_id`='&1'",
        0,
        $login->userid
    )
);
$tpl->assign(
    'total_dnf',
    sql_value(
        "SELECT COUNT(*)
        FROM `cache_logs`
        LEFT JOIN `caches`
            ON `caches`.`cache_id`=`cache_logs`.`cache_id`
        WHERE `cache_logs`.`type`=2
        AND `caches`.`user_id`='&1'",
        0,
        $login->userid
    )
);
$tpl->assign(
    'total_recommended',
    sql_value(
        "SELECT COUNT(*)
        FROM `cache_rating`
        LEFT JOIN `caches` ON `caches`.`cache_id`=`cache_rating`.`cache_id`
        WHERE `caches`.`user_id`='&1'",
        0,
        $login->userid
    )
);

require __DIR__ . '/newlogs.php';
