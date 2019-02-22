<?php
/****************************************************************************
 * ./lib/settings.inc.php
 * -------------------
 * begin                : Mon June 14 2004
 *
 * For license information see LICENSE.md
 ****************************************************************************/

/****************************************************************************
 * Default settings for OC.de developer system. See also
 *   - settings-dist.inc.php for sample settings
 *   - settings.inc.php for local settings
 *   - config2/settings* for version-2-code settings
 ****************************************************************************/

//relative path to the root directory
if (!isset($rootpath)) {
    $rootpath = __DIR__ . '/../';
}

//default used language
if (!isset($lang)) {
    $lang = 'de';
}

//default timezone
if (!isset($timezone)) {
    $timezone = 'Europe/Berlin';
}

//default used style
$style = 'ocstyle';

// include common settings of lib1 and lib2
require_once __DIR__ . '/../config2/settings-dist-common.inc.php';

//id of the node; see config2/settings-dist.inc.php
$oc_nodeid = 4;
$opt['logic']['node']['id'] = $oc_nodeid;

//name of the cookie
$opt['cookie']['name'] = 'ocdocker';
$opt['cookie']['path'] = '/';
$opt['cookie']['domain'] = '.team-opencaching.de';
$opt['session']['cookiename'] = 'ocdocker';

//name of the cookie
if (!isset($cookiename)) {
    $cookiename = $opt['cookie']['name'];
}
if (!isset($cookiepath)) {
    $cookiepath = $opt['cookie']['path'];
}
if (!isset($cookiedomain)) {
    $cookiedomain = $opt['cookie']['domain'];
}

//Debug?
if (!isset($debug_page)) {
    $debug_page = true;
}
$develWarning = '<div id="debugoc"><font size="5" face="arial" color="red"><center>Entwicklersystem - nur Testdaten</center></font></div>';
$sql_debug = isset($_REQUEST['sqldebug']) && $_REQUEST['sqldebug'] == 1;

set_absolute_urls(
    $opt,
    $dev_baseurl,
    isset($dev_shortlink_domain) ? $dev_shortlink_domain : 'opencaching.de',
    1
);

// display error messages on the website - not recommended for productive use!
$opt['db']['error']['display'] = true;
$opt['db']['error']['mail'] = 'root';

// EMail address of the sender and team
if (!isset($maildomain)) {
    $maildomain = 'local.team-opencaching.de';
}
if (!isset($emailaddr)) {
    $emailaddr = 'root@' . $maildomain;
}
if (!isset($opt['mail']['contact'])) {
    $opt['mail']['contact'] = 'contact@' . $maildomain;
}

$opt['page']['showdonations'] = true;
$opt['page']['showsocialmedia'] = true;

// date format
$opt['db']['dateformat'] = 'Y-m-d H:i:s';

// warnlevel for sql-execution
$sql_errormail = 'root';
$dberrormail = $sql_errormail;
$sql_warntime = 100000;

// replacements for sql()
$sql_replacements['db'] = $dbname;
$sql_replacements['tmpdb'] = $tmpdbname;

// safemode_zip-binary
$safemode_zip = '/var/www/html/bin/phpzip.php';
$zip_basedir = $dev_basepath . ($dev_codepath == '*' ? '' : $dev_codepath . 'htdocs/') . 'download/zip/';
$zip_wwwdir = 'download/zip/';

$googlemap_key = '<key>';
$googlemap_type = 'G_MAP_TYPE'; // alternativ: _HYBRID_TYPE

$opt['translate']['debug'] = false;

$opt['tracking']['googleAnalytics'] = 'UA-59334952-4';

/* maximum number of failed logins per hour before that IP address is blocked
 * (used to prevent brute-force-attacks)
 */
$opt['page']['max_logins_per_hour'] = 1000; // for development ...
$opt['page']['headoverlay'] = 'oc_head_alpha3';

// data license
$opt['logic']['license']['disclaimer'] = true; // also in lib2/settings-dist.inc.php
$opt['logic']['license']['terms'] = $absolute_server_URI . 'articles.php?page=impressum#datalicense';

$opt['logic']['admin']['listingadmin_notification'] = 'root';

// include all locale settings
require_once __DIR__ . '/../config2/locale.inc.php';

/* replicated slave databases
 * use same config as in config2/settings.inc.php (!)
 */
$opt['db']['slaves'] = [];
$opt['db']['slave']['max_behind'] = 180;

// use this slave when a specific slave must be connected
// (e.g. xml-interface and mapserver-results)
// you can use -1 to use the master (not recommended, because replicated to slaves)
$opt['db']['slave']['primary'] = - 1;

// NL translation is incomplete, but can be tested
$opt['template']['locales']['NL']['status'] = OC_LOCALE_ACTIVE;
