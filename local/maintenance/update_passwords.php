<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *    This script converts all md5-passwords to salted hash passwords.
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

global $opt;
$opt['rootpath'] = __DIR__ . '/../../htdocs/';
require $opt['rootpath'] . 'lib2/cli.inc.php';
require $opt['rootpath'] . 'lib2/logic/crypt.class.php';

if (!isset($opt['logic']['password_salt']) || strlen($opt['logic']['password_salt']) < 32) {
    echo "Warning!\nPassword Salt not set or too short!\n\n";

    return;
}
if (!$opt['logic']['password_hash']) {
    echo "Warning!\nHashed Passwords not enabled!\n\n";

    return;
}

$rs = sql("SELECT * FROM user WHERE password IS NOT NULL");
while ($r = sql_fetch_array($rs)) {
    $password = $r['password'];
    if (strlen($password) == 128) {
        echo "Password seems to be already converted, ommit this password\n";
        continue;
    }
    if (strlen($password) < 32) {
        $password = crypt::firstStagePasswordEncryption($password);
    }
    $pwhash = crypt::secondStagePasswordEncryption($password);

    $oldpw = sql_value("SELECT `password` FROM `user` WHERE `user_id`='&1'", '', $r['user_id']);
    sql("UPDATE `user` SET `password`='&1' WHERE `user_id`='&2'", $pwhash, $r['user_id']);

    if ($pwhash != sql_value("SELECT `password` FROM `user` WHERE `user_id`='&1'", '', $r['user_id'])) {
        sql("UPDATE `user` SET `password`='&1' WHERE `user_id`='&2'", $oldpw, $r['user_id']);
        echo "Error!\nCould not store new password. Password field not updated to 128 chars?\n\n";
        break;
    }
}

mysql_free_result($rs);

echo "Update of passwords finished.\n";
