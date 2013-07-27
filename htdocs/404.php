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
	
	// simplify variables if exists
	$redirectUrl = '';
	$isRedirect404 = false;
	if (isset($_SERVER['REDIRECT_URL']))
	{
		$redirectUrl = $_SERVER['REDIRECT_URL'];
		$isRedirect404 = true;
	}
	
	// assign redirection status
	$tpl->assign('isRedirect404', $isRedirect404);
	
	// check if original path starts with "/" and remove it
	if (substr($redirectUrl, 0, 1) == '/')
		$redirectUrl = substr($redirectUrl, 1);
	
	// get number of subdirectories (-1 because the last part of url is treated as file)
	$numDirs = count(explode('/', $redirectUrl)) - 1 -2;
	
	// put ../ together according to $numDirs
	$prePath = '';
	for ($i=0; $i<$numDirs;  $i++)
	{
		$prePath .= '../';
	}
	
	// assign path
	$tpl->assign('actualpath',$prePath);

	// website, if is 404 redirection
	if ($isRedirect404)
	{
		// check length
		$uril = 70;
		$uri = 'http://'.strtolower($_SERVER['SERVER_NAME']).$_SERVER['REQUEST_URI'];
		// limit to $uril
		if(strlen($uri) > $uril) {
			$uri = substr($uri,0,$uril-3).'...';
		}
		
		// assign uri
		$tpl->assign('website',$uri);
	}
	else
		$tpl->assign('website','');
	
	// set feeds and options
	$feeds = array('blog', 'forum', 'wiki');
	$options = $feeds;
	array_push($options, 'newcaches');
	
	// simplify $opt
	foreach ($options as $option)
	{
		$opt404[$option] = (isset($opt['page']['404'][$_SERVER['SERVER_NAME']][$option]) ? $opt['page']['404'][$_SERVER['SERVER_NAME']][$option] : $opt['page']['404']['www.opencaching.de'][$option]);
	}
	
	// get feeds from $feeds array
	foreach ($feeds as $feed)
	{
		if ($isRedirect404)
		{ 
			if ($opt404[$feed]['show'])
				$tpl->assign($feed, $getNew->feedForSmarty($feed,3,$opt404[$feed]['feedurl']));
		}
		else
			$tpl->assign($feed, $getNew->feedForSmarty($feed,3));
	}
	
	// get newest caches
	if ($isRedirect404)
	{
		if ($opt404['newcaches']['show'])
			$tpl->assign_rs('newcaches', $getNew->rsForSmarty('cache',array($sUserCountry, $opt['template']['locale'],3)));
	}
	else
		$tpl->assign_rs('newcaches', $getNew->rsForSmarty('cache',array($sUserCountry, $opt['template']['locale'],3)));

	// assign $opt404
	$tpl->assign('opt404', $opt404);
	
	// assign contact
	$tpl->assign('contact', $opt['mail']['contact']);

	// show page
	$tpl->display();
?>
