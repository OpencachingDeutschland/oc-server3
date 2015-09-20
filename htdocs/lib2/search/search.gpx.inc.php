<?php
	/****************************************************************************
		For license information see doc/license.txt

		Unicode Reminder メモ

		GPX search output (GC compatible)
		used by Ocprop
	****************************************************************************/

	require_once('lib2/logic/npas.inc.php');

	$search_output_file_download = true;
	$content_type_plain = 'application/gpx';


function search_output()
{
	global $opt, $login;
	global $cache_note_text;

	$server_domain = $opt['page']['domain'];
	$server_address = $opt['page']['default_absolute_url'];
	$server_name = $opt['page']['sitename'];

	$gpxHead =
'<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" version="1.0" creator="'.$server_name.' - '.$server_address.'" xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd http://www.groundspeak.com/cache/1/0/1 http://www.groundspeak.com/cache/1/0/1/cache.xsd" xmlns="http://www.topografix.com/GPX/1/0">
  <name>Cache listing generated from '.$server_name.'</name>
  <desc>This is a waypoint file generated from '.$server_name.'{wpchildren}</desc>
  <author>Opencaching.de</author>
  <email>'.$opt['mail']['contact'].'</email>
  <url>'.$server_domain.'</url>
  <urlname>'.$opt['page']['slogan'].'</urlname>
  <time>{time}</time>
  <keywords>cache, geocache, opencaching, waypoint</keywords>
';

	$gpxLine =
'  <wpt lat="{lat}" lon="{lon}">
    <time>{time}</time>
    <name>{waypoint}</name>
    <desc>{cachename}</desc>
    <src>'.$server_domain.'</src>
    <url>' . $server_address . 'viewcache.php?cacheid={cacheid}</url>
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
    <url>' . $server_address . 'viewcache.php?cacheid={cacheid}</url>
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

	$childwphandler = new ChildWp_Handler();
	$children='';
	$rs = sql('SELECT &searchtmp.`cache_id` `cacheid` FROM &searchtmp');
	while ($r = sql_fetch_array($rs) && $children == '')
	{
		if (count($childwphandler->getChildWps($r['cacheid'])))
			$children = ' (HasChildren)';
	}
	mysql_free_result($rs);

	$gpxHead = mb_ereg_replace('{wpchildren}', $children, $gpxHead);
	$gpxHead = mb_ereg_replace('{time}', date($gpxTimeFormat, time()), $gpxHead);
	append_output($gpxHead);

	$user_id = $login->userid;

	$rs = sql_slave("SELECT SQL_BUFFER_RESULT &searchtmp.`cache_id` `cacheid`, &searchtmp.`longitude` `longitude`, &searchtmp.`latitude` `latitude`,
							`cache_location`.`adm2` `state`, `caches`.`wp_oc` `waypoint`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`,
							`caches`.`country` `country`, `countries`.`name` AS `country_name`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `caches`.`desc_languages` `desc_languages`,
							`caches`.`size` `size`, `caches`.`type` `type`, `caches`.`status` `status`, `user`.`username` `username`, `caches`.`user_id` `userid`, `user`.`data_license`,
							`cache_desc`.`desc` `desc`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`hint` `hint`, `cache_desc`.`language` AS `desc_language`,
							IFNULL(`stat_cache_logs`.`found`, 0) AS `found`
						FROM &searchtmp
							INNER JOIN `caches` ON &searchtmp.`cache_id`=`caches`.`cache_id`
							INNER JOIN `countries` ON `caches`.`country`=`countries`.`short`
							INNER JOIN `user` ON &searchtmp.`user_id`=`user`.`user_id`
							INNER JOIN `cache_desc` ON `caches`.`cache_id`=`cache_desc`.`cache_id`AND `caches`.`default_desclang`=`cache_desc`.`language`
							LEFT JOIN `cache_location` ON &searchtmp.`cache_id`=`cache_location`.`cache_id`
							LEFT JOIN `stat_cache_logs` ON &searchtmp.`cache_id`=`stat_cache_logs`.`cache_id` AND `stat_cache_logs`.`user_id`='&1'", $user_id);

	while ($r = sql_fetch_array($rs))
	{
		if (strlen($r['desc_languages']) > 2)
			$r = get_locale_desc($r);

		$thisline = $gpxLine;

		$lat = sprintf('%01.5f', $r['latitude']);
		$thisline = mb_ereg_replace('{lat}', $lat, $thisline);

		$lon = sprintf('%01.5f', $r['longitude']);
		$thisline = mb_ereg_replace('{lon}', $lon, $thisline);

		$time = date($gpxTimeFormat, strtotime($r['date_hidden']));
		$thisline = mb_ereg_replace('{time}', $time, $thisline);
		$thisline = mb_ereg_replace('{waypoint}', $r['waypoint'], $thisline);
		$thisline = mb_ereg_replace('{cacheid}', $r['cacheid'], $thisline);
		$thisline = mb_ereg_replace('{cachename}', text_xmlentities($r['name']), $thisline);
		$thisline = mb_ereg_replace('{country}', text_xmlentities($r['country_name']), $thisline);
		$thisline = mb_ereg_replace('{state}', text_xmlentities($r['state']), $thisline);

		if ($r['hint'] == '')
			$thisline = mb_ereg_replace('{hints}', '', $thisline);
		else
		{
			// Ocprop:  <groundspeak:encoded_hints>(.*?)<\/groundspeak:encoded_hints>
			$hint = html_entity_decode(strip_tags($r['hint']), ENT_COMPAT, "UTF-8");
			$thisline = mb_ereg_replace('{hints}', '      <groundspeak:encoded_hints>' . text_xmlentities($hint) . '</groundspeak:encoded_hints>
', $thisline);
		}

		$thisline = mb_ereg_replace('{shortdesc}', text_xmlentities($r['short_desc']), $thisline);

		$desc = str_replace(' src="images/uploads/',' src="' . $server_address . 'images/uploads/', $r['desc']);
		$license = getLicenseDisclaimer(
			$r['userid'], $r['username'], $r['data_license'], $r['cacheid'], $opt['template']['locale'], true, true);
		if ($license != "")
			$desc .= "<p><em>$license</em></p>\n";
		$desc .= get_desc_npas($r['cacheid']);
		$thisline = mb_ereg_replace('{desc}', text_xmlentities(decodeEntities($desc)), $thisline);

		$thisline = mb_ereg_replace('{images}', text_xmlentities(getPictures($r['cacheid'],$server_address)), $thisline);

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

		$thisline = mb_ereg_replace('{owner}', text_xmlentities($r['username']), $thisline);
		$thisline = mb_ereg_replace('{userid}', $r['userid'], $thisline);

		if ($r['found'] > 0)
			$thisline = mb_ereg_replace('{sym}', text_xmlentities($gpxSymFound), $thisline);
		else
			$thisline = mb_ereg_replace('{sym}', text_xmlentities($gpxSymNormal), $thisline);

		// clear cache specific data
		$logentries = '';
		$cacheNote = false;
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
				$thislog = mb_ereg_replace('{username}', text_xmlentities($login->username), $thislog);
				$thislog = mb_ereg_replace('{type}', $gpxLogType[3], $thislog);
				$thislog = mb_ereg_replace('{text}', text_xmlentities($cacheNote['note']), $thislog);

				$logentries .= $thislog . "\n";
			}

			// current users logs
			$rsLogs = sql_slave("SELECT `cache_logs`.`id`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username`, `user`.`user_id` FROM `cache_logs`, `user` WHERE `cache_logs`.`user_id`=`user`.`user_id` AND `cache_logs`.`cache_id`=&1 AND `user`.`user_id`=&2 ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`date_created` DESC", $r['cacheid'], $user_id);
			while ($rLog = sql_fetch_array($rsLogs))
			{
				$thislog = $gpxLog;

				$thislog = mb_ereg_replace('{id}', $rLog['id'], $thislog);
				$thislog = mb_ereg_replace('{date}', date($gpxTimeFormat, strtotime($rLog['date'])), $thislog);
				$thislog = mb_ereg_replace('{userid}', $rLog['user_id'], $thislog);
				$thislog = mb_ereg_replace('{username}', text_xmlentities($rLog['username']), $thislog);

				if (isset($gpxLogType[$rLog['type']]))
					$logtype = $gpxLogType[$rLog['type']];
				else
					$logtype = $gpxLogType[0];

				$thislog = mb_ereg_replace('{type}', $logtype, $thislog);
				$thislog = mb_ereg_replace('{text}', text_xmlentities(decodeEntities($rLog['text'])), $thislog);

				$logentries .= $thislog . "\n";
			}
			mysql_free_result($rsLogs);
		}

		// newest 20 logs (except current users)
		$rsLogs = sql_slave("SELECT `cache_logs`.`id`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username`, `user`.`user_id` FROM `cache_logs`, `user` WHERE `cache_logs`.`user_id`=`user`.`user_id` AND `cache_logs`.`cache_id`=&1 AND `user`.`user_id`!=&2 ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`date_created` DESC LIMIT 20", $r['cacheid'], $user_id);
		while ($rLog = sql_fetch_array($rsLogs))
		{
			$thislog = $gpxLog;

			$thislog = mb_ereg_replace('{id}', $rLog['id'], $thislog);
			$thislog = mb_ereg_replace('{date}', date($gpxTimeFormat, strtotime($rLog['date'])), $thislog);
			$thislog = mb_ereg_replace('{userid}', $rLog['user_id'], $thislog);
			$thislog = mb_ereg_replace('{username}', text_xmlentities($rLog['username']), $thislog);

			if (isset($gpxLogType[$rLog['type']]))
				$logtype = $gpxLogType[$rLog['type']];
			else
				$logtype = $gpxLogType[0];

			$thislog = mb_ereg_replace('{type}', $logtype, $thislog);
			$thislog = mb_ereg_replace('{text}', text_xmlentities(decodeEntities($rLog['text'])), $thislog);

			$logentries .= $thislog . "\n";
		}
		mysql_free_result($rsLogs);
		$thisline = mb_ereg_replace('{logs}', $logentries, $thisline);

		// attributes
		$rsAttributes = sql_slave("SELECT `gc_id`, `gc_inc`, `gc_name`
		                             FROM `caches_attributes`
		                       INNER JOIN `cache_attrib` ON `cache_attrib`.`id`=`caches_attributes`.`attrib_id`
		                            WHERE `caches_attributes`.`cache_id`=&1", $r['cacheid']);
		$gc_ids = array();
		while ($rAttrib = sql_fetch_array($rsAttributes))
		{
			// Multiple OC attributes can be mapped to one GC attribute, either with
			// the same "inc"s or with different. Both may disturb applications, so we
			// output each GC ID only once.
			if (!isset($gc_ids[$rAttrib['gc_id']]))
			{
				$thisattribute = mb_ereg_replace('{attrib_id}', $rAttrib['gc_id'], $gpxAttributes);
				$thisattribute = mb_ereg_replace('{attrib_inc}', $rAttrib['gc_inc'], $thisattribute);
				$thisattribute = mb_ereg_replace('{attrib_name}', text_xmlentities($rAttrib['gc_name']), $thisattribute);
				$attribentries .= $thisattribute . "\n";
				$gc_ids[$rAttrib['gc_id']] = true;
			}
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
			$thiskrety = mb_ereg_replace('{gkname}', text_xmlentities($rGK['name']), $thiskrety);

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
			$thiswp = mb_ereg_replace('{cachename}', text_xmlentities($r['name']), $thiswp);
			$thiswp = mb_ereg_replace('{comment}',text_xmlentities($childWaypoint['description']), $thiswp);
			$thiswp = mb_ereg_replace('{desc}', text_xmlentities($childWaypoint['name']), $thiswp);
			switch ($childWaypoint['type'])
			{
				case 1: $wp_typename = "Parking Area"; break;  // well-known garmin symbols
				case 2: $wp_typename = "Flag, Green"; break;   // stage / ref point
				case 3: $wp_typename = "Flag, Blue"; break;    // path
				case 4: $wp_typename = "Circle with X"; break; // final
				case 5: $wp_typename = "Diamond, Green"; break;  // point of interest
				default: $wp_typename = "Flag, Blue"; break;  // for the case new types are forgotten here ..
			}
			$thiswp = mb_ereg_replace('{type}', text_xmlentities($wp_typename), $thiswp);
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
			$thiswp = mb_ereg_replace('{cachename}', text_xmlentities($r['name']), $thiswp);
			$thiswp = mb_ereg_replace('{comment}', text_xmlentities($cacheNote['note']), $thiswp);
			$thiswp = mb_ereg_replace('{desc}', text_xmlentities($cache_note_text), $thiswp);
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
}


	function decodeEntities($str)
	{
		$str = changePlaceholder($str);
		$str = html_entity_decode($str, ENT_COMPAT, "UTF-8");
		$str = changePlaceholder($str, true);
		return $str;
	}

	function changePlaceholder($str, $inverse = false)
	{
		static $translate = array(
			'&lt;' => '{oc-placeholder-lt}',
			'&gt;' => '{oc-placeholder-gt}',
			'&amp;' => '{oc-placeholder-amp}'
		);

		foreach ($translate as $entity => $placeholder)
		{
			if (!$inverse)
				$str = mb_ereg_replace($entity, $placeholder, $str);
			else
				$str = mb_ereg_replace($placeholder, $entity, $str);
		}
		return $str;
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
	function getPictures($cacheid, $server_address)
	{
		$retval = "";
		$rs = sql_slave("SELECT uuid, title, url, spoiler FROM pictures
		                 WHERE object_id='&1' AND object_type=2 AND display=1
                     ORDER BY date_created", $cacheid);

		while ($r = sql_fetch_array($rs))
		{
			$retval .= '<div style="float:left; padding:8px"><a href="' . $r['url'] . '" target="_blank">' .
			           '<img src="' . $server_address . 'thumbs.php?type=2&uuid=' . $r["uuid"]. '" />' .
			           '</a><br />' . $r['title'];
			if ($r['spoiler'])
				$retval .= ' (' . _('click on spoiler to display') . ')';
			$retval .= "</div>";
		}
		mysql_free_result($rs);

		return $retval;
	}

?>
