<?php
/***************************************************************************
 * for license information see LICENSE.md
 *  This module contains the main initialisation routine and often used
 *  functions. It is included by web.inc.php and cli.inc.php.
 *  TODO: accept-language des Browser auswerten
 ***************************************************************************/

require_once __DIR__ . '/../vendor/autoload.php';

$opt['rootpath'] = __DIR__ . '/../';

spl_autoload_register(
    function ($className): void {
        if (!preg_match('/^[\w]{1,}$/', $className)) {
            return;
        }

        $file1 = __DIR__ . '/' . $className . '.class.php';
        $file2 = __DIR__ . '/logic/' . $className . '.class.php';
        if (file_exists($file1)) {
            require_once $file1;
        } elseif (file_exists($file2)) {
            require_once $file2;
        }
    }
);

if (!function_exists('bindtextdomain')) {
    function bindtextdomain(): void
    {
        // dummy function for travis
    }
}

if (!function_exists('textdomain')) {
    function textdomain(): void
    {
        // dummy function for travis
    }
}

if (!function_exists('gettext')) {
    function gettext(): void
    {
        // dummy function for travis
    }
}


// check for broken browsers
$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$useragent_msie = preg_match('/MSIE ([1-9]+).[0-9]+/', $useragent, $ua_matches) && !strpos($useragent, 'Opera');
$useragent_msie_version = null;
if (count($ua_matches) > 1) {
    $useragent_msie_version = $ua_matches[1];
}

// yepp, we will use UTF-8
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

// set options
require_once __DIR__ . '/../config2/settings-dist.inc.php';
require_once __DIR__ . '/../config2/settings.inc.php';
require_once __DIR__ . '/../config2/verify-settings.inc.php';

foreach ($opt['page']['banned_user_agents'] as $ua) {
    if (strpos($useragent, $ua) !== false) {
        die();
    }
}

// backward compatibility for old settings
if ($opt['forum']['url'] == '' &&
    isset($opt['cron']['phpbbtopics']['url']) && $opt['cron']['phpbbtopics']['url'] != '' &&
    isset($opt['cron']['phpbbtopics']['name']) && $opt['cron']['phpbbtopics']['name'] != '') {
    $opt['forum']['url'] = $opt['cron']['phpbbtopics']['url'];
    $opt['forum']['name'] = $opt['cron']['phpbbtopics']['name'];
}

set_domain_config();

if (!(isset($_REQUEST['sqldebug']) && $_REQUEST['sqldebug'] == '1')) {
    $opt['debug'] = $opt['debug'] & ~DEBUG_SQLDEBUGGER;
}

if (($opt['debug'] & DEBUG_FORCE_TRANSLATE) != DEBUG_FORCE_TRANSLATE) {
    if (($opt['debug'] & DEBUG_TRANSLATE) == DEBUG_TRANSLATE
        && isset($_REQUEST['trans']) && $_REQUEST['trans'] == '1'
    ) {
        $opt['debug'] = $opt['debug'] | DEBUG_TEMPLATES;
    } else {
        $opt['debug'] = $opt['debug'] & ~DEBUG_TRANSLATE;
    }
}

require_once __DIR__ . '/errorhandler.inc.php';
configure_php();


$cookie = new Oc\Session\SessionDataCookie();

normalize_settings();
set_language();
set_usercountry();
set_timezone();
// set stylepath and langpath
if (isset($opt['template']['style'])) {
    if (strpos($opt['template']['style'], '.') !== false ||
        strpos($opt['template']['style'], '/') !== false
    ) {
        $opt['template']['style'] = $opt['template']['default']['style'];
    }

    if (!is_dir(__DIR__ . '/../templates2/' . $opt['template']['style'])) {
        $opt['template']['style'] = $opt['template']['default']['style'];
    }
} else {
    $opt['template']['style'] = $opt['template']['default']['style'];
}
$opt['stylepath'] = __DIR__ . '/../templates2/' . $opt['template']['style'] . '/';

check_useragent();

/* setup smarty
 *
 */
require __DIR__ . '/OcSmarty.class.php';
$tpl = new OcSmarty();

// include all we need
require_once __DIR__ . '/logic/const.inc.php';
require_once __DIR__ . '/error.inc.php';
require_once __DIR__ . '/util.inc.php';
require_once __DIR__ . '/db.inc.php';
require_once __DIR__ . '/login.class.php';
require_once __DIR__ . '/menu.class.php';
require_once __DIR__ . '/logic/labels.inc.php';

// apply post configuration
if (function_exists('post_config')) {
    post_config();
}

// check for email address problems
// use direct database access instead of user class for performance reasons - need not
// to include user.class.php in any script
if (!isset($disable_verifyemail) &&
    $login->userid > 0 &&
    sql_value("SELECT `email_problems` FROM `user` WHERE `user_id`='&1'", 0, $login->userid) != 0
) {
    header('Location: verifyemail.php?page=' . basename($_SERVER['REQUEST_URI']));
    exit;
}

