<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Searches for files which are no longer Unicode-encoded
 ***************************************************************************/

chdir(__DIR__ . '/../../htdocs');
require_once 'lib2/cli.inc.php';


scan('.', false);

foreach (['api', 'lang', 'lib', 'lib2', 'src/Oc', 'templates2', 'util', 'util2', 'xml'] as $dir) {
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
                    test_encoding($path);
                }
            }
        }
        closedir($hDir);
    }
}


function test_encoding($path)
{
    static $ur_exclude = [  // no unicode reminder needed
        'lang/de/ocstyle/search1/search.result.caches',
        'lib2/b2evo-captcha',
        'lib2/HTMLPurifier',
        'lib2/html2text.class.php',
        'lib2/imagebmp.inc.php',
        'lib2/Net/IDNA2',
        'lib2/smarty',
    ];

    $contents = file_get_contents($path, false, null, 0, 2048);
    $ur = stripos($contents, "Unicode Reminder");
    if ($ur) {
        if (mb_trim(mb_substr($contents, $ur + 17, 2)) != "メモ") {
            $ur = mb_stripos($contents, "Unicode Reminder");
            if (mb_trim(mb_substr($contents, $ur + 17, 2)) != "メモ") {
                echo "Bad Unicode Reminder found in $path: " . mb_trim(mb_substr($contents, $ur + 17, 2)) . "\n";
            } else {
                echo "Unexpected non-ASCII chars (BOMs?) in header of $path\n";
            }
        }
    } else {
        $ok = false;
        foreach ($ur_exclude as $exclude) {
            if (mb_strpos($path, $exclude) === 0) {
                $ok = true;
            }
        }
        if (!$ok) {
            echo "No Unicode Reminder found in $path\n";
        }
    }
}
