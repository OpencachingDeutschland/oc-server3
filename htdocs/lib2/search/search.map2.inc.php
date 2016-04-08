<?php
/***************************************************************************
 *    For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Execute search / filtering request for map2.php
 *  (use caching of the same quries)
 ***************************************************************************/

if (!$enable_mapdisplay) {
    $tpl->error(ERROR_INVALID_OPERATION);
}

$sqlchecksum = sprintf('%u', crc32($cachesFilter . "\n" . $sqlFilter));

// check if query was already executed within the cache period
$rsMapCache = sql(
    "
    SELECT `result_id`
    FROM `map2_result`
    WHERE `sqlchecksum`='&1'
    AND DATE_ADD(`date_created`, INTERVAL '&2' SECOND)>NOW()
    AND `sqlquery`='&3'",
    $sqlchecksum,
    $opt['map']['maxcacheage'],
    $sqlFilter
);
if ($rMapCache = sql_fetch_assoc($rsMapCache)) {
    $resultId = $rMapCache['result_id'];
    sql("UPDATE `map2_result` SET `shared_counter`=`shared_counter`+1 WHERE `result_id`='" . ($resultId + 0) . "'");
} else {
    // ensure that query is performed without errors before reserving the result_id
    sql_temp_table_slave('tmpmapresult');
    sql_slave("CREATE TEMPORARY TABLE &tmpmapresult (`cache_id` INT UNSIGNED NOT NULL, PRIMARY KEY (`cache_id`)) ENGINE=MEMORY");
    sql_slave("INSERT INTO &tmpmapresult (`cache_id`) " . $sqlFilter);

    sql(
        "
        INSERT INTO `map2_result` (`slave_id`, `sqlchecksum`, `sqlquery`, `date_created`, `date_lastqueried`)
        VALUES ('&1', '&2', '&3', NOW(), NOW())",
        $db['slave_id'],
        $sqlchecksum,
        $cachesFilter . "\n" . $sqlFilter
    );
    $resultId = sql_insert_id();

    sql_slave(
        "INSERT IGNORE INTO `map2_data` (`result_id`, `cache_id`) SELECT '&1', `cache_id` FROM &tmpmapresult",
        $resultId
    );
    sql_drop_temp_table_slave('tmpmapresult');
}
sql_free_result($rsMapCache);

if ($map2_bounds) {
    $rs = sql_slave(
        "SELECT MIN(`latitude`) AS `lat_min`,
                MAX(`latitude`) AS `lat_max`,
                MIN(`longitude`) AS `lon_min`,
                MAX(`longitude`) AS `lon_max`
         FROM `map2_data`, `caches`
         WHERE `result_id`='&1'
         AND `caches`.`cache_id`=`map2_data`.`cache_id`",
        $resultId
    );
    if (($rBounds = sql_fetch_assoc($rs)) && $rBounds['lat_min'] !== null /* >0 caches */) {
        if ($rBounds['lat_min'] == $rBounds['lat_max'] &&
            $rBounds['lon_min'] == $rBounds['lon_max']
        ) { // 1 Cache
            $halfwin = 0.02;
            $rBounds['lat_min'] -= $halfwin;
            $rBounds['lat_max'] += $halfwin;
            $rBounds['lon_min'] -= $halfwin;
            $rBounds['lon_max'] += $halfwin;
        }
        $bounds_param = "&lat_min=" . round($rBounds['lat_min'], 5) . "&lat_max=" . round($rBounds['lat_max'], 5) . '&lon_min=' . round($rBounds['lon_min'], 5) . '&lon_max=' . round($rBounds['lon_max'], 5);
    }
    sql_free_result($rs);

    $tpl->redirect('map2.php?queryid=' . $options['queryid'] . '&resultid=' . $resultId . $bounds_param);
} else {
    echo $resultId;
}

exit;
