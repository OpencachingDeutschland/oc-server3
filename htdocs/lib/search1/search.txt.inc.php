<?php

use \OpenCachingDE\Conversions\Coordinates;

	/***************************************************************************
		For license information see doc/license.txt

		Unicode Reminder メモ
                              				                                
		GPX search output
	****************************************************************************/

	$search_output_file_download = true;
	$content_type_plain = 'text/plain';
	$zip_threshold = 1;
	$add_to_zipfile = false;


function search_output()
{
	global $absolute_server_URI, $locale;
	global $converted_from_html;
	global $phpzip, $bUseZip;

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

	$rs = sql_slave('
		SELECT SQL_BUFFER_RESULT
			`searchtmp`.`cache_id` `cacheid`,
			`searchtmp`.`longitude` `longitude`,
			`searchtmp`.`latitude` `latitude`,
			`caches`.`wp_oc` `waypoint`,
			`caches`.`date_hidden` `date_hidden`,
			`caches`.`name` `name`,
			`caches`.`country` `country`,
			`caches`.`terrain` `terrain`,
			`caches`.`difficulty` `difficulty`,
			`caches`.`desc_languages` `desc_languages`,
			`cache_size`.`de` `size`,
			`cache_type`.`de` `type`,
			`cache_status`.`de` `status`,
			`user`.`username` `username`,
			`cache_desc`.`desc` `desc`,
			`cache_desc`.`short_desc` `short_desc`,
			`cache_desc`.`hint` `hint`,
			`cache_desc`.`desc_html` `html`,
			`user`.`user_id`,
			`user`.`username`,
			`user`.`data_license`
		FROM
			`searchtmp`,
			`caches`,
			`user`,
			`cache_desc`,
			`cache_type`,
			`cache_status`,
			`cache_size`
		WHERE
			`searchtmp`.`cache_id`=`caches`.`cache_id` AND
			`caches`.`cache_id`=`cache_desc`.`cache_id` AND
			`caches`.`default_desclang`=`cache_desc`.`language` AND
			`searchtmp`.`user_id`=`user`.`user_id` AND
			`caches`.`type`=`cache_type`.`id` AND
			`caches`.`status`=`cache_status`.`id` AND
			`caches`.`size`=`cache_size`.`id`');

	while ($r = sql_fetch_array($rs))
	{
		$thisline = $txtLine;
		
		$lat = sprintf('%01.5f', $r['latitude']);
		$thisline = mb_ereg_replace('{lat}', Coordinates::latToDegreeStr($lat), $thisline);
		
		$lon = sprintf('%01.5f', $r['longitude']);
		$thisline = mb_ereg_replace('{lon}', Coordinates::lonToDegreeStr($lon), $thisline);

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
			$thisline = mb_ereg_replace('{htmlwarn}', " ($converted_from_html)", $thisline);
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
				$thislog = mb_ereg_replace('{text}', decodeEntities(strip_tags($rLog['text'])), $thislog);
			else
				$thislog = mb_ereg_replace('{text}', html2txt($rLog['text']), $thislog);

			$logentries .= $thislog . "\n";
		}
		$thisline = mb_ereg_replace('{logs}', $logentries, $thisline);

		$thisline = lf2crlf($thisline);
		if (($rCount['count'] == 1) && !$bUseZip)
			echo $thisline;
		else
		{
			$phpzip->add_data($r['waypoint'] . '.txt', $thisline);
		}
	}
	mysql_free_result($rs);
}
	

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