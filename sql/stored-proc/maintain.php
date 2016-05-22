#!/usr/bin/php -q
<?php
 /***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Load current stored procs and triggers into database.
 ***************************************************************************/

$opt['rootpath'] = __DIR__ . '/../../htdocs/';
require $opt['rootpath'] . 'lib2/cli.inc.php';

if ($opt['db']['maintenance_user'] == '') {
    die("ERROR: \$opt['db']['maintenance_user'] is not set in config2/settings.inc.php\n");
}

// retrieve DB password
if ($opt['db']['maintenance_password'] == '') {
    if (in_array('--flush', $argv)) {
        echo "\nenter DB " . $opt['db']['maintenance_user'] . " password:\n";
        flush();
    } else {
        echo 'enter DB ' . $opt['db']['maintenance_user'] . ' password: ';
    }

    $fh = fopen('php://stdin', 'r');
    $opt['db']['maintenance_password'] = trim(fgets($fh, 1024));
    fclose($fh);
    if ($opt['db']['maintenance_password'] == '') {
        die("no DB password - aborting.\n");
    }
}

// connect to database
if (!sql_connect_maintenance()) {
    echo 'Unable to connect to database';
    exit;
}

// set variables used by old maintenance scripts
$lang = $opt['template']['locale'];

// include the requested maintain version file
$dbsv = in_array('--dbsv', $argv);
if ($dbsv) {
    $versionfile = 'maintain-' . $argv[$dbsv + 1] . '.inc.php';
    if (!file_exists(__DIR__ . '/' . $versionfile)) {
        die($versionfile . " not found\n");
    } else {
        require $versionfile;
    }
    @unlink($opt['rootpath'] . 'cache2/dbsv-running');
} else {
    require 'maintain-current.inc.php';
}


function current_triggerversion()
{
    return sql_value("SELECT `value` FROM `sysconfig` WHERE `name`='db_version'", 0);
}
