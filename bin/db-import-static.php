#!/usr/bin/php
<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

$rootpath = $opt['rootpath'] = __DIR__ . '/../htdocs/';
chdir($rootpath);
require_once __DIR__ . '/../htdocs/lib2/cli.inc.php';

echo "\nImporting static data\n";
system(
    'cat ' . $rootpath . '../sql/static-data/*.sql |' .
    ' mysql -h' . $opt['db']['servername'] . ' -u' . $opt['db']['username'] . ' --password=' . $opt['db']['password'] . ' ' . $opt['db']['placeholder']['db']
);
