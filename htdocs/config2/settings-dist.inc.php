<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 *
 *  Default settings for all options in settings.inc.php
 *  Do not modify this file - use settings.inc.php!
 ***************************************************************************/

require_once __DIR__ . '/locale.inc.php';
require_once __DIR__ . '/settings-dist-common.inc.php';

/* PHP settings
 *
 * PHP_DEBUG_SKIP
 *
 *  don't use ini_set()
 *
 * PHP_DEBUG_OFF
 *
 *  use the following php.ini-settings
 *    display_errors = On
 *    error_reporting = E_ALL & ~E_NOTICE
 *    mysql.trace_mode = Off
 *
 *  strongly recommended settings
 *    register_globals = Off
 *
 * PHP_DEBUG_ON
 *
 *  use the following php.ini-settings
 *    display_errors = On
 *    error_reporting = E_ALL
 *    mysql.trace_mode = On
 */
$opt['php']['debug'] = PHP_DEBUG_SKIP;
$opt['php']['timezone'] = 'Europe/Berlin';
$opt['php']['semaphores'] = true;

// database connection

/* hostname or IP Address
 * to connect to mysql socket use ':/path/to/mysql.sock';
 */
$opt['db']['servername'] = 'localhost';
$opt['db']['username'] = '';
$opt['db']['password'] = '';
$opt['db']['pconnect'] = false;

/**
 * user for manual maintenance functions
 * needs all privileges except for GRANT
 *
 * Set the password ONLY ON DEVELOPER SYSTEMS !!
 */
$opt['db']['maintenance_user'] = '';
$opt['db']['maintenance_password'] = '';

// begin throotling when more than 80%
// of max_connections is reached on db server
$opt['db']['throttle_connection_count'] = 240;

// log the last N seconds for throttling
$opt['db']['throttle_access_time'] = 300;

// throttle users that have more than N access log
// entries in the last [throttle_access_time] seconds
$opt['db']['throttle_access_count'] = 200;

/* replicated slave databases
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

// TODO: use this slave when a specific slave must be connected
// (e.g. xml-interface and mapserver-results)
// you can use -1 to use the master (not recommended, because replicated to slaves)
$opt['db']['slave']['primary'] = - 1;

// ... how long a query can take without warning (0 <= disabled)
$opt['db']['warn']['time'] = 0;
$opt['db']['warn']['mail'] = 'developer@devel.opencaching.de'; // set '' to disable
$opt['db']['warn']['subject'] = 'sql_warn';

// database placeholder

// productive database with opencaching-tables
$opt['db']['placeholder']['db'] = '';    // selected by default

// empty database for temporary table creation
$opt['db']['placeholder']['tmpdb'] = '';

// date format
$opt['db']['dateformat'] = 'Y-m-d H:i:s';

// email delivery processing from syslog-ng eventlog DB; all fields must be set
$opt['system']['maillog']['syslog_db_host'] = '';
$opt['system']['maillog']['syslog_db_name'] = '';
$opt['system']['maillog']['syslog_db_user'] = '';
$opt['system']['maillog']['syslog_db_password'] = '';
$opt['system']['maillog']['syslog_db_table'] = '';
$opt['system']['maillog']['syslog_oc_host'] = '';  // 'host_name' column in syslog DB
$opt['system']['maillog']['syslog_mta'] = 'postfix/smtp%';  // 'program' column in syslog DB
$opt['system']['maillog']['column']['id'] = 'id';               // 'ID'
$opt['system']['maillog']['column']['created'] = 'created';     // 'ReceivedAt'
$opt['system']['maillog']['column']['host_name'] = 'host_name'; // 'FromHost'
$opt['system']['maillog']['column']['message'] = 'message';     // 'Message'
$opt['system']['maillog']['column']['program'] = 'program';     // 'SysLogTag'
$opt['system']['maillog']['inactivity_warning'] = 30;   // warn after N days without new entries

/* cookie or session
 *
 * SAVE_COOKIE            = only use cookies
 * SAVE_SESSION           = use php sessions
 *
 * to use SESSIONS set php.ini to session default values:
 *
 * session.auto_start = 0
 * session.use_cookies = 1
 * session.use_only_cookies = 0
 * session.cookie_lifetime = 0
 * session.cookie_path = "/"
 * session.cookie_domain = ""
 * session.cookie_secure = off
 * session.use_trans_sid = 0
 *
 * set session.safe_path to a secure place
 *
 * other parameters may be customized
 */
