<?php
	/***************************************************************************
															./lib/search.gpx.inc.php
																-------------------
			begin                : November 1 2005 

		For license information see doc/license.txt
	****************************************************************************/

	/****************************************************************************
		           
		Unicode Reminder メモ
                              				                                
		GPX search output
		
	****************************************************************************/

	global $content, $bUseZip, $sqldebug, $locale;

	$txtLine = "Name: {cachename} von {owner}
Koordinaten: {lon} {lat}
Status: {status}

Versteckt am: {time}
Wegpunkt: {waypoint}
Land: {country}
Cacheart: {type}
Behälter: {container}
D/T: {difficulty}/{terrain}
Online: " . $absolute_server_URI . "viewcache.php?wp={waypoint}

Kurzbeschreibung: {shortdesc}

Beschreibung{htmlwarn}:
<===================>
{desc}
<===================>

Zusätzliche Hinweise:
<===================>
{hints}
<===================>
A|B|C|D|E|F|G|H|I|J|K|L|M
N|O|P|Q|R|S|T|U|V|W|X|Y|Z

Logeinträge:
{logs}
";

	$txtLogs = "<===================>
{username} / {date} / {type}

{text}
";

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
	$sql .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, 
	            `caches`.`size` `size`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, 
	            `caches`.`user_id` `user_id`,
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
	sql_slave('CREATE TEMPORARY TABLE `txtcontent` ' . $sql . $sqlLimit, $sqldebug);

	$rsCount = sql_slave('SELECT COUNT(*) `count` FROM `txtcontent`');
	$rCount = sql_fetch_array($rsCount);
	mysql_free_result($rsCount);
	
	if ($rCount['count'] == 1)
	{
		$rsName = sql_slave('SELECT `caches`.`wp_oc` `wp_oc` FROM `txtcontent`, `caches` WHERE `txtcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
		$rName = sql_fetch_array($rsName);
		mysql_free_result($rsName);
		
		$sFilebasename = $rName['wp_oc'];
	}
	else
		$sFilebasename = 'ocde' . $options['queryid'];

	$bUseZip = ($rCount['count'] > 1);
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
			header("Content-type: text/plain");
			header("Content-Disposition: attachment; filename=" . $sFilebasename . ".txt");
		}
	}

	// ok, ausgabe ...
	
	$rs = sql_slave('SELECT SQL_BUFFER_RESULT `txtcontent`.`cache_id` `cacheid`, `txtcontent`.`longitude` `longitude`, `txtcontent`.`latitude` `latitude`, `caches`.`wp_oc` `waypoint`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`country` `country`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `caches`.`desc_languages` `desc_languages`, `cache_size`.`de` `size`, `cache_type`.`de` `type`, `cache_status`.`de` `status`, `user`.`username` `username`, `cache_desc`.`desc` `desc`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`hint` `hint`, `cache_desc`.`desc_html` `html`, `user`.`user_id`, `user`.`username`, `user`.`data_license` FROM `txtcontent`, `caches`, `user`, `cache_desc`, `cache_type`, `cache_status`, `cache_size` WHERE `txtcontent`.`cache_id`=`caches`.`cache_id` AND `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`default_desclang`=`cache_desc`.`language` AND `txtcontent`.`user_id`=`user`.`user_id` AND `caches`.`type`=`cache_type`.`id` AND `caches`.`status`=`cache_status`.`id` AND `caches`.`size`=`cache_size`.`id`');
	while($r = sql_fetch_array($rs))
	{
		$thisline = $txtLine;
		
		$lat = sprintf('%01.5f', $r['latitude']);
		$thisline = mb_ereg_replace('{lat}', help_latToDegreeStr($lat), $thisline);
		
		$lon = sprintf('%01.5f', $r['longitude']);
		$thisline = mb_ereg_replace('{lon}', help_lonToDegreeStr($lon), $thisline);

		$time = date('d.m.Y', strtotime($r['date_hidden']));
		$thisline = mb_ereg_replace('{time}', $time, $thisline);
		$thisline = mb_ereg_replace('{waypoint}', $r['waypoint'], $thisline);
		$thisline = mb_ereg_replace('{cacheid}', $r['cacheid'], $thisline);
		$thisline = mb_ereg_replace('{cachename}', $r['name'], $thisline);
		$thisline = mb_ereg_replace('{country}', db_CountryFromShort($r['country']), $thisline);
		
		if ($r['hint'] == '')
			$thisline = mb_ereg_replace('{hints}', '', $thisline);
		else
			$thisline = mb_ereg_replace('{hints}', str_rot13_html(decodeEntities(strip_tags($r['hint']))), $thisline);
		
		$thisline = mb_ereg_replace('{shortdesc}', $r['short_desc'], $thisline);
		
		$license = getLicenseDisclaimer(
			$r['user_id'], $r['username'], $r['data_license'], $r['cacheid'], $locale, true, false, true);
		if ($license != "")
			$license = "\r\n\r\n$license";

		if ($r['html'] == 0)
		{
			$thisline = mb_ereg_replace('{htmlwarn}', '', $thisline);
			$thisline = mb_ereg_replace('{desc}', decodeEntities(strip_tags($r['desc'])) . $license, $thisline);
		}
		else
		{
			$thisline = mb_ereg_replace('{htmlwarn}', ' (Vorsicht, aus HTML konvertiert)', $thisline);
			$thisline = mb_ereg_replace('{desc}', html2txt($r['desc']) . $license, $thisline);
		}
		
		$thisline = mb_ereg_replace('{type}', $r['type'], $thisline);
		$thisline = mb_ereg_replace('{container}', $r['size'], $thisline);
		$thisline = mb_ereg_replace('{status}', $r['status'], $thisline);
		
		$difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
		$thisline = mb_ereg_replace('{difficulty}', $difficulty, $thisline);

		$terrain = sprintf('%01.1f', $r['terrain'] / 2);
		$thisline = mb_ereg_replace('{terrain}', $terrain, $thisline);

		$thisline = mb_ereg_replace('{owner}', $r['username'], $thisline);

		// logs ermitteln
		$logentries = '';
		$rsLogs = sql_slave("SELECT `cache_logs`.`id`, `cache_logs`.`text_html`, `log_types`.`de` `type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username` FROM `cache_logs`, `user`, `log_types` WHERE `cache_logs`.`user_id`=`user`.`user_id` AND `cache_logs`.`type`=`log_types`.`id` AND `cache_logs`.`cache_id`=&1 ORDER BY `cache_logs`.`date` DESC LIMIT 20", $r['cacheid']);
		while ($rLog = sql_fetch_array($rsLogs))
		{
			$thislog = $txtLogs;
			
			$thislog = mb_ereg_replace('{id}', $rLog['id'], $thislog);
			if (substr($rLog['date'],11) == "00:00:00")
				$dateformat = "d.m.Y";
			else
				$dateformat = "d.m.Y H:i";
			$thislog = mb_ereg_replace('{date}', date($dateformat, strtotime($rLog['date'])), $thislog);
			$thislog = mb_ereg_replace('{username}', $rLog['username'], $thislog);
			
			$logtype = $rLog['type'];
			
			$thislog = mb_ereg_replace('{type}', $logtype, $thislog);
			if ($rLog['text_html'] == 0)
				$thislog = mb_ereg_replace('{text}', strip_tags($rLog['text']), $thislog);
			else
				$thislog = mb_ereg_replace('{text}', html2txt($rLog['text']), $thislog);

			$logentries .= $thislog . "\n";
		}
		$thisline = mb_ereg_replace('{logs}', $logentries, $thisline);

		$thisline = lf2crlf($thisline);

		if (($rCount['count'] == 1) && ($bUseZip == false))
			echo $thisline;
		else
		{
			$phpzip->add_data($r['waypoint'] . '.txt', $thisline);
		}
	}
	mysql_free_result($rs);
	
	if ($sqldebug == true) sqldbg_end();
	
	// phpzip versenden
	if ($bUseZip == true)
	{
		echo $phpzip->save($sFilebasename . '.zip', 'b');
	}

	exit;

	function decodeEntities($str)
	{
		$str = html_entity_decode($str, ENT_COMPAT, "UTF-8");
		return $str;
	}

	function html2txt($html)
	{
		$str = mb_ereg_replace("\r\n", '', $html);
		$str = mb_ereg_replace("\n", '', $str);
		$str = mb_ereg_replace('<br />', "\n", $str);
		$str = strip_tags($str);
		$str = decodeEntities($str);
		return $str;
	}
	
	function lf2crlf($str)
	{
		return mb_ereg_replace("\r\r\n" ,"\r\n" , mb_ereg_replace("\n" ,"\r\n" , $str));
	}
?>