// normalize paths and urls
function normalize_settings(): void
{
    global $opt;

    $opt['charset']['iconv'] = strtoupper($opt['charset']['iconv']);

    if (substr($opt['logic']['pictures']['url'], -1, 1) != '/') {
        $opt['logic']['pictures']['url'] .= '/';
    }
    if (substr($opt['logic']['pictures']['dir'], -1, 1) != '/') {
        $opt['logic']['pictures']['dir'] .= '/';
    }
    if (substr($opt['logic']['pictures']['thumb_url'], -1, 1) != '/') {
        $opt['logic']['pictures']['thumb_url'] .= '/';
    }
    if (substr($opt['logic']['pictures']['thumb_dir'], -1, 1) != '/') {
        $opt['logic']['pictures']['thumb_dir'] .= '/';
    }

    if (isset($opt['logic']['cachemaps']['wmsurl']) && strstr($opt['logic']['cachemaps']['wmsurl'], '://')) {
        $opt['logic']['cachemaps']['wmsurl'] =
            $opt['page']['protocol'] . strstr($opt['logic']['cachemaps']['wmsurl'], '://');
    }
}

function configure_php(): void
{
    global $opt;

    if ($opt['php']['debug'] == PHP_DEBUG_ON) {
        ini_set('display_errors', true);
        ini_set('error_reporting', E_ALL);
        ini_set('mysql.trace_mode', true);
        // SQL_CALC_FOUND_ROWS will not work with trace_mode on!
        // Use the next two functions below as workaround.
        register_errorhandlers();
    } else {
        ini_set('display_errors', false);
        ini_set('error_reporting', E_ALL & ~E_NOTICE);
        ini_set('mysql.trace_mode', false);
        register_errorhandlers();
    }
}

function sql_enable_foundrows(): void
{
    ini_set('mysql.trace_mode', false);
}

function sql_foundrows_done(): void
{
    global $opt;

    if ($opt['php']['debug'] == PHP_DEBUG_ON) {
        ini_set('mysql.trace_mode', true);
    }
}

function set_domain_config(): void
{
    global $opt;

    $domain = $opt['page']['domain'];

    if (isset($opt['domain'][$domain]['style'])) {
        $opt['template']['default']['style'] = $opt['domain'][$domain]['style'];
    }
    if (isset($opt['domain'][$domain]['cookiedomain'])) {
        $opt['session']['domain'] = $opt['domain'][$domain]['cookiedomain'];
    }

    set_common_domain_config($opt);
}

function set_language(): void
{
    global $opt, $cookie;

    $savelocale = true;
    if (isset($_REQUEST['locale'])) {
        $opt['template']['locale'] = strtoupper($_REQUEST['locale']);
    } elseif (isset($_REQUEST['templocale'])) {
        $opt['template']['locale'] = strtoupper($_REQUEST['templocale']);
        $savelocale = false;
    } else {
        $opt['template']['locale'] = strtoupper($cookie->get('locale', $opt['template']['default']['locale']));
    }

    if (isset($opt['template']['locale']) && $opt['template']['locale'] != '') {
        if (strpos($opt['template']['locale'], '.') !== false ||
            strpos($opt['template']['locale'], '/') !== false
        ) {
            $opt['template']['locale'] = $opt['template']['default']['locale'];
        }

        if (!isset($opt['locale'][$opt['template']['locale']])) {
            $opt['template']['locale'] = $opt['template']['default']['locale'];
        }
    } else {
        $opt['template']['locale'] = $opt['template']['default']['locale'];
    }

    if ($savelocale) {
        $cookie->set('locale', $opt['template']['locale'], $opt['template']['default']['locale']);
    }

    bindtextdomain('messages', __DIR__ . '/../var/cache2/translate');
    set_php_locale();
    textdomain('messages');
}

function set_usercountry(): void
{
    global $cookie;

    if (isset($_REQUEST['usercountry'])) {
        $cookie->set('usercountry', $_REQUEST['usercountry']);
    }
}

function set_timezone(): void
{
    global $opt;

    date_default_timezone_set($opt['php']['timezone']);
}

function check_useragent(): void
{
    global $ocpropping;

    // are we Ocprop?
    $ocpropping = isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Ocprop/') !== false;
}

// Exchange the protocol (http or https) in an URL to *this* website to the
// protocol of the current request. Do not change external links.
// This prevents i.e. Internet Explorer nag screens when embedding images
// into a https-requested page.

function use_current_protocol($url)
{
    global $opt;

    if (strtolower(substr($url, 0, strlen($opt['page']['absolute_http_url']))) == $opt['page']['absolute_http_url']
        && $opt['page']['https']['active']
    ) {
        return 'https' . strstr($url, '://');
    } elseif (strtolower(substr($url, 0, strlen($opt['page']['absolute_https_url'])))
        == $opt['page']['absolute_https_url']
        && !$opt['page']['https']['active']
    ) {
        return 'http' . strstr($url, '://');
    }

    return $url;
}


function use_current_protocol_in_html($url)
{
    global $opt;

    if ($opt['page']['https']['active']) {
        return str_replace($opt['page']['absolute_http_url'], $opt['page']['absolute_https_url'], $url);
    }

    return $url;
}
