<?php
/***************************************************************************
 *  Unicode Reminder メモ
 *
 *  Sample settincs.inc.php file for a developer machine
 ***************************************************************************/

// installation paths
$dev_basepath = '/var/www/html/';
$dev_codepath = '*';
$dev_baseurl = 'http://local.opencaching.de ';

// common developer system settings
require("settings-dev.inc.php");

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

// Blog news on start page
// $opt['news']['count'] = 6;
// $opt['news']['include'] = 'http://blog.opencaching.de/feed';

// Forum topics on start page
// $opt['forum']['count'] = 8;
// $opt['forum']['url'] = 'http://forum.opencaching.de/index.php?action=.xml;type=rss;limit=25';
// $opt['cron']['phpbbtopics']['name'] = 'forum.forum.opencaching.de';
