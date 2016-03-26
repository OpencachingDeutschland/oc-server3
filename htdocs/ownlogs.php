<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require 'lib2/web.inc.php';

	$tpl->name = 'ownlogs';
	$tpl->menuitem = MNU_MYPROFILE_OWNLOGS;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect('login.php?target=ownlogs.php');
	$userid = $login->userid;

	$tpl->assign('ownlogs', true);

	require 'newlogs.php';
