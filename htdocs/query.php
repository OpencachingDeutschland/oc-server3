<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'query';
	$tpl->menuitem = MNU_MYPROFILE_QUERIES;

	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect('login.php?target=query.php');

	if ($action == 'save')
	{
		$queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid']+0 : 0;
		$queryname = isset($_REQUEST['queryname']) ? $_REQUEST['queryname'] : '';
		$submit = isset($_REQUEST['submit']) ? ($_REQUEST['submit'] == '1') : false;

		savequery($queryid, $queryname, false, $submit, 0);
	}
	else if ($action == 'saveas')
	{
		$queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid']+0 : 0;
		$queryname = isset($_REQUEST['queryname']) ? $_REQUEST['queryname'] : '';
		$submit = isset($_REQUEST['submit']) ? ($_REQUEST['submit'] == '1') : false;
		$oldqueryid = isset($_REQUEST['oldqueryid']) ? $_REQUEST['oldqueryid']+0 : 0;

		savequery($queryid, $queryname, true, $submit, $oldqueryid);
	}
	else if ($action == 'delete')
	{
		$queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid']+0 : 0;
		deletequery($queryid);
	}
	else // default: view
	{
		viewqueries();
	}

function deletequery($queryid)
{
	global $tpl, $login;

	sql("DELETE FROM `queries` WHERE `id`='&1' AND `user_id`='&2' LIMIT 1", $queryid, $login->userid);

	$tpl->redirect('query.php?action=view');
}

function viewqueries()
{
	global $tpl, $login;

	$tpl->assign('action', 'view');

	$rs = sql("SELECT `id`, `name` FROM `queries` WHERE `user_id`='&1' ORDER BY `name` ASC", $login->userid);
	$tpl->assign_rs('queries', $rs);
	sql_free_result($rs);

	$tpl->display();
}

function savequery($queryid, $queryname, $saveas, $submit, $saveas_queryid)
{
	global $login, $tpl;

	if ($submit == true)
	{
		// check if query exists
		if (sql_value("SELECT COUNT(*) FROM `queries` WHERE `id`='&1'", 0, $queryid) == 0)
			$tpl->error(ERROR_UNKNOWN);

		if ($saveas == false)
		{
			$bError = false;
			if ($queryname == '')
			{
				$tpl->assign('errorEmptyName', true);
				$bError = true;
			}

			if (sql_value("SELECT COUNT(*) FROM `queries` WHERE `name`='&1' AND `user_id`='&2'", 0, $queryname, $login->userid) > 0)
			{
				$tpl->assign('errorNameExists', true);
				$bError = true;
			}

			if ($bError == false)
			{
				// save
				sql("UPDATE `queries` SET `user_id`='&1', `name`='&2' WHERE `id`='&3'", $login->userid, $queryname, $queryid);
				$tpl->redirect('query.php?action=view');
			}
		}
		else
		{
			if (sql_value("SELECT COUNT(*) FROM `queries` WHERE `id`='&1' AND `user_id`='&2'", 0, $saveas_queryid, $login->userid) == 0)
				$tpl->assign('errorMustSelectQuery',true);
			else
			{
				// save as
				$oOptions = sql_value("SELECT `options` FROM `queries` WHERE `id`='&1'", array(), $queryid);
				sql("UPDATE `queries` SET `options`='&1' WHERE `id`='&2'", $oOptions, $saveas_queryid);

				$tpl->redirect('query.php?action=view');
			}
		}
	}

	$rs = sql("SELECT `id`, `name` FROM `queries` WHERE `user_id`='&1' ORDER BY `name` ASC", $login->userid);
	$tpl->assign_rs('queries', $rs);
	sql_free_result($rs);

	$tpl->assign('queryid', $queryid);
	$tpl->assign('queryname', $queryname);

	$tpl->assign('action', 'save');
	$tpl->display();
}
?>