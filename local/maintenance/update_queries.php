<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  This script was used for adding nano queries when nano size was implemented.
 *  It may be adopted for future additions of cache sizes or types.
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$opt['rootpath'] = '../../htdocs/';
require $opt['rootpath'] . 'lib2/web.inc.php';

if ($argc != 2 || $argv[1] != 'go') {
    die("run with parameter 'go' to update queries\n");
}

$rs = sql('SELECT * FROM queries');
while ($r = sql_fetch_array($rs)) {
    $query = unserialize($r['options']);
    if (isset($query['cachesize'])) {
        $cachesize = $query['cachesize'];

        // add nano size to all queries that include micro or other
        if (preg_match('/[;][12][;]/', ';' . $cachesize . ';') &&
            !preg_match('/[;][8][;]/', ';' . $cachesize . ';')
        ) {
            // echo $r['name'] . ": " . $cachesize . " -> " . $query['cachesize'] .= ";8\n";
            $query['cachesize'] .= ';8';
            $saveopt = serialize($query);
            // sql("UPDATE queries SET `options`='&1' WHERE `id`='&2'", $saveopt, $r['id']);
            // ^^ run this line only if you are absolutely sure what you are doing
            echo 'added nano to query ' . $r['id'] . "(" . $cachesize . ') of user ' . $r['user_id'] . "\n";
        }
    }
}

sql_free_result($rs);
