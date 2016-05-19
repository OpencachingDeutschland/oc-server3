<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *    This script guesses the primary language of all users who did not login
 *  after release of OC 3.0.14.
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$opt['rootpath'] = __DIR__ . '/../../htdocs/';
require_once $opt['rootpath'] . 'lib2/cli.inc.php';
require_once $opt['rootpath'] . 'lib2/logic/user.class.php';

$write = ($argc == 2 && $argv[1] = 'write');
if (!$write) {
    echo "use parameter 'write' to write changes to database\n";
}
$processed = 0;
$set = 0;

$rs = sql('SELECT `user_id` FROM `user` WHERE `language` IS NULL');
while ($r = sql_fetch_assoc($rs)) {
    $user = new user($r['user_id']);
    $lang = $user->guessLanguage();
    if ($lang) {
        if ($write) {
            sql(
                "UPDATE `user` SET `language`='&2', `language_guessed`=1 WHERE `user_id`='&1'",
                $r['user_id'],
                $lang
            );
        }
        ++ $set;
    }
    if (++ $processed % 1000 == 0) {
        echo "$set of $processed " . ($write ? '' : 'would be ') . "set\n";
    }
}
echo "$set of $processed " . ($write ? '' : 'would be ') . "set\n";
sql_free_result($rs);
