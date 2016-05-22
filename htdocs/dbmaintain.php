<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'dbmaintain';
$tpl->menuitem = MNU_ADMIN_DBMAINTAIN;

$login->verify();
if ($login->userid == 0) {
    $tpl->redirect('login.php?target=dbmaintain.php');
}

if (($login->admin & ADMIN_MAINTAINANCE) != ADMIN_MAINTAINANCE) {
    $tpl->error(ERROR_NO_ACCESS);
}

$procedures = array();
$procedures[] = 'sp_updateall_caches_descLanguages';
$procedures[] = 'sp_updateall_logstat';
$procedures[] = 'sp_updateall_hiddenstat';
$procedures[] = 'sp_updateall_watchstat';
$procedures[] = 'sp_updateall_ignorestat';
$procedures[] = 'sp_updateall_topratingstat';
$procedures[] = 'sp_updateall_cache_picturestat';
$procedures[] = 'sp_updateall_cachelog_picturestat';
$procedures[] = 'sp_updateall_cache_listingdates';
$procedures[] = 'sp_updateall_cachelog_logdates';
$procedures[] = 'sp_updateall_rating_dates';
$procedures[] = 'sp_updateall_cachelist_counts';

$tpl->assign('procedures', $procedures);

if (isset($_REQUEST['ok'])) {
    $proc = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

//        if (sql_connect_maintenance() == false)
//            $tpl->error(ERROR_DB_NO_ROOT);

    // @todo change if to switch/case
    $bError = false;
    if ($proc == 'sp_updateall_caches_descLanguages') {
        sql("CALL sp_updateall_caches_descLanguages(@c)");
    } elseif ($proc == 'sp_updateall_logstat') {
        sql("CALL sp_updateall_logstat(@c)");
    } elseif ($proc == 'sp_updateall_hiddenstat') {
        sql("CALL sp_updateall_hiddenstat(@c)");
    } elseif ($proc == 'sp_updateall_watchstat') {
        sql("CALL sp_updateall_watchstat(@c)");
    } elseif ($proc == 'sp_updateall_ignorestat') {
        sql("CALL sp_updateall_ignorestat(@c)");
    } elseif ($proc == 'sp_updateall_topratingstat') {
        sql("CALL sp_updateall_topratingstat(@c)");
    } elseif ($proc == 'sp_updateall_cache_picturestat') {
        sql("CALL sp_updateall_cache_picturestat(@c)");
    } elseif ($proc == 'sp_updateall_cachelog_picturestat') {
        sql("CALL sp_updateall_cachelog_picturestat(@c)");
    } elseif ($proc == 'sp_updateall_cache_listingdates') {
        sql("CALL sp_updateall_cache_listingdates(@c)");
    } elseif ($proc == 'sp_updateall_cachelog_logdates') {
        sql("CALL sp_updateall_cachelog_logdates(@c)");
    } elseif ($proc == 'sp_updateall_rating_dates') {
        sql("CALL sp_updateall_rating_dates(@c)");
    } elseif ($proc == 'sp_updateall_cachelist_counts') {
        sql("CALL sp_updateall_cachelist_counts(@c)");
    } else {
        $bError = true;
    }

    if ($bError === false) {
        $count = sql_value("SELECT @c", 0);
        $tpl->assign('executed', true);
        $tpl->assign('proc', $proc);
        $tpl->assign('count', $count);
    }
}

$tpl->display();
