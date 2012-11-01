<?php
	/****************************************************************************
		For license information see doc/license.txt
		    
		Unicode Reminder メモ
                                     				                                
		GPX search output (GC compatible)
		
	****************************************************************************/

	global $content, $bUseZip, $sqldebug;

	$gpxHead = 
'<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" version="1.0" creator="Opencaching.de - http://www.opencaching.de" xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://www.groundspeak.com/cache/1/0/1 http://www.groundspeak.com/cache/1/0/1/cache.xsd" xmlns="http://www.topografix.com/GPX/1/0">
  <name>Cache Listing Generated from Opencaching.de</name>
  <desc>This is a waypoint file generated from Opencaching.de</desc>
  <author>Opencaching.de</author>
  <email>info@opencaching.de</email>
  <url>http://www.opencaching.de</url>
  <urlname>Geocaching in Deutschland, Oesterreich und der Schweiz</urlname>
  <time>{time}</time>
  <keywords>cache, geocache, opencaching, waypoint, opencachingnetwork</keywords>';
	
	$gpxLine = 
'  <wpt lat="{lat}" lon="{lon}">
	<time>{time}</time>
	<name>{waypoint}</name>
	<desc>{cachename}</desc>
	<src>www.opencaching.de</src>
	<url>http://www.opencaching.de/viewcache.php?cacheid={cacheid}</url>
	<urlname>{cachename}</urlname>
	<sym>{sym}</sym>
	<type>Geocache|{type}</type>
	<groundspeak:cache  id="{cacheid}" {status} xmlns:groundspeak="http://www.groundspeak.com/cache/1/0">
	  <groundspeak:name>{cachename}</groundspeak:name>
	  <groundspeak:placed_by>{owner}</groundspeak:placed_by>
	  <groundspeak:owner id="{userid}">{owner}</groundspeak:owner>
	  <groundspeak:type>{type}</groundspeak:type>
	  <groundspeak:container>{container}</groundspeak:container>
	  <groundspeak:attributes>
{attributes}	  </groundspeak:attributes>
	  <groundspeak:difficulty>{difficulty}</groundspeak:difficulty>
	  <groundspeak:terrain>{terrain}</groundspeak:terrain>
	  <groundspeak:country>{country}</groundspeak:country>
	  <groundspeak:state>{state}</groundspeak:state>
	  <groundspeak:short_description html="True">{shortdesc}</groundspeak:short_description>
	  <groundspeak:long_description html="True">{desc}</groundspeak:long_description>
	  {hints}
	  <groundspeak:logs>
{logs}	  </groundspeak:logs>
	  <groundspeak:travelbugs>
{geokrety}	  </groundspeak:travelbugs>
	</groundspeak:cache>
  </wpt>
