<?php
	/***************************************************************************
															./lib/search.ovl.inc.php
																-------------------
			begin                : November 5 2005 
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
                                				                                
		ovl search output for TOP25, TOP50 etc.
		
	****************************************************************************/

	global $content, $bUseZip, $sqldebug;

	$ovlLine = "[Symbol {symbolnr1}]\r\nTyp=6\r\nGroup=1\r\nWidth=20\r\nHeight=20\r\nDir=100\r\nArt=1\r\nCol=3\r\nZoom=1\r\nSize=103\r\nArea=2\r\nXKoord={lon}\r\nYKoord={lat}\r\n[Symbol {symbolnr2}]\r\nTyp=2\r\nGroup=1\r\nCol=3\r\nArea=1\r\nZoom=1\r\nSize=130\r\nFont=1\r\nDir=100\r\nXKoord={lonname}\r\nYKoord={latname}\r\nText={cachename}\r\n";
	$ovlFoot = "[Overlay]\r\nSymbols={symbolscount}\r\n";

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
	$sql .= '`caches`.`cache_id` `cache_id`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`,
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
	sql_slave('CREATE TEMPORARY TABLE `ovlcontent` ' . $sql . $sqlLimit, $sqldebug);

	$rsCount = sql_slave('SELECT COUNT(*) `count` FROM `ovlcontent`');
	$rCount = sql_fetch_array($rsCount);
	mysql_free_result($rsCount);
	
	if ($rCount['count'] == 1)
	{
		$rsName = sql_slave('SELECT `caches`.`wp_oc` `wp_oc` FROM `ovlcontent`, `caches` WHERE `ovlcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
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
			header("Content-type: application/ovl");
			header("Content-Disposition: attachment; filename=" . $sFilebasename . ".ovl");
		}
	}

	// ok, ausgabe ...
	
/*
	{symbolnr1}
	{lon}
	{lat}
	{symbolnr2}
	{lonname}
	{latname}
	{cachename}
*/

	$nr = 1;
	$rs = sql_slave('SELECT SQL_BUFFER_RESULT `ovlcontent`.`cache_id` `cacheid`, `ovlcontent`.`longitude` `longitude`, `ovlcontent`.`latitude` `latitude`, `caches`.`name` `name` FROM `ovlcontent`, `caches` WHERE `ovlcontent`.`cache_id`=`caches`.`cache_id`');
	while($r = sql_fetch_array($rs))
	{
		$thisline = $ovlLine;
		
		$lat = sprintf('%01.5f', $r['latitude']);
		$thisline = mb_ereg_replace('{lat}', $lat, $thisline);
		$thisline = mb_ereg_replace('{latname}', $lat, $thisline);
		
		$lon = sprintf('%01.5f', $r['longitude']);
		$thisline = mb_ereg_replace('{lon}', $lon, $thisline);
		$thisline = mb_ereg_replace('{lonname}', $lon, $thisline);

		$thisline = mb_ereg_replace('{cachename}', convert_string($r['name']), $thisline);
		$thisline = mb_ereg_replace('{symbolnr1}', $nr, $thisline);
		$thisline = mb_ereg_replace('{symbolnr2}', $nr + 1, $thisline);

		append_output($thisline);
		$nr += 2;
	}
	mysql_free_result($rs);
	
	$ovlFoot = mb_ereg_replace('{symbolscount}', $nr - 1, $ovlFoot);
	append_output($ovlFoot);
	
	if ($sqldebug == true) sqldbg_end();
	
	// phpzip versenden
	if ($bUseZip == true)
	{
		$phpzip->add_data($sFilebasename . '.ovl', $content);
		echo $phpzip->save($sFilebasename . '.zip', 'b');
	}

	exit;
	
	function convert_string($str)
	{
		$newstr = iconv("UTF-8", "ISO-8859-1", $str);
		if ($newstr == false)
			return $str;
		else
			return $newstr;
	}
	
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