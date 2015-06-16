<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Shortcut for cachelist search
 ***************************************************************************/

	require('./lib2/web.inc.php');

	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] + 0 : 0;
	if ($id) 
		$tpl->redirect("search.php?searchto=searchbylist&listid=" . $id . "&showresult=1&f_disabled=0&f_inactive=0&f_ignored=1&sort=byname");
	else
		$tpl->redirect("cachelists.php");

?>
