<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Due to a floating point precision problem in the DB row editor, up to
 *  commit 42c1f54 (October 2012) duplicate entries were inserted into
 *  table cache_coordinates. This script cleans them up. It may be used
 *  later to check for new duplicate problems.
 *
 *  Fixes http://redmine.opencaching.de/issues/943
 *
 ***************************************************************************/

$opt['rootpath'] = __DIR__ . '/../../htdocs/';
require $opt['rootpath'] . 'lib2/cli.inc.php';

$rs = sql(
    'SELECT `id`, `cache_id`, `latitude`, `longitude`
     FROM `cache_coordinates`
     ORDER BY `cache_id`, `date_created`'
);
$duplicates = [];
$last_cache_id = null;

while ($r = sql_fetch_assoc($rs)) {
    $latRounded = round($r['latitude'], 6);
    $longRounded = round($r['longitude'], 6);

    if ($r['cache_id'] === $last_cache_id) {
        if ($latRounded == $lastLatitude && $longRounded == $lastLongitude) {
            $duplicates[] = $r['id'];
        }
    } else {
        $last_cache_id = $r['cache_id'];
    }
    $lastLatitude = $latRounded;
    $lastLongitude = $longRounded;
}
sql_free_result($rs);

if ($duplicates) {
    if ($argc == 2 && $argv[1] == 'go') {
        echo 'deleting ' . count($duplicates) . " duplicate coordinate records\n";
        sql(
            'DELETE FROM `cache_coordinates`
             WHERE `id` IN (' . implode(',', $duplicates) . ')'
        );
    } else {
        echo
            count($duplicates) . ' duplicate coordinates found. ' .
            "Add parameter 'go' to delete them.\n";
    }
} else {
    echo "no duplicate coordinates found\n";
}
