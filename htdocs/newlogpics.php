<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require('./lib2/logic/logpics.inc.php');

	$tpl->name = 'newlogpics';
	$tpl->menuitem = MNU_START_NEWLOGPICS;

	$tpl->caching = true;
	$tpl->cache_lifetime = 300;

	if (!$tpl->is_cached())
		$tpl->assign('pictures', get_logpics(LOGPICS_FOR_NEWPICS_GALLERY));

	$tpl->display();
?>
