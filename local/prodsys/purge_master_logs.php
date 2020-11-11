#!/usr/bin/php -q
<?php
/***************************************************************************
 * You can find the license in the docs directory
 *  Cronjob to clean up master logs that we dont need any more for any
 *  configured slave server. Run this script once every day.
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

$dblink = @mysqli_connect($dbserver, $dbuser, $dbpassword, $dbname);
if ($dblink === false) {
    mysqli_close($dblink);
    exit;
}

$rs = mysqli_query(
    $dblink,
    'SELECT COUNT(*) AS `c`
     FROM `sys_repl_slaves`
     WHERE (`active`=1 AND `online`=0)
     OR (`active`=1 AND `online`=1 AND `current_log_name`=\'\')'
);
$r = mysqli_fetch_array($rs);
mysqli_free_result($rs);

if ($r[0] == 0) {
    $rs = mysqli_query(
        $dblink,
        'SELECT MIN(`current_log_name`) FROM `sys_repl_slaves` WHERE `active`=1 AND `online`=1'
    );
    $rLastLog = mysqli_fetch_array($rs);
    mysqli_free_result($rs);

    mysqli_query("PURGE MASTER LOGS TO '" . mysqli_real_escape_string($dblink, $rLastLog[0]) . "'", $dblink);
}

mysqli_close($dblink);
