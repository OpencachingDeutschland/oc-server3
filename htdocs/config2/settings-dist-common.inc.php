<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Settings shared by all configurations of lib1 and lib2.
 *  See also locale.inc.php, which is included in both lib1 and lib2.
 ***************************************************************************/

/* Database charset
 *   Frontend and PHP charsets are UTF-8.
 *   MySQL database default charset is 'utf8' (16 bit restricted Unicode).
 *   For MySQL or MariaDB >= 5.5, this can be changed to 'utf8mb4' (21 bit full Unicode).
 *   bin/dbsv-update.php will then migrate the tables' charset.
 */
$opt['charset']['iconv'] = 'UTF-8'; // 'ISO-8859-1'; // use iconv compatible charset-name
$opt['charset']['mysql'] = 'utf8';     // use mysql compatible charset-name

// handling of SQL and PHP errors
$opt['db']['error']['display'] = false;
$opt['db']['error']['mail'] = 'root';  // set '' to disable

// page title
$opt['page']['title'] = 'OPENCACHING';
$opt['page']['subtitle1'] = 'Geocaching with Opencaching';
$opt['page']['subtitle2'] = '';
$opt['page']['sitename'] = 'Opencaching.de';
$opt['page']['slogan'] = 'Opencaching.de - Geocaching in Deutschland, Österreich und der Schweiz';

// directory of rotator pictures and script, relative to head images dir
$opt['page']['headimagepath'] = '';

// sponsor link on e.g. print preview and garmin-plugin
$opt['page']['sponsor']['popup'] = '';
$opt['page']['sponsor']['bottom'] = 'Driven by the Opencaching community';

$opt['page']['showdonations'] = false; // Show donations button
$opt['page']['showsocialmedia'] = false;

/* maximum number of failed logins per hour before that IP address is blocked
 * (used to prevent brute-force-attacks)
 */
$opt['page']['max_logins_per_hour'] = 25;

// block troublemakers
$opt['page']['banned_user_agents'] = [];

/*
 * Main locale and style: The country and language with most content on this site.
 */
$opt['page']['main_country'] = 'DE';
$opt['page']['main_locale'] = 'DE';

/* Domain-dependend default settings;
 * can all be overwritten by corresponding $opt['domain'][<domain>['...'] settings.
 * Additionally, the cookie domain (different vor lib1 and lib2) can be overwritten.
 * See examples for overriding in settings-sample.inc.php.
 */
$opt['page']['meta']['keywords'] = 'Geocaching, Geocache, Cache, Schatzsuche, GPS, kostenlos, GPX, Koordinaten, Hobby, Natur';  // 5-10 keywords are recommended
// see http://forum.opencaching.de/index.php?topic=3065.0
// and http://forum.opencaching.de/index.php?topic=3065.0 regarding description
$opt['page']['meta']['description'] = 'Opencaching.de ist das freie Portal für Geocaching, ein Schatzsuche-Spiel. Mittels GPS-Koordinaten sind Behälter oder Objekte zu finden.';
$opt['page']['headoverlay'] = 'oc_head_alpha3_generic';
$opt['template']['default']['locale'] = 'DE';
$opt['template']['default']['article_locale'] = 'EN';
$opt['template']['default']['fallback_locale'] = 'EN';
$opt['template']['default']['style'] = 'ocstyle';
$opt['template']['default']['country'] = 'DE';

// smiley path
$opt['template']['smiley'] = 'resource2/tinymce/plugins/emotions/img/';

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

/* geocache recommendation settings
 */
$opt['logic']['rating']['percentageOfFounds'] = 10;

/* admin functions
 */
// admin may use OC-team-comment log flag only when processing a cache report
// see also setting in lib/settings.inc.php!
$opt['logic']['admin']['team_comments_only_for_reports'] = true;
$opt['logic']['admin']['enable_listing_admins'] = false;
$opt['logic']['admin']['listingadmin_notification'] = '';  // Email address(es), comma separated

/*
 * html purifier
 */
$opt['html_purifier']['cache_path'] = dirname(__FILE__) . '/../cache2/html_purifier/';

/*
 * CMS links for external pages
 */

// explanation of common login errors
$opt['cms']['login'] = 'http://wiki.opencaching.de/index.php/Login_auf_Opencaching.de';

