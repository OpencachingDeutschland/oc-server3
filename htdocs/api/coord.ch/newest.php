<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

header('Content-type: text/html; charset=utf-8');

$opt['rootpath'] = __DIR__ . '/../../';
require $opt['rootpath'] . 'lib2/web.inc.php';

$bFirstRow = true;

$rs = sql(
    'SELECT SQL_BUFFER_RESULT `caches`.`wp_oc` , `caches`.`name` , `user`.`user_id` , `user`.`username`
     FROM `caches`
     INNER JOIN `user` ON `caches`.`user_id` = `user`.`user_id`
     INNER JOIN `cache_status` ON `caches`.`status` = `cache_status`.`id`
     WHERE `cache_status`.`allow_user_view` = 1
     ORDER BY `caches`.`date_created`
     DESC LIMIT 3'
);
while ($r = sql_fetch_assoc($rs)) {
    if ($bFirstRow === true) {
        $bFirstCol = true;
        foreach ($r as $k => $v) {
            if ($bFirstCol === false) {
                echo ';';
            }
            echo strGetCsv($k);
            $bFirstCol = false;
        }
        echo "\n";

        $bFirstRow = false;
    }

    $bFirstCol = true;
    foreach ($r as $k => $v) {
        if ($bFirstCol === false) {
            echo ';';
        }
        echo strGetCsv($v);
        $bFirstCol = false;
    }

    echo "\n";
}
sql_free_result($rs);

// renamed function from "str_getcsv" to avoid collision with PHP 5.3 str_getcsv()
function strGetCsv($str)
{
    return '"' . mb_ereg_replace('"', '\"', $str) . '"';
}
