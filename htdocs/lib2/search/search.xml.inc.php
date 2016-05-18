<?php

/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 *
 * XML search output
 *
 * This is a very old and primitive API for downloading cache data.
 * It is available on all OC sites and presented in this way:
 *
 * OC.de fork:  undocumented, not referenced anywhere
 * OC.pl fork:  linked on the search results page
 * OC.cz fork:  well documented: http://www.opencaching.cz/articles.php?page=api
 ****************************************************************************/

$search_output_file_download = false;


function search_output()
{
    global $db, $opt;
    global $distance_unit, $startat, $count, $sql, $sqlLimit;

    $encoding = 'UTF-8';

    $xmlLine = "    <cache>
        <name><![CDATA[{cachename}]]></name>
        <owner id=\"{ownerid}\"><![CDATA[{owner}]]></owner>
        <id>{cacheid}</id>
        <waypoint>{waypoint}</waypoint>
        <hidden>{time}</hidden>
        <status id=\"{statusid}\">{status}</status>
        <lon value=\"{lonvalue}\">{lon}</lon>
        <lat value=\"{latvalue}\">{lat}</lat>
        <distance unit=\"" . $distance_unit . "\">{distance}</distance>
        <type id=\"{typeid}\">{type}</type>
        <difficulty>{difficulty}</difficulty>
        <terrain>{terrain}</terrain>
        <size id=\"{sizeid}\">{container}</size>
        <country id=\"{countryid}\">{country}</country>
        <link><![CDATA[" . $opt['page']['default_absolute_url'] . "viewcache.php?wp={waypoint}]]></link>
        <desc><![CDATA[{shortdesc}]]></desc>
        <hints><![CDATA[{hints}]]></hints>
    </cache>
";

    // create temporary table
    sql_temp_table_slave('searchtmp');
    sql_slave('CREATE TEMPORARY TABLE &searchtmp SELECT SQL_BUFFER_RESULT SQL_CALC_FOUND_ROWS ' . $sql . $sqlLimit);

    $resultcount = sql_value_slave('SELECT FOUND_ROWS()', 0);

    $rsCount = sql_slave('SELECT COUNT(*) `count` FROM &searchtmp');
    $rCount = sql_fetch_array($rsCount);
    mysql_free_result($rsCount);

    // start output
    if (!$db['debug']) {
        header("Content-type: application/xml; charset=" . $encoding);
        //header("Content-Disposition: attachment; filename=" . $sFilebasename . ".txt");

        echo "<?xml version=\"1.0\" encoding=\"" . $encoding . "\"?>\n";
        echo "<result>\n";

        echo "    <docinfo>\n";
        echo "        <results>" . $rCount['count'] . "</results>\n";
        echo "        <startat>" . $startat . "</startat>\n";
        echo "        <perpage>" . $count . "</perpage>\n";
        echo "        <total>" . $resultcount . "</total>\n";
        echo "    </docinfo>\n";
    }

    $rs = sql_slave(
        "SELECT &searchtmp.`cache_id` `cacheid`,
                    &searchtmp.`longitude` `longitude`,
                    &searchtmp.`latitude` `latitude`,
                    `caches`.`wp_oc` `waypoint`,
                    `caches`.`date_hidden` `date_hidden`,
                    `caches`.`name` `name`,
                    `caches`.`country` `countrycode`,
                    `caches`.`terrain` `terrain`,
                    `caches`.`difficulty` `difficulty`,
                    `caches`.`desc_languages` `desc_languages`,
                    `cache_size`.`name` `size`,
                    `cache_size`.`id` `size_id`,
                    `cache_type`.`name` `type`,
                    `cache_type`.`id` `type_id`,
                    `cache_status`.`name` `status`,
                    `cache_status`.`id` `status_id`,
                    `user`.`username` `username`,
                    `user`.`user_id` `user_id`,
                    `cache_desc`.`desc` `desc`,
                    `cache_desc`.`short_desc` `short_desc`,
                    `cache_desc`.`hint` `hint`,
                    `cache_desc`.`desc_html` `html`,
                    &searchtmp.`distance` `distance`,
                    `sys_trans_text`.`text` `country`
         FROM &searchtmp
         INNER JOIN `caches` ON &searchtmp.`cache_id`=`caches`.`cache_id`
         INNER JOIN `user` ON &searchtmp.`user_id`=`user`.`user_id`
         INNER JOIN `cache_desc`
             ON `caches`.`cache_id`=`cache_desc`.`cache_id`
             AND `caches`.`default_desclang`=`cache_desc`.`language`
         INNER JOIN `cache_type` ON `caches`.`type`=`cache_type`.`id`
         INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
         INNER JOIN `cache_size` ON `caches`.`size`=`cache_size`.`id`
         LEFT JOIN `countries` ON `countries`.`short`=`caches`.`country`
         LEFT JOIN `sys_trans_text` ON `sys_trans_text`.`trans_id`=`countries`.`trans_id`
         AND `sys_trans_text`.`lang`='&1'",
        $opt['template']['locale']
    );

    while ($r = sql_fetch_array($rs)) {
        if (strlen($r['desc_languages']) > 2) {
            $r = get_locale_desc($r);
        }

        $thisline = $xmlLine;

        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = str_replace('{lat}', help_latToDegreeStr($lat), $thisline);
        $thisline = str_replace('{latvalue}', $lat, $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = str_replace('{lon}', help_lonToDegreeStr($lon), $thisline);
        $thisline = str_replace('{lonvalue}', $lon, $thisline);

        $time = date('d.m.Y', strtotime($r['date_hidden']));
        $thisline = str_replace('{time}', $time, $thisline);
        $thisline = str_replace('{waypoint}', $r['waypoint'], $thisline);
        $thisline = str_replace('{cacheid}', $r['cacheid'], $thisline);
        $thisline = str_replace('{cachename}', filterevilchars($r['name']), $thisline);
        $thisline = str_replace('{country}', text_xmlentities($r['country']), $thisline);
        $thisline = str_replace('{countryid}', $r['country'], $thisline);

        if ($r['hint'] == '') {
            $thisline = str_replace('{hints}', '', $thisline);
        } else {
            $thisline = str_replace(
                '{hints}',
                str_rot13_gc(decodeEntities(filterevilchars(strip_tags($r['hint'])))),
                $thisline
            );
        }

        $thisline = str_replace('{shortdesc}', filterevilchars($r['short_desc']), $thisline);

        if ($r['html'] == 0) {
            $thisline = str_replace('{htmlwarn}', '', $thisline);
            $thisline = str_replace('{desc}', filterevilchars(strip_tags($r['desc'])), $thisline);
        } else {
            $thisline = str_replace('{htmlwarn}', ' (Text converted from HTML)', $thisline);
            $thisline = str_replace('{desc}', html2txt(filterevilchars($r['desc'])), $thisline);
        }

        $thisline = str_replace('{type}', $r['type'], $thisline);
        $thisline = str_replace('{typeid}', $r['type_id'], $thisline);
        $thisline = str_replace('{container}', $r['size'], $thisline);
        $thisline = str_replace('{sizeid}', $r['size_id'], $thisline);
        $thisline = str_replace('{status}', $r['status'], $thisline);
        $thisline = str_replace('{statusid}', $r['status_id'], $thisline);

        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $thisline = str_replace('{difficulty}', $difficulty, $thisline);

        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $thisline = str_replace('{terrain}', $terrain, $thisline);

        $thisline = str_replace('{owner}', filterevilchars($r['username']), $thisline);
        $thisline = str_replace('{ownerid}', filterevilchars($r['user_id']), $thisline);
        $thisline = str_replace('{distance}', text_xmlentities(sprintf("%01.1f", $r['distance'])), $thisline);

        $thisline = lf2crlf($thisline);

        if (!$db['debug']) {
            echo $thisline;
        }
    }
    mysql_free_result($rs);
    sql_drop_temp_table_slave('searchtmp');

    if (!$db['debug']) {
        echo "</result>\n";
    }
}


function decodeEntities($str)
{
    return html_entity_decode($str, ENT_COMPAT, "UTF-8");
}

function html2txt($html)
{
    $str = str_replace("\r\n", '', $html);
    $str = str_replace("\n", '', $str);
    $str = str_replace('<br />', "\n", $str);
    $str = strip_tags($str);

    return $str;
}

function lf2crlf($str)
{
    return str_replace("\r\r\n", "\r\n", str_replace("\n", "\r\n", $str));
}

function filterevilchars($str)
{
    $evilchars = array(
        31 => 31,
        30 => 30,
        29 => 29,
        28 => 28,
        27 => 27,
        26 => 26,
        25 => 25,
        24 => 24,
        23 => 23,
        22 => 22,
        21 => 21,
        20 => 20,
        19 => 19,
        18 => 18,
        17 => 17,
        16 => 16,
        15 => 15,
        14 => 14,
        12 => 12,
        11 => 11,
        9 => 9,
        8 => 8,
        7 => 7,
        6 => 6,
        5 => 5,
        4 => 4,
        3 => 3,
        2 => 2,
        1 => 1,
        0 => 0
    );

    foreach ($evilchars as $ascii) {
        $str = str_replace(chr($ascii), '', $str);
    }

    $str = preg_replace('/&([a-zA-Z]{1})caron;/', '\\1', $str);
    $str = preg_replace('/&([a-zA-Z]{1})acute;/', '\\1', $str);

    return $str;
}
