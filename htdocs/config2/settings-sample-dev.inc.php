<?php
/***************************************************************************
 *  Unicode Reminder メモ
 *
 *  Sample settincs.inc.php file for a developer machine
 ***************************************************************************/

// installation paths
$dev_basepath = '/var/www/html/';
$dev_codepath = 'oc-server/server-3.0/';
$dev_baseurl = 'http://local.opencaching.de/oc-server/server-3.0/htdocs';

$debug_startpage_news = false;

// common developer system settings
require __DIR__ . '/settings-dev.inc.php';

// database access
$opt['db']['servername'] = 'localhost';
$opt['db']['username'] = 'oc';
$opt['db']['password'] = 'developer';
$opt['db']['pconnect'] = false;

$opt['db']['maintenance_user'] = 'root';
$opt['db']['maintenance_password'] = 'developer';

// database names
$opt['db']['placeholder']['db'] = 'opencaching';
$opt['db']['placeholder']['tmpdb'] = 'octmp';
