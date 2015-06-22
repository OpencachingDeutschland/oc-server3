<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/cache.class.php');

	$tpl->name = 'viewlogs';
	$tpl->menuitem = MNU_CACHES_VIEWLOGS;

	// 'tagloadlogs' produces a stripped-down version of the loglist for
	// log autoloading (see viewcache.php). The actual log block to be inserted
	// is tagged with <ocloadlogs>...</ocloadlogs>.
	$tagloadlogs = (@$_REQUEST['tagloadlogs'] == 1);
	$tpl->popup = $tagloadlogs;

	$login->verify();

	$cache_id = 0;
	if (isset($_REQUEST['cacheid']))
	{
		$cache_id = $_REQUEST['cacheid'];
	}
	else if (isset($_REQUEST['wp']))
	{
		$cache_id = sql_value("SELECT `cache_id` FROM `caches` WHERE `wp_oc`='&1'", "", $_REQUEST['wp']);
	}
	$start = 0;
	if (isset($_REQUEST['start']))
	{
		$start = $_REQUEST['start'];
		if (!is_numeric($start)) $start = 0;
	}
	$count = 5000;
	if (isset($_REQUEST['count']))
	{
		$count = $_REQUEST['count'];
		if (!is_numeric($count)) $count = 5000;
	}
	$admin_access = ($login->admin && ADMIN_USER) > 0;
	$deleted = @$_REQUEST['deleted'] > 0 && $admin_access;

	//$tpl->caching = true;
	//$tpl->cache_lifetime = 31*24*60*60;
	//$tpl->cache_id = $cache_id . '|' . $start . '|' . $count;

	if ($cache_id != 0)
	{ 
		//get cache record
		$rs = sql("SELECT `caches`.`cache_id`, `caches`.`wp_oc` AS `wpoc`, `caches`.`cache_id` AS `cacheid`, 
											`caches`.`user_id` AS `userid`, `caches`.`name`, 
											`caches`.`status` AS `status`,
											`caches`.`type` AS `type`,
											IFNULL(`stat_caches`.`found`, 0) AS `found`, 
											IFNULL(`stat_caches`.`notfound`, 0) AS `notfound`, 
											IFNULL(`stat_caches`.`will_attend`, 0) AS `willattend`,
											IFNULL(`stat_caches`.`note`, 0) AS `note`, 
											`cache_status`.`allow_user_view` 
							 FROM `caches` 
							 INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` 
							 LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id` 
							 WHERE `caches`.`cache_id`='&1'", $cache_id);
		$rCache = sql_fetch_array($rs);
		sql_free_result($rs);

		if ($rCache === false)
			$tpl->error(ERROR_CACHE_NOT_EXISTS);
		else
		{
			if ($rCache['allow_user_view'] != 1 && $rCache['userid'] != $login->userid && !$admin_access)
				$tpl->error(ERROR_NO_ACCESS);
		}
	}
	else
		$tpl->error(ERROR_CACHE_NOT_EXISTS);

	$rCache['adminlog'] = ($login->admin & ADMIN_USER);
	$tpl->assign('cache', $rCache);

	$tpl->assign('logs', cache::getLogsArray($cache_id, $start, $count, $deleted));
	$tpl->assign('tagloadlogs', $tagloadlogs);
	

	$tpl->display();
?>
