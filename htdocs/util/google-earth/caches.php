<?php
  /*
		Unicode Reminder ..

    BBOX=2.38443,45.9322,20.7053,55.0289
  */

  $opt['rootpath'] = '../../';
  header('Content-type: text/html; charset=utf-8');
  require($opt['rootpath'] . 'lib2/web.inc.php');

  $bbox = isset($_REQUEST['BBOX']) ? $_REQUEST['BBOX'] : '0,0,0,0';
  $abox = mb_split(',', $bbox);

  if (count($abox) != 4) exit;

  if (!is_numeric($abox[0])) exit;
  if (!is_numeric($abox[1])) exit;
  if (!is_numeric($abox[2])) exit;
  if (!is_numeric($abox[3])) exit;

  $lat_from = $abox[1];
  $lon_from = $abox[0];
  $lat_to = $abox[3];
  $lon_to = $abox[2];

  /*
   kml processing
  */

  $kmlLine = 
'
<Placemark>
  <description><![CDATA[<a href="{urlbase}viewcache.php?cacheid={cacheid}">Beschreibung ansehen</a><br>Von {username}<br>&nbsp;<br><table cellspacing="0" cellpadding="0" border="0"><tr><td>{typeimgurl} </td><td>Art: {type}<br>Gr&ouml;&szlig;e: {size}</td></tr><tr><td colspan="2">Schwierigkeit: {difficulty} von 5.0<br>Gel&auml;nde: {terrain} von 5.0</td></tr></table>]]></description>
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
</Placemark>
';

  $kmlHead =
