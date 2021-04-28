<?php
/***************************************************************************
 *  Sample settings.inc.php file for a developer machine
 ***************************************************************************/

// installation paths
$dev_basepath = '/var/www/html/';
$dev_codepath = '*';
$dev_baseurl = 'http://docker.team-opencaching.de';

// enable HTTPS
if (defined('HTTPS_ENABLED')) {
    $opt['page']['https']['mode'] = HTTPS_ENABLED;
}

$opt['debug'] = true;
$opt['httpd']['user'] = 'application';
$opt['httpd']['group'] = 'application';

// show blog and forum news on index.php
$debug_startpage_news = false;

// common developer system settings
require __DIR__ . '/settings-dev.inc.php';

// database access
$opt['db']['servername'] = 'mariadb';
$opt['db']['username'] = 'root';
$opt['db']['password'] = 'root';
$opt['db']['pconnect'] = false;

$opt['db']['maintenance_user'] = 'root';
$opt['db']['maintenance_password'] = 'root';

// database names
$opt['db']['placeholder']['db'] = 'opencaching';
$opt['db']['placeholder']['tmpdb'] = 'octmp';

$opt['charset']['mysql'] = 'utf8mb4';

// only for local.team-opencaching.de usage
$opt['lib']['w3w']['apikey'] = 'X27PDW41';

// activate debug for vagrant system
// $opt['debug'] = true;

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
 * other parameters may be customized
 */
$opt['session']['cookiename'] = 'ocdevelopment'; // only with SAVE_COOKIE
$opt['session']['path'] = '/';
$opt['session']['domain'] = '.team-opencaching.de';    // may be overwritten by $opt['domain'][...]['cookiedomain']

/* Default locale and style
         *
         */
$opt['template']['default']['locale'] = 'DE';
$opt['template']['default']['style'] = 'ocstyle';
$opt['locale']['DE']['page']['subtitle1'] = 'Geocaching mit Opencaching';
$opt['locale']['DE']['page']['subtitle2'] = '';
$opt['page']['title'] = 'OPENCACHING.de';
$opt['page']['headoverlay'] = 'oc_head_alpha3_generic';
$opt['page']['teampic_url'] = 'https://www.opencaching.de/images/team/';
// data license settings
$opt['logic']['license']['disclaimer'] = true;

$opt['page']['banned_user_agents'] = ['Netsparker'];

// admin extensions
$opt['logic']['adminreports']['cachexternal']['OC-Clean'] = '';
$opt['logic']['adminreports']['external_maintainer']['url'] = '';
$opt['logic']['adminreports']['external_maintainer']['msg'] = '';

// email problems API for OCC
$opt['logic']['api']['email_problems']['key'] = '';

$opt['page']['banned_user_agents'] = ['Netsparker'];

/* other template options
         *
         */
$opt['page']['name'] = 'Geocaching mit Opencaching';
$opt['page']['max_logins_per_hour'] = 250;
$opt['mail']['from'] = 'noreply@test.opencaching.de';

/* data license options
 */
$opt['logic']['license']['admin'] = false;

/* Sponsoring advertisements
 * (plain HTML)
 */
$opt['page']['sponsor']['topright'] = '';
$opt['page']['sponsor']['bottom'] = 'Driven by the Opencaching Community';
$opt['page']['sponsor']['popup'] = '';

/* forum news integration (index.php)
 */
$opt['forum']['url'] = 'https://forum.opencaching.de/index.php?action=.xml;type=rss;limit=10';
$opt['forum']['link'] = 'http://forum.opencaching.de';
$opt['forum']['name'] = 'forum.opencaching.de';

/* Well known node id's - required for synchronization
 * 1 Opencaching Deutschland (www.opencaching.de)
 * 2 Opencaching Polen (www.opencaching.pl)
 * 3 Opencaching Tschechien (www.opencaching.cz)
 * 4 Local Development
 * 5 Opencaching Entwicklung Deutschland (devel.opencaching.de)
 */
$opt['logic']['node']['id'] = 4;

/* pregenerated waypoint list for new caches
 * - Waypoint prefix (OC, OP, OZ ... AA=local development)
 * - When pool contains less than min_count, generation process starts
 *   and fills up the pool until max_count is reached.
 */
$opt['logic']['waypoint_pool']['prefix'] = 'OC';
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

/* cracklib-check for users passwords enabled?
 * (requires php extension crack_check)
 */
$opt['logic']['cracklib'] = false;

