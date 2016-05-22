#!/usr/bin/php
<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

/*
 * update all code-version-dependent data on a developer system
 */

$rootpath = $opt['rootpath'] = __DIR__ . '/../htdocs/';
chdir($rootpath);
require_once 'lib2/cli.inc.php';

echo "updating composer dependencies\n";
system('composer install --ignore-platform-reqs');

echo "applying sql deltas\n";
require 'dbsv-update.php';

echo "importing data.sql\n";
system(
    'cat ' . $rootpath . '../sql/static-data/data.sql |' .
    ' mysql -h' . $opt['db']['servername'] . ' -u' . $opt['db']['username'] . ' --password=' . $opt['db']['password'] . ' ' . $opt['db']['placeholder']['db']
);

echo "importing triggers\n";
chdir($rootpath . '../sql/stored-proc');
system('php maintain.php');

// We do *two* tests for OKAPI presence to get some robustness agains internal OKAPI changes.
//
// This should be replaced by a facade function call, but current OKAPI implementation
// does not work well when called from the command line, due to exception handling problems
// (see http://code.google.com/p/opencaching-api/issues/detail?id=243).
$okapi_vars = sql_table_exists('okapi_vars');
$okapi_syncbase = sql_field_exists('caches', 'okapi_syncbase');
if ($okapi_vars != $okapi_syncbase) {
    echo "!! unknown OKAPI configuration; either dbupdate.php needs an update or your database configuration is wrong\n";
} elseif ($okapi_vars) {
    echo "updating OKAPI database\n";
    chdir($rootpath . '../bin');
    system('php okapi-update.php | grep -i -e mutation');
}

echo "resetting webcache:\n";
chdir($rootpath . '../bin');
system('php clear-webcache.php');
