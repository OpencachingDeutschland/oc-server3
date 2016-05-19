<?php
/*
      For license information see doc/license.txt

      Unicode Reminder メモ

  BBOX=2.38443,45.9322,20.7053,55.0289
*/

header('Content-type: text/html; charset=utf-8');

$opt['rootpath'] = '../../';
require $opt['rootpath'] . 'lib2/web.inc.php';
require $opt['rootpath'] . 'templates2/ocstyle/search.tpl.inc.php';

$bbox = isset($_REQUEST['BBOX']) ? $_REQUEST['BBOX'] : '0,0,0,0';
$abox = mb_split(',', $bbox);

if (count($abox) != 4) {
    exit;
}

if (!is_numeric($abox[0])) {
    exit;
}
if (!is_numeric($abox[1])) {
    exit;
}
if (!is_numeric($abox[2])) {
    exit;
}
if (!is_numeric($abox[3])) {
    exit;
}

$lat_from = $abox[1];
$lon_from = $abox[0];
$lat_to = $abox[3];
$lon_to = $abox[2];

/*
 kml processing
*/

// see also lib2/search/search.kml.inc.php
$kmlLine =
    '
<Placemark>
  <description><![CDATA[' . $t_by . ' {username}<br><br><a href="{urlbase}viewcache.php?cacheid={cacheid}">' . $t_showdesc . '</a><br>&nbsp;<br><table cellspacing="0" cellpadding="0" border="0"><tr><td>{typeimgurl} </td><td>' . $t_type . ' {type}<br>' . $t_size . ' {size}</td></tr><tr><td colspan="2">' . $t_difficulty . '<br>' . $t_terrain . '</td></tr></table>]]></description>
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

// see also resource2/misc/google-earth/search.result.caches.kml.head.xml
$kmlHead =
    '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.0">
    <Document>
        <Style id="tradi">
            <IconStyle>
                <scale>1</scale>
                <Icon>
                    <href>{urlbase}resource2/ocstyle/images/map/caches2/cachetype-2.png</href>
                </Icon>
            </IconStyle>
            <LabelStyle>
                <scale>0.6</scale>
            </LabelStyle>
        </Style>
        <Style id="multi">
            <IconStyle>
                <scale>1</scale>
                <Icon>
                    <href>{urlbase}resource2/ocstyle/images/map/caches2/cachetype-3.png</href>
                </Icon>
            </IconStyle>
            <LabelStyle>
                <scale>0.6</scale>
            </LabelStyle>
        </Style>
        <Style id="virtual">
            <IconStyle>
                <scale>1</scale>
                <Icon>
                    <href>{urlbase}resource2/ocstyle/images/map/caches2/cachetype-4.png</href>
                </Icon>
            </IconStyle>
            <LabelStyle>
                <scale>0.6</scale>
            </LabelStyle>
        </Style>
        <Style id="webcam">
            <IconStyle>
                <scale>1</scale>
                <Icon>
                    <href>{urlbase}resource2/ocstyle/images/map/caches2/cachetype-5.png</href>
                </Icon>
            </IconStyle>
            <LabelStyle>
                <scale>0.6</scale>
            </LabelStyle>
        </Style>
        <Style id="event">
            <IconStyle>
                <scale>1</scale>
                <Icon>
                    <href>{urlbase}resource2/ocstyle/images/map/caches2/cachetype-6.png</href>
                </Icon>
            </IconStyle>
            <LabelStyle>
                <scale>0.6</scale>
            </LabelStyle>
        </Style>
        <Style id="mystery">
            <IconStyle>
                <scale>1</scale>
                <Icon>
                    <href>{urlbase}resource2/ocstyle/images/map/caches2/cachetype-7.png</href>
                </Icon>
            </IconStyle>
            <LabelStyle>
                <scale>0.6</scale>
            </LabelStyle>
        </Style>
        <Style id="mathe">
            <IconStyle>
                <scale>1</scale>
                <Icon>
                    <href>{urlbase}resource2/ocstyle/images/map/caches2/cachetype-8.png</href>
                </Icon>
            </IconStyle>
            <LabelStyle>
                <scale>0.6</scale>
            </LabelStyle>
        </Style>
        <Style id="drivein">
            <IconStyle>
                <scale>1</scale>
                <Icon>
                    <href>{urlbase}resource2/ocstyle/images/map/caches2/cachetype-10.png</href>
                </Icon>
            </IconStyle>
            <LabelStyle>
                <scale>0.6</scale>
            </LabelStyle>
        </Style>
        <Style id="moving">
            <IconStyle>
                <scale>1</scale>
                q<Icon>
                    <href>{urlbase}resource2/ocstyle/images/map/caches2/cachetype-9.png</href>
                </Icon>
            </IconStyle>
            <LabelStyle>
                <scale>0.6</scale>
            </LabelStyle>
        </Style>
        <Style id="unknown">
            <IconStyle>
                <scale>1</scale>
                <Icon>
                    <href>{urlbase}resource2/ocstyle/images/map/caches2/cachetype-1.png</href>
                </Icon>
            </IconStyle>
            <LabelStyle>
                <scale>0.6</scale>
            </LabelStyle>
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