';

	$gpxAttributes = '		  <groundspeak:attribute id="{attrib_id}" inc="1">{attrib_name}</groundspeak:attribute>';

	$gpxLog = '		<groundspeak:log id="{id}">
		  <groundspeak:date>{date}</groundspeak:date>
		  <groundspeak:type>{type}</groundspeak:type>
		  <groundspeak:finder id="{userid}">{username}</groundspeak:finder>
		  <groundspeak:text encoded="False">{text}</groundspeak:text>
		</groundspeak:log>';

	$gpxGeokrety = '		<groundspeak:travelbug id="{gkid}" ref="{gkref}">
		  <groundspeak:name>{gkname}</groundspeak:name>
		</groundspeak:travelbug>';


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

	// known by gpx
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

	$gpxSymNormal = 'Geocache';
	$gpxSymFound = 'Geocache Found';

	// 1st set of attributes - attributes that correlate to GC attributes
		// conditions
		$gpxAttribID[59] = '6';
		$gpxAttribName[59] = 'Recommended for kids';
		$gpxAttribID[28] = '10';
		$gpxAttribName[28] = 'Difficult climbing';
		$gpxAttribID[26] = '11';
		$gpxAttribName[26] = 'May require wading';
		$gpxAttribID[29] = '12';
		$gpxAttribName[29] = 'May require swimming';
		$gpxAttribID[38] = '13';
		$gpxAttribName[38] = 'Available at all times';
		$gpxAttribID[1] = '14';
		$gpxAttribName[1] = 'Recommended at night';
		$gpxAttribID[44] = '15';
		$gpxAttribName[44] = 'Available during winter';
		$gpxAttribID[55] = '47';
		$gpxAttribName[55] = 'Field puzzle';
		$gpxAttribID[24] = '53';
		$gpxAttribName[24] = 'Park and grab';
		$gpxAttribID[60] = '162';
		$gpxAttribName[60] = 'Seasonal access';
		// facilities
		$gpxAttribID[18] = '25';
		$gpxAttribName[18] = 'Parking available';
		$gpxAttribID[19] = '26';
		$gpxAttribName[19] = 'Public transportation';
		$gpxAttribID[20] = '27';
		$gpxAttribName[20] = 'Drinking water nearby';
		$gpxAttribID[21] = '28';
		$gpxAttribName[21] = 'Public restrooms nearby';
		$gpxAttribID[22] = '29';
		$gpxAttribName[22] = 'Telephone nearby';
		// hazards
		$gpxAttribID[11] = '21';
		$gpxAttribName[11] = 'Cliff / falling rocks';
		$gpxAttribID[12] = '12';
		$gpxAttribName[12] = 'Hunting';
		$gpxAttribID[9] = '23';
		$gpxAttribName[9] = 'Dangerous area';
		$gpxAttribID[16] = '17';
		$gpxAttribName[16] = 'Poisonous plants';
		$gpxAttribID[13] = '39';
		$gpxAttribName[13] = 'Thorns';
		$gpxAttribID[17] = '18';
		$gpxAttribName[17] = 'Dangerous animals';
		$gpxAttribID[14] = '19';
		$gpxAttribName[14] = 'Ticks';
		$gpxAttribID[15] = '20';
		$gpxAttribName[15] = 'Abandoned mines';
		// equipment
		$gpxAttribID[36] = '2';
		$gpxAttribName[36] = 'Access or parking fee';
		$gpxAttribID[49] = '3';
		$gpxAttribName[49] = 'Climbing gear';
		$gpxAttribID[48] = '44';
		$gpxAttribName[48] = 'Flashlight required';
		$gpxAttribID[52] = '4';
		$gpxAttribName[52] = 'Boat';
		$gpxAttribID[51] = '5';
		$gpxAttribName[51] = 'Scuba gear';
		$gpxAttribID[46] = '51';
		$gpxAttribName[46] = 'Special tool required';

	// 2nd set of attributes - OC only attributes, changed ID (+100) to be save in oc-gc-mixed environments
		$gpxAttribID[6] = '106';
		$gpxAttribName[6] = 'Only loggable at Opencaching';
		$gpxAttribID[7] = '107';
		$gpxAttribName[7] = 'Hyperlink to another caching portal only';
		$gpxAttribID[8] = '108';
		$gpxAttribName[8] = 'Letterbox (needs stamp)';
		$gpxAttribID[10] = '110';
		$gpxAttribName[10] = 'Active railway nearby';
		$gpxAttribID[23] = '123';
		$gpxAttribName[23] = 'First aid available';
		$gpxAttribID[25] = '125';
		$gpxAttribName[25] = 'Long walk';
		$gpxAttribID[27] = '127';
		$gpxAttribName[27] = 'Hilly area';
		$gpxAttribID[30] = '130';
		$gpxAttribName[30] = 'Point of interest';
		$gpxAttribID[31] = '131';
		$gpxAttribName[31] = 'Moving target';
		$gpxAttribID[32] = '132';
		$gpxAttribName[32] = 'Webcam';
		$gpxAttribID[33] = '133';
		$gpxAttribName[33] = 'Within enclosed rooms (caves, buildings etc.)';
		$gpxAttribID[34] = '134';
		$gpxAttribName[34] = 'In the water';
		$gpxAttribID[35] = '135';
		$gpxAttribName[35] = 'Without GPS (letterboxes, cistes, compass juggling ...)';
		$gpxAttribID[37] = '137';
		$gpxAttribName[37] = 'Overnight stay necessary';
		$gpxAttribID[39] = '139';
		$gpxAttribName[39] = 'Only available at specified times';
		$gpxAttribID[40] = '140';
		$gpxAttribName[40] = 'by day only';
		$gpxAttribID[41] = '141';
		$gpxAttribName[41] = 'Tide';
		$gpxAttribID[42] = '142';
		$gpxAttribName[42] = 'All seasons';
		$gpxAttribID[43] = '143';
		$gpxAttribName[43] = 'Breeding season / protected nature';
		$gpxAttribID[47] = '147';
		$gpxAttribName[47] = 'Compass';
		$gpxAttribID[50] = '150';
		$gpxAttribName[50] = 'Cave equipment';
		$gpxAttribID[53] = '153';
		$gpxAttribName[53] = 'Aircraft';
		$gpxAttribID[54] = '154';
		$gpxAttribName[54] = 'Investigation';
		$gpxAttribID[56] = '156';
		$gpxAttribName[56] = 'Arithmetical problem';
		$gpxAttribID[57] = '157';
		$gpxAttribName[57] = 'Other cache type';
		$gpxAttribID[58] = '158';
		$gpxAttribName[58] = 'Ask owner for start conditions';

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
	if ($options['sort'] == 'bylastlog')
	{
		$sAddField = ', MAX(`cache_logs`.`date`) AS `lastLog`';
		$sAddJoin = ' LEFT JOIN `cache_logs` ON `caches`.`cache_id`=`cache_logs`.`cache_id`';
		$sGroupBy = ' GROUP BY `cache_logs`.`cache_id`';
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

	if ($sortby == 'bylastlog')
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
	$startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
	if (!is_numeric($startat)) $startat = 0;
	
	if (isset($_REQUEST['count']))
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
	$bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
	
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
	
	$gpxHead = mb_ereg_replace('{time}', date($gpxTimeFormat, time()), $gpxHead);
	append_output($gpxHead);

	// ok, output ...

	if ($usr === false)
		$user_id = 0;
	else
		$user_id = $usr['userid'];
	
	$rs = sql_slave("SELECT SQL_BUFFER_RESULT `gpxcontent`.`cache_id` `cacheid`, `gpxcontent`.`longitude` `longitude`, `gpxcontent`.`latitude` `latitude`, 
							`gpxcontent`.`state` `state`, `caches`.`wp_oc` `waypoint`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, 
							`caches`.`country` `country`, `countries`.`name` AS `country_name`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `caches`.`desc_languages` `desc_languages`, 
							`caches`.`size` `size`, `caches`.`type` `type`, `caches`.`status` `status`, `user`.`username` `username`, `caches`.`user_id` `userid`, 
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
			$thisline = mb_ereg_replace('{hints}', '<groundspeak:encoded_hints>' . xmlentities(strip_tags($r['hint'])) . '</groundspeak:encoded_hints>', $thisline);

		$thisline = mb_ereg_replace('{shortdesc}', xmlentities($r['short_desc']), $thisline);
		$thisline = mb_ereg_replace('{desc}', xmlentities($r['desc']), $thisline);

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

		// clear logs, attributes and geokrety
		$logentries = '';
		$attribentries = '';
		$gkentries = '';

		// fetch logs

		if ($user_id != 0)
		{
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
				$thislog = mb_ereg_replace('{text}', xmlentities($rLog['text']), $thislog);
				
				$logentries .= $thislog . "\n";
			}
			mysql_free_result($rsLogs);
		}

		// first 20 logs (except current users)
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
			$thislog = mb_ereg_replace('{text}', xmlentities($rLog['text']), $thislog);
			
			$logentries .= $thislog . "\n";
		}
		mysql_free_result($rsLogs);
		$thisline = mb_ereg_replace('{logs}', $logentries, $thisline);

		// attributes
		$rsAttributes = sql_slave("SELECT `caches_attributes`.`attrib_id` FROM `caches_attributes` WHERE `caches_attributes`.`cache_id`=&1", $r['cacheid']);
		while ($rAttrib = sql_fetch_array($rsAttributes))
		{
			$thisattribute = $gpxAttributes;

			$thisattribute_id = $gpxAttribID[$rAttrib['attrib_id']];
			$thisattribute_name = $gpxAttribName[$rAttrib['attrib_id']];
			
			$thisattribute = mb_ereg_replace('{attrib_id}', $thisattribute_id, $thisattribute);
			$thisattribute = mb_ereg_replace('{attrib_name}', $thisattribute_name, $thisattribute);
			
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
			$thiskrety = mb_ereg_replace('{gkname}', $rGK['name'], $thiskrety);
			
			$gkentries .= $thiskrety . "\n";
		}
		mysql_free_result($rsGeokrety);
		$thisline = mb_ereg_replace('{geokrety}', $gkentries, $thisline);

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
	
	function xmlentities($str)
	{
		$from[0] = '&'; $to[0] = '&amp;';
		$from[1] = '<'; $to[1] = '&lt;';
		$from[2] = '>'; $to[2] = '&gt;';
		$from[3] = '"'; $to[3] = '&quot;';
		$from[4] = '\''; $to[4] = '&apos;';
		$from[5] = ']]>'; $to[5] = ']] >';

		for ($i = 0; $i <= 4; $i++)
			$str = mb_ereg_replace($from[$i], $to[$i], $str);

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
?>