$opt['session']['cookiename'] = 'ocdevelopment'; // only with SAVE_COOKIE
$opt['session']['path'] = '/';
$opt['session']['domain'] = '';    // may be overwritten by $opt['domain'][...]['cookiedomain']

/* Debug level (combine with OR | )
 *  DEBUG_NO              = productive use
 *  DEBUG_DEVELOPER       = developer system
 *  DEBUG_TEMPLATES       = no template caching; makes some templates very slow!
 *  DEBUG_OUTOFSERVICE    = only admin login (includes DEBUG_TEMPLATES)
 *  DEBUG_TESTING         = display warning (includes DEBUG_TEMPLATES)
 *  DEBUG_SQLDEBUGGER     = sql debugger (use &sqldebug=1 when calling the site)
 *  DEBUG_TRANSLATE       = read translate messages (use &trans=1 when calling the site, includes DEBUG_TEMPLATES)
 *  DEBUG_FORCE_TRANSLATE = force read of translate messages (includes DEBUG_TRANSLATE)
 *  DEBUG_CLI             = print debug messages of cli scripts
 */
$opt['debug'] = DEBUG_DEVELOPER;

/* other template options
 *
 */
$opt['page']['origin_url'] = 'https://www.opencaching.de/';  // production installation for this OC site
$opt['page']['develsystem'] = false;
$opt['page']['teampic_url'] = 'https://www.opencaching.de/images/team/';
$opt['page']['teammember_url'] = 'https://www.opencaching.de/';

/*
 * configure infos on 404.php
 */
$opt['page']['404']['www.opencaching.de'] = [
    'blog' => [
        'show' => false,
        'feedurl' => '',
        'url' => '',
        'timeout' => null,
        'urlname' => '',
    ],
    'forum' => [
        'show' => false,
        'feedurl' => '',
        'url' => '',
        'timeout' => null,
        'urlname' => '',
    ],
    'wiki' => [
        'show' => false,
        'feedurl' => '',
        'url' => '',
        'timeout' => null,
        'urlname' => '',
    ],

    'newcaches' => [
        'show' => true,
        'url' => 'https://www.opencaching.de',
        'urlname' => '',  // optional: show other name than the url-domain
    ],
];

/* Well known node id's - required for synchronization
 *  1 Opencaching Deutschland (www.opencaching.de)
 *  2 Opencaching Polen (opencaching.pl)
 *  3 Opencaching Tschechien (www.opencaching.cz)
 *  4 Local Development
 *  5 Opencaching Entwicklung Deutschland (devel.opencaching.de)
 *  6 Opencaching Schweden (www.opencaching.se)
 *  7 Opencaching Großbritannien (opencache.uk)
 *  8 Opencaching Norwegen (www.opencaching.no)
 *  9 Opencaching Lettland (?)
 * 10 Opencaching USA (www.opencaching.us)
 * 11 Opencaching Japan (eingestellt)
 * 12 Opencaching Russland  (?)
 * 13 Garmin (www.opencaching.com)
 * 14 Opencaching Niederlande (www.opencaching.nl)
 * 16 Opencaching Rumänien (www.opencaching.ro)
 */
$opt['logic']['node']['id'] = 0;

/* settings for business layer
 *
 */
$opt['logic']['rating']['topdays_mainCountry'] = 30;
$opt['logic']['rating']['topdays_otherCountry'] = 180;

/*
 * count of identical logs (date and text) that shows a warning message on
 * next log
 */
$opt['logic']['masslog']['count'] = 20;

/* location of uploaded images
 */
