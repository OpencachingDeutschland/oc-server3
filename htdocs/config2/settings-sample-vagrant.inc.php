<?php
/***************************************************************************
 *  Unicode Reminder メモ
 *
 *  Sample settincs.inc.php file for a developer machine
 ***************************************************************************/

// installation paths
$dev_basepath = '/var/www/html/';
$dev_codepath = '*';
$dev_baseurl = 'http://local.opencaching.de';

// enable HTTPS
if (defined('HTTPS_ENABLED')) {
    $opt['page']['https']['mode'] = HTTPS_ENABLED;
}

// show blog and forum news on index.php
$debug_startpage_news = false;

// common developer system settings
require __DIR__ . '/settings-dev.inc.php';

// database access
$opt['db']['servername'] = 'localhost';
$opt['db']['username'] = 'root';
$opt['db']['password'] = 'root';
$opt['db']['pconnect'] = false;

$opt['db']['maintenance_user'] = 'root';
$opt['db']['maintenance_password'] = 'root';

// database names
$opt['db']['placeholder']['db'] = 'opencaching';
$opt['db']['placeholder']['tmpdb'] = 'octmp';

$opt['charset']['mysql'] = 'utf8mb4';