echo mb_ereg_replace('{urlbase}', xmlentities($opt['page']['default_absolute_url']), $kmlHead);

if ((abs($lon_from - $lon_to) > 2) || (abs($lat_from - $lat_to) > 2)) {
    echoZoomIn($lon_from, $lon_to, $lat_from, $lat_to);
} else {
    $rs = sql(
        "SELECT `caches`.`cache_id` AS `cacheid`,
                          `caches`.`longitude` AS `longitude`,
                          `caches`.`latitude` AS `latitude`,
                          `caches`.`type` AS `type`,
                          `caches`.`status`,
                          `caches`.`date_hidden` AS `date_hidden`,
                          `caches`.`name` AS `name`,
                          IFNULL(`stt_type`.`text`, `cache_type`.`en`) `typedesc`,
                          `cache_type`.`kml_name`,
                          `cache_type`.`icon_large`,
                          IFNULL(`stt_size`.`text`, `cache_size`.`en`) `sizedesc`,
                          `caches`.`terrain` AS `terrain`,
                          `caches`.`difficulty` AS `difficulty`,
                          `user`.`username` AS `username`
                     FROM `caches`
               INNER JOIN `cache_type` ON `caches`.`type`=`cache_type`.`id`
               INNER JOIN `cache_size` ON `caches`.`size`=`cache_size`.`id`
               INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
                LEFT JOIN `sys_trans_text` `stt_type` ON `stt_type`.`trans_id`=`cache_type`.`trans_id`
                LEFT JOIN `sys_trans_text` `stt_size` ON `stt_size`.`trans_id`=`cache_size`.`trans_id`
                    WHERE `caches`.`status`=1 AND
                          `caches`.`longitude`>='&1' AND
                                            `caches`.`longitude`<='&2' AND
                                            `caches`.`latitude`>='&3' AND
                                            `caches`.`latitude`<='&4' AND
                                            `stt_type`.`lang`='&5' AND `stt_size`.`lang`='&5'",
        $lon_from,
        $lon_to,
        $lat_from,
        $lat_to,
        $opt['template']['locale']
    );

    $nCount = 0;
    while ($r = sql_fetch_array($rs)) {
        $nCount = $nCount + 1;
        $thisline = $kmlLine;

        $typeimgurl = '<img src="' . $opt['page']['default_absolute_url'] . 'resource2/' . $opt['template']['style'] . '/images/cacheicon/' . $r['icon_large'] . '" alt="' . $r['typedesc'] . '" title="' . $r['typedesc'] . '" />';

        $thisline = mb_ereg_replace('{icon}', $r['kml_name'], $thisline);
        $thisline = mb_ereg_replace('{typeimgurl}', $typeimgurl, $thisline);

        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = mb_ereg_replace('{lat}', $lat, $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = mb_ereg_replace('{lon}', $lon, $thisline);

        $time = date($kmlTimeFormat, strtotime($r['date_hidden']));
        $thisline = mb_ereg_replace('{time}', $time, $thisline);

        $thisline = mb_ereg_replace('{name}', xmlentities($r['name']), $thisline);

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

        $thisline = mb_ereg_replace('{urlbase}', xmlentities($opt['page']['default_absolute_url']), $thisline);

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
    $sLines = mb_split("\n", $sZoomIn);
    for ($i = 0; $i < count($sLines); $i ++) {
        $sLines[$i] = str_pad($sLines[$i], ($nColumnsCount - 1), ' ');
    }

    $nDegreePerLine = ($lat_to - $lat_from) / count($sLines);
    $nDegreePerColumn = ($lon_to - $lon_from) / $nColumnsCount;

    for ($nLine = 0; $nLine < count($sLines); $nLine ++) {
        for ($nColumn = 0; $nColumn < $nColumnsCount; $nColumn ++) {
            if (substr($sLines[$nLine], $nColumn, 1) == '#') {
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
