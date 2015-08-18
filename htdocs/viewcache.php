<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 * TODO: use cache() class at all
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/labels.inc.php');
	require_once('./lib2/logic/cache.class.php');
	require_once('./lib2/logic/attribute.class.php');
	require_once('./lib2/logic/cachelist.class.php');
	require_once('./lib2/logic/coordinate.class.php');
	require_once('./lib2/logic/useroptions.class.php');
	require_once('./lib2/logic/logpics.inc.php');

	$login->verify();

function getChildWaypoints($cacheid)
{
  $wphandler = new ChildWp_Handler();
  $waypoints = $wphandler->getChildWps($cacheid);
  $count = count($waypoints);

  if ($count > 0)
  {
    $formatter = new Coordinate_Formatter();

    for ($i = 0; $i < $count; $i++)
    {
      $waypoints[$i]['coordinateHtml'] = $formatter->formatHtml($waypoints[$i]['coordinate'], '<br />');
      $waypoints[$i]['description'] = trim($waypoints[$i]['description']);
    }
  }

  return $waypoints;
}

	$tpl->name = 'viewcache';
	$tpl->menuitem = MNU_CACHES_SEARCH_VIEWCACHE;
	$tpl->assign('use_tooltiplib', true);

	// get cacheid
	$cacheid = 0;
	if (isset($_REQUEST['cacheid']))
		$cacheid = $_REQUEST['cacheid']+0;
	else if (isset($_REQUEST['uuid']))
		$cacheid = cache::cacheIdFromUUID($_REQUEST['uuid']);
	else if (isset($_REQUEST['wp']))
		$cacheid = cache::cacheIdFromWP($_REQUEST['wp']);

	$cache = new cache($cacheid);

	if ($cache->exist() == false)
		$tpl->error(ERROR_CACHE_NOT_EXISTS);

	if ($cache->allowView() == false)
		$tpl->error(ERROR_NO_ACCESS);

	if (isset($_REQUEST['visitcounter']) && $_REQUEST['visitcounter'] == 1)
	{
		cache::visitCounter($login->userid, $_SERVER["REMOTE_ADDR"], $cacheid);
		exit;
	}

	$bCrypt = !isset($_REQUEST['nocrypt']) || ($_REQUEST['nocrypt'] != 1);
	$tpl->assign('crypt', $bCrypt);

	if (isset($_REQUEST['desclang']))
		$sPreferedDescLang = $_REQUEST['desclang'] . ',' . $opt['template']['locale'] . ',EN';
	else
		$sPreferedDescLang = $opt['template']['locale'] . ',EN';

	$logpics = isset($_REQUEST['logpics']) && ($_REQUEST['logpics'] == 1); 

	//$tpl->caching = true;
	//$tpl->cache_lifetime = 31*24*60*60;
	//$tpl->cache_id = $cacheid . '|' . $sPreferedDescLang;

	if ($login->userid!=0)
	{
		$tpl->assign('ignored', sql_value("SELECT 1 FROM `cache_ignore` WHERE `cache_id`='&1' AND `user_id`='&2'", 0, $cacheid, $login->userid));
		$tpl->assign('watched', sql_value("SELECT 1 FROM `cache_watches` WHERE `cache_id`='&1' AND `user_id`='&2'", 0, $cacheid, $login->userid));
	}

	//get cache record
	$rs = sql("SELECT	`caches`.`cache_id` AS `cacheid`,
				`caches`.`listing_last_modified` AS `lastmodified`,
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
				`caches`.`is_publishdate` AS `is_publishdate`,
				`caches`.`difficulty` AS `difficulty`,
				`caches`.`terrain` AS `terrain`,
				`caches`.`show_cachelists` AS `show_cachelists`,
				`cache_desc`.`language` AS `desclanguage`,
				`cache_desc`.`short_desc` AS `shortdesc`,
				`cache_desc`.`desc` AS `desc`,
				`cache_desc`.`hint` AS `hint`,
				`cache_desc`.`desc_html` AS `deschtml`,
				`cache_status`.`allow_user_log`=1 OR `caches`.`user_id`='&4' AS `log_allowed`,
				IFNULL(`stat_caches`.`found`, 0) AS `found`,
				IFNULL(`stat_caches`.`notfound`, 0) AS `notfound`,
				IFNULL(`stat_caches`.`note`, 0) AS `note`,
				IFNULL(`stat_caches`.`will_attend`, 0) AS `willattend`,
				IFNULL(`stat_caches`.`maintenance`, 0) AS `maintenance`,
				IFNULL(`stat_caches`.`watch`, 0) AS `watcher`,
				`caches`.`desc_languages` AS `desclanguages`,
				IFNULL(`stat_caches`.`ignore`, 0) AS `ignorercount`,
				IFNULL(`stat_caches`.`toprating`, 0) AS `topratings`,
				IFNULL(`cache_visits`.`count`, 0) AS `visits`,
				`user`.`username` AS `username`,
				IFNULL(`cache_location`.`code1`, '') AS `code1`,
				IFNULL(`trans2`.`text`, IFNULL(`cache_location`.`adm1`,'')) AS `adm1`,
				IFNULL(`cache_location`.`adm2`, '') AS `adm2`,
				IFNULL(`cache_location`.`adm3`, '') AS `adm3`,
				IFNULL(`cache_location`.`adm4`, '') AS `adm4`
		     FROM `caches`
		    INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
		    INNER JOIN `cache_desc` ON `caches`.`cache_id`=`cache_desc`.`cache_id` AND `cache_desc`.`language`=PREFERED_LANG(`caches`.`desc_languages`, '&3')
		    INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
		     LEFT JOIN `cache_location` ON `caches`.`cache_id` = `cache_location`.`cache_id`
		     LEFT JOIN `countries` ON `caches`.`country`=`countries`.`short`
		     LEFT JOIN `sys_trans` AS `tCountry` ON `countries`.`trans_id`=`tCountry`.`id` AND `countries`.`name`=`tCountry`.`text`
		     LEFT JOIN `sys_trans_text` AS `ttCountry` ON `tCountry`.`id`=`ttCountry`.`trans_id` AND `ttCountry`.`lang`='&2'
		     LEFT JOIN `countries` `c2` ON `c2`.`short`=`cache_location`.`code1`
		     LEFT JOIN `sys_trans_text` `trans2` ON `trans2`.`trans_id`=`c2`.`trans_id` AND `trans2`.`lang`='&2'
		     LEFT JOIN `cache_visits` ON `cache_visits`.`cache_id`=`caches`.`cache_id` AND `user_id_ip`='0'
		     LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id`
		    WHERE `caches`.`cache_id`='&1'", $cacheid, $opt['template']['locale'], $sPreferedDescLang, $login->userid);
	$rCache = sql_fetch_assoc($rs);
	sql_free_result($rs);
	if ($rCache === false)
		$tpl->error(ERROR_CACHE_NOT_EXISTS);

	// format waylength
	if ($rCache['waylength'] < 5)
		if (round($rCache['waylength'],2) != round($rCache['waylength'],1))
			$digits = 2;
		else
			$digits = 1;
	else if ($rCache['waylength'] < 50)
		if (round($rCache['waylength'],1) != round($rCache['waylength'],0))
			$digits = 1;
		else
			$digits = 0;
	else
		$digits = 0;
	$rCache['waylength'] = sprintf('%.'.$digits.'f', $rCache['waylength']);

	// not published?
	if ($rCache['status'] == 5)
	{
		$tpl->caching = false;
		$login->verify();
		if ($rCache['userid'] != $login->userid)
			$tpl->error(ERROR_CACHE_NOT_PUBLISHED);
	}

	$rCache['adminlog'] = !$rCache['log_allowed'] && ($login->admin & ADMIN_USER);

	$rs = sql("
		SELECT `short` `code`, `native_name`, `stt`.`text` AS `name`
		FROM `languages`
		JOIN `cache_desc` ON `cache_desc`.`language`=`languages`.`short`
		LEFT JOIN `sys_trans_text` `stt` ON `stt`.`trans_id`=`languages`.`trans_id` AND `stt`.`lang`='&2'
		WHERE `cache_desc`.`cache_id`='&1'",
		$cacheid, $opt['template']['locale']);
	$desclanguages = sql_fetch_assoc_table($rs);
	if (count($desclanguages) == 1 && $desclanguages[0]['code'] == $opt['template']['locale'])
		$rCache['desclanguages'] = array();
	else
		$rCache['desclanguages'] = $desclanguages;

	$rCache['sizeName'] = labels::getLabelValue('cache_size', $rCache['size']);
	$rCache['statusName'] = labels::getLabelValue('cache_status', $rCache['status']);
	$rCache['typeName'] = labels::getLabelValue('cache_type', $rCache['type']);

	$rCache['userhasfound'] = false;
	if ($login->userid != 0)
		$rCache['userhasfound'] = (sql_value("SELECT COUNT(*) FROM `cache_logs` WHERE `cache_id`='&1' AND `user_id`='&2' AND `type` IN (1,7)", 0, $cacheid, $login->userid) > 0);

	$tpl->assign('cache', $rCache);
	$tpl->title = $rCache['wpoc'] . ' ' . $rCache['name'];

	$coord = new coordinate($rCache['latitude'], $rCache['longitude']);
	$tpl->assign('coordinates', $coord->getDecimalMinutes());

	// pictures
	$rs = sql("SELECT `id`, `uuid`, `url`, `title`, `thumb_url`, `spoiler`, `display` FROM `pictures` WHERE `object_type`=2 AND `object_id`='&1' AND `display`!=0 ORDER BY `date_created` ASC", $cacheid);
	$tpl->assign_rs('pictures', $rs);
	sql_free_result($rs);

	$tpl->assign('childWaypoints', getChildWaypoints($cacheid));

	if ($login->userid != 0)
	{
		$cacheNotePresenter = new CacheNote_Presenter(new Http_Request(), new Language_Translator());
		$cacheNotePresenter->init(new CacheNote_Handler(), $login->userid, $cacheid);

		if (isset($_POST['submit_cache_note']) && $cacheNotePresenter->validate())
		{
			$cacheNotePresenter->doSubmit();
		}

		$cacheNotePresenter->prepare($tpl);
	}

	$tpl->assign('enableCacheNote', $login->userid != 0);

	/* Logentries */

	/* begin insertion/change Uwe 20091215 for printing purposes
	   reworked on 20100106 for better performance after Olivers intervention
	   rewritten 2012-07-22 following for bugfix, first log was lost in print
	 */

	$rscount = MAX_LOGENTRIES_ON_CACHEPAGE;

	if (isset($_REQUEST['log']))
	  switch ($_REQUEST['log'])
		{
			case 'N': $rscount = 0; 
								break;
								
			case 'A': $rscount = current(cache::getLogsCount($cacheid));
			          // This option is also used when referencing logs, e.g. from log lists
			          // and picture galleries.
								break;
 								
			default:  if ($_REQUEST['log'] > 0)
									$rscount = $_REQUEST['log'] + 0;
		}	

	$logs = cache::getLogsArray($cacheid, 0, $rscount+1);
	
	if (isset($logs[$rscount])) 
	{
		unset($logs[$rscount]);
		$tpl->assign('morelogs', true);
	}
	$loganz = sizeof($logs);
	$tpl->assign('logs', $logs);
	$tpl->assign('loganz', $loganz);

	/*end insertion Uwe 20091215*/

	/* nature protection areas
	 */
	$rs = sql("SELECT `npa_areas`.`id` AS `npaId`, `npa_areas`.`type_id` AS `npaType`, `npa_areas`.`name` AS `npaName`, `npa_types`.`name` AS `npaTypeName` 
	             FROM `cache_npa_areas` 
	       INNER JOIN `npa_areas` ON `cache_npa_areas`.`npa_id`=`npa_areas`.`id` 
	       INNER JOIN `npa_types` ON `npa_areas`.`type_id`=`npa_types`.`id` 
	            WHERE `cache_npa_areas`.`cache_id`='&1' AND `npa_types`.`no_warning`=0
	         GROUP BY `npa_areas`.`type_id`, `npa_areas`.`name`
	         ORDER BY `npa_types`.`ordinal` ASC", 
	                  $cacheid);
	$tpl->assign_rs('npaareasWarning', $rs);
	sql_free_result($rs);

	$rs = sql("SELECT `npa_areas`.`id` AS `npaId`, `npa_areas`.`type_id` AS `npaType`, `npa_areas`.`name` AS `npaName`, `npa_types`.`name` AS `npaTypeName` 
	             FROM `cache_npa_areas` 
	       INNER JOIN `npa_areas` ON `cache_npa_areas`.`npa_id`=`npa_areas`.`id` 
	       INNER JOIN `npa_types` ON `npa_areas`.`type_id`=`npa_types`.`id` 
	            WHERE `cache_npa_areas`.`cache_id`='&1' AND `npa_types`.`no_warning`=1
	         GROUP BY `npa_areas`.`type_id`, `npa_areas`.`name`
	         ORDER BY `npa_types`.`ordinal` ASC", 
	                  $cacheid);
	$tpl->assign_rs('npaareasNoWarning', $rs);
	sql_free_result($rs);

	/* attributes and cache lists
	 */
	$tpl->assign('attributes', attribute::getAttrbutesListArrayByCacheId($cacheid));
	$tpl->assign('cachelists', cachelist::getListsByCacheId($cacheid, $rCache['show_cachelists']));
	$tpl->assign('watchclinfo', isset($_REQUEST['watchinfo']) && $_REQUEST['watchinfo'] == 1 && 
	                            cachelist::watchingCacheByListsCount($login->userid, $cacheid) > 0);

	/* geokrets
	 */
	$rsGeoKret = sql("SELECT `gk_item`.`id`, `gk_item`.`name` AS `itemname`, `gk_user`.`name` AS `username` FROM `gk_item` INNER JOIN `gk_item_waypoint` ON `gk_item`.`id`=`gk_item_waypoint`.`id` INNER JOIN `gk_user` ON `gk_item`.`userid`=`gk_user`.`id` INNER JOIN `caches` ON `gk_item_waypoint`.`wp`=`caches`.`wp_oc` WHERE `caches`.`cache_id`='&1' AND `gk_item`.`typeid`!=2 AND `gk_item`.`stateid` IN (0, 3) AND `gk_item_waypoint`.`wp`!='' UNION 
	             SELECT `gk_item`.`id`, `gk_item`.`name` AS `itemname`, `gk_user`.`name` AS `username` FROM `gk_item` INNER JOIN `gk_item_waypoint` ON `gk_item`.`id`=`gk_item_waypoint`.`id` INNER JOIN `gk_user` ON `gk_item`.`userid`=`gk_user`.`id` INNER JOIN `caches` ON `gk_item_waypoint`.`wp`=`caches`.`wp_gc` WHERE `caches`.`cache_id`='&1' AND `gk_item`.`typeid`!=2 AND `gk_item`.`stateid` IN (0, 3) AND `gk_item_waypoint`.`wp`!='' UNION 
	             SELECT `gk_item`.`id`, `gk_item`.`name` AS `itemname`, `gk_user`.`name` AS `username` FROM `gk_item` INNER JOIN `gk_item_waypoint` ON `gk_item`.`id`=`gk_item_waypoint`.`id` INNER JOIN `gk_user` ON `gk_item`.`userid`=`gk_user`.`id` INNER JOIN `caches` ON `gk_item_waypoint`.`wp`=`caches`.`wp_nc` WHERE `caches`.`cache_id`='&1' AND `gk_item`.`typeid`!=2 AND `gk_item`.`stateid` IN (0, 3) AND `gk_item_waypoint`.`wp`!='' ORDER BY `itemname`", $cacheid);
	$tpl->assign_rs('geokret', $rsGeoKret);
	$tpl->assign('geokret_count', sql_num_rows($rsGeoKret));
	sql_free_result($rsGeoKret);

	if (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y')
	{
		$tpl->popup = 1;
		$tpl->assign('print', true);
		$tpl->name = 'viewcache_print';
		$tpl->assign('log', $_REQUEST['log']);
	}
	else
	{
		$tpl->assign('print', false);
	}

	/* logpics
	 */
	$tpl->assign('show_logpics', $logpics ? 1 : 0);
	if ($logpics)
	{
		set_paged_pics(LOGPICS_FOR_CACHE_GALLERY, 0, $cacheid, "viewcache.php?cacheid=" . $cacheid . "&logpics=1");
		$tpl->assign('subtitle',"&lt;&lt; <a href='viewcache.php?cacheid=" . $cacheid . "'>" .
		             $translate->t("Back to the cache description", "", "", 0) . "</a>");

	}
	else
		$tpl->assign('logpics', get_logpics(LOGPICS_FOR_CACHE_STAT, 0, $cacheid));

	/* process profile settings
	 */
	$userzoom = 11;
	if ($login->userid > 0)
	{
		$useropt = new useroptions($login->userid);
		$userzoom = $useropt->getOptValue(USR_OPT_GMZOOM);
		$autoload_logs = $useropt->getOptValue(USR_OPT_LOG_AUTOLOAD);
	}
	else
	{
		$autoload_logs = true;
	}
	$tpl->assign('userzoom', $userzoom);
	$tpl->assign('autoload_logs', $autoload_logs);

	// get the correct mapkey
	$sHost = strtolower($_SERVER['HTTP_HOST']);

  $sGMKey = '';
	if (isset($opt['lib']['google']['mapkey'][$sHost]))
		$sGMKey = $opt['lib']['google']['mapkey'][$sHost];

  $cachemap['iframe'] = $opt['logic']['cachemaps']['iframe'];
	$url = $opt['logic']['cachemaps']['url'];
	$url = str_replace('{userzoom}', $userzoom, $url);
	$url = str_replace('{latitude}', $rCache['latitude'], $url);
	$url = str_replace('{longitude}', $rCache['longitude'], $url);
	$url = str_replace('{gmkey}', $sGMKey, $url);
	$cachemap['url'] = $url;
	$tpl->assign('cachemap', $cachemap);

	$tpl->assign('shortlink_domain', $opt['logic']['shortlink_domain']);
	$tpl->assign('listing_admin', $login->listingAdmin());
	$tpl->assign('npahelplink', helppagelink('npa'));

	// display the page
	$tpl->display();
?>