$opt['logic']['pictures']['maxsize'] = 15000 * 1024;
$opt['logic']['pictures']['unchg_size'] = 250 * 1024;
if (extension_loaded('imagick')) {
    $opt['logic']['pictures']['extensions'] = 'jpg;jpeg;gif;png;bmp;tif;psd;pcx;svg;xpm';
} else {
    $opt['logic']['pictures']['extensions'] = 'jpg;jpeg;gif;png';
}

/* Thumbnail sizes
 */
$opt['logic']['pictures']['thumb_max_width'] = 175;
$opt['logic']['pictures']['thumb_max_height'] = 175;
$opt['logic']['pictures']['listing_thumbs_per_row'] = 4;

/* Defaults for picture replacement on declined license
 *
 * replacement picture must be square sized
 */
$opt['logic']['pictures']['dummy']['bgcolor'] = [255, 255, 255];
$opt['logic']['pictures']['dummy']['text'] = '';
$opt['logic']['pictures']['dummy']['textcolor'] = [0, 0, 0];
$opt['logic']['pictures']['dummy']['replacepic'] = $opt['rootpath'] . 'images/';

/* cachemaps (obsolete)
 */
/*
    $opt['logic']['cachemaps']['url'] = 'images/cachemaps/';
    $opt['logic']['cachemaps']['dir'] = $opt['rootpath'] . $opt['logic']['cachemaps']['url'];
    $opt['logic']['cachemaps']['wmsurl'] = 'cachemaps.php?wp={wp_oc}';
    $opt['logic']['cachemaps']['size']['lat'] = 0.2;
    $opt['logic']['cachemaps']['size']['lon'] = 0.2;
    $opt['logic']['cachemaps']['pixel']['y'] = 200;
    $opt['logic']['cachemaps']['pixel']['x'] = 200;
*/

/* cachemaps (new)
 * how to display the cache map on viewcache.php (200x200 pixel)
 *
 * option 1) via <img> tag (e.g. google maps)
 *        2) via <iframe> tag (e.g. own mapserver)
 *
 * placeholders:
 * {userzoom} = user zoomlevel (see myprofile.php)
 * {latitude} = latitude of the cache
 * {longitude} = longitude of the cache
* {gmkey} = google maps key for current domain
 */
$opt['logic']['cachemaps']['url'] = 'http://maps.google.com/maps/api/staticmap?center={latitude},{longitude}&zoom={userzoom}&size=200x200&maptype=hybrid&markers=color:blue|label:|{latitude},{longitude}&sensor=false&key={gmkey}';
$opt['logic']['cachemaps']['iframe'] = false;

/* Minimap for the new-caches list on the front page.
 * If the url string is empty, no minimap is displayed on the front page.
 *
 * Coordinates of new caches are appended to the url.
 */
$opt['logic']['minimapurl'] = 'https://www.opencaching.de/theme/frontend/images/logo/globi_cache.png?active=false'; // 'http://maps.googleapis.com/maps/api/staticmap?sensor=false&key={gmkey}&size=220x220&maptype=roadmap&markers=color:blue|size:small';

/* target vars
 * all _REQUEST-vars that identifiy the current page for target redirection after login
 */
$opt['logic']['targetvars'] = [
    'cacheid',
    'userid',
    'logid',
    'desclang',
    'descid',
    'wp',
    'uuid',
    'id',
    'action',
    'rid',
    'ownerid',
];

/* cracklib-check for users passwords enabled?
 * (requires php extension crack_check)
 */
$opt['logic']['cracklib'] = false;

/* password authentication method
 * (true means extra hash on the digested password)
 */
$opt['logic']['password_hash'] = false;

/* password salt
 * is a random generated String that is appended to the password
 */
$opt['logic']['password_salt'] = '';

/* new lows style
 */
$opt['logic']['new_logs_per_country'] = true;

/* search engines
 * will be excluded from cache visitor count
 * current active bots on www.opencaching.de in 03/2013:
 *
 * (I added this and then noticed that is may be unnecessary, as the
 * visit counter function is special javascript code which probably is not
 * executed by search engines.  -- following)
 */
