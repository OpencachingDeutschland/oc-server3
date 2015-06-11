<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'newcaches';
	$tpl->menuitem = MNU_START_NEWCACHES;

	$startat = isset($_REQUEST['startat']) ? $_REQUEST['startat']+0 : 0;
	$country = isset($_REQUEST['country']) ? $_REQUEST['country'] : '';
	$cachetype = isset($_REQUEST['cachetype']) ? $_REQUEST['cachetype']+0 : 0;
	$bEvents = ($cachetype == 6);

	$perpage = 100;
	$startat -= $startat % $perpage;
	if ($startat < 0) $startat = 0;

	$tpl->caching = true;
	$tpl->cache_id = $startat . "-" . $country . "-" . $cachetype;
	if ($startat > 10 * $perpage)
		$tpl->cache_lifetime = 3600;
	else
		$tpl->cache_lifetime = 300;

	if (!$tpl->is_cached())
	{
		require($opt['rootpath'] . 'lib2/logic/cacheIcon.inc.php');

		$cachetype_condition = ($cachetype ? " AND `caches`.`type` = " . sql_escape($cachetype) : "");
		if ($bEvents)
			$cachetype_condition .= " AND `date_hidden` >= curdate()";
		$date_field = ($bEvents ? 'date_hidden' : 'date_created');
		$sort_order = ($bEvents ? 'ASC' : 'DESC');
		$newCaches = array();

		$rsNewCaches = sql_slave(
					"SELECT `caches`.`cache_id` `cacheid`, `caches`.`wp_oc` `wpoc`,
					        `caches`.`name` `cachename`, `caches`.`type`, `caches`.`country` `country`,
					        `caches`.`$date_field` `date_created`,
					        IFNULL(`sys_trans_text`.`text`,`countries`.`en`) AS `country_name`,
					        `user`.`user_id` `userid`, `user`.`username` `username`,
					        `ca`.`attrib_id` IS NOT NULL AS `oconly`
					  FROM `caches`
			INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
			 LEFT JOIN `countries` ON `countries`.`short` = `caches`.`country`
			 LEFT JOIN `sys_trans_text` ON `sys_trans_text`.`trans_id` = `countries`.`trans_id`
			           AND `sys_trans_text`.`lang` = '" . sql_escape($opt['template']['locale']) . "'
			 LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6
			     WHERE `caches`.`status` = 1" . ($country ? " AND `caches`.`country`='" . sql_escape($country) . "'" : "") .
			           $cachetype_condition . "
			  ORDER BY `caches`.`$date_field` $sort_order
				   LIMIT " . ($startat+0) . ', ' . ($perpage+0));
			// see also write_newcaches_urls() in sitemap.class.php
		while ($rNewCache = sql_fetch_assoc($rsNewCaches))
			$newCaches[] = $rNewCache;
		sql_free_result($rsNewCaches);
		$tpl->assign('newCaches', $newCaches);

		$startat = isset($_REQUEST['startat']) ? $_REQUEST['startat']+0 : 0;
		$cacheype_par = ($cachetype ? "&cachetype=$cachetype" : "");
		if ($country == '')
		{
			$count = sql_value_slave("SELECT COUNT(*) FROM `caches` WHERE `caches`.`status`=1" . $cachetype_condition, 0);
			$pager = new pager("newcaches.php?startat={offset}" . $cacheype_par);
		}
		else
		{
			$count = sql_value_slave("SELECT COUNT(*) FROM `caches` WHERE `caches`.`status`=1 AND `caches`.`country`='&1'" . $cachetype_condition, 0, $country);
			$pager = new pager("newcaches.php?country=".$country."&startat={offset}" . $cacheype_par);
		}
		$pager->make_from_offset($startat, $count, 100);

		$tpl->assign('defaultcountry', $opt['template']['default']['country']);
		$tpl->assign('countryCode', $country);
		if ($country != '')
		{
			$tpl->assign(
				'countryName',
				sql_value("SELECT IFNULL(`sys_trans_text`.`text`, `countries`.`name`) 
		                FROM `countries`
		           LEFT JOIN `sys_trans` ON `countries`.`trans_id`=`sys_trans`.`id`
		           LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&2'
		               WHERE `countries`.`short`='&1'", '', $country, $opt['template']['locale'])
			);
   	}

		$tpl->assign('events', $bEvents);
	}

	$tpl->display();
?>