// explanation of nature protection areas
$opt['cms']['npa'] = 'articles.php?page=npa&wiki';

/* HTTPS settings
 *
 * mode:  HTTPS_DISABLED:  https requests will be redirected to http
 *        HTTPS_ENABLED:   all requests will stay within the same protocol
 *        HTTPS_ENFORCED:  http requests will be redirected to https
 *
 * is_default:   true:     links in exported data will point to https:
 *               false:    links in exported data will point to http:
 *
 * force_login:  true:     login forms submit to https:
 *               false     login forms submit to the current protocol
 */
if (!isset($opt['page']['https']['mode'])) {
    $opt['page']['https']['mode'] = HTTPS_DISABLED;
}
if (!isset($opt['page']['https']['is_default'])) {
    $opt['page']['https']['is_default'] = false;
}
if (!isset($opt['page']['https']['force_login'])) {
    $opt['page']['https']['force_login'] = false;
}


/* The following additional variables are generated:
 *
 * $opt['page']['absolute_url']           the base URL of the current request, http or https + current domain
 *   $absolute_server_URI                   ... the same, only in lib1
 * $opt['page']['absolute_http_url']      the http:// base URL of the current domain
 * $opt['page']['absolute_https_url']     the https:// base URL of the currenbt domain
 * $opt['page']['default_absolute_url']   the default-protocol base URL of the current domain (used in exported data)
 * $opt['page']['default_primary_url']    the default-protocol base URL of the primary domain of this site
 * $opt['page']['shortlink_url']          shortlink URL of the current protocol or false
 * $opt['page']['default_shortlink_url']  default-protocol shortlink URL or false
 * $opt['page']['default_primary_shortlink_url']  default-protocol shortlink URL of primary domain of this site, or false
 * $opt['page']['https']['active']        true if the current request is https, else false
 * $opt['page']['protocol']               the protocol of the current request, 'http' or 'https'
 *
 * These settings allow to run a consistently multi-protocol and multi-domain OC site.
 * All generated URls end on '/'.
 */

