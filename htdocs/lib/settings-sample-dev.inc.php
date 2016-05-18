<?php
/***************************************************************************
 *  You may enter special or testing settings for your developer maching here
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

// installation paths
$dev_basepath = '/var/www/html/';
$dev_codepath = 'oc-server/server-3.0/';
$dev_baseurl = 'http://local.opencaching.de/oc-server/server-3.0/htdocs';

// database acccess
$dbserver = 'localhost';
$dbusername = 'oc';
$dbpasswd = 'developer';
$dbpconnect = false;

// database names
$dbname = 'opencaching';
$tmpdbname = 'octmp';   // empty db with CREATE and DROP privileges

// common developer system settings
require 'settings-dev.inc.php';

$sql_errormail = 'root';
