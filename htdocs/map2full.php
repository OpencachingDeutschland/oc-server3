<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/cache.class.php');
	require_once('./lib2/logic/user.class.php');
	require_once('./lib2/logic/attribute.class.php');

	/* because the map does access some private info like
	 * ignored caches, we need to verify the login data
	 */
	$login->verify();

	$sMode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
	if ($sMode == 'locate')
	{
		$nLat = isset($_REQUEST['lat']) ? $_REQUEST['lat']+0 : 0;
		$nLon = isset($_REQUEST['lon']) ? $_REQUEST['lon']+0 : 0;
		$nDistance = isset($_REQUEST['distance']) ? $_REQUEST['distance']+0 : 5;
		if ($nDistance > 150) $nDistance = 150;

		cache_locate($nLat, $nLon, $nDistance);
	}
	else if ($sMode == 'wpsearch')
	{
		$sWaypoint = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';
		output_cachexml($sWaypoint);
	}
	else if ($sMode == 'namesearch')
	{
	  $sName = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
		$nLat = isset($_REQUEST['lat']) ? $_REQUEST['lat']+0 : 0;
		$nLon = isset($_REQUEST['lon']) ? $_REQUEST['lon']+0 : 0;
		$nResultId = isset($_REQUEST['resultid']) ? $_REQUEST['resultid']+0 : 0;
    output_namesearch($sName, $nLat, $nLon, $nResultId);
	}
	else if ($sMode == 'searchresult')
	{
		$nResultId = isset($_REQUEST['resultid']) ? $_REQUEST['resultid']+0 : 0;
		$nLon1 = isset($_REQUEST['lon1']) ? $_REQUEST['lon1']+0 : 0;
		$nLon2 = isset($_REQUEST['lon2']) ? $_REQUEST['lon2']+0 : 0;
		$nLat1 = isset($_REQUEST['lat1']) ? $_REQUEST['lat1']+0 : 0;
		$nLat2 = isset($_REQUEST['lat2']) ? $_REQUEST['lat2']+0 : 0;

		output_searchresult($nResultId, $nLon1, $nLat1, $nLon2, $nLat2);
	}
	else if ($sMode == 'fullscreen')
	{
		$tpl->popup = true;
		$tpl->popupmargin = false;
	}

	$tpl->name = 'map2full';
	$tpl->menuitem = MNU_MAP;
	$tpl->nowpsearch = true;

	// get the correct mapkey
	$sHost = strtolower($_SERVER['HTTP_HOST']);
	if (isset($opt['lib']['google']['mapkey'][$sHost]))
		$sGMKey = $opt['lib']['google']['mapkey'][$sHost];
	else
		$tpl->error($translate->t('There is no google maps key registered for this domain.', '', '', 0));

  //$tpl->add_header_javascript('https://maps.googleapis.com/maps/api/js?key=' . urlencode($sGMKey) . '&amp;sensor=false');
  $tpl->add_header_javascript('https://maps.googleapis.com/maps/api/js?sensor=false&key=' . urlencode($sGMKey));
  $tpl->add_header_javascript('resource2/misc/map/CacheMarker.js');
  $tpl->add_body_load('mapLoad()');
  $tpl->add_body_unload('GUnload()');

	// process start params
	$bGMInitCookiePos = true;
	if (isset($_REQUEST['lat']) &&
	    isset($_REQUEST['lon']) && 
	    isset($_REQUEST['zoom']))
	{
		$nGMInitLat = $_REQUEST['lat']+0;
		$nGMInitLon = $_REQUEST['lon']+0;
		$nGMInitZoom = $_REQUEST['zoom']+0;
		$bGMInitCookiePos = false;
	}
	else
	{
		// init GM from user country selection
		$rsCoords = sql("SELECT `gmLat`, `gmLon`, `gmZoom` FROM `countries_options` WHERE `country`='&1'", $login->getUserCountry());
		$rCoord = sql_fetch_assoc($rsCoords);
		sql_free_result($rsCoords);
		
		if ($rCoord !== false)
		{
			$nGMInitLat = $rCoord['gmLat'];
			$nGMInitLon = $rCoord['gmLon'];
			$nGMInitZoom = $rCoord['gmZoom'];
		}
		else
		{
			// europe is the last fallback
			$nGMInitLat = 52.74959372674114;
			$nGMInitLon = 10.01953125;
			$nGMInitZoom = 4;
		}
	}

	$sGMInitWaypoint = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';
	if ($sGMInitWaypoint != '')
	{
		$cache = cache::fromWP($sGMInitWaypoint);
		if ($cache == null)
			$sGMInitWaypoint = '';
		else
		{
			if ($cache->allowView())
			{
				$nGMInitLon = $cache->getLongitude();
				$nGMInitLat = $cache->getLatitude();
				$nGMInitZoom = 13;
				$bGMInitCookiePos = false;
			}
			else
				$sGMInitWaypoint = '';
		}
	}
	
	$nUserLon = 0;
	$nUserLat = 0;
	if( $login->userid != 0 )
	{
		$user = new user($login->userid);
		$nUserLat = $user->getLatitude();
		$nUserLon = $user->getLongitude();
	}
	$tpl->assign('nUserLon', $nUserLon);
	$tpl->assign('nUserLat', $nUserLat);
	
	$tpl->assign('nGMInitLon', $nGMInitLon);
	$tpl->assign('nGMInitLat', $nGMInitLat);
	$tpl->assign('nGMInitZoom', $nGMInitZoom);
	$tpl->assign('bGMInitCookiePos', ($bGMInitCookiePos ? 1 : 0));
	$tpl->assign('sGMInitWaypoint', $sGMInitWaypoint);
	$tpl->assign('bFullscreen', ($sMode == 'fullscreen' ? 1 : 0));
	$tpl->assign('bDisableFullscreen', $opt['map']['disablefullscreen']);

	$rsCacheType = sql("SELECT `cache_type`.`id`, IFNULL(`sys_trans_text`.`text`, `cache_type`.`name`) AS `text` FROM `cache_type` LEFT JOIN `sys_trans` ON `cache_type`.`trans_id`=`sys_trans`.`id` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' ORDER BY `cache_type`.`ordinal` ASC", $opt['template']['locale']);
	$tpl->assign_rs('aCacheType', $rsCacheType);
	sql_free_result($rsCacheType);

	$rsCacheSize = sql("SELECT `cache_size`.`id`, IFNULL(`sys_trans_text`.`text`, `cache_size`.`name`) AS `text` FROM `cache_size` LEFT JOIN `sys_trans` ON `cache_size`.`trans_id`=`sys_trans`.`id` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' ORDER BY `cache_size`.`ordinal` ASC", $opt['template']['locale']);
	$tpl->assign_rs('aCacheSize', $rsCacheSize);
	sql_free_result($rsCacheSize);

	/* assign attributes */
	$tpl->assign('aAttributes', attribute::getAttrbutesListArray());

	$aAttributesDisabled = array();
	$rs = sql("SELECT `id` FROM `cache_attrib`");
	while ($r = sql_fetch_assoc($rs))
		$aAttributesDisabled[] = $r['id'];
	sql_free_result($rs);
	$tpl->assign('aAttributesDisabled', $aAttributesDisabled);

	$tpl->display();

