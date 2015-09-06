<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require_once('./lib2/web.inc.php');
	require('./lib2/logic/logpics.inc.php');

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

	// locked/hidden caches are visible for the user and must be added to public stats
	$rUser['hidden'] += sql_value("SELECT COUNT(*) FROM `caches` WHERE `user_id`='&1' AND `status`=7", 0, $login->userid);
	$tpl->assign('hidden', $rUser['hidden']);

	//get last logs
	sql_enable_foundrows();
	$tpl->assign_rs('logs', sql("SELECT SQL_CALC_FOUND_ROWS `cache_logs`.`cache_id` `cacheid`, `cache_logs`.`type` `type`, `cache_logs`.`date` `date`, `caches`.`name` `name`,
	                                    `user`.`user_id` AS `userid`, `user`.`username`, `caches`.`wp_oc`, `ca`.`attrib_id` IS NOT NULL AS `oconly`,
	                                    `cache_rating`.`rating_date` IS NOT NULL AS `recommended`
	                               FROM `cache_logs`
	                         INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
	                         INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
	                          LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6
	                          LEFT JOIN `cache_rating` ON `cache_rating`.`cache_id`=`caches`.`cache_id` AND `cache_rating`.`user_id`=`cache_logs`.`user_id` AND `cache_rating`.`rating_date`=`cache_logs`.`date`
	                              WHERE `cache_logs`.`user_id`='&1'
	                           ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`date_created` DESC
														    LIMIT 10", $login->userid));
	$tpl->assign('morelogs', sql_value("SELECT FOUND_ROWS()", 0) > 10);
	sql_foundrows_done();

	//get last hidden caches
	$tpl->assign_rs('caches', sql("SELECT `caches`.`cache_id`, `caches`.`name`, `caches`.`type`,
		                                    `caches`.`date_hidden`, `caches`.`status`, `caches`.`wp_oc`,
		                                    `found`,`stat_caches`.`toprating`,
		                                    `ca`.`attrib_id` IS NOT NULL AS `oconly`,
		                                    MAX(`cache_logs`.`date`) AS `lastlog`,
		                                    (SELECT `type` FROM `cache_logs` `cl2`
																				 WHERE `cl2`.`cache_id`=`caches`.`cache_id`
																			   ORDER BY `date` DESC,`id` DESC LIMIT 1) AS `lastlog_type` 
	                                 FROM `caches`
	                            LEFT JOIN `stat_caches` ON `stat_caches`.`cache_id`=`caches`.`cache_id`
	                            LEFT JOIN `cache_logs` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
	                            LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6
	                                WHERE `caches`.`user_id`='&1'
	                                  AND `caches`.`status` != 5
	                             GROUP BY `caches`.`cache_id`
	                             ORDER BY `caches`.`date_hidden` DESC, `caches`.`date_created` DESC",
															   $login->userid));
	if ($useragent_msie && $useragent_msie_version < 9)
		$tpl->assign('dotfill','');
	else
		$tpl->assign('dotfill','...........................................................................................................');
	$tpl->add_body_load('myHomeLoad()');

	//get not published caches
	$tpl->assign_rs('notpublished', sql("SELECT `caches`.`cache_id`, `caches`.`name`, `caches`.`date_hidden`, `caches`.`date_activate`, `caches`.`status`, `caches`.`wp_oc`, `caches`.`type`
	                                       FROM `caches`
	                                      WHERE `user_id`='&1'
	                                        AND `caches`.`status` = 5
	                                   ORDER BY `date_activate` DESC, `caches`.`date_created` DESC", $login->userid));

	// get number of sent emails
	// useless information when email protocol is cleaned-up (cronjob 'purge_logs')
	// $tpl->assign('emails', sql_value("SELECT COUNT(*) FROM `email_user` WHERE `from_user_id`='&1'", 0, $login->userid));
	
	// get log pictures
	$allpics = isset($_REQUEST['allpics']) && $_REQUEST['allpics'];
	$all_pictures = get_logpics(LOGPICS_FOR_MYHOME_GALLERY);
	if ($allpics)
		set_paged_pics(LOGPICS_FOR_MYHOME_GALLERY, 0, 0, "myhome.php?allpics=1");
	else
		$tpl->assign('pictures',$all_pictures);
	$tpl->assign('allpics', $allpics ? 1 : 0);
	$tpl->assign('total_pictures', count($all_pictures));

	// display
	$tpl->display();
?>