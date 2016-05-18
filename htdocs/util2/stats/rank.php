<?php
/***************************************************************************
 *    For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

header('Content-type: text/html; charset=utf-8');

$opt['rootpath'] = '../../';
require $opt['rootpath'] . 'lib2/web.inc.php';

$n = 1;
$rs = sql('SELECT `user`.`username`, `stat_user`.`found`
               FROM `stat_user`
         INNER JOIN `user` ON `stat_user`.`user_id`=`user`.`user_id`
              WHERE `user`.`is_active_flag`=1
           ORDER BY `stat_user`.`found` DESC
              LIMIT 100');
while ($r = sql_fetch_assoc($rs)) {
    echo $n . ' ' . $r['username'] . ': ' . $r['found'] . "\n";
    $n ++;
}
sql_free_result($rs);
