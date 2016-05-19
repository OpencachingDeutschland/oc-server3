<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * Google KML search output
 ****************************************************************************/

$search_output_file_download = true;
$content_type_plain = 'vnd.google-earth.kml';
$content_type_zipped = 'vnd.google-earth.kmz';

function search_output()
{
    global $opt;
    global $state_temporarily_na, $state_archived, $state_locked;
    global $t_showdesc, $t_by, $t_type, $t_size, $t_difficulty, $t_terrain;

    // see also util2/google-earth/caches.php
    $kmlLine =
        '
<Placemark>
  <description><![CDATA[' . $t_by . ' {username}<br><br><a href="' . $opt['page']['default_absolute_url'] . 'viewcache.php?cacheid={cacheid}">' . $t_showdesc . '</a><br>&nbsp;<br><table cellspacing="0" cellpadding="0" border="0"><tr><td>{typeimgurl} </td><td>' . $t_type . ' {type}<br>' . $t_size . ' {size}</td></tr><tr><td colspan="2">' . $t_difficulty . '<br>' . $t_terrain . '</td></tr></table>]]></description>
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
  <Snippet>D: {difficulty}/T: {terrain} {size}  ' . $t_by . ' {username}</Snippet>
</Placemark>
';

    $kmlFoot = '</Folder></Document></kml>';

    $kmlTimeFormat = 'Y-m-d\TH:i:s\Z';
    $style = $opt['template']['style'];
    $kmlDetailHead = file_get_contents("resource2/misc/google-earth/search.result.caches.kml.head.xml");
    $kmlDetailHead = mb_ereg_replace("{site_url}", $opt['page']['default_absolute_url'], $kmlDetailHead);

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

    $rs = sql_slave(
        "
        SELECT SQL_BUFFER_RESULT
            &searchtmp.`cache_id` `cacheid`,
            &searchtmp.`longitude`,
            &searchtmp.`latitude`,
            `caches`.`date_hidden`,
            `caches`.`name`,
            `caches`.`status`,
            IFNULL(`stt_type`.`text`, `cache_type`.`en`) `typedesc`,
            `cache_type`.`kml_name`,
            `cache_type`.`icon_large`,
            IFNULL(`stt_size`.`text`, `cache_size`.`en`) `sizedesc`,
            `caches`.`terrain`,
            `caches`.`difficulty`,
            `user`.`username`
        FROM &searchtmp
        JOIN `caches` ON &searchtmp.`cache_id`=`caches`.`cache_id`
        JOIN `cache_type` ON `caches`.`type`=`cache_type`.`id`
        JOIN `cache_size` ON `caches`.`size`=`cache_size`.`id`
        LEFT JOIN `user` ON &searchtmp.`user_id`=`user`.`user_id`
        LEFT JOIN `sys_trans_text` `stt_type` ON `stt_type`.`trans_id`=`cache_type`.`trans_id` AND `stt_type`.`lang`='&1'
        LEFT JOIN `sys_trans_text` `stt_size` ON `stt_size`.`trans_id`=`cache_size`.`trans_id` AND `stt_size`.`lang`='&1'
        ",
        $opt['template']['locale']
    );

    while ($r = sql_fetch_array($rs)) {
        $thisline = $kmlLine;
        $typeimgurl = '<img src="{urlbase}resource2/' . $style . '/images/cacheicon/' . $r['icon_large'] . '" alt="' . $r['typedesc'] . '" title="' . $r['typedesc'] . '" />';

        $thisline = mb_ereg_replace('{icon}', $r['kml_name'], $thisline);
        $thisline = mb_ereg_replace('{typeimgurl}', $typeimgurl, $thisline);

        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = mb_ereg_replace('{lat}', $lat, $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = mb_ereg_replace('{lon}', $lon, $thisline);

        $time = date($kmlTimeFormat, strtotime($r['date_hidden']));
        $thisline = mb_ereg_replace('{time}', $time, $thisline);

        $thisline = mb_ereg_replace('{name}', xmlentities($r['name']), $thisline);

        if (($r['status'] == 2) || ($r['status'] == 3) || ($r['status'] == 6)) {
            if ($r['status'] == 2) {
                $thisline = mb_ereg_replace('{archivedflag}', ' (' . $state_temporarily_na . ')', $thisline);
            } elseif ($r['status'] == 3) {
                $thisline = mb_ereg_replace('{archivedflag}', ' (' . $state_archived . ')', $thisline);
            } else {
                $thisline = mb_ereg_replace('{archivedflag}', ' (' . $state_locked . ')', $thisline);
            }
        } else {
            $thisline = mb_ereg_replace('{archivedflag}', '', $thisline);
        }

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

        append_output($thisline);
    }
    mysql_free_result($rs);

    append_output($kmlFoot);
}
