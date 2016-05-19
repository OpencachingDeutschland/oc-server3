<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Searches for Unicode byte order marks in code and template files
 ***************************************************************************/

chdir(__DIR__ . '/../../htdocs');
require_once 'lib2/cli.inc.php';


scan('.', false);

foreach (['api', 'lang', 'lib', 'lib2', 'okapi', 'src', 'templates2', 'util', 'util2', 'xml'] as $dir) {
    scan($dir, true);
}

exit;


function scan($dir, $subdirs)
{
    $hDir = opendir($dir);
    if ($hDir !== false) {
        while (($file = readdir($hDir)) !== false) {
            $path = $dir . '/' . $file;
            if (is_dir($path) && substr($file, 0, 1) !== '.' && $subdirs) {
                scan($path, $subdirs);
            } else {
                if (is_file($path) && ((substr($file, - 4) === '.tpl') || (substr($file, - 4) === '.php'))) {
                    testforbom($path);
                }
            }
        }
        closedir($hDir);
    }
}


function testforbom($path)
{
    $filestart = file_get_contents($path, false, null, 0, 2);
    if (ord($filestart) > 126) {
        printf("%02X-%02X found in %s\n", ord($filestart), ord(substr($filestart, 1)), $path);
    }
}
