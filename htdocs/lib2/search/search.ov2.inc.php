<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * OV2 search output
 ****************************************************************************/

require_once $opt['rootpath'] . 'lib2/charset.inc.php';

$search_output_file_download = true;
$content_type_plain = 'application/ov2';


function search_output()
{
    global $sqldebug;

    /*
        cacheid
        name
        latitude
        longitude
        type
        size
        difficulty
        terrain
        username
        waypoint
    */

    $sql = '
        SELECT
            &searchtmp.`cache_id` `cacheid`,
            &searchtmp.`longitude`,
            &searchtmp.`latitude`,
            `caches`.`name`,
            `caches`.`wp_oc`,
            `caches`.`terrain`,
            `caches`.`difficulty`,
            `cache_type`.`short` `typedesc`,
            `cache_size`.`name` `sizedesc`,
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
            &searchtmp.`user_id`=`user`.`user_id`';

    $rs = sql_slave($sql, $sqldebug);

    while ($r = sql_fetch_array($rs)) {
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
}


function convert_string($str)
{
    return utf8ToIso88591($str);
}
