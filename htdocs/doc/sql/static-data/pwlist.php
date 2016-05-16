<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Read anti-cracking password list into database
 ***************************************************************************/

$opt['rootpath'] = '../../../';
require(__DIR__ . '/../../../lib2/web.inc.php');

$pwf = @fopen('pw_dict', 'r');
if ($pwf) {
    // sql("TRUNCATE TABLE `pw_dict`");
    $n = 0;

    while (!feof($pwf)) {
        $pw = fgets($pwf);
        sql("INSERT IGNORE INTO `pw_dict` (`pw`) VALUES ('&1')", trim($pw));
        ++ $n;
    }
    fclose($pwf);

    echo 'inserted ' . $n . " passwords\n";
} else {
    echo "could not open pw_dict\n";
}
