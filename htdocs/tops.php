<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'tops';
	$tpl->menuitem = MNU_CACHES_TOPS;

	$tpl->caching = true;
	$tpl->cache_lifetime = 600;

	if (!$tpl->is_cached())
	{
		$rs = sql("SELECT `cache_location`.`adm1`, `cache_location`.`adm3`, 
		                  `rating_tops`.`rating` AS `idx`, `stat_caches`.`toprating` AS `ratings`, `stat_caches`.`found` AS `founds`, 
					      			`caches`.`name`, `caches`.`wp_oc` AS `wpoc`, `user`.`username`, `user`.`user_id` AS `userid`
		             FROM `rating_tops` 
		       INNER JOIN `caches` ON `rating_tops`.`cache_id`=`caches`.`cache_id` 
		       INNER JOIN `cache_location` ON `rating_tops`.`cache_id`=`cache_location`.`cache_id` 
		       INNER JOIN `stat_caches` ON `rating_tops`.`cache_id`=`stat_caches`.`cache_id` 
		       INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` 
		         ORDER BY `cache_location`.`adm1` ASC, 
			                `cache_location`.`adm3` ASC, 
								      `rating_tops`.`rating` DESC, 
								      `caches`.`name` ASC");
		$tpl->assign_rs('tops', $rs);
		sql_free_result($rs);
	}

	$tpl->display();
?>