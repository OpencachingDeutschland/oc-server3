<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$login->verify();

$tpl->name = 'cachelists';
$tpl->menuitem = MNU_CACHES_LISTS;

if (isset($_REQUEST['watchlist'])) {
    $list = new cachelist($_REQUEST['watchlist'] + 0);
    if ($list->exist()) {
        $list->watch(true);
    }
} elseif (isset($_REQUEST['dontwatchlist'])) {
    $list = new cachelist($_REQUEST['dontwatchlist'] + 0);
    if ($list->exist()) {
        $list->watch(false);
    }
}

$maxItems = 30;
$startAt = isset($_REQUEST['startat']) ? max(0, floor($_REQUEST['startat'] + 0)) : 0;
$name_filter = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
$by_filter = isset($_REQUEST['by']) ? $_REQUEST['by'] : '';
$listCount = cachelist::getPublicListCount($name_filter, $by_filter);

$tpl->assign('name_filter', $name_filter);
$tpl->assign('by_filter', $by_filter);
$tpl->assign('cachelists', cachelist::getPublicLists($startAt, $maxItems, $name_filter, $by_filter));
$tpl->assign('show_bookmarks', true);
$tpl->assign('show_status', false);
$tpl->assign('show_user', true);
// Do not show watchers because this would allow conclusions on what the list owner watches.
$tpl->assign('show_watchers', false);
$tpl->assign('show_edit', false);
$tpl->assign('togglewatch', 'cachelists.php');

$pager = new pager("cachelists.php?startat={offset}");
$pager->make_from_offset($startAt, $listCount, $maxItems);

$tpl->display();
