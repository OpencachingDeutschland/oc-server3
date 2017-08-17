<?php
/***************************************************************************
 *  You may enter special or testing settings for your developer maching here
 ***************************************************************************/

// installation paths
$dev_basepath = '/var/www/html/';
$dev_codepath = '';
$dev_baseurl = '__FRONTEND_URL__';

error_reporting(E_ALL);
ini_set('display_errors', 'on');

// database access
$dbserver = '__DB_HOST__';
$dbusername = '__DB_USER__';
$dbpasswd = '__DB_PASSWORD__';
$dbpconnect = false;

// database names
$dbname = '__DB_NAME__';
$tmpdbname = 'octmp';   // empty db with CREATE and DROP privileges

// enable HTTPS
if (defined('HTTPS_ENABLED')) {
    $opt['page']['https']['mode'] = HTTPS_ENABLED;
}

// common developer system settings
require __DIR__ . '/settings-dev.inc.php';

$sql_errormail = 'root';
