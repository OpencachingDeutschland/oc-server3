<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *  Unicode Reminder メモ
 *  Searches for files which are no longer Unicode-encoded
 ***************************************************************************/

chdir(__DIR__ . '/../../htdocs');
require_once __DIR__ . '/../../htdocs/lib2/cli.inc.php';

scan('.', false);

foreach (['api', 'lang', 'lib', 'lib2', 'src/Oc', 'templates2', 'util', 'util2', 'xml'] as $dir) {
    scan($dir, true);
}

exit;

function scan($dir, $subDirs)
{
    $hDir = opendir($dir);
    if ($hDir !== false) {
        while (($file = readdir($hDir)) !== false) {
            $path = $dir . '/' . $file;
            if ($subDirs && is_dir($path) && substr($file, 0, 1) !== '.') {
                scan($path, $subDirs);
            } else {
                if (is_file($path) && ((substr($file, -4) === '.tpl') || (substr($file, -4) === '.php'))) {
                    testEncoding($path);
                }
            }
        }
        closedir($hDir);
    }
}

/**
 * @param $path
 */
function testEncoding($path)
{
    static $ur_exclude = [  // no unicode reminder needed
        'lib2/html2text.class.php',
        'lib2/imagebmp.inc.php',
        'lib2/Net/IDNA2',
    ];

    $contents = file_get_contents($path, false, null, 0, 2048);
    $ur = stripos($contents, 'Unicode Reminder');
    if ($ur) {
        if (mb_trim(mb_substr($contents, $ur + 17, 2)) !== 'メモ') {
            $ur = mb_stripos($contents, 'Unicode Reminder');
            if (mb_trim(mb_substr($contents, $ur + 17, 2)) !== 'メモ') {
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
