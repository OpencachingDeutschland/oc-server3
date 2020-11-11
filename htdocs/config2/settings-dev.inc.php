<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 *
 *  Default settings for OC.de developer system. See also
 *    - config2/settings-dist.inc.php for common settings
 *    - config2/settings.inc.php for local settings
 *    - lib/settings* for version-1-code settings
 ***************************************************************************/

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
$opt['php']['debug'] = PHP_DEBUG_ON;
$opt['php']['semaphores'] = false;

/* settings for the template engine
 *
 */
// ... how long a query can take without warning (0 <= disabled)
$opt['db']['warn']['time'] = 0;
$opt['db']['warn']['mail'] = 'root';
$opt['db']['warn']['subject'] = 'sql_warn';

// display error messages on the website - not recommended for productive use!
$opt['db']['error']['display'] = true;
$opt['db']['error']['mail'] = 'root';

/* Debug level (combine with OR | )
 *  DEBUG_NO              = productive use
 *  DEBUG_DEVELOPER       = developer system
 *  DEBUG_TEMPLATES       = no template caching; makes some templates very slow!
 *  DEBUG_OUTOFSERVICE    = only admin login (includes DEBUG_TEMPLATES)
 *  DEBUG_TESTING         = display warning (includes DEBUG_TEMPLATES)
 *  DEBUG_SQLDEBUGGER     = sql debugger (use &sqldebug=1 when calling the site)
 *  DEBUG_TRANSLATE       = read translate messages (use &trans=1 when calling the site)
 *  DEBUG_FORCE_TRANSLATE = force read of translate messages
 *  DEBUG_CLI             = print debug messages of cli scripts
 */
$opt['debug'] = DEBUG_DEVELOPER | DEBUG_SQLDEBUGGER | DEBUG_TRANSLATE | DEBUG_FORCE_TRANSLATE;

// database charset
$opt['charset']['mysql'] = 'utf8mb4';

// node options
// see settings-dist.inc.php for known node IDs
$opt['logic']['node']['id'] = 4;
$opt['logic']['waypoint_pool']['prefix'] = 'OC';

/* server options
 *
 */
set_absolute_urls(
    $opt,
    $dev_baseurl,
    isset($dev_shortlink_domain) ? $dev_shortlink_domain : 'opencaching.de',
    2
);

$opt['page']['develsystem'] = true;
$opt['page']['max_logins_per_hour'] = 1000;    // for development ...

$opt['mail']['from'] = 'root';
$opt['mail']['subject'] = '[local.team-opencaching.de] ';

/* disable cronjobs which are not needed on devel site
 */

$opt['cron']['sitemaps']['generate'] = true;
$opt['cron']['geokrety']['run'] = false;

/* Purge log files - age in days (0 = keep infinite)
 */
$opt['logic']['logs']['purge_email'] = 0;
$opt['logic']['logs']['purge_userdata'] = 0;

// 3rd party library options
// only for local.team-opencaching.de usage
$opt['lib']['w3w']['apikey'] = 'X27PDW41';
$opt['lib']['google']['mapkey']['local.team-opencaching.de'] = 'AIzaSyC8n28u0AELTMd_Mi9KNaMGzluCNWdOIT4';

// other settings
$opt['page']['showdonations'] = true;
$opt['page']['showsocialmedia'] = true;
$opt['page']['headoverlay'] = 'oc_head_alpha3';

$opt['tracking']['googleAnalytics'] = '';

$opt['logic']['pictures']['dummy']['replacepic'] = $dev_basepath . $dev_codepath . 'htdocs/images/no_image_license.png';
$opt['logic']['license']['disclaimer'] = true;
$opt['logic']['admin']['listingadmin_notification'] = 'root';

// NL translation is incomplete, but can be tested
$opt['template']['locales']['NL']['status'] = OC_LOCALE_ACTIVE;

if (isset($debug_startpage_news) && $debug_startpage_news) {
    // Blog news on start page
    $opt['news']['count'] = 6;
    $opt['news']['include'] = 'http://blog.opencaching.de/feed';

    // Forum topics on start page
    $opt['forum']['count'] = 8;
    $opt['forum']['url'] = 'http://forum.opencaching.de/index.php?action=.xml;type=rss;limit=25';
    $opt['forum']['name'] = 'forum.opencaching.de';
}


$opt['logic']['cachemaps']['url'] = 'http://docker.team-opencaching.de/static_map.php?center={latitude},{longitude}&markers={latitude},{longitude}&size=185x185&zoom={userzoom}';
$opt['logic']['cachemaps']['iframe'] = false;
