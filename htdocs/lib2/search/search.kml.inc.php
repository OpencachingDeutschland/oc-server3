<?php
	/***************************************************************************
		For license information see doc/license.txt

		Unicode Reminder メモ

		Google KML search output
	****************************************************************************/

	$search_output_file_download = true;
	$content_type_plain = 'vnd.google-earth.kml';
	$content_type_zipped = 'vnd.google-earth.kmz';


function search_output()
{
	global $opt;
	global $state_temporarily_na, $state_archived, $state_locked;

	$kmlLine =
'
<Placemark>
  <description><![CDATA[<a href="'.$opt['page']['absolute_url'].'viewcache.php?cacheid={cacheid}">Beschreibung ansehen</a><br>Von {username}<br>&nbsp;<br><table cellspacing="0" cellpadding="0" border="0"><tr><td>{typeimgurl} </td><td>Art: {type}<br>Größe: {size}</td></tr><tr><td colspan="2">Schwierigkeit: {difficulty} von 5.0<br>Gelände: {terrain} von 5.0</td></tr></table>]]></description>
  <name>{name}{archivedflag}</name>
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
	$style = $opt['template']['style'];
	$kmlDetailHead = file_get_contents("templates2/$style/search.result.caches.kml.head.tpl");

	$rsMinMax = sql_slave('
		SELECT
			MIN(`longitude`) `minlon`,
			MAX(`longitude`) `maxlon`,
			MIN(`latitude`) `minlat`,
			MAX(`latitude`) `maxlat`
		FROM
			&searchtmp');
	$rMinMax = sql_fetch_array($rsMinMax);
	mysql_free_result($rsMinMax);

	$kmlDetailHead = mb_ereg_replace('{minlat}', $rMinMax['minlat'], $kmlDetailHead);
	$kmlDetailHead = mb_ereg_replace('{minlon}', $rMinMax['minlon'], $kmlDetailHead);
	$kmlDetailHead = mb_ereg_replace('{maxlat}', $rMinMax['maxlat'], $kmlDetailHead);
	$kmlDetailHead = mb_ereg_replace('{maxlon}', $rMinMax['maxlon'], $kmlDetailHead);
	$kmlDetailHead = mb_ereg_replace('{time}', date($kmlTimeFormat), $kmlDetailHead);

	append_output($kmlDetailHead);

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

	$rs = sql_slave('
		SELECT SQL_BUFFER_RESULT
			&searchtmp.`cache_id` `cacheid`,
			&searchtmp.`longitude`,
			&searchtmp.`latitude`,
			&searchtmp.`type`,
			`caches`.`date_hidden`,
			`caches`.`name`,
			`caches`.`status`,
			`cache_type`.`de` `typedesc`,
			`cache_size`.`de` `sizedesc`,
			`caches`.`terrain`,
			`caches`.`difficulty`,
			`user`.`username`
		FROM
			&searchtmp,
			`caches`,
			`cache_type`,
			`cache_size`,
			`user`
		WHERE
			&searchtmp.`cache_id`=`caches`.`cache_id` AND
			&searchtmp.`type`=`cache_type`.`id` AND
			&searchtmp.`size`=`cache_size`.`id` AND
			&searchtmp.`user_id`=`user`.`user_id`');

	while ($r = sql_fetch_array($rs))
	{
		$thisline = $kmlLine;

		// icon suchen
		switch ($r['type'])
		{
			case 2:
				$icon = 'tradi';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/'.$style.'ocstyle/images/cacheicon/traditional.gif" alt="Normaler Cache" title="Normaler Cache" />';
				break;
			case 3:
				$icon = 'multi';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/'.$style.'/images/cacheicon/multi.gif" alt="Multicache" title="Multicache" />';
				break;
			case 4:
				$icon = 'virtual';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/'.$style.'/images/cacheicon/virtual.gif" alt="virtueller Cache" title="virtueller Cache" />';
				break;
			case 5:
				$icon = 'webcam';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/'.$style.'/images/cacheicon/webcam.gif" alt="Webcam Cache" title="Webcam Cache" />';
				break;
			case 6:
				$icon = 'event';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/'.$style.'/images/cacheicon/event.gif" alt="Event Cache" title="Event Cache" />';
				break;
			case 7:
				$icon = 'mystery';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/'.$style.'/images/cacheicon/mystery.gif" alt="Rätselcache" title="Event Cache" />';
				break;
			case 8:
				$icon = 'mathe';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/'.$style.'/images/cacheicon/mathe.gif" alt="Mathe-/Physik-Cache" title="Event Cache" />';
				break;
			case 9:
				$icon = 'moving';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/'.$style.'/images/cacheicon/moving.gif" alt="Moving Cache" title="Event Cache" />';
				break;
			case 10:
				$icon = 'drivein';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/'.$style.'/images/cacheicon/drivein.gif" alt="Drive-In Cache" title="Event Cache" />';
				break;
			default:
				$icon = 'other';
				$typeimgurl = '<img src="http://www.opencaching.de/resource2/'.$style.'/images/cacheicon/unknown.gif" alt="unbekannter Cachetyp" title="unbekannter Cachetyp" />';
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

		if (($r['status'] == 2) || ($r['status'] == 3) || ($r['status'] == 6))
		{
			if ($r['status'] == 2)
				$thisline = mb_ereg_replace('{archivedflag}', ' ('.$state_temporarily_na.')', $thisline);
			elseif ($r['status'] == 3)
				$thisline = mb_ereg_replace('{archivedflag}', ' ('.$state_archived.')', $thisline);
			else
				$thisline = mb_ereg_replace('{archivedflag}', ' ('.$state_locked.')', $thisline);
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
}


?>