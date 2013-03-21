<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'newcachesrest';
	$tpl->menuitem = MNU_START_NEWCACHESREST;

	$tpl->caching = true;
	$tpl->cache_lifetime = 3600;

	if (!$tpl->is_cached())
	{
		require($opt['rootpath'] . 'lib2/logic/cacheIcon.inc.php');

		$newCaches = array();

		sql_temp_table_slave('cachelist');
		sql_slave("CREATE TEMPORARY TABLE &cachelist (`cache_id` INT(11) PRIMARY KEY) SELECT `cache_id` FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE `cache_status`.`allow_user_view`=1 AND `country`!='DE' ORDER BY `date_created` DESC LIMIT 200");

		$rsNewCaches = sql_slave("SELECT IFNULL(`sys_trans_text`.`text`, `countries`.`name`) `country_name`, `caches`.`cache_id` `cacheid`, `caches`.`wp_oc` `wpoc`, `user`.`user_id` `userid`, `caches`.`country` `country`, `caches`.`name` `cachename`, `caches`.`type`, `caches`.`country` `country`, `user`.`username` `username`, `caches`.`date_created` `date_created`, `cache_type`.`icon_large` `icon_large`
		                            FROM `caches`
		                      INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
		                      INNER JOIN `cache_type` ON `caches`.`type`=`cache_type`.`id`
		                      INNER JOIN &cachelist ON &cachelist.`cache_id`=`caches`.`cache_id`
		                      INNER JOIN `countries` ON `countries`.`short`=`caches`.`country`
		                       LEFT JOIN `sys_trans` ON `countries`.`trans_id`=`sys_trans`.`id`
		                       LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1'
		                           WHERE `status` = 1
		                        ORDER BY `country_name`, `caches`.`date_created` DESC LIMIT 200", 
		                                 $opt['template']['locale']);
		while ($rNewCache = sql_fetch_assoc($rsNewCaches))
		{
			$rNewCache['icon_large'] = getSmallCacheIcon($rNewCache['icon_large']);
			$newCaches[] = $rNewCache;
		}
		sql_free_result($rsNewCaches);

		sql_drop_temp_table_slave('cachelist');

		$tpl->assign('newCaches', $newCaches);
	}

	$tpl->display();
?>