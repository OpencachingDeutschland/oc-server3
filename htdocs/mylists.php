<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

use OcLegacy\Editor\EditorConstants;

require __DIR__ . '/lib2/web.inc.php';
require_once __DIR__ . '/lib2/edithelper.inc.php';

$tpl->name = 'mylists';
$tpl->menuitem = MNU_MYPROFILE_LISTS;

$login->verify();
if ($login->userid == 0) {
    $tpl->redirect('login.php?target=' . urlencode($tpl->target));
}
$user = new user($login->userid);

$list_name = isset($_REQUEST['list_name']) ? trim($_REQUEST['list_name']) : '';
$list_visibility = isset($_REQUEST['list_visibility']) ? $_REQUEST['list_visibility'] + 0 : 0;
$list_password = isset($_REQUEST['list_password']) ? $_REQUEST['list_password'] : '';
$watch = isset($_REQUEST['watch']);
$desctext = isset($_REQUEST['desctext']) ? $_REQUEST['desctext'] : '';
$switchDescMode = isset($_REQUEST['switchDescMode']) && $_REQUEST['switchDescMode'] == 1;
$fromsearch = isset($_REQUEST['fromsearch']) && $_REQUEST['fromsearch'] == 1;

if (isset($_REQUEST['list_caches'])) {
    $list_caches = strtoupper(trim($_REQUEST['list_caches']));
} elseif (isset($_REQUEST['addCache']) &&  $_REQUEST['addCache'] >= 1) {
    $list_caches = $_REQUEST['addCache'];
} else {
    $list_caches = '';
}

if (isset($_REQUEST['addCache'])){
    foreach ($list_caches as $nCacheId) {
        $cache = new cache($nCacheId);
        $oc_codes[] = $cache->getWPOC();
    }
    $list_caches = implode(" ", $oc_codes);
}

if (isset($_REQUEST['descMode'])) {
    $descMode = min(EditorConstants::EDITOR_MODE, max(EditorConstants::HTML_MODE, $_REQUEST['descMode'] + 0));
} else {
    $descMode = EditorConstants::EDITOR_MODE;
}

$edit_list = false;
$name_error = false;
$invalid_waypoints = false;

// open a 'create new list' form
if (isset($_REQUEST['new'])) {
    $tpl->assign('newlist_mode', true);
    $tpl->assign('show_editor', false);
    $list_name = '';
    $list_visibility = 0;
    $list_password = '';
    $watch = false;
    $desctext = '';
    // keep descMode of previous operation
}

// save the data entered in the 'create new list' form
if (isset($_REQUEST['create'])) {
    $list = new cachelist(ID_NEW, $login->userid);
    $name_error = $list->setNameAndVisibility($list_name, $list_visibility);
    if ($name_error) {
        $tpl->assign('newlist_mode', true);
    } else {
        $list->setNode($opt['logic']['node']['id']);
        $list->setPassword($list_password);
        $purifier = new OcHTMLPurifier($opt);
        $list->setDescription($purifier->purify($desctext), $descMode == EditorConstants::EDITOR_MODE);
        if ($list->save()) {
            if ($list_caches != '') {
                $result = $list->addCachesByWPs($list_caches);
                $invalid_waypoints = ($result === true ? false : implode(', ', $result));
                $tpl->assign('invalid_waypoints', $invalid_waypoints);
            }
            if ($watch) {
                $list->watch(true);
            }
        }
    }
}

// open an 'edit list' form
if (isset($_REQUEST['edit'])) {
    $list = new cachelist($_REQUEST['edit'] + 0);
    if ($list->exist() && $list->getUserId() == $login->userid) {
        $edit_list = true;
        $list_name = $list->getName();
        $list_visibility = $list->getVisibility();
        $list_password = $list->getPassword();
        $watch = $list->isWatchedByMe();
        $desctext = $list->getDescription();
        $descMode = $list->getDescHtmledit() ? EditorConstants::EDITOR_MODE : EditorConstants::HTML_MODE;
    }
}

