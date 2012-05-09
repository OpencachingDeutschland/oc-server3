<?php
/****************************************************************************
   Unicode Reminder メモ

 ****************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/labels.inc.php');
	require_once('./lib2/logic/cache.class.php');
 
	$tpl->name = 'garmin';
	$tpl->popup = 1;
	$tpl->assign('popup', true);
	$tpl->assign('garmin', true);

	// get cacheid
	$cacheid = 0;
	if (isset($_REQUEST['cacheid']))
		$cacheid = $_REQUEST['cacheid']+0;
	else if (isset($_REQUEST['uuid']))
		$cacheid = cache::cacheIdFromUUID($_REQUEST['uuid']);
	else if (isset($_REQUEST['wp']))
		$cacheid = cache::cacheIdFromWP($_REQUEST['wp']);

	// important: when the domain does not fit the api key, you must be redirected to the correct domain
	if (($opt['lib']['garmin']['domain'] != $_SERVER['HTTP_HOST']) && !isset($_REQUEST['redirect']))
	{
		$redirect = $opt['lib']['garmin']['redirect'];
		$redirect = str_replace('{cacheid}', $cacheid, $redirect);
		$tpl->redirect($redirect);
		exit;
	}

	$cache = new cache($cacheid);

	if ($cache->exist() == false)
		$tpl->error(ERROR_CACHE_NOT_EXISTS);

	if ($cache->allowView() == false)
		$tpl->error(ERROR_NO_ACCESS);

	$bCrypt = isset($_REQUEST['nocrypt']) ? ($_REQUEST['nocrypt']!=1) : true;
	$tpl->assign('crypt', $bCrypt);

	if (isset($_REQUEST['desclang']))
		$sPreferedDescLang = $_REQUEST['desclang'] . ',' . $opt['template']['locale'] . ',EN';
	else
		$sPreferedDescLang = $opt['template']['locale'] . ',EN';

	//get cache record
	$rs = sql("SELECT	`caches`.`cache_id` AS `cacheid`,
				`caches`.`user_id` AS `userid`,
				`caches`.`status` AS `status`,
				`caches`.`latitude` AS `latitude`,
				`caches`.`longitude` AS `longitude`,
				`caches`.`name` AS `name`,
				`caches`.`type` AS `type`,
				`caches`.`size` AS `size`,
				`caches`.`search_time` AS `searchtime`,
				`caches`.`way_length` AS `waylength`,
				`caches`.`country` AS `countryCode`,
				IFNULL(`ttCountry`.`text`, `countries`.`name`) AS `country`, 
				`caches`.`logpw` AS `logpw`,
				`caches`.`date_hidden` AS `datehidden`,
				`caches`.`wp_oc` AS `wpoc`,
				`caches`.`wp_gc` AS `wpgc`,
				`caches`.`wp_nc` AS `wpnc`,
				`caches`.`date_created` AS `datecreated`,
				`caches`.`difficulty` AS `difficulty`,
				`caches`.`terrain` AS `terrain`,
				`cache_desc`.`language` AS `desclanguage`,
				`cache_desc`.`short_desc` AS `shortdesc`,
				`cache_desc`.`desc` AS `desc`,
				`cache_desc`.`hint` AS `hint`,
				`cache_desc`.`desc_html` AS `deschtml`,
				IFNULL(`stat_caches`.`found`, 0) AS `found`,
				IFNULL(`stat_caches`.`notfound`, 0) AS `notfound`,
				IFNULL(`stat_caches`.`note`, 0) AS `note`,
				IFNULL(`stat_caches`.`will_attend`, 0) AS `willattend`,
				IFNULL(`stat_caches`.`watch`, 0) AS `watcher`,
				`caches`.`desc_languages` AS `desclanguages`,
				IFNULL(`stat_caches`.`ignore`, 0) AS `ignorercount`,
				IFNULL(`stat_caches`.`toprating`, 0) AS `topratings`,
				IFNULL(`cache_visits`.`count`, 0) AS `visits`,
				`user`.`username` AS `username`,
				IFNULL(`cache_location`.`code1`, '') AS `code1`,
				IFNULL(`cache_location`.`adm1`, '') AS `adm1`,
				IFNULL(`cache_location`.`adm2`, '') AS `adm2`,
				IFNULL(`cache_location`.`adm3`, '') AS `adm3`,
				IFNULL(`cache_location`.`adm4`, '') AS `adm4`
		     FROM `caches`
		    INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
		    INNER JOIN `cache_desc` ON `caches`.`cache_id`=`cache_desc`.`cache_id` AND `cache_desc`.`language`=PREFERED_LANG(`caches`.`desc_languages`, '&3')
		     LEFT JOIN `countries` ON `caches`.`country`=`countries`.`short`
		     LEFT JOIN `sys_trans` AS `tCountry` ON `countries`.`trans_id`=`tCountry`.`id` AND `countries`.`name`=`tCountry`.`text`
		     LEFT JOIN `sys_trans_text` AS `ttCountry` ON `tCountry`.`id`=`ttCountry`.`trans_id` AND `ttCountry`.`lang`='&2'
		     LEFT JOIN `cache_visits` ON `cache_visits`.`cache_id`=`caches`.`cache_id` AND `user_id_ip`='0'
		     LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id`
		     LEFT JOIN `cache_location` ON `caches`.`cache_id` = `cache_location`.`cache_id`
		    WHERE `caches`.`cache_id`='&1'", $cacheid, $opt['template']['locale'], $sPreferedDescLang, $login->userid);
	$rCache = sql_fetch_assoc($rs);
	sql_free_result($rs);
	if ($rCache === false)
		$tpl->error(ERROR_CACHE_NOT_EXISTS);

	// not published?
	if ($rCache['status'] == 5)
	{
		$tpl->caching = false;
		$login->verify();
		if ($rCache['userid'] != $login->userid)
			$tpl->error(ERROR_CACHE_NOT_PUBLISHED);
	}

	$rCache['sizeName'] = labels::getLabelValue('cache_size', $rCache['size']);
	$rCache['statusName'] = labels::getLabelValue('cache_status', $rCache['status']);
	$rCache['typeName'] = labels::getLabelValue('cache_type', $rCache['type']);

	$tpl->assign('cache', $rCache);
	$tpl->title = $rCache['name'];

	$tpl->display();
?>