<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Production system sample of settings.inc.php - all lib2 settings needed
 *  to run the website. In addition to this, you must create settings.inc.php
 *  files from .dist files at the following places:
 *
 *    lib
 *    util/notifications
 *    util/publish_caches
 *    util/watchlist
 *
 *  This file may be outdated.
 *
 ***************************************************************************/

/* PHP settings
 * see settings-dist.inc.php for explanation
 */
$opt['php']['debug'] = PHP_DEBUG_OFF;
$opt['php']['timezone'] = 'Europe/Berlin';

/* database settings
 */
$opt['db']['servername'] = 'localhost';
$opt['db']['username'] = '<user>';
$opt['db']['password'] = '<pw>';
$opt['db']['pconnect'] = false;
$opt['db']['maintenance_user'] = '<priviledged_user>';

// ... how long a query can take without warning (0 <= disabled)
$opt['db']['warn']['time'] = 180;
$opt['db']['warn']['mail'] = 'root';
$opt['db']['warn']['subject'] = 'sql_warn';

// display mysql error messages on the website - not recommended for productive use!
$opt['db']['error']['display'] = false;
$opt['db']['error']['mail'] = 'root';
$opt['db']['error']['subject'] = 'sql_error';

// database names
$opt['db']['placeholder']['db'] = 'ocde';
$opt['db']['placeholder']['tmpdb'] = 'ocdetmp';

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
$opt['session']['mode'] = SAVE_COOKIE;
$opt['session']['cookiename'] = '<cookiename>';   // e.g. 'ocde'
$opt['session']['domain'] = '<.do.main>';  // may be overwritten by $opt['domain'][...]['cookiedomain']

/* If the Referer was sent by the client and the substring was not found,
 * the embedded session id will be marked as invalid.
 * Only used with session.mode = SAVE_SESSION
 */
$opt['session']['check_referer'] = true;

/* Debug level (combine with OR | )
 *  DEBUG_NO              = productive use
 *  DEBUG_DEVELOPER       = developer system
 *  DEBUG_TEMPLATES       = no template caching
 *  DEBUG_OUTOFSERVICE    = only admin login (includes DEBUG_TEMPLATES)
 *  DEBUG_TESTING         = display warning (includes DEBUG_TEMPLATES)
 *  DEBUG_SQLDEBUGGER     = sql debugger (use &sqldebug=1 when calling the site)
 *  DEBUG_TRANSLATE       = read translate messages (use &trans=1 when calling the site)
 *  DEBUG_FORCE_TRANSLATE = force read of translate messages
 *  DEBUG_CLI             = print debug messages of cli scripts
 */
$opt['debug'] = DEBUG_NO;
//$opt['debug'] = DEBUG_DEVELOPER|DEBUG_TEMPLATES|DEBUG_SQLDEBUGGER|DEBUG_TRANSLATE|DEBUG_FORCE_TRANSLATE;
//$opt['debug'] = DEBUG_DEVELOPER|DEBUG_TEMPLATES|DEBUG_SQLDEBUGGER;
//$opt['debug'] = DEBUG_DEVELOPER|DEBUG_SQLDEBUGGER;

/* other template options
 *
 */
$opt['page']['name'] = 'Geocaching mit Opencaching';
$opt['mail']['from'] = '<notification email from address>';
$opt['page']['max_logins_per_hour'] = 250;

/* default locale
 */
$opt['template']['default']['locale'] = 'DE';   // can be overwritten by $opt['domain'][<domain>]['locale']

/* multi-domain settings
 *
 * If one of the domains matches $_SERVER['SERVER_NAME'], the default values (in
 * common-settings.inc.php) will be overwritten. Can be used to host more than one
 * locale on one server with multiple default-locales.
 * Must be overwritten in BOTH lib1 and lib2 settings.inc.php,
 * and BEFORE calling set_absolute_urls!
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
set_absolute_urls($opt, 'http://www.opencaching.de/', 'opencaching.de', 2);

/* The OC site's ID; see settings-dist.inc.php for known IDs.
 */
$opt['logic']['node']['id'] = 0;