// switch between HTML and Wysiwyg editor mode
if ($switchDescMode) {
    if (isset($_REQUEST['listid'])) {
        // switching editor mode while editing existing list
        $list = new cachelist($_REQUEST['listid'] + 0);
        if ($list->exist() && $list->getUserId() == $login->userid) {
            $edit_list = true;
            $tpl->assign('show_editor', true);
        }
    } else {
        // switching desc mode while creating new list
        $tpl->assign('newlist_mode', true);
        $tpl->assign('show_editor', true);
    }
}

// save data entered in the 'edit list' form
if (isset($_REQUEST['save']) && isset($_REQUEST['listid'])) {
    $list = new cachelist($_REQUEST['listid'] + 0);
    if ($list->exist() && $list->getUserId() == $login->userid) {
        $name_error = $list->setNameAndVisibility($list_name, $list_visibility);
        if ($name_error) {
            $edit_list = true;
        }
        $list->setPassword($list_password);
        $purifier = new OcHTMLPurifier($opt);
        $list->setDescription($purifier->purify($desctext), $descMode == EditorConstants::EDITOR_MODE);
        $list->save();

        $list->watch($watch);
        if ($list_caches != '') {
            $result = $list->addCachesByWPs($list_caches);
            $invalid_waypoints = ($result === true ? false : implode(', ', $result));
            $tpl->assign('invalid_waypoints', $invalid_waypoints);
            $list_caches = '';
        }
        foreach ($_REQUEST as $key => $value) {
            if (substr($key, 0, 7) == 'remove_') {
                $list->removeCacheById(substr($key, 7));
            }
        }
    }
}

// delete a list
if (isset($_REQUEST['delete'])) {
    sql(
        "DELETE FROM `cache_lists` WHERE `user_id`='&1' AND `id`='&2'",
        $login->userid,
        $_REQUEST['delete'] + 0
    );
    // All dependent deletion and cleanup is done via trigger.
}

// unbookmark a list
if (isset($_REQUEST['unbookmark'])) {
    $list = new cachelist($_REQUEST['unbookmark'] + 0);
    if ($list->exist()) {
        $list->unbookmark();
    }
}

// redirect to list search output after editing a list from the search output page
if ($fromsearch && !$switchDescMode && !$name_error && isset($_REQUEST['listid'])) {
    $iwp = ($invalid_waypoints ? '&invalidwp=' . urlencode($invalid_waypoints) : '');
    $tpl->redirect('cachelist.php?id=' . ($_REQUEST['listid'] + 0) . $iwp);
}

// prepare editor and editing
if ($descMode == EditorConstants::EDITOR_MODE) {
    $tpl->add_header_javascript('resource2/tinymce/tiny_mce_gzip.js');
    $tpl->add_header_javascript('resource2/tinymce/config/list.js.php?lang=' . strtolower($opt['template']['locale']));
}
$tpl->add_header_javascript(editorJsPath());
if ($edit_list) {
    $tpl->assign('edit_list', true);
    $tpl->assign('listid', $list->getId());
    $tpl->assign('caches', $list->getCaches());
}

// prepare rest of template
$tpl->assign('cachelists', cachelist::getMyLists());
$tpl->assign('bookmarked_lists', cachelist::getBookmarkedLists());

$tpl->assign('show_status', true);
$tpl->assign('show_user', false);
$tpl->assign('show_watchers', true);
$tpl->assign('show_edit', true);
$tpl->assign('togglewatch', false);
$tpl->assign('fromsearch', $fromsearch);
$tpl->assign('name_error', $name_error);

$tpl->assign('list_name', $list_name);
$tpl->assign('list_visibility', $list_visibility);
$tpl->assign('list_password', $list_password);
$tpl->assign('watch', $watch);
$tpl->assign('desctext', $desctext);
$tpl->assign('descMode', $descMode);
$tpl->assign('list_caches', $list_caches);

$tpl->assign('scrollposx', isset($_REQUEST['scrollposx']) ? $_REQUEST['scrollposx'] + 0 : 0);
$tpl->assign('scrollposy', isset($_REQUEST['scrollposy']) ? $_REQUEST['scrollposy'] + 0 : 0);

$tpl->display();