function cache_locate($nLat, $nLon, $nDistance)
{
	global $login;

	$rsCache = sql_slave("SELECT " . geomath::getSqlDistanceFormula($nLon, $nLat, $nDistance) . " AS `distance`, 
	                              `caches`.`wp_oc`
	                         FROM `caches` 
	                   INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
	                        WHERE `caches`.`latitude`>'&1' AND 
      										      `caches`.`latitude`<'&2' AND 
										            `caches`.`longitude`>'&3' AND 
										            `caches`.`longitude`<'&4' AND
										            (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`='&1')
					             ORDER BY `distance` ASC LIMIT 1",
										            geomath::getMinLat($nLon, $nLat, $nDistance),
										            geomath::getMaxLat($nLon, $nLat, $nDistance),
										            geomath::getMinLon($nLon, $nLat, $nDistance),
										            geomath::getMaxLon($nLon, $nLat, $nDistance),
										            $login->userid);
	$rCache = sql_fetch_assoc($rsCache);
	sql_free_result($rsCache);

	if ($rCache === false)
	{
		echo '<caches></caches>';
		exit;
	}

	output_cachexml($rCache['wp_oc']);
}

function output_cachexml($sWaypoint)
{
	global $opt, $login;

	$rsCache = sql_slave("SELECT `caches`.`name`, `caches`.`wp_oc`, `caches`.`cache_id`, `caches`.`type`,
	                             `caches`.`longitude`, `caches`.`latitude`, 
	                             IF(`caches`.`status`=2, 1, 0) AS `tna`, 
	                             IFNULL(`trans_status_text`.`text`, `cache_status`.`name`) AS `statustext`,
	                             IFNULL(`trans_type_text`.`text`, `cache_type`.`name`) AS `type_text`, `cache_type`.`id` AS `type_id`, 
	                             IFNULL(`trans_size_text`.`text`, `cache_size`.`name`) AS `size`, 
	                             `caches`.`difficulty`, `caches`.`terrain`, 
	                             `caches`.`date_created`, 
	                             IFNULL(`stat_caches`.`toprating`, 0) AS `toprating`, 
	                             IF(`caches`.`user_id`='&1', 1, 0) AS `owner`, 
	                             `user`.`username`, `user`.`user_id`
	                        FROM `caches` 
	                  INNER JOIN `cache_type` ON `caches`.`type`=`cache_type`.`id` 
	                  INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` 
	                  INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` 
	                  INNER JOIN `cache_size` ON `caches`.`size`=`cache_size`.`id` 
	                   LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id` 
	                   LEFT JOIN `sys_trans` AS `trans_status` ON `cache_status`.`trans_id`=`trans_status`.`id` AND `cache_status`.`name`=`trans_status`.`text`
	                   LEFT JOIN `sys_trans_text` AS `trans_status_text` ON `trans_status`.`id`=`trans_status_text`.`trans_id` AND `trans_status_text`.`lang`='&2'
	                   LEFT JOIN `sys_trans` AS `trans_type` ON `cache_type`.`trans_id`=`trans_type`.`id` AND `cache_type`.`name`=`trans_type`.`text`
	                   LEFT JOIN `sys_trans_text` AS `trans_type_text` ON `trans_type`.`id`=`trans_type_text`.`trans_id` AND `trans_type_text`.`lang`='&2'
	                   LEFT JOIN `sys_trans` AS `trans_size` ON `cache_size`.`trans_id`=`trans_size`.`id` AND `cache_size`.`name`=`trans_size`.`text`
	                   LEFT JOIN `sys_trans_text` AS `trans_size_text` ON `trans_size`.`id`=`trans_size_text`.`trans_id` AND `trans_size_text`.`lang`='&2'
	                       WHERE (`caches`.`wp_oc`='&3' OR (`caches`.`wp_oc`!='&3' AND `caches`.`wp_gc`='&3') OR (`caches`.`wp_oc`!='&3' AND `caches`.`wp_nc`='&3')) AND 
      									       (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`='&1')",
										           $login->userid, $opt['template']['locale'], $sWaypoint);
	$rCache = sql_fetch_assoc($rsCache);
	sql_free_result($rsCache);

	if ($rCache === false)
	{
		echo '<caches></caches>';
		exit;
	}

	$nGeokretyCount = sql_value_slave("SELECT COUNT(*) FROM `gk_item_waypoint` WHERE `wp`='&1'", 0, $sWaypoint);
	$nNotFoundCount = $nAttendedCount = 0;
	$nFoundCount = sql_value_slave("SELECT COUNT(*) FROM `cache_logs` WHERE `user_id`='&1' AND `cache_id`='&2' AND `type`=1", 0, $login->userid, $rCache['cache_id']);
	if ($nFoundCount == 0)
		$nNotFoundCount = sql_value_slave("SELECT COUNT(*) FROM `cache_logs` WHERE `user_id`='&1' AND `cache_id`='&2' AND `type`=2", 0, $login->userid, $rCache['cache_id']);
	if ($rCache['type'] == 6)
		$nAttendedCount = sql_value_slave("SELECT COUNT(*) FROM `cache_logs` WHERE `user_id`='&1' AND `cache_id`='&2' AND `type`=7", 0, $login->userid, $rCache['cache_id']);

	echo '<caches>' . "\n";
	echo '  <cache ';
	echo 'name="' . xmlentities($rCache['name']) . '" ';
	echo 'wpoc="' . xmlentities($rCache['wp_oc']) . '" ';
	echo 'coords="' . $rCache['longitude'] . ',' . $rCache['latitude'] . '" ';
	echo 'status_tna="' . xmlentities($rCache['tna']) . '" ';
	echo 'status_text="' . xmlentities($rCache['statustext']) . '" ';
	echo 'type_id="' . xmlentities($rCache['type_id']) . '" ';
	echo 'type_text="' . xmlentities($rCache['type_text']) . '" ';
	echo 'size="' . xmlentities($rCache['size']) . '" ';
	echo 'difficulty="' . xmlentities($rCache['difficulty']/2) . '" ';
	echo 'terrain="' . xmlentities($rCache['terrain']/2) . '" ';
	echo 'listed_since="' . xmlentities(strftime($opt['locale'][$opt['template']['locale']]['format']['date'], strtotime($rCache['date_created']))) . '" ';
	echo 'toprating="' . xmlentities($rCache['toprating']) . '" ';
	echo 'geokreties="' . xmlentities($nGeokretyCount) . '" ';
	echo 'found="' . xmlentities(($nFoundCount>0) ? 1 : 0) . '" ';
	echo 'notfound="' . xmlentities(($nNotFoundCount>0) ? 1 : 0) . '" ';
	echo 'attended="' . xmlentities(($nAttendedCount>0) ? 1 : 0) . '" ';
	echo 'owner="' . xmlentities($rCache['owner']) . '" ';
	echo 'username="' . xmlentities($rCache['username']) . '" ';
	echo 'userid="' . xmlentities($rCache['user_id']) . '" />' . "\n";
	echo '</caches>';

	exit;
}

function output_namesearch($sName, $nLat, $nLon, $nResultId)
{
	global $login;

  $sName = '%' . $sName . '%';

  echo '<caches>' . "\n";
  $rs = sql_slave("SELECT " . geomath::getSqlDistanceFormula($nLon, $nLat, 0) . " AS `distance`, 
                          `caches`.`name`, `caches`.`wp_oc` 
                     FROM `map2_data` 
               INNER JOIN `caches` ON `map2_data`.`cache_id`=`caches`.`cache_id`
               INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
                    WHERE `caches`.`name` LIKE '&1' 
                      AND (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`='&3')
                      AND `map2_data`.`result_id`='&2'
					       ORDER BY `distance` ASC LIMIT 30",
					                $sName,
					                $nResultId,
					                $login->userid);
  while ($r = sql_fetch_assoc($rs))
  {
    echo '<cache name="' . xmlentities($r['name']) . '" wpoc="' . xmlentities($r['wp_oc']) . '" />' . "\n";
  }
  sql_free_result($rs);
  echo '</caches>';
  exit;
}

function output_searchresult($nResultId, $nLon1, $nLat1, $nLon2, $nLat2)
{
	global $login, $opt;

	// check if data is available and connect the right slave server
	$nSlaveId = sql_value("SELECT `slave_id` FROM `map2_result` WHERE `result_id`='&1' AND DATE_ADD(`date_created`, INTERVAL '&2' SECOND)>NOW()", -2, $nResultId, $opt['map']['maxcacheage']);
	if ($nSlaveId == -2)
	{
		echo '<searchresult count="0" available="0">';
		echo '</searchresult>';
		exit;
	}
	sql_connect_slave($nSlaveId);

	sql("UPDATE `map2_result` SET `request_counter`=`request_counter`+1, `date_lastqueried`=NOW() WHERE `result_id`='&1'", $nResultId);

	// execute query and return search result
	$nRecordCount = sql_value_slave("SELECT COUNT(*) FROM `map2_data` INNER JOIN `caches` ON `map2_data`.`cache_id`=`caches`.`cache_id` WHERE `map2_data`.`result_id`='&1' AND `caches`.`longitude`>'&2' AND `caches`.`longitude`<'&3' AND `caches`.`latitude`>'&4' AND `caches`.`latitude`<'&5'", 0, $nResultId, $nLon1, $nLon2, $nLat1, $nLat2);
	// TODO: SQL_CALC_FOUND_ROWS + $nRecordCount = sql_value_slave("SELECT FOUND_ROWS()", 0);

	$bMaxRecordReached = false;
	if ($nRecordCount > $opt['map']['maxrecords'])
		$bMaxRecordReached = true;

	echo '<searchresult count="' . $nRecordCount . '" available="1" maxrecordreached="' . ($bMaxRecordReached ? '1' : '0') . '">' . "\n";

	if ($bMaxRecordReached == false)
	{
		$foundQuery = "(SELECT COUNT(*) FROM `cache_logs` WHERE `cache_logs`.`cache_id`=`caches`.`cache_id` AND `user_id`='" . $login->userid . "' AND `cache_logs`.`type` IN (1,7))";
		$ownedQuery = "IF(`caches`.`user_id`='" . $login->userid . "', 1, 0 )";

		$rs = sql_slave("SELECT SQL_BUFFER_RESULT `caches`.`wp_oc`, `caches`.`longitude`, `caches`.`latitude`, `caches`.`type`, " . $ownedQuery . " AS `owned`, " . $foundQuery . " AS `found` FROM `map2_data` INNER JOIN `caches` ON `map2_data`.`cache_id`=`caches`.`cache_id` WHERE `map2_data`.`result_id`='&1' AND `caches`.`longitude`>'&2' AND `caches`.`longitude`<'&3' AND `caches`.`latitude`>'&4' AND `caches`.`latitude`<'&5' LIMIT " . ($opt['map']['maxrecords']+0), $nResultId, $nLon1, $nLon2, $nLat1, $nLat2);
		while ($r = sql_fetch_assoc($rs))
		{
			$oc_wp = xmlentities($r['wp_oc']);
			$owned = xmlentities($r['owned']);
			$found = (xmlentities($r['found']) > 0 ? 1 : 0);
			echo '<cache wp="' . $oc_wp . '" lon="' . xmlentities($r['longitude']) . '" lat="' . xmlentities($r['latitude']) . '" type="' . xmlentities($r['type']) . '" owned="' . $owned . '" found="' . $found . '" />' . "\n";
		}
		sql_free_result($rs);
	}
	echo '</searchresult>';

	exit;
}
?>
