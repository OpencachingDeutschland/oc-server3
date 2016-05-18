<?php
/****************************************************************************
 * ./lib/settings.inc.php
 * -------------------
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * sample settings for an OC production system;
 * see header of config2/settings.inc.php for more setting files
 *
 * this file may be outdated
 ****************************************************************************/

//relative path to the root directory
if (!isset($rootpath)) {
    $rootpath = './';
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
require_once $rootpath . 'config2/settings-dist-common.inc.php';

//id of the node; see list in config2/settings-dist.inc.php
$oc_nodeid = 0;
$opt['logic']['node']['id'] = $oc_nodeid;

//name of the cookie
$opt['cookie']['name'] = 'oc_deu';
$opt['cookie']['path'] = '/';
$opt['cookie']['domain'] = '<.do.main>';

//Debug?
if (!isset($debug_page)) {
    $debug_page = false;
}
// $develwarning = '<div id="debugoc"><font size="5" face="arial" color="red"><center>Entwicklersystem - nur Testdaten!</center></font></div>';
$develwarning = '';

//site in service? Set to false when doing bigger work on the database to prevent error's
if (!isset($site_in_service)) {
    $site_in_service = true;
}

/* multi-domain settings
 *
 * If one of the domains matches $_SERVER['SERVER_NAME'], the default values (in
 * config2/common-settings.inc.php) will be overwritten. Can be used to host more
 * than one locale on one server with multiple default-locales.
 * Must be overwritten in BOTH lib1 and lib2 settings.inc.php!!
 */
//$opt['domain']['www.opencaching.de']['url'] = 'http://www.opencaching.de/';
//$opt['domain']['www.opencaching.de']['shortlink_domain'] = 'opencaching.de';
//$opt['domain']['www.opencaching.de']['sitename'] = 'Opencaching.de';
//$opt['domain']['www.opencaching.de']['locale'] = 'DE';
//$opt['domain']['www.opencaching.de']['fallback_locale'] = 'EN';
//$opt['domain']['www.opencaching.de']['style'] = 'ocstyle';
//$opt['domain']['www.opencaching.de']['cookiedomain'] = '.opencaching.de';
//$opt['domain']['www.opencaching.de']['country'] = 'DE';
//$opt['domain']['www.opencaching.de']['keywords'] = 'Geocaching, Geocache, Cache, Schatzsuche, GPS, kostenlos, GPX, Koordinaten, Hobby, Natur';  // 5-10 keywords are recommended
//$opt['domain']['www.opencaching.de']['description'] = 'Opencaching.de ist das freie Portal für Geocaching, ein Schatzsuche-Spiel. Mittels GPS-Koordinaten sind Behälter oder Objekte zu finden.';
//$opt['domain']['www.opencaching.de']['headoverlay'] = 'oc_head_alpha3';
//
// When overriding HTTPS settings, you must override *all* of them!
//$opt['domain']['www.opencaching.de']['https']['mode'] = HTTPS_ENABLED;
//$opt['domain']['www.opencaching.de']['https']['is_default'] = false;
//$opt['domain']['www.opencaching.de']['https']['force_login'] = true;

//$opt['domain']['www.opencaching.pl']['url'] = 'http://www.opencaching.pl/';
//$opt['domain']['www.opencaching.pl']['sitename'] = 'Opencaching.PL';
//$opt['domain']['www.opencaching.pl']['locale'] = 'PL';
//$opt['domain']['www.opencaching.pl']['fallback_locale'] = 'EN';
//$opt['domain']['www.opencaching.pl']['style'] = 'ocstyle';
//$opt['domain']['www.opencaching.pl']['cookiedomain'] = '.opencaching.pl';
//$opt['domain']['www.opencaching.pl']['country'] = 'PL';
//$opt['domain']['www.opencaching.pl']['keywords'] = 'geocaching, geocache, cache, poszukiwanie skarbów, GPS, wolne, GPX, koordynować, hobby, natura';  // 5-10 keywords are recommended
//$opt['domain']['www.opencaching.pl']['description'] = 'Opencaching.pl jest darmowy portal dla Geocaching, gry Treasure Hunt. Za pomocą współrzędnych GPS można znaleźć pojemniki lub obiektów.';
//$opt['domain']['www.opencaching.pl']['headoverlay'] = 'oc_head_alpha3_pl';

// Supply the site's primary URL and the shortlink domain here.
// Can be overriden by domain settings.
// Set shortlink domain to false if not available.
set_absolute_urls($opt, 'http://www.opencaching.de/', 'opencaching.de', 1);

// 'From' EMail address for admin error messages and log removals
if (!isset($emailaddr)) {
    $emailaddr = 'noreply@do.main';
}

// team contact email address
if (!isset($opt['mail']['contact'])) {
    $opt['mail']['contact'] = 'contact@do.main';
}

// news settings
$use_news_approving = true;
$news_approver_email = 'news-approver@<do.main>';

//local database settings
$dbusername = 'username';
$dbname = 'database';
$dbserver = 'server';
$dbpasswd = 'password';
$dbpconnect = false;

$tmpdbname = 'temp'; // empty db with CREATE and DROP priviledges

// date format
$opt['db']['dateformat'] = 'Y-m-d H:i:s';

// warnlevel for sql-execution
$sql_errormail = 'root';
$sql_warntime = 180;

// sql debugging
$sql_allow_debug = 0;
$sql_debug_cryptkey = 'this is my very, very secret \'secret key\'';  // min. 24 chars

// replacements for sql()
$sql_replacements['db'] = $dbname;
$sql_replacements['tmpdb'] = $tmpdbname;

// safemode_zip-binary
$safemode_zip = '/path/to/phpzip.php';
$zip_basedir = '/path/to/html/download/zip/';
$zip_wwwdir = 'download/zip/';

$opt['translate']['debug'] = false;

/* data license settings
 * The text and licsense link are determined by $opt['locale'][<locale>]['page']['license']
 * and $opt['locale'][<locale>]['page']['license_url'].
 */
$opt['logic']['license']['disclaimer'] = false;
$opt['logic']['license']['terms'] = 'articles.php?page=impressum#datalicense';

/* default locale
 */
$opt['template']['default']['locale'] = 'DE';   // can be overwritten by $opt['domain'][<domain>]['locale']

// include all locale settings
require_once $rootpath . 'config2/locale.inc.php';

/* replicated slave databases
 * use same config as in config2/settings.inc.php (!)
 */
$opt['db']['slaves'] = [];

/*
    $opt['db']['slaves'][0]['server'] = 'slave-ip-or-socket';

    // if a slave is no active, the slave will not be tracked
    // by online-check or purge of master logs!
    // Therefore you might have to initialize the replication again,
    // after activating a slave.
    $opt['db']['slaves'][0]['active'] = true;

    // relative weight compared to other slaves
    // see doc2/replicaiton.txt (!)
    $opt['db']['slaves'][0]['weight'] = 100;
    $opt['db']['slaves'][0]['username'] = '';
    $opt['db']['slaves'][0]['password'] = '';

    $opt['db']['slaves'][1]...
*/

// maximum time (sec) a slave is allowed to be behind
// the state of the master database before no connection
// is redirected to this slave
$opt['db']['slave']['max_behind'] = 180;

// use this slave when a specific slave must be connected
// (e.g. xml-interface and mapserver-results)
// you can use -1 to use the master (not recommended, because replicated to slaves)
$opt['db']['slave']['primary'] = - 1;


/* post_config() is invoked directly before the first HTML line of the main.tpl.php is sent to the client.
 */
function post_config()
{
    global $menu, $locale;

    $menu[] = [
        'title' => t('Geokrety'),
        'menustring' => t('Geokrety'),
        'siteid' => 'geokrety',
        'visible' => true,
        'filename' => 'http://geokrety.org/index.php?lang=' . (isset($locale) ? strtolower($locale) : 'de')
    ];

    $menu[] = [
        'title' => 'API',
        'menustring' => 'API',
        'siteid' => 'API',
        'visible' => true,
        'filename' => 'okapi'
    ];
}
