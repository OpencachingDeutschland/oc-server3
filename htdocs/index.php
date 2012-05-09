<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$sUserCountry = $login->getUserCountry();
	
	$tpl->name = 'start';
	$tpl->menuitem = MNU_START;

	$tpl->caching = true;
	$tpl->cache_lifetime = 300;
	$tpl->cache_id = $sUserCountry;

	if (!$tpl->is_cached())
	{
		// news entries
		$tpl->assign('news_onstart', $opt['news']['onstart'] );

		if ($opt['news']['include'] == '')
		{
			$news = array();
			$rs = sql_slave('SELECT `news`.`date_created` `date`, `news`.`content` `content`, `news_topics`.`name` `topic` FROM `news` INNER JOIN `news_topics` ON (`news`.`topic` = `news_topics`.`id`) WHERE `news`.`display`=1 ORDER BY `news`.`date_created` DESC LIMIT 0, 6');
			$tpl->assign_rs('news', $rs);
			sql_free_result($rs);
			
			$tpl->assign('extern_news', false);
		}
		else
		{
			$url = $opt['news']['include'];
			$url = str_replace('{style}', $opt['template']['style'], $url);
			$newscontent = read_file($url, $opt['news']['maxsize']);

			$tpl->assign('news', $newscontent);
			$tpl->assign('extern_news', true);
		}
/*
		// forum entries
		if (file_exists($opt['rootpath'] . 'cache2/phpbb.inc.php'))
			require_once($opt['rootpath'] . 'cache2/phpbb.inc.php');
		else
*/
			$phpbb_topics = array();
		$tpl->assign('phpbb_topics', $phpbb_topics);
		$tpl->assign('phpbb_enabled', ($opt['cron']['phpbbtopics']['url'] != ''));
		$tpl->assign('phpbb_name', $opt['cron']['phpbbtopics']['name']);
		$tpl->assign('phpbb_link', $opt['cron']['phpbbtopics']['link']);

		// current cache and log-counters
		$tpl->assign('count_hiddens', sql_value_slave('SELECT COUNT(*) AS `hiddens` FROM `caches` WHERE `status`=1', 0));
		$tpl->assign('count_founds', sql_value_slave('SELECT COUNT(*) AS `founds` FROM `cache_logs` WHERE `type`=1', 0));
		$tpl->assign('count_users', sql_value_slave('SELECT COUNT(*) AS `users` FROM (SELECT DISTINCT `user_id` FROM `cache_logs` UNION DISTINCT SELECT DISTINCT `user_id` FROM `caches`) AS `t`', 0));

		// new events
		$events = array();
		$rs = sql_slave("SELECT `user`.`user_id` `user_id`,
														`user`.`username` `username`,
														`caches`.`cache_id` `cache_id`,
														`caches`.`name` `name`,
														`caches`.`longitude` `longitude`,
														`caches`.`latitude` `latitude`,
														`caches`.`date_created` `date_created`,
														`caches`.`country` `country`,
														`caches`.`difficulty` `difficulty`,
														`caches`.`terrain` `terrain`,
														`caches`.`date_hidden`,
														`cache_location`.`adm1`,
														`cache_location`.`adm2`,
														`cache_location`.`adm3`,
														`cache_location`.`adm4`
											 FROM `caches`
								 INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
								 INNER JOIN `user` ON `user`.`user_id`=`caches`.`user_id`
									LEFT JOIN `cache_location` ON `caches`.`cache_id`=`cache_location`.`cache_id`
											WHERE `caches`.`country`='&1' AND 
											      `caches`.`date_hidden` >= curdate() AND 
														`caches`.`type` = 6 AND 
														`cache_status`.`allow_user_view`=1
									 ORDER BY `date_hidden` ASC LIMIT 0, 10",
									          $sUserCountry);
		$tpl->assign_rs('events', $rs);
		sql_free_result($rs);

		// new caches
		$rs = sql_slave("SELECT	`user`.`user_id` `user_id`,
														`user`.`username` `username`,
														`caches`.`cache_id` `cache_id`,
														`caches`.`name` `name`,
														`caches`.`longitude` `longitude`,
														`caches`.`latitude` `latitude`,
														`caches`.`date_created` `date_created`,
														`caches`.`country` `country`,
														`caches`.`difficulty` `difficulty`,
														`caches`.`terrain` `terrain`,
														`caches`.`date_hidden`,
														`caches`.`type`,
														`cache_location`.`adm1`,
														`cache_location`.`adm2`,
														`cache_location`.`adm3`,
														`cache_location`.`adm4`
											 FROM `caches` 
								 INNER JOIN `user` ON `user`.`user_id`=`caches`.`user_id` 
									LEFT JOIN `cache_location` ON `caches`.`cache_id`=`cache_location`.`cache_id`
											WHERE `caches`.`country`='&1' AND 
											      `caches`.`type` != 6 AND 
														`caches`.`status` = 1
									 ORDER BY `caches`.`date_created` DESC LIMIT 0, 10",
									          $sUserCountry);
		$tpl->assign_rs('newcaches', $rs);
		sql_free_result($rs);

		$rs = sql_slave("SELECT COUNT(`cache_logs`.`cache_id`) AS `cRatings`, 
																	`cache_logs`.`cache_id`, 
																	MAX(`cache_logs`.`date`) AS `dLastLog`, 
																	`user`.`user_id` AS `user_id`,
																	`user`.`username` AS `username`,
																	`caches`.`cache_id` AS `cache_id`,
																	`caches`.`name` AS `name`,
																	`caches`.`longitude` AS `longitude`,
																	`caches`.`latitude` AS `latitude`,
																	`caches`.`date_created` AS `date_created`,
																	`caches`.`country` AS `country`,
																	`caches`.`difficulty` AS `difficulty`,
																	`caches`.`terrain` AS `terrain`,
																	`caches`.`date_hidden`,
																	`caches`.`type`,
																	`cache_location`.`adm1`,
																	`cache_location`.`adm2`,
																	`cache_location`.`adm3`,
																	`cache_location`.`adm4`
														 FROM `cache_logs` 
											 INNER JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND 
																										`cache_logs`.`user_id`=`cache_rating`.`user_id` 
											 INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
											 INNER JOIN `user` ON `user`.`user_id`=`caches`.`user_id`
												LEFT JOIN `cache_location` ON `caches`.`cache_id`=`cache_location`.`cache_id`
														WHERE `caches`.`country`='&1' AND 
														      `cache_logs`.`type`=1 AND 
																	`cache_logs`.`date`>DATE_SUB(NOW(), INTERVAL 30 DAY) AND 
																	`caches`.`type`!=6 AND 
																	`caches`.`status`=1
												 GROUP BY `cache_logs`.`cache_id` 
												 ORDER BY `cRatings` DESC, 
																	`dLastLog` DESC 
														LIMIT 0, 10",
									                $sUserCountry);
		$tpl->assign_rs('topratings', $rs);
		sql_free_result($rs);

		$sUserCountryName = sql_value("SELECT IFNULL(`sys_trans_text`.`text`, `countries`.`name`) 
		                                 FROM `countries` 
		                            LEFT JOIN `sys_trans` ON `countries`.`trans_id`=`sys_trans`.`id`
		                            LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&2'
		                                WHERE `countries`.`short`='&1'", '', $sUserCountry, $opt['template']['locale']);
		$tpl->assign('usercountry', $sUserCountryName);
	}

	$tpl->display();
?>
