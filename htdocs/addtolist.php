<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'addtolist';
$tpl->menuitem = MNU_CACHES_ADDTOLIST;

$login->verify();
if ($login->userid == 0) {
    $tpl->redirect_login();
}

$cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : 0;
if (!$cacheid) {
    $tpl->redirect('index.php');
}
$tpl->assign('cacheid', $cacheid);

if (isset($_REQUEST['cancel'])) {
    $tpl->redirect('viewcache.php?cacheid=' . $cacheid);
}

$newlist_name = isset($_REQUEST['newlist_name']) ? trim($_REQUEST['newlist_name']) : false;
if ($newlist_name == $translate->t('New cache list', '', __FILE__, __LINE__)) {
    $newlist_name = false;
}
$newlist_public = isset($_REQUEST['newlist_public']);
$newlist_watch = isset($_REQUEST['newlist_watch']);
$default_list = isset($_REQUEST['listid']) ? (int) $_REQUEST['listid'] : cachelist::getMyLastAddedToListId();

if (isset($_REQUEST['save']) && isset($_REQUEST['listid'])) {
    $listid = $_REQUEST['listid'] + 0;
    if ($listid == 0) {
        $cachelist = new cachelist(ID_NEW, $login->userid);
        $name_error = $cachelist->setNameAndVisibility($newlist_name, $newlist_public ? 2 : 0);
        if ($name_error) {
            $tpl->assign('name_error', $name_error);
        } else {
            $cachelist->setNode($opt['logic']['node']['id']);
            if ($cachelist->save()) {
                $cachelist->addCacheByID($cacheid);
                if ($newlist_watch) {
                    $cachelist->watch(true);
                }
            }
            $tpl->redirect('viewcache.php?cacheid=' . $cacheid);
        }
    } else {
        $cachelist = new cachelist($listid);
        if ($cachelist->exist()) {
            $cachelist->addCacheByID($cacheid);
        }
        $tpl->redirect('viewcache.php?cacheid=' . $cacheid);
    }
}

$tpl->assign('cachename', sql_value("SELECT `name` FROM `caches` WHERE `cache_id`='&1'", '', $cacheid));
$tpl->assign('cachelists', cachelist::getMyLists());
$tpl->assign('default_list', $default_list);
$tpl->assign('newlist_name', $newlist_name);
$tpl->assign('newlist_public', $newlist_public);
$tpl->assign('newlist_watch', $newlist_watch);
$tpl->display();
