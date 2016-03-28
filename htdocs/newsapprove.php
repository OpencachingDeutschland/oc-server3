<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require('./lib2/logic/user.class.php');

	$tpl->name = 'newsapprove';
	$tpl->menuitem = MNU_ADMIN_NEWS;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect_login();

	if (($login->admin & ADMIN_NEWS) != ADMIN_NEWS)
		$tpl->error(ERROR_NO_ACCESS);

	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'display';
	$id = isset($_REQUEST['id']) ? $_REQUEST['id']+0 : 0;
	if ($action == 'display')
	{
		action_display();
	}
	else if ($action == 'hide')
	{
		action_hide($id);
	}
	else if ($action == 'show')
	{
		action_show($id);
	}
	else if ($action == 'delete')
	{
		action_delete($id);
	}
	$tpl->redirect('newsapprove.php');

function action_display()
{
	global $tpl;

	$rs = sql('SELECT `news`.`id` AS `id`, `news`.`date_created` AS `date_created`, `news`.`content` AS `content`, `news`.`display` AS `display`, `news_topics`.`name` AS `topic`
	             FROM `news`
	       INNER JOIN `news_topics` ON `news`.`topic`=`news_topics`.`id`
	         ORDER BY `news`.`date_created` DESC');
	$tpl->assign_rs('newsentries', $rs);
	sql_free_result($rs);
	
	$tpl->display();
}

function action_hide($id)
{
	sql("UPDATE `news` SET `display`=0 WHERE `id`='&1'", $id);
}

function action_show($id)
{
	sql("UPDATE `news` SET `display`=1 WHERE `id`='&1'", $id);
}

function action_delete($id)
{
	sql("DELETE FROM `news` WHERE `id`='&1'", $id);
}