/* News configuration
 *
 * filename to the include file containing the newscontent
 * (RSS format)
 * if no filename is given, the own news-code is used
 * (table news and newstopic.php)
 *
 * you have to correct entries in table sys_menu and
 * lang/de/stdtyle/lib/menu.php respectively
 */
$opt['news']['include'] = 'http://blog.opencaching.de/feed/';
$opt['news']['count'] = 8;
$opt['news']['timeout'] = 10;

/* current forum topcis on start page
 * requires url to be a vaild rss feed
 *  show the number of 'count' topics from rss feed
*/
$opt['forum']['url'] = 'http://forum.opencaching.de/index.php?action=.xml;type=rss;limit=50';
$opt['forum']['count'] = 10;
$opt['forum']['timeout'] = 10;

/* Wiki news on the 404 page
*/
$opt['wikinews']['timeout'] = 10;

// redirect news.php to the following url
$opt['news']['redirect'] = 'http://blog.opencaching.de';

$opt['page']['showdonations'] = true;
$opt['page']['showsocialmedia'] = true;


/*
* configure infos on 404.php
*/
$opt['page']['404']['test.opencaching.de'] = [
    'blog' => [
        'show' => true,
        'feedurl' => $opt['news']['include'],
        'url' => $opt['news']['redirect'],
    ],
    'forum' => [
        'show' => true,
        'feedurl' => $opt['forum']['url'],
        'url' => 'http://forum.opencaching.de',
    ],
    'wiki' => [
        'show' => true,
        'feedurl' => 'http://wiki.opencaching.de/index.php/Spezial:Neue_Seiten?feed=rss',
        'url' => 'http://wiki.opencaching.de',
    ],
    'newcaches' => [
        'show' => true,
        'url' => 'http://test.opencaching.de',
    ],
];


/* domain specific configuration
* will be loaded when all standard include files are processed
* (db-connection exists, menu is loaded, user login validated etc.)
* Take caution on caching issues (!)
*/

$opt['map']['towns']['enable'] = true;  // for releasing v14

define('MNU_INFO', 1015);
define('MNU_TEAMBLOG', 1016);
define('MNU_FORUM', 1017);
define('MNU_CHAT', 1018);
define('MNU_GEOKRETY', 1019);
define('MNU_API', 1020);

function post_config(): void
{
    global $opt, $menuitem, $tpl, $translate;

    $domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
    if ($domain == '') {
        return;
    }

    $menuitem[MNU_INFO] = [
        'title' => 'Wiki',
        'menustring' => 'Wiki',
        'authlevel' => 0,
        'href' => 'https://' . $domain . '/articles.php?page=helpindex&wiki',
        'visible' => 1,
        'sublevel' => 1,
    ];
    $menuitem[0]['subitems'][] = MNU_INFO;

    config_domain_test_opencaching_de();


    // Link to Geokrety
    $menuitem[MNU_GEOKRETY] = [
        'title' => 'Geokrety',
        'menustring' => 'Geokrety',
        'authlevel' => 0,
        'href' => 'https://www.geokrety.org/',
        'target' => 'target="_blank"',
        'visible' => 1,
        'sublevel' => 1,
    ];
    $menuitem[0]['subitems'][] = MNU_GEOKRETY;

    $menuitem[MNU_API] = [
        'title' => 'API',
        'menustring' => 'API',
        'authlevel' => 0,
        'href' => 'http://docker.team-opencaching.de/okapi',
# OKAPI does not support https yet
        'visible' => 1,
        'sublevel' => 1,
    ];
    $menuitem[0]['subitems'][] = MNU_API;
}

function config_domain_test_opencaching_de(): void
{
    global $opt, $menuitem, $login, $translate;

    $opt['page']['headoverlay'] = 'oc_head_alpha3';
    $opt['cms']['login'] = 'http://wiki.opencaching.de/index.php/Login';

    /* add additional main menu links
    */
    $menuitem[MNU_TEAMBLOG] = [
        'title' => 'Teamblog',
        'menustring' => 'Teamblog',
        'authlevel' => 0,
        'href' => 'http://blog.opencaching.de/',
        'visible' => 1,
        'sublevel' => 1,
    ];
    $menuitem[MNU_FORUM] = [
        'title' => 'Forum',
        'menustring' => 'Forum',
        'authlevel' => 0,
        'href' => 'http://forum.opencaching.de',
        'visible' => 1,
        'sublevel' => 1,
    ];
    $menuitem[0]['subitems'][] = MNU_TEAMBLOG;
    $menuitem[0]['subitems'][] = MNU_FORUM;
}
