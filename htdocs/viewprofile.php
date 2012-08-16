<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/useroptions.class.php');

	$tpl->name = 'viewprofile';
	$tpl->menuitem = MNU_CACHES_USERPROFILE;

	$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid']+0 : 0;

	$rs = sql("SELECT `user`.`username`, `user`.`last_login`, `user`.`pmr_flag`, `user`.`date_created`, `user`.`password`, `user`.`email`, `user`.`is_active_flag`, `user`.`latitude`, `user`.`longitude`, `countries`.`de` AS `country`, `stat_user`.`hidden`, `stat_user`.`found`, `stat_user`.`notfound`, `stat_user`.`note`, `user`.`uuid` FROM `user` LEFT JOIN `stat_user` ON `user`.`user_id`=`stat_user`.`user_id` LEFT JOIN `countries` ON `user`.`country`=`countries`.`short` WHERE `user`.`user_id`='&1'", $userid);
	$record = sql_fetch_array($rs);
	sql_free_result($rs);

	if ($record === false)
		$tpl->error(ERROR_USER_NOT_EXISTS);

	$rs = sql("SELECT IFNULL(`tt`.`text`, `p`.`name`) AS `name`, `u`.`option_value`, `u`.`option_id` AS `option_id`
		           FROM `profile_options` AS `p`
		      LEFT JOIN `user_options` AS `u` ON `p`.`id`=`u`.`option_id`
		      LEFT JOIN `sys_trans` AS `st` ON `st`.`id`=`p`.`trans_id` AND `st`.`text`=`p`.`name`
		      LEFT JOIN `sys_trans_text` AS `tt` ON `st`.`id`=`tt`.`trans_id` AND `tt`.`lang` = '&2'
		          WHERE `u`.`option_visible`=1
		            AND `p`.`internal_use`=0
		            AND `u`.`user_id`='&1'
		       ORDER BY `p`.`option_order`", 
		                $userid, 
		                $opt['template']['locale']);
	$tpl->assign_rs('useroptions', $rs);
	sql_free_result($rs);

	$rs = sql("SELECT COUNT(*) AS `anzahl`, `t`.`id`, IFNULL(`tt`.`text`, `t`.`name`) AS `cachetype`
		           FROM `caches` AS `c`
		      LEFT JOIN `cache_type` AS `t` ON `t`.`id`=`c`.`type`
		      LEFT JOIN `sys_trans` AS `st` ON `st`.`id`=`t`.`trans_id` AND `t`.`name`=`st`.`text`
		      LEFT JOIN `sys_trans_text` AS `tt` ON `st`.`id`=`tt`.`trans_id` AND `tt`.`lang`='&2'
		          WHERE `c`.`user_id`='&1'
		       GROUP BY `t`.`id`
		       ORDER BY `anzahl` DESC", 
		                $userid, 
		                $opt['template']['locale']);
	$tpl->assign_rs('userstatshidden', $rs);
	sql_free_result($rs);

	$rs = sql("SELECT COUNT(*) AS `anzahl`, `t`.`id`, IFNULL(`tt`.`text`, `t`.`name`) AS `cachetype`
		           FROM `cache_logs` AS `l`
		      LEFT JOIN `caches` AS `c` ON `l`.`cache_id`=`c`.`cache_id`
		      LEFT JOIN `cache_type` AS `t` ON `t`.`id`=`c`.`type`
		      LEFT JOIN `sys_trans` AS `st` ON `st`.`id`=`t`.`trans_id` AND `t`.`name`=`st`.`text`
		      LEFT JOIN `sys_trans_text` AS `tt` ON `st`.`id`=`tt`.`trans_id` AND `tt`.`lang`='&2'
		          WHERE `l`.`user_id`='&1' AND (`l`.`type`=1 OR `l`.`type`=7)
		       GROUP BY `t`.`id`
		       ORDER BY `anzahl` DESC", 
		                $userid, 
		                $opt['template']['locale']);
	$tpl->assign_rs('userstatsfound', $rs);
	sql_free_result($rs);

	$useropt = new useroptions($userid);
	$tpl->assign('show_statistics', ($useropt->getOptValue(USR_OPT_SHOWSTATS) == 1));

	$tpl->assign('username', $record['username']);
	$tpl->assign('userid', $userid);
	$tpl->assign('uuid', $record['uuid']);
	$tpl->assign('founds', $record['found'] <= 0 ? '0' : $record['found']);
	$tpl->assign('notfound', $record['notfound'] <= 0 ? '0' : $record['notfound']);
	$tpl->assign('note', $record['note'] <= 0 ? '0' : $record['note']);
	$tpl->assign('hidden', $record['hidden'] <= 0 ? '0' : $record['hidden']);
	$tpl->assign('recommended', sql_value("SELECT COUNT(*) FROM `cache_rating` WHERE `user_id`='&1'", 0, $userid));
	$tpl->assign('maxrecommended', floor($record['found'] * $opt['logic']['rating']['percentageOfFounds'] / 100));

	$tpl->assign('showcountry', (strlen(trim($record['country'])) > 0));
	$tpl->assign('country', $record['country']);
	$tpl->assign('registered', $record['date_created']);
	
	/* set last_login to one of 5 categories
	 *   1 = this month or last month
	 *   2 = between one and 6 months
	 *   3 = between 6 and 12 months
	 *   4 = more than 12 months
	 *   5 = unknown, we need this, because we dont
	 *       know the last_login of all accounts.
	 *       Can be removed after one year.
	 *   6 = user account is not active
	 */
	if ($record['password'] == null || $record['email'] == null || $record['is_active_flag'] != 1)
		$tpl->assign('lastlogin', 6);
	else if ($record['last_login'] == null)
		$tpl->assign('lastlogin', 5);
	else
	{
		$record['last_login'] = strtotime($record['last_login']);
		$record['last_login'] = mktime(date('G', $record['last_login']), date('i', $record['last_login']), date('s', $record['last_login']), 
																	 date('n', $record['last_login']), date(1, $record['last_login']), date('Y', $record['last_login']));
		if ($record['last_login'] >= mktime(0, 0, 0, date("m")-1, 1, date("Y")))
			$tpl->assign('lastlogin', 1);
		else if ($record['last_login'] >= mktime(0, 0, 0, date("m")-6, 1, date("Y")))
			$tpl->assign('lastlogin', 2);
		else if ($record['last_login'] >= mktime(0, 0, 0, date("m")-12, 1, date("Y")))
			$tpl->assign('lastlogin', 3);
		else
			$tpl->assign('lastlogin', 4);
	}

	$tpl->assign('pmr', $record['pmr_flag']);

	$tpl->display();
?>