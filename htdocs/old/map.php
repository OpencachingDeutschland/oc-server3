<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/cache.class.php');

	/* because the map does access some private info like
	 * ignored caches, we need to verify the login data
	 */
	$login->verify();

	$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
	if ($mode == 'locate')
	{
		$lat = isset($_REQUEST['lat']) ? $_REQUEST['lat']+0 : 0;
		$lon = isset($_REQUEST['lon']) ? $_REQUEST['lon']+0 : 0;
		cache_locate($lat, $lon);
	}
	else if ($mode == 'wpsearch')
	{
		$wp = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';
		output_cachexml($wp);
	}
	else if ($mode == 'namesearch')
	{
	  $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
		$lat = isset($_REQUEST['lat']) ? $_REQUEST['lat']+0 : 0;
		$lon = isset($_REQUEST['lon']) ? $_REQUEST['lon']+0 : 0;
    output_namesearch($name, $lat, $lon);
	}
	else if ($mode == 'fullscreen')
	{
		$tpl->popup = true;
		$tpl->popupmargin = false;
	}

	$tpl->name = 'map';
	$tpl->menuitem = MNU_MAP;
	$tpl->nowpsearch = true;

	// get the correct mapkey
	$sHost = strtolower($_SERVER['HTTP_HOST']);
	if (isset($opt['lib']['google']['mapkey'][$sHost]))
		$sGMKey = $opt['lib']['google']['mapkey'][$sHost];
	else
		$tpl->error($translate->t('There is no google maps key registered for this domain.', '', '', 0));

  $tpl->add_header_javascript('http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=' . urlencode($sGMKey));
  $tpl->add_header_javascript('resource2/misc/map/dragzoom_packed.js');
  $tpl->add_body_load('loadMap()');
  $tpl->add_body_unload('GUnload()');

	// process start params
	$gm_initcookiepos = true;
	if (isset($_REQUEST['lat']) &&
	    isset($_REQUEST['lon']) && 
	    isset($_REQUEST['zoom']))
	{
		$gm_initlat = $_REQUEST['lat'];
		$gm_initlon = $_REQUEST['lon'];
		$gm_initzoom = $_REQUEST['zoom'];
		$gm_initcookiepos = false;
	}
	else
	{
		$gm_initlat = 51;
		$gm_initlon = 10;
		$gm_initzoom = 6;
	}

	$gm_initwp = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';
	if ($gm_initwp != '')
	{
		$cache = cache::fromWP($gm_initwp);
		if ($cache == null)
			$gm_initwp = '';
		else
		{
			if ($cache->allowView())
			{
				$gm_initlon = $cache->getLongitude();
				$gm_initlat = $cache->getLatitude();
				$gm_initzoom = 13;
				$gm_initcookiepos = false;
			}
			else
				$gm_initwp = '';
		}
	}

	$tpl->assign('gm_initlon', $gm_initlon);
	$tpl->assign('gm_initlat', $gm_initlat);
	$tpl->assign('gm_initzoom', $gm_initzoom);
	$tpl->assign('gm_initcookiepos', ($gm_initcookiepos ? 1 : 0));
	$tpl->assign('gm_initwp', $gm_initwp);
	$tpl->assign('fullscreen', ($mode == 'fullscreen' ? 1 : 0));

	$tpl->display();

function cache_locate($lat, $lon)
{
	$max_distance = 5;

	$rsCache = sql_slave("SELECT " . geomath::getSqlDistanceFormula($lon, $lat, $max_distance) . " AS `distance`, 
	                              `caches`.`wp_oc`
	                         FROM `caches` 
	                        WHERE `caches`.`latitude`>'&1' AND 
      										      `caches`.`latitude`<'&2' AND 
										            `caches`.`longitude`>'&3' AND 
										            `caches`.`longitude`<'&4' AND
										            `caches`.`status` IN (1, 2)
					             ORDER BY `distance` ASC LIMIT 1",
										            geomath::getMinLat($lon, $lat, $max_distance),
										            geomath::getMaxLat($lon, $lat, $max_distance),
										            geomath::getMinLon($lon, $lat, $max_distance),
										            geomath::getMaxLon($lon, $lat, $max_distance));
	$rCache = sql_fetch_assoc($rsCache);
	sql_free_result($rsCache);

	if ($rCache === false)
	{
		echo '<caches></caches>';
		exit;
	}

	output_cachexml($rCache['wp_oc']);
}

function output_cachexml($wp)
{
	global $opt, $login;

	$rsCache = sql_slave("SELECT `caches`.`name`, `caches`.`wp_oc`, `caches`.`cache_id`, 
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
	                       WHERE `caches`.`wp_oc`='&3' AND 
      									       `caches`.`status` IN (1, 2)",
										           $login->userid, $opt['template']['locale'], $wp);
	$rCache = sql_fetch_assoc($rsCache);
	sql_free_result($rsCache);

	if ($rCache === false)
	{
		echo '<caches></caches>';
		exit;
	}

	$nGeokretyCount = sql_value_slave("SELECT COUNT(*) FROM `gk_item_waypoint` WHERE `wp`='&1'", 0, $wp);
	$nNotFoundCount = 0;
	$nFoundCount = sql_value_slave("SELECT COUNT(*) FROM `cache_logs` WHERE `user_id`='&1' AND `cache_id`='&2' AND `type`=1", 0, $login->userid, $rCache['cache_id']);
	if ($nFoundCount == 0)
		$nNotFoundCount = sql_value_slave("SELECT COUNT(*) FROM `cache_logs` WHERE `user_id`='&1' AND `cache_id`='&2' AND `type`=2", 0, $login->userid, $rCache['cache_id']);

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
	echo 'owner="' . xmlentities($rCache['owner']) . '" ';
	echo 'username="' . xmlentities($rCache['username']) . '" ';
	echo 'userid="' . xmlentities($rCache['user_id']) . '" />' . "\n";
	echo '</caches>';

	exit;
}

function output_namesearch($name, $lat, $lon)
{
  $name = '%' . $name . '%';

  echo '<caches>' . "\n";
  $rs = sql_slave("SELECT " . geomath::getSqlDistanceFormula($lon, $lat, 0) . " AS `distance`, 
                          `caches`.`name`, `caches`.`wp_oc` 
                     FROM `caches` 
                    WHERE `name` LIKE '&1' 
                      AND `status` IN (1, 2) 
					       ORDER BY `distance` ASC LIMIT 30",
					                $name);
  while ($r = sql_fetch_assoc($rs))
  {
    echo '<cache name="' . xmlentities($r['name']) . '" wpoc="' . xmlentities($r['wp_oc']) . '" />' . "\n";
  }
  sql_free_result($rs);
  echo '</caches>';
  exit;
}
?>