$opt['logic']['search_engines'] = 'AcoonBot;AhrefsBot;Baiduspider;bingbot;Exabot;Ezooms;Googlebot;Googlebot-mobile;ia_archiver,Linguee Bot;Mail.RU_Bot;MJ12bot;msnbot;SISTRIX Crawler;Sophora Linkchecker;TweetmemeBot;WBSearchBot;Yahoo! Slurp;YandexBot';

/* default maximum of OConly-81 ranklist members
 */
$opt['logic']['oconly81']['default_maxusers'] = 60;

/* opencaching prefixes in database available to search for
 */
$opt['logic']['ocprefixes'] = 'oc';

/* Username for cronjobs or CLI tools
 * is used e.g. for cache auto-archiving and auto-publishing
 */
$opt['logic']['systemuser']['user'] = '';

/* Purge log files - age in days (0 = keep infinite)
 */
$opt['logic']['logs']['purge_email'] = 30;
$opt['logic']['logs']['purge_userdata'] = 14;

/* license-related functions
 */
$opt['logic']['license']['newusers'] = 2;  // see license constants in lib2/logic/const.inc.php
$opt['logic']['license']['admin'] = true;
$opt['logic']['license']['disclaimer'] = false;
$opt['logic']['license']['terms'] = 'articles.php?page=impressum#datalicense';
// 'disclaimer' and 'terms' also in lib/settings.inc.php

/* optional APIs
 */
$opt['logic']['api']['email_problems']['key'] = '';   // must be set to enable
$opt['logic']['api']['user_inactivity']['key'] = '';  // must be set to enable

/* cache report info settings
 */
$opt['logic']['cache_reports']['delaydays'] = 2;
$opt['logic']['cache_reports']['min_processperday'] = 5;   // set to 0 to disable
$opt['logic']['cache_reports']['max_processperday'] = 20;  // set to 0 to disable

/* cronjob
 */
$opt['cron']['username'] = 'apache';   // system username for cronjobs

/* generate sitemap.xml and upload to search engines
 *
 * NOTE
 *
 * testing server: disbale submit and add OC-source-directory to robots.txt (disallow /)
 * productive server: enable submit and add "Sitemap: sitemap.xml" to you robots.txt
 */
$opt['cron']['sitemaps']['generate'] = true;
$opt['cron']['sitemaps']['submit'] = false;

/* other cronjobs
 */

$opt['cron']['geokrety']['run'] = true;
$opt['cron']['geokrety']['xml_archive'] = false;
$opt['cron']['autoarchive']['run'] = false;
$opt['cron']['replicate']['delete_hidden_caches']['url'] = '';

/* E-Mail settings
 *
 */

// outgoing mails
$opt['mail']['from'] = 'noreply@devel.opencaching.de';
$opt['mail']['subject'] = '[devel.opencaching.de] ';

// email address for user contact emails
// has to be an autoresponder informing about wrong mail usage
$opt['mail']['usermail'] = 'usermail@devel.opencaching.de';

// contact address
$opt['mail']['contact'] = 'contact@devel.opencaching.de';

// blog news on start page
$opt['news']['include'] = '';
$opt['news']['count'] = 3;
$opt['news']['timeout'] = 20;

/* current forum topcis on start page
 * requires url to be a vaild rss feed
 * -> show the number of 'count' topics from rss feed
 */
$opt['forum']['url'] = '';
$opt['forum']['count'] = 5;
$opt['forum']['timeout'] = 20;
$opt['forum']['url'] = '';
$opt['forum']['name'] = '';

// settings for Wiki news on the 404 page
$opt['wikinews']['url'] = 'http://wiki.opencaching.de/index.php/Spezial:Neue_Seiten?feed=rss';
$opt['wikinews']['count'] = 5;
$opt['wikinews']['timeout'] = 20;

// 3rd party library options
// developer.what3words.com API key
$opt['lib']['w3w']['apikey'] = 'YOURAPIKEY';

// Google Maps API key
// https://developers.google.com/maps/documentation/static-maps/?hl=de
$opt['lib']['google']['mapkey'] = [];
$opt['lib']['google']['mapkey']['docker.team-opencaching.de'] = 'AIzaSyDNltPz6LygW02F74zqGpwMloy8ZicsX1E';

/* config of map.php
 */

