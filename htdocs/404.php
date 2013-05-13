<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('lib2/web.inc.php');
	require('lib2/logic/logpics.inc.php');
	$sUserCountry = $login->getUserCountry();

	// create object for "newest" information
	$getNew = new getNew($sUserCountry);

	$tpl->main_template = 'sys_oc404';
	$tpl->name = 'sys_oc404';

	$tpl->caching = false;
	$tpl->cache_lifetime = 300;
	$tpl->cache_id = $sUserCountry;

	// rootpath
	$tpl->assign('rootpath',$opt['rootpath']);

	// website
	// check length
	$uril = 70;
	$uri = 'http://'.strtolower($_SERVER['SERVER_NAME']).$_SERVER['REQUEST_URI'];
	// limit to $uril
	if(strlen($uri) > $uril) {
		$uri = substr($uri,0,$uril-3).'...';
	}
	// $tpl->assign('website',$uri);
	$tpl->assign('website','');

	// get newest blog entries
	$tpl->assign('blog', $getNew->feedForSmarty('blog',3));

	// get newest forum posts
	$tpl->assign('forum',$getNew->feedForSmarty('forum',3));

	// get newest wiki
	$tpl->assign('wiki', $getNew->feedForSmarty('wiki',3));

	// get newest caches
	$tpl->assign_rs('newcaches', $getNew->rsForSmarty('cache',array($sUserCountry, $opt['template']['locale'],3)));

	$tpl->assign('contact', $opt['mail']['contact']);

	$tpl->display();
?>
