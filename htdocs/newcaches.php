<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'newcaches';
	$tpl->menuitem = MNU_START_NEWCACHES;

	$startat = isset($_REQUEST['startat']) ? $_REQUEST['startat']+0 : 0;
	$perpage = 100;
	$startat -= $startat % $perpage;
	if ($startat < 0) $startat = 0;

	$tpl->caching = true;
	$tpl->cache_id = $startat;
	if ($startat > 10 * $perpage)
		$tpl->cache_lifetime = 3600;
	else
		$tpl->cache_lifetime = 300;

	if (!$tpl->is_cached())
	{
		require($opt['rootpath'] . 'lib2/logic/cacheIcon.inc.php');

		$newCaches = array();

		$rsNewCaches = sql_slave('SELECT `caches`.`cache_id` `cacheid`, `caches`.`wp_oc` `wpoc`, `user`.`user_id` `userid`, `caches`.`country` `country`, `caches`.`name` `cachename`, `user`.`username` `username`, `caches`.`date_created` `date_created`, `cache_type`.`icon_large` FROM `caches` INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` INNER JOIN `cache_type` ON `caches`.`type`=`cache_type`.`id` WHERE `caches`.`status` = 1 ORDER BY `caches`.`date_created` DESC LIMIT ' . ($startat+0) . ', ' . ($perpage+0));
		while ($rNewCache = sql_fetch_assoc($rsNewCaches))
		{
			$rNewCache['icon_large'] = getSmallCacheIcon($rNewCache['icon_large']);
			$newCaches[] = $rNewCache;
		}
		sql_free_result($rsNewCaches);
		$tpl->assign('newCaches', $newCaches);

		$count = sql_value_slave('SELECT COUNT(*) `count` FROM `caches`INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE `cache_status`.`allow_user_view`=1', 0);
		$maxstart = (ceil($count / $perpage)-1) * $perpage;

		if ($startat < 4 * $perpage)
		{
			$firstpage = 0;
			$lastpage = 8 * $perpage;
		}
		else
		{
			$firstpage = $startat - 4 * $perpage;
			$lastpage = $firstpage + 8 * $perpage;
		}
		if ($lastpage > $maxstart)
			$lastpage = $maxstart;

		$tpl->assign('firstpage', $firstpage);
		$tpl->assign('lastpage', $lastpage);
		$tpl->assign('perpage', $perpage);

		$tpl->assign('startat', $startat);
		$tpl->assign('maxstart', $maxstart);
	}

	$tpl->display();
?>