// search result cache behaviour
$opt['map']['maxcacheage'] = 3600;

// execute cleanup when the size of table map2_data is greater than maxcachesize (in bytes)
$opt['map']['maxcachesize'] = 20 * 1048576; // = 20MB

// cache size after deleting old entries
$opt['map']['maxcachereducedsize'] = 10 * 1048576; // = 10MB

// max number of caches displayed in google maps
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $user_agent = ' ' . $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'MSIE') && !strpos($user_agent, 'Opera')) {
        $opt['map']['maxrecords'] = 200;
    } else {
        $opt['map']['maxrecords'] = 2500;
    }
} else {
    $opt['map']['maxrecords'] = 200;
}
// ... selectable by user:
$opt['map']['min_maxrecords'] = 100;
$opt['map']['max_maxrecords'] = 4000;

/*
 * OKAPI
 */
$opt['okapi']['var_dir'] = $opt['rootpath'] . 'var/okapi';
$opt['okapi']['github_access_token'] = null;

/* Opencaching Node Daemon
 *
 */
// temporary file to collect status info of all child forks
$opt['ocnd']['statusfile'] = '/tmp/ocndaemon.tmp';

// polling behaviour of status option
$opt['ocnd']['timeout'] = 10; // seconds

// IP address to listen
$opt['ocnd']['ip'] = '0.0.0.0';
// TCP port to listen
$opt['ocnd']['port'] = 15000;
// maximum connects buffer (see php manual of socket_listen() )
$opt['ocnd']['connectbuffer'] = 10;
// print out every line sent and received
$opt['ocnd']['debugtcp'] = true;
// do not check openssl version (version check is available in php 5.2+)
$opt['ocnd']['noopensslcheck'] = false;

/* owner and group of files created by apache daemon
 * (used to change ownership in shell scripts)
 */
$opt['httpd']['user'] = 'apache';
$opt['httpd']['group'] = 'apache';

/*
 * small map town list default settings (adjusted for OC.de)
 *
 * set zoom to 0 to disable a town
 */
$opt['map']['towns']['enable'] = true;
$opt['map']['towns']['DE']['enable'] = true;
$opt['map']['towns']['DE']['zoom'] = 11;
$opt['map']['towns']['AT']['enable'] = true;
$opt['map']['towns']['AT']['zoom'] = 10;
$opt['map']['towns']['CH']['enable'] = true;
$opt['map']['towns']['CH']['zoom'] = 10;
$opt['map']['towns']['IT']['enable'] = true;
$opt['map']['towns']['IT']['zoom'] = 8;
$opt['map']['towns']['IT']['Bolzano']['zoom'] = 9;
$opt['map']['towns']['IT']['Udine']['zoom'] = 9;
$opt['map']['towns']['ES']['enable'] = true;
$opt['map']['towns']['ES']['zoom'] = 9;
$opt['map']['towns']['FR']['enable'] = true;
$opt['map']['towns']['FR']['zoom'] = 9;
$opt['map']['towns']['FR']['Strasbourg']['zoom'] = 10;

// example for completely overriding small-map town list for a country:
/*
    $opt['map']['towns']['IT']['enable'] = false;
    $mapmenu = 2001;
    $menuitem[$mapmenu+0] = array('title' => 'Bologna',
                                  'menustring' => 'Bologna',
                                  'authlevel' => 0,
                                  'href' => 'map2.php?mode=normalscreen&lat=44.497&lon=11.343&zoom=8',
                                  'visible' => 1,
                                  'sublevel' => 1,
                                  'parent' => MNU_MAP
                                  );
    $menuitem[$mapmenu+1] = array('title' => 'Bolzano',
                                  'menustring' => 'Bolzano',
                                  'authlevel' => 0,
                                  'href' => 'map2.php?lat=46.502&lon=11.354&zoom=10',
                                  'visible' => 1,
                                  'sublevel' => 1,
                                  'parent' => MNU_MAP
                                  );
    $menuitem[MNU_MAP]['subitems'][] = $mapmenu+0;
    $menuitem[MNU_MAP]['subitems'][] = $mapmenu+1;
*/
