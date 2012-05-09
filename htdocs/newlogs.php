<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'newlogs';
	$tpl->menuitem = MNU_START_NEWLOGS;

	$tpl->caching = true;
	$tpl->cache_lifetime = 300;

	if (!$tpl->is_cached())
	{
		$newLogs = array();

		sql_temp_table_slave('loglist');
		sql_slave("CREATE TEMPORARY TABLE &loglist (`id` INT(11) PRIMARY KEY) SELECT `cache_logs`.`id` FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE `cache_status`.`allow_user_view`=1 ORDER BY `cache_logs`.`date_created` DESC LIMIT 200");

		if ($opt['logic']['new_logs_per_country'])
			$sqlOrderBy = '`countries`.`de` ASC, ';
		else
			$sqlOrderBy = '';
		
		$rsLogs = sql_slave("SELECT IFNULL(`sys_trans_text`.`text`, `countries`.`name`) AS `country_name`, 
		                            `cache_logs`.`id`, 
																`cache_logs`.`date_created`, 
																`caches`.`name` AS `cachename`, 
																`caches`.`wp_oc`, 
																`cache_logs`.`type`, 
																`cacheloguser`.`user_id`, 
																`cacheloguser`.`username` 
													 FROM &loglist 
										 INNER JOIN `cache_logs` ON &loglist.`id`=`cache_logs`.`id` 
										 INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` 
										 INNER JOIN `user` AS `cacheloguser` ON `cache_logs`.`user_id`=`cacheloguser`.`user_id` 
										 INNER JOIN `countries` ON `caches`.`country`=`countries`.`short` 
										  LEFT JOIN `sys_trans_text` ON `countries`.`trans_id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1'
										   ORDER BY " . $sqlOrderBy . "`cache_logs`.`date_created` DESC",
											          $opt['template']['locale']);
		while ($rLog = sql_fetch_assoc($rsLogs))
		{
			$newLogs[] = $rLog;
		}
		sql_free_result($rsLogs);

		sql_drop_temp_table_slave('loglist');

		$tpl->assign('newLogs', $newLogs);

		$tpl->assign('newLogsPerCountry', $opt['logic']['new_logs_per_country']);
	}

	$tpl->display();
?>