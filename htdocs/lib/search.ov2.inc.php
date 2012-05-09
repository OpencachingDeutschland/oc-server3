<?php
	/***************************************************************************
															./lib/search.ov2.inc.php
																-------------------
			begin                : November 2 2005 
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
                                   				                                
		ov2 search output
		
	****************************************************************************/

	global $content, $bUseZip, $sqldebug;

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
	sql_slave('CREATE TEMPORARY TABLE `ov2content` ' . $sql . $sqlLimit);

	$rsCount = sql_slave('SELECT COUNT(*) `count` FROM `ov2content`');
	$rCount = sql_fetch_array($rsCount);
	mysql_free_result($rsCount);
	
	if ($rCount['count'] == 1)
	{
		$rsName = sql_slave('SELECT `caches`.`wp_oc` `wp_oc` FROM `ov2content`, `caches` WHERE `ov2content`.`cache_id`=`caches`.`cache_id` LIMIT 1');
		$rName = sql_fetch_array($rsName);
		mysql_free_result($rsName);
		
		$sFilebasename = $rName['wp_oc'];
	}
	else
		$sFilebasename = 'ocde' . $options['queryid'];
		
	$bUseZip = ($rCount['count'] > 20);
	$bUseZip = $bUseZip || (isset($_REQUEST['zip']) ? $_REQUEST['zip'] == '1' : false);
	
	if ($bUseZip == true)
	{
		$content = '';
		require_once($opt['rootpath'] . 'lib/phpzip/ss_zip.class.php');
		$phpzip = new ss_zip('',6);
	}

	// ok, ausgabe starten
	
	if ($sqldebug == false)
	{
		if ($bUseZip == true)
		{
			header("content-type: application/zip");
			header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
		}
		else
		{
			header("Content-type: application/ov2");
			header("Content-Disposition: attachment; filename=" . $sFilebasename . ".ov2");
		}
	}

	// ok, ausgabe ...
	
	/*
		cacheid
		name
		lon
		lat
		
		archivedflag
		type
		size
		difficulty
		terrain
		username
	*/

	$sql = 'SELECT `ov2content`.`cache_id` `cacheid`, `ov2content`.`longitude` `longitude`, `ov2content`.`latitude` `latitude`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`wp_oc` `wp_oc`, `cache_type`.`short` `typedesc`, `cache_size`.`de` `sizedesc`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `user`.`username` `username` FROM `ov2content`, `caches`, `cache_type`, `cache_size`, `user` WHERE `ov2content`.`cache_id`=`caches`.`cache_id` AND `ov2content`.`type`=`cache_type`.`id` AND `ov2content`.`size`=`cache_size`.`id` AND `ov2content`.`user_id`=`user`.`user_id`';
	$rs = sql_slave($sql, $sqldebug);

	while($r = sql_fetch_array($rs))
	{
		$lat = sprintf('%07d', $r['latitude'] * 100000);
		$lon = sprintf('%07d', $r['longitude'] * 100000);
		$name = convert_string($r['name']);
		$username = convert_string($r['username']);
		$type = convert_string($r['typedesc']);
		$size = convert_string($r['sizedesc']);
		$difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
		$terrain = sprintf('%01.1f', $r['terrain'] / 2);
		$cacheid = convert_string($r['wp_oc']);

		$line = "$name by $username, $type, $size, $cacheid";
		$record = pack("CLllA*x", 2, 1 + 4 + 4 + 4 + strlen($line) + 1, (int)$lon, (int)$lat, $line);
		
		append_output($record);
	}
	mysql_free_result($rs);
	
	if ($sqldebug == true) sqldbg_end();
	
	// phpzip versenden
	if ($bUseZip == true)
	{
		$phpzip->add_data($sFilebasename . '.ov2', $content);
		echo $phpzip->save($sFilebasename . '.zip', 'b');
	}

	exit;

	function convert_string($str)
	{
		$newstr = iconv("UTF-8", "ISO-8859-1", $str);
		if ($newstr == false)
			return "--- charset error ---";
		else
			return $newstr;
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