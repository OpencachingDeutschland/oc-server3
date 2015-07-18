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
	$password = isset($_REQUEST['key']) ? $_REQUEST['key'] : '';
	$watch = isset($_REQUEST['watch']);
	$dontwatch = isset($_REQUEST['dontwatch']);
	$bookmark = isset($_REQUEST['bookmark']);

	if ($id)
	{
		$login->verify();
		$list = new cachelist($id);

		if ($list->exist())
		{
			if (($watch && $list->allowView($password)) || $dontwatch)
				$list->watch($watch);
			if ($bookmark)
				$list->bookmark($password);
		}

		$tpl->redirect("search.php?searchto=searchbylist&listid=" . $id .
		               ($password != "" ? "&listkey=" . urlencode($password) : "") . 
		               "&showresult=1&f_disabled=0&f_inactive=0&f_ignored=1&sort=byname");
	}
	else
		$tpl->redirect("cachelists.php");

?>