'<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.0">
	<Document>
		<Style id="tradi">
			<IconStyle>
				<Icon>
					<href>{urlbase}resource2/stdstyle/images/google-earth/tradi.png</href>
				</Icon>
			</IconStyle>
		</Style>
		<Style id="multi">
			<IconStyle>
				<Icon>
					<href>{urlbase}resource2/stdstyle/images/google-earth/multi.png</href>
				</Icon>
			</IconStyle>
		</Style>
		<Style id="myst">
			<IconStyle>
				<Icon>
					<href>{urlbase}resource2/stdstyle/images/google-earth/myst.png</href>
				</Icon>
			</IconStyle>
		</Style>
		<Style id="math">
			<IconStyle>
				<Icon>
					<href>{urlbase}resource2/stdstyle/images/google-earth/math.png</href>
				</Icon>
			</IconStyle>
		</Style>
		<Style id="drivein">
			<IconStyle>
				<Icon>
					<href>{urlbase}resource2/stdstyle/images/google-earth/drivein.png</href>
				</Icon>
			</IconStyle>
		</Style>
		<Style id="virtual">
			<IconStyle>
				<Icon>
					<href>{urlbase}resource2/stdstyle/images/google-earth/virtual.png</href>
				</Icon>
			</IconStyle>
		</Style>
		<Style id="webcam">
			<IconStyle>
				<Icon>
					<href>{urlbase}resource2/stdstyle/images/google-earth/webcam.png</href>
				</Icon>
			</IconStyle>
		</Style>
		<Style id="event">
			<IconStyle>
				<Icon>
					<href>{urlbase}resource2/stdstyle/images/google-earth/event.png</href>
				</Icon>
			</IconStyle>
		</Style>
		<Style id="moving">
			<IconStyle>
				<Icon>
					<href>{urlbase}resource2/stdstyle/images/google-earth/moving.png</href>
				</Icon>
			</IconStyle>
		</Style>
		<Style id="unknown">
			<IconStyle>
				<Icon>
					<href>{urlbase}resource2/stdstyle/images/google-earth/unknown.png</href>
				</Icon>
			</IconStyle>
		</Style>
		<Folder>
			<name>Geocaches (Opencaching)</name>
			<open>0</open>
			';

	$kmlFoot = '
			</Folder>
		</Document>
	</kml>';

	$kmlTimeFormat = 'Y-m-d\TH:i:s\Z';

	//  header("Content-type: application/vnd.google-earth.kml");
	//  header("Content-Disposition: attachment; filename=ge.kml");

	echo mb_ereg_replace('{urlbase}', xmlentities($opt['page']['absolute_url']), $kmlHead);

	if ((abs($lon_from - $lon_to) > 2) || (abs($lat_from - $lat_to) > 2))
	{
		echoZoomIn($lon_from, $lon_to, $lat_from, $lat_to);
	}
	else
	{
		$rs = sql("SELECT `caches`.`cache_id` AS `cacheid`, `caches`.`longitude` AS `longitude`, `caches`.`latitude` AS `latitude`, `caches`.`type` AS `type`, `caches`.`date_hidden` AS `date_hidden`, `caches`.`name` AS `name`, `cache_type`.`de` AS `typedesc`, `cache_size`.`de` AS `sizedesc`, `caches`.`terrain` AS `terrain`, `caches`.`difficulty` AS `difficulty`, `user`.`username` AS `username`
		             FROM `caches`
		       INNER JOIN `cache_type` ON `caches`.`type`=`cache_type`.`id`
		       INNER JOIN `cache_size` ON `caches`.`size`=`cache_size`.`id`
		       INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
		            WHERE `caches`.`status`=1 AND
		                  `caches`.`longitude`>='&1' AND 
											`caches`.`longitude`<='&2' AND 
											`caches`.`latitude`>='&3' AND 
											`caches`.`latitude`<='&4'",
											$lon_from, $lon_to, $lat_from, $lat_to);

		$nCount = 0;
		while ($r = sql_fetch_array($rs))
		{
			$nCount = $nCount + 1;
			$thisline = $kmlLine;
			
			// icon suchen
			switch ($r['type'])
			{
				case 2:
					$icon = 'tradi';
					$typeimgurl = '<img src="{urlbase}lang/de/stdstyle/images/cache/traditional.gif" alt="Normaler Cache" title="Normaler Cache" />';
					break;
				case 3:
					$icon = 'multi';
					$typeimgurl = '<img src="{urlbase}lang/de/stdstyle/images/cache/multi.gif" alt="Multicache" title="Multicache" />';
					break;
				case 4:
					$icon = 'virtual';
					$typeimgurl = '<img src="{urlbase}lang/de/stdstyle/images/cache/virtual.gif" alt="virtueller Cache" title="virtueller Cache" />';
					break;
				case 5:
					$icon = 'webcam';
					$typeimgurl = '<img src="{urlbase}lang/de/stdstyle/images/cache/webcam.gif" alt="Webcam Cache" title="Webcam Cache" />';
					break;
				case 6:
					$icon = 'event';
					$typeimgurl = '<img src="{urlbase}lang/de/stdstyle/images/cache/event.gif" alt="Event Cache" title="Event Cache" />';
					break;
				case 7:
					$icon = 'myst';
					$typeimgurl = '<img src="{urlbase}lang/de/stdstyle/images/cache/mystery.gif" alt="Event Cache" title="Event Cache" />';
					break;
				case 8:
					$icon = 'math';
					$typeimgurl = '<img src="{urlbase}lang/de/stdstyle/images/cache/mathe.gif" alt="Event Cache" title="Event Cache" />';
					break;
				case 9:
					$icon = 'moving';
					$typeimgurl = '<img src="{urlbase}lang/de/stdstyle/images/cache/moving.gif" alt="Event Cache" title="Event Cache" />';
					break;
				case 10:
					$icon = 'drivein';
					$typeimgurl = '<img src="{urlbase}lang/de/stdstyle/images/cache/drivein.gif" alt="Event Cache" title="Event Cache" />';
					break;
				default:
					$icon = 'unknown';
					$typeimgurl = '<img src="{urlbase}lang/de/stdstyle/images/cache/unknown.gif" alt="unbekannter Cachetyp" title="unbekannter Cachetyp" />';
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
					$thisline = mb_ereg_replace('{archivedflag}', 'Momentan nicht verf&uuml;gbar', $thisline);
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

			$thisline = mb_ereg_replace('{urlbase}', xmlentities($opt['page']['absolute_url']), $thisline);

			echo $thisline;
		}
		sql_free_result($rs);
	}

  echo $kmlFoot;
  exit;

function echoZoomIn($lon_from, $lon_to, $lat_from, $lat_to)
{
	$nColumnsCount = 60;
	$sZoomIn =
	'

		
		
		
		
           #######  #######  #######  #     #
                #   #     #  #     #  ##   ##
               #    #     #  #     #  # # # #
             #      #     #  #     #  #  #  #
            #       #     #  #     #  #     #
           #        #     #  #     #  #     #
           #######  #######  #######  #     #


                      ###  #     #
                       #   ##    #
                       #   # #   #
                       #   #  #  #
                       #   #   # #
                       #   #    ##
                      ###  #     #

		
		
		
		
';

	// prepare lines
	$sZoomIn = str_replace("\r", "", $sZoomIn);
	$sLines = split("\n", $sZoomIn);
	for ($i = 0; $i < count($sLines); $i++)
		$sLines[$i] = str_pad($sLines[$i], ($nColumnsCount-1), ' ');

	$nDegreePerLine = ($lat_to - $lat_from) / count($sLines);
	$nDegreePerColumn = ($lon_to - $lon_from) / $nColumnsCount;

	for ($nLine = 0; $nLine < count($sLines); $nLine++)
	{
		for ($nColumn = 0; $nColumn < $nColumnsCount; $nColumn++)
		{
			if (substr($sLines[$nLine], $nColumn, 1) == '#')
			{
				$nLat = $lat_to - $nDegreePerLine * $nLine;
				$nLon = $lon_from + $nDegreePerColumn * $nColumn;
				
				echo '
				<Placemark>
					<description><![CDATA[You have to zoom in to see the Geocaches]]></description>
					<name></name>
					<LookAt>
						<longitude>' . $nLon . '</longitude>
						<latitude>' . $nLat . '</latitude>
						<range>5000</range>
						<tilt>0</tilt>
						<heading>3</heading>
					</LookAt>
					<Point>
						<coordinates>' . $nLon . ',' . $nLat . ',0</coordinates>
					</Point>
				</Placemark>
				';
				
			}
		}
	}
}

?>
