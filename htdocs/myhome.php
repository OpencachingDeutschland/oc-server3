<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require_once('./lib2/web.inc.php');
	$tpl->name = 'myhome';
	$tpl->menuitem = MNU_MYPROFILE_OVERVIEW;
	$login->verify();

	if ($login->userid == 0)
	{
		$tpl->redirect('login.php?target=myhome.php');
	}

	//get user record
	$rsUser = sql("SELECT IFNULL(`stat_user`.`found`, 0) AS `found`, IFNULL(`stat_user`.`hidden`, 0) AS `hidden` FROM `user` LEFT JOIN `stat_user` ON `user`.`user_id`=`stat_user`.`user_id` WHERE `user`.`user_id`='&1' LIMIT 1", $login->userid);
	$rUser = sql_fetch_array($rsUser);
	sql_free_result($rsUser);
	$tpl->assign('found', $rUser['found']);
	$tpl->assign('hidden', $rUser['hidden']);

	//get last logs
	$tpl->assign_rs('logs', sql("SELECT `cache_logs`.`cache_id` `cacheid`, `cache_logs`.`type` `type`, `cache_logs`.`date` `date`, `caches`.`name` `name`,
	                                    `user`.`user_id` AS `userid`, `user`.`username`, `caches`.`wp_oc`
	                               FROM `cache_logs`, `caches`, `user`
	                              WHERE `cache_logs`.`user_id`='&1'
	                                AND `cache_logs`.`cache_id`=`caches`.`cache_id`
	                                AND `caches`.`user_id`=`user`.`user_id`
	                           ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`date_created` DESC LIMIT 10", $login->userid));

	//get last hidden caches
	$tpl->assign_rs('caches', sql("SELECT `cache_id`, `name`, `date_hidden`, `status`, `wp_oc`
	                                 FROM `caches`
	                                WHERE `user_id`='&1'
	                                  AND `caches`.`status` != 5
	                             ORDER BY `date_hidden` DESC, `caches`.`date_created` DESC LIMIT 20", $login->userid));

	//get not published caches
	$tpl->assign_rs('notpublished', sql("SELECT `caches`.`cache_id`, `caches`.`name`, `caches`.`date_hidden`, `caches`.`date_activate`, `caches`.`status`, `caches`.`wp_oc`
	                                       FROM `caches`
	                                      WHERE `user_id`='&1'
	                                        AND `caches`.`status` = 5
	                                   ORDER BY `date_activate` DESC, `caches`.`date_created` DESC", $login->userid));

	// get number of sent emails
	$tpl->assign('emails', sql_value("SELECT COUNT(*) FROM `email_user` WHERE `from_user_id`='&1'", 0, $login->userid));

	$tpl->display();
?>