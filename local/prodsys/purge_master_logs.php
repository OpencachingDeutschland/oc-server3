#!/usr/bin/php -q
<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Cronjob to clean up master logs that we dont need any more for any
 *  configured slave server. Run this script once every day.
 *
 *  The user account given below needs SUPER privileges.
 *  This is why you want to place this file outside PHP open_basedir
 *  and restrict read access to the root user.
 ***************************************************************************/

// begin configuration
$dbserver = '';
$dbname = '';
$dbuser = '';
$dbpassword = '';
// end configuration

$dblink = @mysql_connect($dbserver, $dbuser, $dbpassword);
if ($dblink === false) {
    exit;
}

if (mysql_select_db($dbname, $dblink) == false) {
    mysql_close($dblink);
    exit;
}

$rs = mysql_query(
    'SELECT COUNT(*) AS `c`
     FROM `sys_repl_slaves`
     WHERE (`active`=1 AND `online`=0)
     OR (`active`=1 AND `online`=1 AND `current_log_name`=\'\')',
    $dblink
);
$r = mysql_fetch_array($rs);
mysql_free_result($rs);

if ($r[0] == 0) {
    $rs = mysql_query('SELECT MIN(`current_log_name`) FROM `sys_repl_slaves` WHERE `active`=1 AND `online`=1', $dblink);
    $rLastLog = mysql_fetch_array($rs);
    mysql_free_result($rs);

    mysql_query("PURGE MASTER LOGS TO '" . mysql_real_escape_string($rLastLog[0], $dblink) . "'", $dblink);
}

mysql_close($dblink);
