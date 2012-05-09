<?php
	/***************************************************************************
															./lib/search.kml.inc.php
																-------------------
			begin                : November 1 2005 
			copyright            : (C) 2005 The OpenCaching Group
			forum contact at     : http://www.opencaching.com/phpBB2

		***************************************************************************/

	/***************************************************************************
		*                                         				                                
		*   This program is free software; you can redistribute it and/or modify  	
		*   it under the terms of the GNU General Public License as published by  
		*   the Free Software Foundation; either version 2 of the License, or	    	
		*   (at your option) any later version.
		*
		***************************************************************************/

	/****************************************************************************
		   
		Unicode Reminder メモ
                                      				                                
		kml search output
		
	****************************************************************************/

	global $content, $bUseZip, $sqldebug;
	
	$kmlLine = 
'
<Placemark>
  <description><![CDATA[<a href="http://www.opencaching.de/viewcache.php?cacheid={cacheid}">Beschreibung ansehen</a><br>Von {username}<br>&nbsp;<br><table cellspacing="0" cellpadding="0" border="0"><tr><td>{typeimgurl} </td><td>Art: {type}<br>Größe: {size}</td></tr><tr><td colspan="2">Schwierigkeit: {difficulty} von 5.0<br>Gelände: {terrain} von 5.0</td></tr></table>]]></description>
  <name>{name}</name>
  <LookAt>
    <longitude>{lon}</longitude>
    <latitude>{lat}</latitude>
    <range>5000</range>
    <tilt>0</tilt>
    <heading>3</heading>
  </LookAt>
  <styleUrl>#{icon}</styleUrl>
  <Point>
    <coordinates>{lon},{lat},0</coordinates>
  </Point>
  <Snippet>D: {difficulty}/T: {terrain} {size}  von {username}</Snippet>