/* data license settings
 * The text and licsense link are determined by $opt['locale'][<locale>]['page']['license']
 * and $opt['locale'][<locale>]['page']['license_url'].
 */
$opt['logic']['license']['disclaimer'] = false;
$opt['logic']['license']['terms'] = 'articles.php?page=impressum#datalicense';

/* password authentication method
 * (true means extra hash on the digested password)
 */
$opt['logic']['password_hash'] = false;

/* password salt
 * is a random generated String that is appended to the password
 */
$opt['logic']['password_salt'] = '';

/* pregenerated waypoint list for new caches
 * - Waypoint prefix (OC, OP, OZ ... AA=local development)
 * - When pool contains less than min_count, generation process starts
 *   and fills up the pool until max_count is reached.
 */
$opt['logic']['waypoint_pool']['prefix'] = 'AA';
$opt['logic']['waypoint_pool']['min_count'] = 1000;
$opt['logic']['waypoint_pool']['max_count'] = 2000;
// chars used for waypoints. Remember to reinstall triggers and clear cache_waypoint_pool after changing
$opt['logic']['waypoint_pool']['valid_chars'] = '0123456789ABCDEF';
// fill_gaps = true: search for gaps between used waypoints and fill up these gaps
//                   (fill_gaps is slow and CPU intensive on database server. For
//                    productive servers you may want to generate some waypoints
//                    without fill_gaps first)
// fill_gaps = false: continue with the last waypoint
$opt['logic']['waypoint_pool']['fill_gaps'] = false;

/* OC Username for cronjobs or CLI tools
 * is used e.g. for cache auto-archiving and auto-publishing
 */
$opt['logic']['systemuser']['user'] = '';

/* 3rd party library options
 * see https://my.garmin.com/api/communicator/key-generator.jsp
 */
$opt['lib']['garmin']['domain'] = '<domain>';
$opt['lib']['garmin']['key'] = '00112233445566778899AABBCCDDEEFF00';
$opt['lib']['garmin']['url'] = 'http://' . $opt['lib']['garmin']['domain'] . '/';
$opt['lib']['garmin']['page_url'] = 'http://<domain>/';

// developer.what3words.com API Key
$opt['lib']['w3w']['apikey'] = 'YOURAPIKEY';

// Google Maps API key
// http://code.google.com/intl/de/apis/maps/signup.html
$opt['lib']['google']['mapkey']['<domain>'] = 'EEFFGGHH...';

// email address for user contact emails
// has to be an autoresponder informing about wrong mail usage
$opt['mail']['usermail'] = 'usermail@<domain>';

// contact address
$opt['mail']['contact'] = 'contact@<domain>';

$opt['page']['showdonations'] = true;
$opt['page']['showsocialmedia'] = true;

/* index.php news section configuration
 * include '' => from table 'news', else from RSS feed
 */
// $opt['news']['include'] = 'http://blog.opencaching.de/feed';
// $opt['news']['count'] = 8;

/* forum RSS integration (index.php)
 */
// $opt['forum']['url'] = ''http://forum.opencaching.de/index.php?action=.xml;type=rss;limit=50';
// $opt['forum']['count'] = 8;

/* old news integration (index.php)
 */
$opt['cron']['phpbbtopics']['url'] = '';
$opt['cron']['phpbbtopics']['forumids'] = [];
$opt['cron']['phpbbtopics']['name'] = '';
$opt['cron']['phpbbtopics']['link'] = '';
$opt['cron']['phpbbtopics']['count'] = 5;
$opt['cron']['phpbbtopics']['maxcontentlength'] = 230;


function post_config()
{
    global $opt, $menuitem, $tpl;

    $domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
    if ($domain == '') {
        return;
    }

    switch (mb_strtolower($domain)) {
        case 'www.opencaching.de':
            config_domain_www_opencaching_de();
            break;
        case 'www.opencaching.it':
            config_domain_www_opencaching_it();
            break;
        case 'www.opencachingspain.es':
            config_domain_www_opencachingspain_es();
            break;
        default:
            $tpl->redirect('http://www.opencaching.de/index.php');
    }
}
