<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Does license-passive-diclined processing on disabled user accounts.
 *  Caution: This will delete cache description and log contents and pictures!
 *  Deleted texts are saved in table saved_texts.
 *
 *  It is strongly recommended to do intensive test before using this script
 ***************************************************************************/

$opt['rootpath'] = '../../';
require_once $opt['rootpath'] . 'lib2/cli.inc.php';
require_once $opt['rootpath'] . 'lib2/logic/user.class.php';

$login->admin = ADMIN_USER;

$rs = sql(
    "SELECT `user_id`,`username` FROM `user`
    WHERE `is_active_flag`=0 AND `data_license`=0"
);

$n = 0;
while ($r = sql_fetch_assoc($rs)) {
    echo "purging content of user '" . $r['username'] . "'\n";

    $user = new user($r['user_id']);
    $result = $user->disduelicense(true);
    if ($result !== true) {
        die($result);
    }

    ++ $n;
}
sql_free_result($rs);

echo "purged data of $n users\n";
