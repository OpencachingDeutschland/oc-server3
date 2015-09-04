<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require_once('./lib2/web.inc.php');

	$add_where = '';
	$newLogsPerCountry = $opt['logic']['new_logs_per_country'];
	$startat = isset($_GET['startat']) ? $_GET['startat']+0 : 0;
	$urlparams = '';

	if (isset($ownerid))
	{
		// all logs for caches of one owner
		$country = false;
		$exclude_country = '*';
		$include_country = '%';
		$add_where = "AND `caches`.`user_id`='" . sql_escape($ownerid) . "' ";
		if (!$show_own_logs)
			$add_where .= "AND `cache_logs`.`user_id`<>'" . sql_escape($login->userid) . "' ";
		$tpl->caching = false;
		$logcount = 100;
		$paging = true;
		$newLogsPerCountry = false;
		$caches_logged = array();
		$orderByDate = '`cache_logs`.`date` DESC, ';
		if ($show_own_logs) $urlparams = '&ownlogs=1';
	}
	elseif (isset($userid))
	{
		// all logs by one user
		$country = false;
		$exclude_country = '*';
		$include_country = '%';
		$add_where = "AND `cache_logs`.`user_id`='" . sql_escape($userid) . "' ";
		$tpl->caching = false;
		$logcount = 100;
		$paging = true;
		$newLogsPerCountry = false;
		$orderByDate = '`cache_logs`.`date` DESC, ';
	}
	elseif (@$newlogs_rest)
	{
		// latest logs for all countries but Germany
		$tpl->name = 'newlogsrest';
		$tpl->menuitem = MNU_START_NEWLOGS;
		$country = $login->getUserCountry();
		$exclude_country = $opt['page']['main_country'];
		$include_country = '%';
		// As nearly all logs are from Germany, retrieving non-German logs is
		// expensive -> longer cache lifetime.
		$tpl->caching = true;
		$tpl->cache_lifetime = 900;
		$logcount = 250;
		$paging = false;  // paging would have poor performance for all logs
		$orderByDate = '';
	}
	else
	{
		// latest logs for all countries or for one country
		$tpl->name = 'newlogs';
		$tpl->menuitem = MNU_START_NEWLOGS;
		$exclude_country = '*';
		if (isset($_REQUEST['usercountry']))
			$country = $include_country = $_REQUEST['usercountry'];
		else if (isset($_REQUEST['country']))
			$country = $include_country = $_REQUEST['country'];
		else
		{
			$country = '';
			$include_country = '%';
		}
		$tpl->caching = true;
		$tpl->cache_lifetime = 300;
		$logcount = 250;
		$paging = false;  // paging would have poor performance for all logs
		$orderByDate = '';
	}

	$tpl->strip_country_from_baseadr = true;
	$tpl->assign('creation_date', $orderByDate == '');

	if (!$tpl->is_cached())
	{
		if ($paging) sql_enable_foundrows();
		sql_temp_table_slave('loglist');
		sql_slave("CREATE TEMPORARY TABLE &loglist (`id` INT(11) PRIMARY KEY)
			         SELECT " . ($paging ? "SQL_CALC_FOUND_ROWS" : "") . " `cache_logs`.`id`
			           FROM `cache_logs`
			     INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
			     INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
			     INNER JOIN `user` ON `cache_logs`.`user_id`=`user`.`user_id`
			          WHERE `cache_status`.`allow_user_view`=1
			                AND `caches`.`country` LIKE '&1'
			                AND `caches`.`country`<>'&2'
			                AND `username`<>'&3'".
			                $add_where."
			       ORDER BY " . $orderByDate . "`cache_logs`.`date_created` DESC
			          LIMIT &4, &5",
			                $include_country,
			                $exclude_country,
			                isset($_GET['showsyslogs']) ? '' : $opt['logic']['systemuser']['user'],
			                $startat,
			                $logcount);
		if ($paging)
		{
			$total_logs = sql_value("SELECT FOUND_ROWS()", 0);
			sql_foundrows_done();
			$paging = ($total_logs > $logcount);
		}

		if ($newLogsPerCountry)
			$orderByCountry = '`country_name` ASC, ';
		else
			$orderByCountry = '';
		
		$rsLogs = sql_slave("SELECT IFNULL(`sys_trans_text`.`text`, `countries`.`name`) AS `country_name`, 
		                            `cache_logs`.`id`, 
																`cache_logs`.`date_created`,
																`cache_logs`.`date`, 
																`caches`.`name` AS `cachename`, 
																`caches`.`wp_oc`, 
																`caches`.`country` AS `country`,
																`cache_logs`.`type`,
																`cache_logs`.`oc_team_comment`,
																`cacheloguser`.`user_id`, 
																`cacheloguser`.`username`,
																`cache_logs`.`cache_id`,
																`cache_rating`.`rating_date` IS NOT NULL AS `recommended`,
																'' AS `pic_uuid`,
																0 AS `picshown`,
																(SELECT COUNT(*) FROM `pictures` WHERE `object_type`=1 AND `object_id`=`cache_logs`.`id`) AS `pics`
													 FROM &loglist 
										 INNER JOIN `cache_logs` ON &loglist.`id`=`cache_logs`.`id` 
										 INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` 
										 INNER JOIN `user` AS `cacheloguser` ON `cache_logs`.`user_id`=`cacheloguser`.`user_id` 
										 INNER JOIN `countries` ON `caches`.`country`=`countries`.`short` 
										  LEFT JOIN `sys_trans_text` ON `countries`.`trans_id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1'
										  LEFT JOIN `cache_logs_restored` ON `cache_logs_restored`.`id`=`cache_logs`.`id`
										  LEFT JOIN `cache_rating` ON `cache_rating`.`cache_id`=`caches`.`cache_id` AND `cache_rating`.`user_id`=`cache_logs`.`user_id` AND `cache_rating`.`rating_date`=`cache_logs`.`date`
										      WHERE IFNULL(`cache_logs_restored`.`restored_by`,0)=0
										   ORDER BY " . $orderByCountry . $orderByDate . "`cache_logs`.`date_created` DESC",
											          $opt['template']['locale']);

		$newLogs = array();

		$lines_per_pic = 5;
		$tpl->assign('lines_per_pic',$lines_per_pic);
		$pics = 0;

		while ($rLog = sql_fetch_assoc($rsLogs))
		{
			if ($pics <= 0 ||
			    ($pics == $lines_per_pic && count($newLogs)==1 && !$newLogs[0]['picshow']))
			{
				$rsPic = sql_slave("SELECT `uuid`,`url`,`title` FROM `pictures`
                             WHERE `object_type`=1 AND `object_id`='&1'
														   AND `local`=1 AND `display`=1 AND `spoiler`=0 AND `unknown_format`=0
				                     LIMIT 1", $rLog['id']);
				if ($rPic = sql_fetch_assoc($rsPic))
				{
					if (count($newLogs) >= 2)
					{
						$newLogs[count($newLogs)-2]['pic_uuid'] = $rPic['uuid'];
						$newLogs[count($newLogs)-2]['pic_url'] = $rPic['url'];
						$newLogs[count($newLogs)-2]['title'] = $rPic['title'];
						$pics = $lines_per_pic;
					}
					else if (count($newLogs) == 1)
					{
						$newLogs[0]['pic_uuid'] = $rPic['uuid'];
						$newLogs[0]['pic_url'] = $rPic['url'];
						$newLogs[0]['title'] = $rPic['title'];
						$pics = $lines_per_pic+1;
					}
					else
					{
						$rLog['pic_uuid'] = $rPic['uuid'];
						$rLog['pic_url'] = $rPic['url'];
						$rLog['title'] = $rPic['title'];
						$pics = $lines_per_pic+2;
					}
					$rLog['picshown'] = true;
				}
				sql_free_result($rsPic);
			}
			$pics--;

			$rLog['first'] = false;
			if (isset($caches_logged))
			{
				if (!isset($caches_logged[$rLog['cache_id']]))
				{
					$caches_logged[$rLog['cache_id']] = true;
					$rLog['first'] = true;
				}
			}

			$newLogs[] = $rLog;
		}
		sql_free_result($rsLogs);

		sql_drop_temp_table_slave('loglist');

		$tpl->assign('newLogs', $newLogs);
		$tpl->assign('addpiclines', max($pics-1,0));
		$tpl->assign('newLogsPerCountry', $newLogsPerCountry);

		$tpl->assign('countryCode', $country);
		$tpl->assign(
			'countryName',
			sql_value("SELECT IFNULL(`sys_trans_text`.`text`, `countries`.`name`) 
	                FROM `countries`
	           LEFT JOIN `sys_trans` ON `countries`.`trans_id`=`sys_trans`.`id`
	           LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&2'
	               WHERE `countries`.`short`='&1'",
	              '',
	              $country ? $country : $login->getUserCountry(),
	              $opt['template']['locale'])
			);
		$tpl->assign(
			'mainCountryName',
			sql_value("SELECT IFNULL(`sys_trans_text`.`text`, `countries`.`name`) 
	                FROM `countries`
	           LEFT JOIN `sys_trans` ON `countries`.`trans_id`=`sys_trans`.`id`
	           LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&2'
	               WHERE `countries`.`short`='&1'",
	              '',
	              $opt['page']['main_country'],
	              $opt['template']['locale'])
			);

		$tpl->assign('paging', $paging);
		if ($paging)
		{
			$pager = new pager($_SERVER["SCRIPT_NAME"] . '?startat={offset}'. $urlparams);
			$pager->make_from_offset($startat, $total_logs, $logcount);
		}
	}

	$tpl->display();
?>