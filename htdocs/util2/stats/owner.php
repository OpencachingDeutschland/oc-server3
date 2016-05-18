<?php
/***************************************************************************
 *    For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

header('Content-type: text/html; charset=utf-8');

$opt['rootpath'] = '../../';
require $opt['rootpath'] . 'lib2/web.inc.php';

echo "<h2>Top-Owner</h2>\n";

showstats("min. 50 aktive Caches", "TRUE", 50);
showstats("min. 50 aktive Dosen", "`caches`.`size`<>7 AND TRUE", 50);
showstats("min. 10 aktive OConlies", "`ca`.`attrib_id` IS NOT NULL", 10);
showstats("min. 10 aktive OConly-Dosen", "`caches`.`size`<>7 AND `ca`.`attrib_id` IS NOT NULL", 10);


function showstats($header, $condition, $limit)
{
    echo "<h3>$header</h3>\n";
    echo "<table>\n";

    $rs = sql(
        "SELECT COUNT(*) as `count`, `username` as `name`
        FROM `caches`
        LEFT JOIN `user` ON `user`.`user_id`=`caches`.`user_id`
        LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6
        WHERE status=1 AND " . $condition . "
        GROUP BY `caches`.`user_id`
        HAVING COUNT(*) >= $limit
        ORDER BY COUNT(*) DESC"
    );

    $n = 1;
    while ($r = sql_fetch_assoc($rs)) {
        echo "  <tr><td style='text-align:right'>&nbsp;&nbsp;" . ($n ++) . ".&nbsp;&nbsp;&nbsp;</td><td style='text-align:right'>" . $r['count'] . "</td><td>&nbsp;&nbsp;" . $r['name'] . "</td></tr>\n";
    }
    sql_free_result($rs);

    echo "</table>\n";
}