function set_absolute_urls(&$opt, $primary_site_url, $primary_shortlink_domain, $lib)
{
    // $opt is passed as parameter because it is *local* in okapi_settings.php.

    global $absolute_server_URI, $rootpath;

    // 1. create settings for the primary domain, which was passed in $site_url

    $primary_domain = parse_url($primary_site_url, PHP_URL_HOST);
    if (isset($opt['domain'][$primary_domain]['url'])) {
        $primary_site_url = $opt['domain'][$primary_domain]['url'];
    }
    if (substr($primary_site_url, - 1, 1) != '/') {
        $primary_site_url .= '/';
    }

    if (isset($opt['domain'][$primary_domain]['https']['is_default'])) {
        $primary_httpsdefault = $opt['domain'][$primary_domain]['https']['is_default'];
    } else {
        $primary_httpsdefault = $opt['page']['https']['is_default'];
    }
    if ($primary_httpsdefault) {
        $opt['page']['default_primary_url'] = 'https' . strstr($primary_site_url, '://');
    } else {
        $opt['page']['default_primary_url'] = 'http' . strstr($primary_site_url, '://');
    }

    // 2. create settings for the current domain

    $current_domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $primary_domain;
    $opt['page']['domain'] = $current_domain;

    if (isset($opt['domain'][$current_domain]['url'])) {
        $current_site_url = $opt['domain'][$current_domain]['url'];
    } else {
        $current_site_url = 'x://' . $current_domain . parse_url($primary_site_url, PHP_URL_PATH);
    }
    if (substr($current_site_url, - 1, 1) != '/') {
        $current_site_url .= '/';
    }

    if (isset($opt['domain'][$current_domain]['https'])) {
        // This overwrites *all* https settings.
        $opt['page']['https'] = $opt['domain'][$current_domain]['https'];
    }

    $adr = strstr($current_site_url, '://');
    $opt['page']['absolute_http_url'] = 'http' . $adr;
    $opt['page']['absolute_https_url'] = 'https' . $adr;
    $opt['page']['https']['active'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off');

    if ($opt['page']['https']['active']) {
        $opt['page']['absolute_url'] = $opt['page']['absolute_https_url'];
        $opt['page']['protocol'] = 'https';
    } else {
        $opt['page']['absolute_url'] = $opt['page']['absolute_http_url'];
        $opt['page']['protocol'] = 'http';
    }

    if ($lib == 1) {
        $absolute_server_URI = $opt['page']['absolute_url'];
    }

    if ($opt['page']['https']['is_default']) {
        $opt['page']['default_absolute_url'] = $opt['page']['absolute_https_url'];
        $opt['page']['default_protocol'] = 'https';
    } else {
        $opt['page']['default_absolute_url'] = $opt['page']['absolute_http_url'];
        $opt['page']['default_protocol'] = 'http';
    }

    // 3. create shortlink URLs

    if (!$primary_shortlink_domain) {
        $opt['page']['shortlink_url'] = false;
        $opt['page']['default_shortlink_url'] = false;
        $opt['page']['default_primary_shortlink_url'] = false;
    } else {
        if ($primary_httpsdefault) {
            $opt['page']['default_primary_shortlink_url'] = 'https://' . $primary_shortlink_domain . '/';
        } else {
            $opt['page']['default_primary_shortlink_url'] = 'http://' . $primary_shortlink_domain . '/';
        }

        if (isset($opt['domain'][$current_domain]['shortlink_domain']) && $opt['domain'][$current_domain]['shortlink_domain']) {
            $opt['page']['shortlink_url'] = $opt['page']['protocol'] . '://' . $opt['domain'][$current_domain]['shortlink_domain'] . '/';
            $opt['page']['default_shortlink_url'] = $opt['page']['default_protocol'] . '://' . $opt['domain'][$current_domain]['shortlink_domain'] . '/';
        } else {
            if ($current_domain == $primary_domain) {
                $opt['page']['default_shortlink_url'] = $opt['page']['default_primary_shortlink_url'];
                $opt['page']['shortlink_url'] =
                    $opt['page']['protocol'] . strstr($opt['page']['default_shortlink_url'], '://');
            } else {
                $opt['page']['shortlink_url'] = false;
                $opt['page']['default_shortlink_url'] = false;
            }
        }
    }

    // 4. set location of uploaded images

    if (!isset($opt['logic']['pictures']['dir'])) {
        $opt['logic']['pictures']['dir'] = dirname(__FILE__) . '/../images/uploads';
    }  // Ocprop, OKAPI !
    if (!isset($opt['logic']['pictures']['url'])) {
        $opt['logic']['pictures']['url'] = $opt['page']['default_primary_url'] . 'images/uploads';
    }
    if (!isset($opt['logic']['pictures']['thumb_dir'])) {
        $opt['logic']['pictures']['thumb_dir'] = $opt['logic']['pictures']['dir'] . '/thumbs';
    }
    if (!isset($opt['logic']['pictures']['thumb_url'])) {
        $opt['logic']['pictures']['thumb_url'] = $opt['logic']['pictures']['url'] . '/thumbs';
    }
}


function set_common_domain_config(&$opt)
{
    // $opt is passed as parameter because it is *local* in okapi_settings.php.

    $domain = $opt['page']['domain'];

    if (isset($opt['domain'][$domain])) {
        if (isset($opt['domain'][$domain]['locale'])) {
            $opt['template']['default']['locale'] = $opt['domain'][$domain]['locale'];
        }
        if (isset($opt['domain'][$domain]['fallback_locale'])) {
            $opt['template']['default']['fallback_locale'] = $opt['domain'][$domain]['fallback_locale'];
        }

        if (isset($opt['domain'][$domain]['country'])) {
            $opt['template']['default']['country'] = $opt['domain'][$domain]['country'];
        }

        if (isset($opt['domain'][$domain]['sitename'])) {
            $opt['page']['sitename'] = $opt['domain'][$domain]['sitename'];
        }
        if (isset($opt['domain'][$domain]['keywords'])) {
            $opt['page']['meta']['keywords'] = $opt['domain'][$domain]['keywords'];
        }
        if (isset($opt['domain'][$domain]['description'])) {
            $opt['page']['meta']['description'] = $opt['domain'][$domain]['description'];
        }

        if (isset($opt['domain'][$domain]['headoverlay'])) {
            $opt['page']['headoverlay'] = $opt['domain'][$domain]['headoverlay'];
        }
        if (isset($opt['domain'][$domain]['slogan'])) {
            $opt['page']['slogan'] = $opt['domain'][$domain]['slogan'];
        }
    }
}
