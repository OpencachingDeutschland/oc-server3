<?php
	/****************************************************************************
		For license information see doc/license.txt
		    
		Unicode Reminder メモ
                                     				                                
		GPX search output (GC compatible)
		
	****************************************************************************/

	global $content, $bUseZip, $sqldebug, $locale;

	$gpxHead = 
'<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" version="1.0" creator="Opencaching.de - http://www.opencaching.de" xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://www.groundspeak.com/cache/1/0/1 http://www.groundspeak.com/cache/1/0/1/cache.xsd" xmlns="http://www.topografix.com/GPX/1/0">
  <name>Cache listing generated from Opencaching.de</name>
  <desc>This is a waypoint file generated from Opencaching.de{wpchildren}</desc>
  <author>Opencaching.de</author>
  <email>contact@opencaching.de</email>
  <url>http://www.opencaching.de</url>
  <urlname>Opencaching.de - Geocaching in Deutschland, Oesterreich und der Schweiz</urlname>
  <time>{time}</time>
  <keywords>cache, geocache, opencaching, waypoint</keywords>
';
	
	$gpxLine = 
'  <wpt lat="{lat}" lon="{lon}">
    <time>{time}</time>
    <name>{waypoint}</name>
    <desc>{cachename}</desc>
    <src>www.opencaching.de</src>
    <url>' . $absolute_server_URI . 'viewcache.php?cacheid={cacheid}</url>
    <urlname>{cachename}</urlname>
    <sym>{sym}</sym>
    <type>Geocache|{type}</type>
    <groundspeak:cache id="{cacheid}" {status} xmlns:groundspeak="http://www.groundspeak.com/cache/1/0/1">
      <groundspeak:name>{cachename}</groundspeak:name>
      <groundspeak:placed_by>{owner}</groundspeak:placed_by>
      <groundspeak:owner id="{userid}">{owner}</groundspeak:owner>
      <groundspeak:type>{type}</groundspeak:type>
      <groundspeak:container>{container}</groundspeak:container>
      <groundspeak:attributes>
{attributes}      </groundspeak:attributes>
      <groundspeak:difficulty>{difficulty}</groundspeak:difficulty>
      <groundspeak:terrain>{terrain}</groundspeak:terrain>
      <groundspeak:country>{country}</groundspeak:country>
      <groundspeak:state>{state}</groundspeak:state>
      <groundspeak:short_description html="True">{shortdesc}</groundspeak:short_description>
      <groundspeak:long_description html="True">{desc}&lt;br /&gt;{images}</groundspeak:long_description>
{hints}      <groundspeak:logs>
{logs}      </groundspeak:logs>
      <groundspeak:travelbugs>
{geokrety}      </groundspeak:travelbugs>
    </groundspeak:cache>
  </wpt>
{cache_waypoints}';
  /* Ocprop:
   *    <wpt\s+lat=\"([0-9\-\+\.]+)\"\s+lon=\"([0-9\-\+\.]+)\">
   *    <time>(.*?)<\/time>
   *      (Date: ^([0-9]{4})\-([0-9]{2})\-([0-9]{2})T[0-9\:\-\.]+(Z)?$/s)
   *    <name>(.*?)<\/name>
   *    <url>http:\/\/www\.opencaching\.de\/viewcache\.php\?cacheid=([0-9]+)<\/url>
   *    <sym>(.*?)<\/sym>
   *    <groundspeak:cache\s+id=\"[0-9]+\"\s+available=\"(True|False)\"\s+archived=\"(True|False)\"
   *    <groundspeak:name>(.*?)<\/groundspeak:name>
   *    <groundspeak:placed_by>(.*?)<\/groundspeak:placed_by>
   *    <groundspeak:owner id="([0-9])+">(.*?)<\/groundspeak:owner>
   *    <groundspeak:type>(.*?)<\/groundspeak:type>
   *    <groundspeak:container>(.*?)<\/groundspeak:container>
   *    <groundspeak:difficulty>(.*?)<\/groundspeak:difficulty>
   *    <groundspeak:terrain>(.*?)<\/groundspeak:terrain>
   *    <groundspeak:country>(.*?)<\/groundspeak:country>
   *    <groundspeak:state>(.*?)<\/groundspeak:state>
   *    <groundspeak:short_description html="(.*?)".*?>(.*?)<\/groundspeak:short_description>
   *    <groundspeak:long_description html="(.*?)".*?>(.*?)<\/groundspeak:long_description>
   *    <groundspeak:encoded_hints>(.*?)<\/groundspeak:encoded_hints>
   */

	$gpxAttributes = ' 	      <groundspeak:attribute id="{attrib_id}" inc="{attrib_inc}">{attrib_name}</groundspeak:attribute>';

	$gpxLog = '      <groundspeak:log id="{id}">
        <groundspeak:date>{date}</groundspeak:date>
        <groundspeak:type>{type}</groundspeak:type>
        <groundspeak:finder id="{userid}">{username}</groundspeak:finder>
        <groundspeak:text encoded="False">{text}</groundspeak:text>
      </groundspeak:log>';

	$gpxGeokrety = '		<groundspeak:travelbug id="{gkid}" ref="{gkref}">
		  <groundspeak:name>{gkname}</groundspeak:name>
		</groundspeak:travelbug>';

	$gpxWaypoints = '  <wpt lat="{wp_lat}" lon="{wp_lon}">
    <time>{time}</time>
    <name>{name}</name>
    <cmt>{comment}</cmt>
    <desc>{desc}</desc>
    <url>' . $absolute_server_URI . 'viewcache.php?cacheid={cacheid}</url>
    <urlname>{parent} {cachename}</urlname>
    <sym>{type}</sym>
    <type>Waypoint|{type}</type>
    <gsak:wptExtension xmlns:gsak="http://www.gsak.net/xmlv1/4">
      <gsak:Parent>{parent}</gsak:Parent>
    </gsak:wptExtension>
  </wpt>
