<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require('./lib2/logic/logpics.inc.php');
	$sUserCountry = $login->getUserCountry();
	
	$tpl->name = 'webchat';
	$tpl->menuitem = MNU_CHAT;

	$tpl->caching = true;
	$tpl->cache_lifetime = 300;
	$tpl->cache_id = $sUserCountry;

	// check loggedin and set username for chat
	$chatusername = $translate->t('Guest', '', basename(__FILE__), __LINE__);
	if ($login->userid != 0)
		$chatusername = urlencode($login->username);
	
	// assign to template
	$tpl->assign('chatusername',$chatusername);

	$tpl->display();
?>
