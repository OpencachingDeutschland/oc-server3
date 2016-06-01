<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * lOC search output
 ****************************************************************************/

$search_output_file_download = true;
$content_type_plain = 'application/loc';


function search_output()
{
    global $opt;
    global $state_temporarily_na, $state_archived, $state_locked;

    $server_domain = $opt['page']['domain'];
    $server_address = $opt['page']['default_absolute_url'];

    $locHead = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><loc version="1.0" src="' . $server_domain . '">' . "\n";

    $locLine =
        '<waypoint>
    <name id="{waypoint}"><![CDATA[{archivedflag}{name} by {username}]]></name>
    <coord lat="{lat}" lon="{lon}"/>
    <type>Geocache</type>
    <link text="Beschreibung">' . $server_address . 'viewcache.php?cacheid={cacheid}</link>
</waypoint>
';

    $locFoot = '</loc>';

    append_output($locHead);

    /*
        {waypoint}
        status -> {archivedflag}
        {name}
        {username}
        {lon}
        {lat}
        {cacheid}
    */

    $rs = sql_slave(
        '
        SELECT SQL_BUFFER_RESULT
            &searchtmp.`cache_id` `cacheid`,
            &searchtmp.`longitude`,
            &searchtmp.`latitude`,
            `caches`.`name`,
            `caches`.`status`,
            `caches`.`wp_oc` `waypoint`,
            `user`.`username` `username`
        FROM
            &searchtmp,
            `caches`,
            `user`
        WHERE
            &searchtmp.`cache_id`=`caches`.`cache_id` AND
            &searchtmp.`user_id`=`user`.`user_id`'
    );

    while ($r = sql_fetch_array($rs)) {
        $thisline = $locLine;

        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = mb_ereg_replace('{lat}', $lat, $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = mb_ereg_replace('{lon}', $lon, $thisline);

        $thisline = mb_ereg_replace('{waypoint}', $r['waypoint'], $thisline);
        $thisline = mb_ereg_replace('{name}', $r['name'], $thisline);

        if (($r['status'] == 2) || ($r['status'] == 3) || ($r['status'] == 6)) {
            if ($r['status'] == 2) {
                $thisline = mb_ereg_replace('{archivedflag}', $state_temporarily_na . '!, ', $thisline);
            } elseif ($r['status'] == 3) {
                $thisline = mb_ereg_replace('{archivedflag}', $state_archived . '!, ', $thisline);
            } else {
                $thisline = mb_ereg_replace('{archivedflag}', $state_locked . '!, ', $thisline);
            }
        } else {
            $thisline = mb_ereg_replace('{archivedflag}', '', $thisline);
        }

        $thisline = mb_ereg_replace('{username}', $r['username'], $thisline);
        $thisline = mb_ereg_replace('{cacheid}', $r['cacheid'], $thisline);

        append_output($thisline);
    }
    mysql_free_result($rs);

    append_output($locFoot);
}
