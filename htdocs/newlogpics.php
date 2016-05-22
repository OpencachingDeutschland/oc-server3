<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'newlogpics';
$tpl->menuitem = MNU_START_NEWLOGPICS;

$tpl->caching = true;
$tpl->cache_lifetime = 300;

if (!$tpl->is_cached()) {
    $tpl->assign('pictures', LogPics::get(LogPics::FOR_NEWPICS_GALLERY));
}

$tpl->display();
