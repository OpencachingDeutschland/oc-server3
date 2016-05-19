<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

// statistical data for cache and log activity map
// optional script to be released locally into htdocs/api/stat

$opt['rootpath'] = __DIR__ . '/../../htdocs/';
require $opt['rootpath'] . 'lib2/web.inc.php';

error_reporting(error_reporting() & ~E_NOTICE);

$grid = $_GET['grid'];
if ($grid <= 0) {
    $grid = 0.2;
}

// caches created by year
$rs = sql('SELECT latitude, longitude, date_created FROM caches');
while ($cache = sql_fetch_assoc($rs)) {
    $lat = floor($cache['latitude'] / $grid);
    $long = floor($cache['longitude'] / $grid);
    $year = substr($cache['date_created'], 0, 4);
    if ($year >= 2005 && $year <= date("Y") && ($lat != 0 || $long != 0)) {
        $years[$year] = true;
        $liste[$lat][$long]["caches"][$year] ++;
    }
}
mysql_free_result($rs);

// logs per logdate by year
get_logs("cache_logs");
// get_logs("cache_logs_archived");

function get_logs($table)
{
    global $grid, $liste, $years;

    $rs = sql(
        "SELECT latitude, longitude, date
         FROM $table
         INNER JOIN caches ON $table.cache_id=caches.cache_id"
    );
    while ($cache = sql_fetch_assoc($rs)) {
        $lat = floor($cache["latitude"] / $grid);
        $long = floor($cache["longitude"] / $grid);
        $year = substr($cache["date"], 0, 4);
        if ($year >= 2005 && $year <= date("Y") && ($lat != 0 || $long != 0)) {
            $years[$year] = true;
            $liste[$lat][$long]["logs"][$year] ++;
        }
    }
    mysql_free_result($rs);
}

ksort($years);

// active caches and logs
$rs = sql(
    'SELECT latitude, longitude,
     (SELECT COUNT(*) FROM cache_logs WHERE cache_logs.cache_id=caches.cache_id) AS logs
     FROM caches WHERE status=1'
);
while ($cache = sql_fetch_assoc($rs)) {
    $lat = floor($cache["latitude"] / $grid);
    $long = floor($cache["longitude"] / $grid);
    $liste[$lat][$long]["caches"]["all"] ++;
    $liste[$lat][$long]["logs"]["all"] += $cache["logs"];
}
mysql_free_result($rs);

ksort($liste);
$lats = array_keys($liste);
foreach ($lats as $lat) {
    ksort($liste[$lat]);
}

// create output CSV data
header('Content-type: application/comma-separated-value');
header('Content-Disposition: attachment; filename="cachestat.csv"');
echo 'latitude,longitude,caches,logs';
foreach ($years as $year => $dummy) {
    echo ",caches$year,logs$year";
}
echo "\r\n";

foreach ($liste as $lat => $liste2) {
    foreach ($liste2 as $long => $cachecounts) {
        echo ($lat * $grid + 0.5 * $grid) . ',' . ($long * $grid + 0.5 * $grid) . ',' .
            $cachecounts['caches']['all'] . ',' .
            $cachecounts['logs']['all'];
        foreach ($years as $year => $dummy) {
            echo ',' . $cachecounts["caches"][$year] . ',' . $cachecounts["logs"][$year];
        }
        echo "\r\n";
    }
}
