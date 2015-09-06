<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'tops';
	$tpl->menuitem = MNU_CACHES_TOPS;

	$tpl->caching = true;
	$tpl->cache_lifetime = 600;

	if (!$tpl->is_cached())
	{
		$rs = sql("SELECT IFNULL(`sys_trans_text`.`text`,`countries`.`en`) AS `adm1`,
		                  IF(`cache_location`.`code1`=`caches`.`country`,`cache_location`.`adm3`,NULL) AS `adm3`,
		                  `caches`.`country` AS `code1`,
		                  `rating_tops`.`rating` AS `idx`, `stat_caches`.`toprating` AS `ratings`, `stat_caches`.`found` AS `founds`, 
		                  `caches`.`name`, `caches`.`wp_oc` AS `wpoc`, `user`.`username`, `user`.`user_id` AS `userid`,
		                  `ca`.`attrib_id` IS NOT NULL AS `oconly`
		             FROM `rating_tops` 
		       INNER JOIN `caches` ON `rating_tops`.`cache_id`=`caches`.`cache_id` 
		       INNER JOIN `cache_location` ON `rating_tops`.`cache_id`=`cache_location`.`cache_id` 
		        LEFT JOIN `countries` ON `countries`.`short`=`caches`.`country`
		        LEFT JOIN `sys_trans_text` ON `sys_trans_text`.`trans_id`=`countries`.`trans_id` AND `sys_trans_text`.`lang`='&1'
		       INNER JOIN `stat_caches` ON `rating_tops`.`cache_id`=`stat_caches`.`cache_id` 
		       INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` 
		        LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6
		         ORDER BY `adm1` ASC,
		                  `adm3` ASC,
		                  `rating_tops`.`rating` DESC,
		                  `caches`.`name` ASC", $opt['template']['locale']);
		$tpl->assign_rs('tops', $rs);
		sql_free_result($rs);
	}

	$tpl->assign('helppagelink', helppagelink('tops'));
	$tpl->display();
?>