';

	$gpxFoot = '</gpx>';

	$gpxTimeFormat = 'Y-m-d\TH:i:s\Z';

	$gpxStatus[0] = 'available="False" archived="False"'; // other (unavailable, not archived)
	$gpxStatus[1] = 'available="True" archived="False"'; //available, not archived
	$gpxStatus[2] = 'available="False" archived="False"'; //unavailable, not archived
	$gpxStatus[3] = 'available="False" archived="True"'; //unavailable, archived
	$gpxStatus[6] = 'available="False" archived="True"'; //locked, visible

	$gpxContainer[0] = 'Other';
	$gpxContainer[2] = 'Micro';
	$gpxContainer[3] = 'Small';
	$gpxContainer[4] = 'Regular';
	$gpxContainer[5] = 'Large';
	$gpxContainer[6] = 'Large';
	$gpxContainer[7] = 'Virtual';
	$gpxContainer[8] = 'Micro';

	// cache types known by gpx
	$gpxType[0] = 'Unknown Cache';
	$gpxType[2] = 'Traditional Cache';
	$gpxType[3] = 'Multi-cache';
	$gpxType[4] = 'Virtual Cache';
	$gpxType[5] = 'Webcam Cache';
	$gpxType[6] = 'Event Cache';

	// unknown ... converted
	$gpxType[7] = 'Unknown Cache';
	$gpxType[8] = 'Unknown Cache';
	$gpxType[10] = 'Traditional Cache';
	
	$gpxLogType[0] = 'Other';
	$gpxLogType[1] = 'Found it';
	$gpxLogType[2] = 'Didn\'t find it';
	$gpxLogType[3] = 'Write note';
	$gpxLogType[7] = 'Attended';
	$gpxLogType[8] = 'Will attend';
	$gpxLogType[9] = 'Archive';
	$gpxLogType[10] = 'Owner Maintenance';
	$gpxLogType[11] = 'Temporarily Disable Listing';
	$gpxLogType[13] = 'Archive';
	$gpxLogType[14] = 'Archive';

	$gpxSymNormal = 'Geocache';
	$gpxSymFound = 'Geocache Found';

	//prepare the output
	$caches_per_page = 20;
	
	$sql = 'SELECT '; 

	if (isset($lat_rad) && isset($lon_rad))
	{
		$sql .= getSqlDistanceFormula($lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
	}
	else
	{
		if ($usr === false)
		{
			$sql .= '0 distance, ';
		}
		else
		{
			//get the users home coords
			$rs_coords = sql_slave("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
			$record_coords = sql_fetch_array($rs_coords);
			
			if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0)))
			{
				$sql .= '0 distance, ';
			}
			else
			{
				//TODO: load from the users-profile
				$distance_unit = 'km';

				$lon_rad = $record_coords['longitude'] * 3.14159 / 180;   
				$lat_rad = $record_coords['latitude'] * 3.14159 / 180;

				$sql .= getSqlDistanceFormula($record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
			}
			mysql_free_result($rs_coords);
		}
	}
	$sAddJoin = '';
	$sAddGroupBy = '';
	$sAddField = '';
	$sGroupBy = '';
	if ($options['sort'] == 'bylastlog' || $options['sort'] == 'bymylastlog')
	{
		$sAddField = ', MAX(`cache_logs`.`date`) AS `lastLog`';
		$sAddJoin = ' LEFT JOIN `cache_logs` ON `caches`.`cache_id`=`cache_logs`.`cache_id`';
		if ($options['sort'] == 'bymylastlog')
			$sAddJoin .= ' AND `cache_logs`.`user_id`=' . sql_escape($usr === false? 0 : $usr['userid']);
		$sGroupBy = ' GROUP BY `caches`.`cache_id`';
	}
	$sql .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, `caches`.`user_id` `user_id`,
	            IF(IFNULL(`stat_caches`.`toprating`,0)>3, 4, IFNULL(`stat_caches`.`toprating`, 0)) `ratingvalue`,
		 `cache_location`.`adm2` `state`' .
				      $sAddField
		 . ' FROM `caches`
	  LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id`
	  LEFT JOIN `cache_location` ON `caches`.`cache_id`=`cache_location`.`cache_id`' .
				      $sAddJoin
		. ' WHERE `caches`.`cache_id` IN (' . $sqlFilter . ')' .
				      $sGroupBy;
	$sortby = $options['sort'];

	$sql .= ' ORDER BY ';
	if ($options['orderRatingFirst'])
		$sql .= '`ratingvalue` DESC, ';

	if ($sortby == 'bylastlog' || $options['sort'] == 'bymylastlog')
	{
		$sql .= '`lastLog` DESC, ';
		$sortby = 'bydistance';
	}

	if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance'))
	{
		$sql .= '`distance` ASC';
	}
	else if ($sortby == 'bycreated')
	{
		$sql .= '`caches`.`date_created` DESC';
	}
	else // by name
	{
		$sql .= '`caches`.`name` ASC';
	}

	//startat?
	$startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;  // Ocprop
	if (!is_numeric($startat)) $startat = 0;
	
	if (isset($_REQUEST['count']))  // Ocprop
		$count = $_REQUEST['count'];
	else
		$count = $caches_per_page;
	
	if ($count == 'max') $count = 500;
	if (!is_numeric($count)) $count = 0;
	if ($count < 1) $count = 1;
	if ($count > 500) $count = 500;

	$sqlLimit = ' LIMIT ' . $startat . ', ' . $count;

	// create temporary table
	sql_slave('CREATE TEMPORARY TABLE `gpxcontent` ' . $sql . $sqlLimit);

	$rsCount = sql_slave('SELECT COUNT(*) `count` FROM `gpxcontent`');
	$rCount = sql_fetch_array($rsCount);
	mysql_free_result($rsCount);
	
	if ($rCount['count'] == 1)
	{
		$rsName = sql_slave('SELECT `caches`.`wp_oc` `wp_oc` FROM `gpxcontent`, `caches` WHERE `gpxcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
		$rName = sql_fetch_array($rsName);
		mysql_free_result($rsName);
		
		$sFilebasename = $rName['wp_oc'];
	}
	else
		$sFilebasename = 'ocde' . $options['queryid'];
		
	$bUseZip = ($rCount['count'] > 20);
	$bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));  // Ocprop
	
	if ($bUseZip == true)
	{
		$content = '';
		require_once($rootpath . 'lib/phpzip/ss_zip.class.php');
		$phpzip = new ss_zip('',6);
	}

	// ok, let's start
	
	if ($sqldebug == false)
	{
		if ($bUseZip == true)
		{
			header("content-type: application/zip");
			header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
		}
		else
		{
			header("Content-type: application/gpx");
			header("Content-Disposition: attachment; filename=" . $sFilebasename . ".gpx");
		}
	}
	
	$childwphandler = new ChildWp_Handler();

	// ok, output ...

	$children='';
	$rs = sql('SELECT `gpxcontent`.`cache_id` `cacheid` FROM `gpxcontent`');
	while ($r = sql_fetch_array($rs))
		if (count($childwphandler->getChildWps($r['cacheid'])))
			$children=" (HasChildren)"; 
	mysql_free_result($rs);

	$gpxHead = mb_ereg_replace('{wpchildren}', $children, $gpxHead);
	$gpxHead = mb_ereg_replace('{time}', date($gpxTimeFormat, time()), $gpxHead);
	append_output($gpxHead);

	if ($usr === false)
		$user_id = 0;
	else
		$user_id = $usr['userid'];
	
	$rs = sql_slave("SELECT SQL_BUFFER_RESULT `gpxcontent`.`cache_id` `cacheid`, `gpxcontent`.`longitude` `longitude`, `gpxcontent`.`latitude` `latitude`, 
							`gpxcontent`.`state` `state`, `caches`.`wp_oc` `waypoint`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, 
							`caches`.`country` `country`, `countries`.`name` AS `country_name`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `caches`.`desc_languages` `desc_languages`, 
							`caches`.`size` `size`, `caches`.`type` `type`, `caches`.`status` `status`, `user`.`username` `username`, `caches`.`user_id` `userid`, `user`.`data_license`,
							`cache_desc`.`desc` `desc`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`hint` `hint`,
							IFNULL(`stat_cache_logs`.`found`, 0) AS `found`
						FROM `gpxcontent` 
							INNER JOIN `caches` ON `gpxcontent`.`cache_id`=`caches`.`cache_id`
							INNER JOIN `countries` ON `caches`.`country`=`countries`.`short`
							INNER JOIN `user` ON `gpxcontent`.`user_id`=`user`.`user_id`
							INNER JOIN `cache_desc` ON `caches`.`cache_id`=`cache_desc`.`cache_id` 
								AND `caches`.`default_desclang`=`cache_desc`.`language`
								LEFT JOIN `stat_cache_logs` ON `gpxcontent`.`cache_id`=`stat_cache_logs`.`cache_id` AND `stat_cache_logs`.`user_id`='&1'", $user_id);

	while($r = sql_fetch_array($rs))
	{
		$thisline = $gpxLine;
		
		$lat = sprintf('%01.5f', $r['latitude']);
		$thisline = mb_ereg_replace('{lat}', $lat, $thisline);
		
		$lon = sprintf('%01.5f', $r['longitude']);
		$thisline = mb_ereg_replace('{lon}', $lon, $thisline);

		$time = date($gpxTimeFormat, strtotime($r['date_hidden']));
		$thisline = mb_ereg_replace('{time}', $time, $thisline);
		$thisline = mb_ereg_replace('{waypoint}', $r['waypoint'], $thisline);
		$thisline = mb_ereg_replace('{cacheid}', $r['cacheid'], $thisline);
		$thisline = mb_ereg_replace('{cachename}', xmlentities($r['name']), $thisline);
		$thisline = mb_ereg_replace('{country}', $r['country_name'], $thisline);
		$thisline = mb_ereg_replace('{state}', xmlentities($r['state']), $thisline);
		
		if ($r['hint'] == '')
			$thisline = mb_ereg_replace('{hints}', '', $thisline);
		else
		  // Ocprop:  <groundspeak:encoded_hints>(.*?)<\/groundspeak:encoded_hints>
			$hint = html_entity_decode(strip_tags($r['hint']), ENT_COMPAT, "UTF-8");
			$thisline = mb_ereg_replace('{hints}', '      <groundspeak:encoded_hints>' . xmlentities($hint) . '</groundspeak:encoded_hints>
', $thisline);

		$thisline = mb_ereg_replace('{shortdesc}', xmlentities($r['short_desc']), $thisline);

		$desc = str_replace('<img src="images/uploads/','<img src="' . $absolute_server_URI . 'images/uploads/', $r['desc']);		
		$license = getLicenseDisclaimer(
			$r['userid'], $r['username'], $r['data_license'], $r['cacheid'], $locale, true, true);
		if ($license != "")
			$desc .= "<p><em>$license</em></p>";
		$thisline = mb_ereg_replace('{desc}', xmlentities(decodeEntities($desc)), $thisline);

		$thisline = mb_ereg_replace('{images}', xmlentities(getPictures($r['cacheid'])), $thisline);

		if (isset($gpxType[$r['type']]))
		$thisline = mb_ereg_replace('{type}', $gpxType[$r['type']], $thisline);
		else
		$thisline = mb_ereg_replace('{type}', $gpxType[0], $thisline);

		if (isset($gpxContainer[$r['size']]))
		$thisline = mb_ereg_replace('{container}', $gpxContainer[$r['size']], $thisline);
		else
		$thisline = mb_ereg_replace('{container}', $gpxContainer[0], $thisline);

		if (isset($gpxStatus[$r['status']]))
		$thisline = mb_ereg_replace('{status}', $gpxStatus[$r['status']], $thisline);
		else
		$thisline = mb_ereg_replace('{status}', $gpxStatus[0], $thisline);

		$sDiffDecimals = '';
		if ($r['difficulty'] % 2) $sDiffDecimals = '.5';
		$r['difficulty'] -= $r['difficulty'] % 2;
		$thisline = mb_ereg_replace('{difficulty}', ($r['difficulty']/2) . $sDiffDecimals, $thisline);

		$sTerrDecimals = '';
		if ($r['terrain'] % 2) $sTerrDecimals = '.5';
		$r['terrain'] -= $r['terrain'] % 2;
		$thisline = mb_ereg_replace('{terrain}', ($r['terrain']/2) . $sTerrDecimals, $thisline);

		$thisline = mb_ereg_replace('{owner}', xmlentities($r['username']), $thisline);
		$thisline = mb_ereg_replace('{userid}', xmlentities($r['userid']), $thisline);

		if ($r['found'] > 0)
			$thisline = mb_ereg_replace('{sym}', xmlentities($gpxSymFound), $thisline);
		else
			$thisline = mb_ereg_replace('{sym}', xmlentities($gpxSymNormal), $thisline);

		// clear cache specific data
		$logentries = '';
		$cache_note = false;
		$attribentries = '';
		$waypoints = '';
		$gkentries = '';

		// fetch logs

		if ($user_id != 0)
		{
			// insert personal note
			$cacheNote = getCacheNote($user_id, $r['cacheid']);
			if ($cacheNote)
			{
				$thislog = $gpxLog;

				$thislog = mb_ereg_replace('{id}', 0, $thislog);
				$thislog = mb_ereg_replace('{date}', date($gpxTimeFormat), $thislog);
				$thislog = mb_ereg_replace('{userid}', $user_id, $thislog);
				$thislog = mb_ereg_replace('{username}', xmlentities($login->username), $thislog);
				$thislog = mb_ereg_replace('{type}', $gpxLogType[3], $thislog);
				$thislog = mb_ereg_replace('{text}', xmlentities($cacheNote['note']), $thislog);

				$logentries .= $thislog . "\n";
			}

			// current users logs
			$rsLogs = sql_slave("SELECT `cache_logs`.`id`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username`, `user`.`user_id` FROM `cache_logs`, `user` WHERE `cache_logs`.`user_id`=`user`.`user_id` AND `cache_logs`.`cache_id`=&1 AND `user`.`user_id`=&2 ORDER BY `cache_logs`.`date` DESC", $r['cacheid'], $user_id);
			while ($rLog = sql_fetch_array($rsLogs))
			{
				$thislog = $gpxLog;

				$thislog = mb_ereg_replace('{id}', $rLog['id'], $thislog);
				$thislog = mb_ereg_replace('{date}', date($gpxTimeFormat, strtotime($rLog['date'])), $thislog);
				$thislog = mb_ereg_replace('{userid}', xmlentities($rLog['user_id']), $thislog);
				$thislog = mb_ereg_replace('{username}', xmlentities($rLog['username']), $thislog);
				
				if (isset($gpxLogType[$rLog['type']]))
					$logtype = $gpxLogType[$rLog['type']];
				else
					$logtype = $gpxLogType[0];
					
				$thislog = mb_ereg_replace('{type}', $logtype, $thislog);
				$thislog = mb_ereg_replace('{text}', xmlentities(decodeEntities($rLog['text'])), $thislog);
				
				$logentries .= $thislog . "\n";
			}
			mysql_free_result($rsLogs);
		}

		// newest 20 logs (except current users)
		$rsLogs = sql_slave("SELECT `cache_logs`.`id`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username`, `user`.`user_id` FROM `cache_logs`, `user` WHERE `cache_logs`.`user_id`=`user`.`user_id` AND `cache_logs`.`cache_id`=&1 AND `user`.`user_id`!=&2 ORDER BY `cache_logs`.`date` DESC LIMIT 20", $r['cacheid'], $user_id);
		while ($rLog = sql_fetch_array($rsLogs))
		{
			$thislog = $gpxLog;
			
			$thislog = mb_ereg_replace('{id}', $rLog['id'], $thislog);
			$thislog = mb_ereg_replace('{date}', date($gpxTimeFormat, strtotime($rLog['date'])), $thislog);
			$thislog = mb_ereg_replace('{userid}', xmlentities($rLog['user_id']), $thislog);
			$thislog = mb_ereg_replace('{username}', xmlentities($rLog['username']), $thislog);
			
			if (isset($gpxLogType[$rLog['type']]))
				$logtype = $gpxLogType[$rLog['type']];
			else
				$logtype = $gpxLogType[0];
				
			$thislog = mb_ereg_replace('{type}', $logtype, $thislog);
			$thislog = mb_ereg_replace('{text}', xmlentities(decodeEntities($rLog['text'])), $thislog);
			
			$logentries .= $thislog . "\n";
		}
		mysql_free_result($rsLogs);
		$thisline = mb_ereg_replace('{logs}', $logentries, $thisline);

		// attributes
		$rsAttributes = sql_slave("SELECT `gc_id`, `gc_inc`, `gc_name`
		                             FROM `caches_attributes`
		                       INNER JOIN `cache_attrib` ON `cache_attrib`.`id`=`caches_attributes`.`attrib_id`
		                            WHERE `caches_attributes`.`cache_id`=&1", $r['cacheid']);
		while ($rAttrib = sql_fetch_array($rsAttributes))
		{
			$thisattribute = mb_ereg_replace('{attrib_id}', $rAttrib['gc_id'], $gpxAttributes);
			$thisattribute = mb_ereg_replace('{attrib_inc}', $rAttrib['gc_inc'], $thisattribute);
			$thisattribute = mb_ereg_replace('{attrib_name}', xmlentities($rAttrib['gc_name']), $thisattribute);
			$attribentries .= $thisattribute . "\n";
		}
		mysql_free_result($rsAttributes);
		$thisline = mb_ereg_replace('{attributes}', $attribentries, $thisline);

		// geokrety
		$rsGeokrety = sql_slave("SELECT `gk_item`.`id`, `gk_item`.`name`,  `caches`.`wp_oc` FROM `gk_item` INNER JOIN `gk_item_waypoint` ON `gk_item`.`id`=`gk_item_waypoint`.`id` INNER JOIN `caches` ON `gk_item_waypoint`.`wp`=`caches`.`wp_oc` WHERE `caches`.`cache_id`=&1", $r['cacheid']);
		while ($rGK = sql_fetch_array($rsGeokrety))
		{
			$thiskrety = $gpxGeokrety;

			$thiskrety = mb_ereg_replace('{gkid}', $rGK['id'], $thiskrety);
			$thiskrety = mb_ereg_replace('{gkref}', sprintf("GK%04X",$rGK['id']), $thiskrety);
			$thiskrety = mb_ereg_replace('{gkname}', xmlentities($rGK['name']), $thiskrety);
			
			$gkentries .= $thiskrety . "\n";
		}
		mysql_free_result($rsGeokrety);
		$thisline = mb_ereg_replace('{geokrety}', $gkentries, $thisline);

		// additional waypoints, including personal cache note
		$childWaypoints = $childwphandler->getChildWps($r['cacheid']);
		$n = 1;
		$digits = "%0" . strlen(count($childWaypoints)) . "d";

		foreach ($childWaypoints as $childWaypoint)
		{
			$thiswp = $gpxWaypoints;
			$thiswp = mb_ereg_replace('{wp_lat}', sprintf('%01.5f', $childWaypoint['latitude']), $thiswp);
			$thiswp = mb_ereg_replace('{wp_lon}', sprintf('%01.5f', $childWaypoint['longitude']), $thiswp);
			$thiswp = mb_ereg_replace('{time}', $time, $thiswp);
			$thiswp = mb_ereg_replace('{name}', $r['waypoint'].'-'.sprintf($digits,$n) , $thiswp);
			$thiswp = mb_ereg_replace('{cachename}', xmlentities($r['name']), $thiswp);
			$thiswp = mb_ereg_replace('{comment}',xmlentities($childWaypoint['description']), $thiswp);
			$thiswp = mb_ereg_replace('{desc}', xmlentities($childWaypoint['name']), $thiswp);
			switch ($childWaypoint['type'])
			{
				case 1: $wp_typename = "Parking Area"; break;  // well-known garmin symbols
				case 2: $wp_typename = "Flag, Green"; break;   // stage / ref point
				case 3: $wp_typename = "Flag, Blue"; break;    // path
				case 4: $wp_typename = "Circle with X"; break; // final
				case 5: $wp_typename = "Diamond, Green"; break;  // point of interest
				default: $wp_typename = "Flag, Blue"; break;  // for the case new types are forgotten here ..
			}
			$thiswp = mb_ereg_replace('{type}', $wp_typename, $thiswp);
			$thiswp = mb_ereg_replace('{parent}', $r['waypoint'], $thiswp);
			$thiswp = mb_ereg_replace('{cacheid}', $r['cacheid'], $thiswp);
			$waypoints .= $thiswp;
			++$n;
		}

		if ($cacheNote && !empty($cacheNote['latitude']) && !empty($cacheNote['longitude']))
		{
			$thiswp = $gpxWaypoints;
			$thiswp = mb_ereg_replace('{wp_lat}', sprintf('%01.5f', $cacheNote['latitude']), $thiswp);
			$thiswp = mb_ereg_replace('{wp_lon}', sprintf('%01.5f', $cacheNote['longitude']), $thiswp);
			$thiswp = mb_ereg_replace('{time}', $time, $thiswp);
			$thiswp = mb_ereg_replace('{name}', $r['waypoint'].'NOTE', $thiswp);
			$thiswp = mb_ereg_replace('{cachename}', xmlentities($r['name']), $thiswp);
			$thiswp = mb_ereg_replace('{comment}', xmlentities($cacheNote['note']), $thiswp);
			$thiswp = mb_ereg_replace('{desc}', $translate->t('Personal cache note','','',0), $thiswp);
			$thiswp = mb_ereg_replace('{type}', "Reference Point", $thiswp);
			$thiswp = mb_ereg_replace('{parent}', $r['waypoint'], $thiswp);
			$thiswp = mb_ereg_replace('{cacheid}', $r['cacheid'], $thiswp);
			$waypoints .= $thiswp;
		}

		$thisline = mb_ereg_replace('{cache_waypoints}', $waypoints, $thisline);

		append_output($thisline);
	}
	mysql_free_result($rs);
	
	append_output($gpxFoot);

	if ($sqldebug == true) sqldbg_end();
	
	// send using phpzip
	if ($bUseZip == true)
	{
		$phpzip->add_data($sFilebasename . '.gpx', $content);
		echo $phpzip->save($sFilebasename . '.zip', 'b');
	}

	exit;

	function decodeEntities($str)
	{
		$str = changePlaceholder($str);
		$str = html_entity_decode($str, ENT_COMPAT, "UTF-8");
		$str = changePlaceholder($str, true);
		return $str;
	}

	function changePlaceholder($str, $inverse = false)
	{
		$placeholder[0] = '{oc-placeholder-lt}'; $entity[0] = '&lt;';
		$placeholder[1] = '{oc-placeholder-gt}'; $entity[1] = '&gt;';
		$placeholder[2] = '{oc-placeholder-amp}'; $entity[2] = '&amp;';
		for ($i=0;$i<count($placeholder);$i++)
		{
			if (!$inverse)
			{
				$str = mb_ereg_replace($entity[$i], $placeholder[$i], $str);
			}
			else
			{
				$str = mb_ereg_replace($placeholder[$i], $entity[$i], $str);
			}
		}
		return $str;
	}

	function xmlentities($str)
	{
		$str = htmlspecialchars($str, ENT_NOQUOTES, "UTF-8");
		return filterevilchars($str);
	}

	function filterevilchars($str)
	{
		return mb_ereg_replace('[\\x00-\\x09|\\x0B-\\x0C|\\x0E-\\x1F]', '', $str);
	}

	function append_output($str)
	{
		global $content, $bUseZip, $sqldebug;
		if ($sqldebug == true) return;
		
		if ($bUseZip == true)
			$content .= $str;
		else
			echo $str;
	}

	function getCacheNote($userid, $cacheid)
	{
		$cacheNoteHandler = new CacheNote_Handler();
		$cacheNote = $cacheNoteHandler->getCacheNote($userid, $cacheid);

		if (isset($cacheNote['note']) || isset($cacheNote['latitude']) || isset($cacheNote['longitude']))
			return $cacheNote;

		return null;
	}

	// based on oc.pl code, but embedded thumbs instead of full pictures
	// (also to hide spoilers first)
	function getPictures($cacheid)
	{
		global $translate, $absolute_server_URI;

		$retval = "";
		$rs = sql_slave("SELECT uuid, title, url, spoiler FROM pictures
		                 WHERE object_id='&1' AND object_type=2 AND display=1 
                     ORDER BY date_created", $cacheid);

		while ($r = sql_fetch_array($rs))
		{
			$retval .= '<div style="float:left; padding:8px"><a href="' . $r['url'] . '" target="_blank">' .
			           '<img src="' . $absolute_server_URI . 'thumbs.php?uuid=' . $r["uuid"]. '" >' .
			           '</a><br />' . $r['title'];
			if ($r['spoiler'])
				$retval .= ' (' . $translate->t('click on spoiler to display','',basename(__FILE__), __LINE__) . ')';
			$retval .= "</div>";
		}
		mysql_free_result($rs);

		return $retval;
	}

?>
