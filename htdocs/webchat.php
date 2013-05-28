<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$sUserCountry = $login->getUserCountry();
	
	$tpl->name = 'webchat';
	$tpl->menuitem = MNU_CHAT;

	$tpl->caching = false;
	$tpl->cache_id = $sUserCountry;

	// check loggedin and set username for chat
	$chatusername = $translate->t('Guest', '', basename(__FILE__), __LINE__);
	if ($login->userid != 0)
		$chatusername = urlencode($login->username);
	
	// prepare iframe-URL
	$chatiframeurl = str_replace('{chatusername}',$chatusername,$opt['chat']['url']);
	
	// assign to template
	$tpl->assign('chatiframeurl',$chatiframeurl);
	$tpl->assign('chatiframewidth',$opt['chat']['width']);
	$tpl->assign('chatiframeheight',$opt['chat']['height']);

	$tpl->display();
?>