</Placemark>
';

	$kmlFoot = '</Folder></Document></kml>';

	$kmlTimeFormat = 'Y-m-d\TH:i:s\Z';

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
	            IF(IFNULL(`stat_caches`.`toprating`,0)>3, 4, IFNULL(`stat_caches`.`toprating`, 0)) `ratingvalue`' . 
		          $sAddField
		 . ' FROM `caches`
	  LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id`' .
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

	// temporäre tabelle erstellen
	sql_slave('CREATE TEMPORARY TABLE `kmlcontent` ' . $sql . $sqlLimit);

	$rsCount = sql_slave('SELECT COUNT(*) `count` FROM `kmlcontent`');
	$rCount = sql_fetch_array($rsCount);
	mysql_free_result($rsCount);
	
	if ($rCount['count'] == 1)
	{
		$rsName = sql_slave('SELECT `caches`.`wp_oc` `wp_oc` FROM `kmlcontent`, `caches` WHERE `kmlcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
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

	// ok, ausgabe starten
	
	if ($sqldebug == false)
	{
		if ($bUseZip == true)
		{
			header("Content-Type: application/vnd.google-earth.kmz");
			header('Content-Disposition: attachment; filename=' . $sFilebasename . '.kmz');
		}
		else
		{
			header("Content-Type: application/vnd.google-earth.kml");
			header("Content-Disposition: attachment; filename=" . $sFilebasename . ".kml");
		}
	}

	$kmlDetailHead = read_file($stylepath . '/search.result.caches.kml.head.tpl.php');
	
	$rsMinMax = sql_slave('SELECT MIN(`longitude`) `minlon`, MAX(`longitude`) `maxlon`, MIN(`latitude`) `minlat`, MAX(`latitude`) `maxlat` FROM `kmlcontent`', $sqldebug);
	$rMinMax = sql_fetch_array($rsMinMax);
	mysql_free_result($rsMinMax);
	
	$kmlDetailHead = mb_ereg_replace('{minlat}', $rMinMax['minlat'], $kmlDetailHead);
	$kmlDetailHead = mb_ereg_replace('{minlon}', $rMinMax['minlon'], $kmlDetailHead);
	$kmlDetailHead = mb_ereg_replace('{maxlat}', $rMinMax['maxlat'], $kmlDetailHead);
	$kmlDetailHead = mb_ereg_replace('{maxlon}', $rMinMax['maxlon'], $kmlDetailHead);
	$kmlDetailHead = mb_ereg_replace('{time}', date($kmlTimeFormat), $kmlDetailHead);
	
	append_output($kmlDetailHead);

	// ok, ausgabe ...
	
	/*
		wp
		name
		username
		type
		size
		lon
		lat
		icon
	*/

	$rs = sql_slave('SELECT SQL_BUFFER_RESULT `kmlcontent`.`cache_id` `cacheid`, `kmlcontent`.`longitude` `longitude`, `kmlcontent`.`latitude` `latitude`, `kmlcontent`.`type` `type`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`status` `status`, `cache_type`.`de` `typedesc`, `cache_size`.`de` `sizedesc`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` FROM `kmlcontent`, `caches`, `cache_type`, `cache_size`, `user` WHERE `kmlcontent`.`cache_id`=`caches`.`cache_id` AND `kmlcontent`.`type`=`cache_type`.`id` AND `kmlcontent`.`size`=`cache_size`.`id` AND `kmlcontent`.`user_id`=`user`.`user_id`', $sqldebug);
	while($r = sql_fetch_array($rs))
	{
		$thisline = $kmlLine;
		
		// icon suchen
		switch ($r['type'])
		{
			case 2:
				$icon = 'tradi';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/traditional.gif" alt="Normaler Cache" title="Normaler Cache" />';
				break;
			case 3:
				$icon = 'multi';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/multi.gif" alt="Multicache" title="Multicache" />';
				break;
			case 4:
				$icon = 'virtual';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/virtual.gif" alt="virtueller Cache" title="virtueller Cache" />';
				break;
			case 5:
				$icon = 'webcam';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/webcam.gif" alt="Webcam Cache" title="Webcam Cache" />';
				break;
			case 6:
				$icon = 'event';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/event.gif" alt="Event Cache" title="Event Cache" />';
				break;
			case 7:
				$icon = 'mystery';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/mystery.gif" alt="Rätselcache" title="Event Cache" />';
				break;
			case 8:
				$icon = 'mathe';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/mathe.gif" alt="Mathe-/Physik-Cache" title="Event Cache" />';
				break;
			case 9:
				$icon = 'moving';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/moving.gif" alt="Moving Cache" title="Event Cache" />';
				break;
			case 10:
				$icon = 'drivein';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/drivein.gif" alt="Drive-In Cache" title="Event Cache" />';
				break;
			default:
				$icon = 'other';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/ocstyle/images/cacheicon/unknown.gif" alt="unbekannter Cachetyp" title="unbekannter Cachetyp" />';
				break;
		}
		$thisline = mb_ereg_replace('{icon}', $icon, $thisline);
		$thisline = mb_ereg_replace('{typeimgurl}', $typeimgurl, $thisline);
		
		$lat = sprintf('%01.5f', $r['latitude']);
		$thisline = mb_ereg_replace('{lat}', $lat, $thisline);
		
		$lon = sprintf('%01.5f', $r['longitude']);
		$thisline = mb_ereg_replace('{lon}', $lon, $thisline);

		$time = date($kmlTimeFormat, strtotime($r['date_hidden']));
		$thisline = mb_ereg_replace('{time}', $time, $thisline);

		$thisline = mb_ereg_replace('{name}', xmlentities($r['name']), $thisline);
		
		if (($r['status'] == 2) || ($r['status'] == 3))
		{
			if ($r['status'] == 2)
				$thisline = mb_ereg_replace('{archivedflag}', 'Momentan nicht verfügbar!, ', $thisline);
			else
				$thisline = mb_ereg_replace('{archivedflag}', 'Archiviert!, ', $thisline);
		}
		else
			$thisline = mb_ereg_replace('{archivedflag}', '', $thisline);
		
		$thisline = mb_ereg_replace('{type}', xmlentities($r['typedesc']), $thisline);
		$thisline = mb_ereg_replace('{size}', xmlentities($r['sizedesc']), $thisline);
		
		$difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
		$thisline = mb_ereg_replace('{difficulty}', $difficulty, $thisline);

		$terrain = sprintf('%01.1f', $r['terrain'] / 2);
		$thisline = mb_ereg_replace('{terrain}', $terrain, $thisline);

		$time = date($kmlTimeFormat, strtotime($r['date_hidden']));
		$thisline = mb_ereg_replace('{time}', $time, $thisline);

		$thisline = mb_ereg_replace('{username}', xmlentities($r['username']), $thisline);
		$thisline = mb_ereg_replace('{cacheid}', xmlentities($r['cacheid']), $thisline);

		append_output($thisline);
	}
	mysql_free_result($rs);
	
	append_output($kmlFoot);
	
	if ($sqldebug == true) sqldbg_end();
	
	// phpzip versenden
	if ($bUseZip == true)
	{
		$phpzip->add_data($sFilebasename . '.kml', $content);
		// use 'r'=raw instead of 'b'=browser: don't generate new header information!
		echo $phpzip->save($sFilebasename . '.kmz', 'r');
	}

	exit;
	
	function xmlentities($str)
	{
		$from[0] = '&'; $to[0] = '&amp;';
		$from[1] = '<'; $to[1] = '&lt;';
		$from[2] = '>'; $to[2] = '&gt;';
		$from[3] = '"'; $to[3] = '&quot;';
		$from[4] = '\''; $to[4] = '&apos;';
		
		for ($i = 0; $i <= 4; $i++)
			$str = mb_ereg_replace($from[$i], $to[$i], $str);

		return $str;
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