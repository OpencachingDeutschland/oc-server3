<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'addtolist';
$tpl->menuitem = MNU_CACHES_ADDTOLIST;

$login->verify();
if ($login->userid  == 0) {
    $tpl->redirect_login();
}

$cacheId = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : 0;
if (!$cacheId) {
    $tpl->redirect('index.php');
}
$tpl->assign('cacheid', $cacheId);

if (isset($_REQUEST['cancel'])) {
    $tpl->redirect('viewcache.php?cacheid=' . $cacheId);
}

$newListName = isset($_REQUEST['newlist_name']) ? trim($_REQUEST['newlist_name']) : false;
if ($newListName === $translate->t('New cache list', '', __FILE__, __LINE__)) {
    $newListName = false;
}
$newListPublic = isset($_REQUEST['newlist_public']);
$newListWatch = isset($_REQUEST['newlist_watch']);
$default_list = isset($_REQUEST['listid']) ? (int)$_REQUEST['listid'] : cachelist::getMyLastAddedToListId();

if (isset($_REQUEST['save'], $_REQUEST['listid'])) {
    $listId = (int) $_REQUEST['listid'];
    if ($listId === 0) {
        $cacheList = new cachelist(ID_NEW, $login->userid);
        $name_error = $cacheList->setNameAndVisibility($newListName, $newListPublic ? 2 : 0);
        if ($name_error) {
            $tpl->assign('name_error', $name_error);
        } else {
            $cacheList->setNode($opt['logic']['node']['id']);
            if ($cacheList->save()) {
                $cacheList->addCacheByID($cacheId);
                if ($newListWatch) {
                    $cacheList->watch(true);
                }
            }
            $tpl->redirect('viewcache.php?cacheid=' . $cacheId);
        }
    } else {
        $cacheList = new cachelist($listId);
        if ($cacheList->exist()) {
            $cacheList->addCacheByID($cacheId);
        }
        $tpl->redirect('viewcache.php?cacheid=' . $cacheId);
    }
}

$tpl->assign('cachename', sql_value("SELECT `name` FROM `caches` WHERE `cache_id`='&1'", '', $cacheId));
$tpl->assign('cachelists', cachelist::getMyLists());
$tpl->assign('default_list', $default_list);
$tpl->assign('newlist_name', $newListName);
$tpl->assign('newlist_public', $newListPublic);
$tpl->assign('newlist_watch', $newListWatch);
$tpl->display();
