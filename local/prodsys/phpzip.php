#!/usr/bin/php -q
<?php
/***************************************************************************
 * bin/phpzip.php
 * -------------------
 * begin                : December 22 2005
 *
 * For license information see doc/license.txt
 ***************************************************************************/

/***************************************************************************
 *
 * Wrapper for unix-utilities zip, gzip and bz2
 ***************************************************************************/

$basedir = '/path/to/htdocs/download/zip/';

$zipper['zip'] = 'nice --adjustment=19 zip -j -q -1 {dst} {src}';
$zipper['gzip'] = 'nice --adjustment=19 gzip -1 -c {src} > {dst}';
$zipper['bzip2'] = 'nice --adjustment=19 bzip2 -1 -c {src} > {dst}';

if ($argc < 4 || $argv[1] == '--help') {
    echo $argv[0] . " --type=<ziptype> --src=<source> --dst=<destination>
--type   can be zip, gzip or bzip2
--src    relative* path to source file
--dst    relative* path to destination file

*relative to $basedir
";
    exit;
}

if ((substr($argv[1], 0, 7) != '--type=') ||
    (substr($argv[2], 0, 6) != '--src=') ||
    (substr($argv[3], 0, 6) != '--dst=')
) {
    die("wrong paramter\nuse " . $argv[0] . " --help\n");
}

if (isset($argv[4])) {
    die("wrong paramter\nuse " . $argv[0] . " --help\n");
}

$type = substr($argv[1], 7);
$src = substr($argv[2], 6);
$dst = substr($argv[3], 6);

if (!isset($zipper[$type])) {
    die("invaild zip type\nuse " . $argv[0] . " --help\n");
}

if (checkpath($src) == false) {
    die("invaild src\nuse " . $argv[0] . " --help\n");
}

if (checkpath($dst) == false) {
    die("invaild dst\nuse " . $argv[0] . " --help\n");
}

$src = $basedir . $src;
$dst = $basedir . $dst;

if (!file_exists($src)) {
    die("error: source not exist\nuse " . $argv[0] . " --help\n");
}

if (file_exists($dst)) {
    die("error: destination already exists\nuse " . $argv[0] . " --help\n");
}

$cmd = $zipper[$type];
$cmd = str_replace('{src}', escapeshellcmd($src), $cmd);
$cmd = str_replace('{dst}', escapeshellcmd($dst), $cmd);

system($cmd);

function checkpath($path)
{
    $parts = explode('/', $path);

    if ($parts[0] == '') {
        return false;
    }

    for ($i = 0; $i < count($parts); $i ++) {
        if (($parts[$i] == '..') || ($parts[$i] == '.')) {
            return false;
        }

        if (!preg_match('/^[a-zA-Z0-9.-_]{1,}/', $parts[$i])) {
            return false;
        }
    }

    return true;
}
