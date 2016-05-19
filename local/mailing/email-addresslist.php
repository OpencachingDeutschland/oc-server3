<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

header('Content-type: text/html; charset=utf-8');

$opt['rootpath'] = __DIR__ . '/../../htdocs/';
require $opt['rootpath'] . 'lib2/web.inc.php';

$rs = sql(
    'SELECT email FROM `user`
     WHERE NOT ISNULL(email)
     AND is_active_flag!=0
     AND email_problems=0
     ORDER BY user_id DESC'
);
while ($r = sql_fetch_assoc($rs)) {
    echo $r['email'] . "\n";
}
sql_free_result($rs);
