<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/../lib2/web.inc.php';

header('Content-type: text/plain; charset=utf-8');

if ($opt['logic']['api']['user_inactivity']['key']
    && isset($_REQUEST['key'])
    && $opt['logic']['api']['user_inactivity']['key'] == $_REQUEST['key']
    && isset($_REQUEST['userid'])
) {
    $loginLag = sql_value(
        "SELECT DATEDIFF(NOW(),`last_login`)
         FROM `user`
         WHERE `user_id`='&1'",
        null,
        $_REQUEST['userid']
    );
    if ($loginLag !== null) {
        echo floor($loginLag / 30.5);
    } else {
        echo 'unknown';
    }
}
