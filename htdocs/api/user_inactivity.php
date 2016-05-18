<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$opt['rootpath'] = __DIR__ . '/../';
require $opt['rootpath'] . 'lib2/web.inc.php';

header('Content-type: text/plain; charset=utf-8');

if ($opt['logic']['api']['user_inactivity']['key'] &&
    isset($_REQUEST['key']) &&
    $opt['logic']['api']['user_inactivity']['key'] == $_REQUEST['key'] &&
    isset($_REQUEST['userid'])
) {
    $loginlag = sql_value(
        "SELECT DATEDIFF(NOW(),`last_login`)
         FROM `user`
         WHERE `user_id`='&1'",
        null,
        $_REQUEST['userid']
    );
    if ($loginlag !== null) {
        echo floor($loginlag / 30.5);
    } else {
        echo 'unknown';
    }
}
