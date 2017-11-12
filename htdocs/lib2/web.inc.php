<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 *
 *  This module is included by each site with HTML-output and contains
 *  functions that are specific to HTML-output. common.inc.php is included
 *  and will do the setup.
 *
 *  If you include this script from any subdir, you have to set the
 *  variable $opt['rootpath'], so that it points (relative or absolute)
 *  to the root.
 ***************************************************************************/

// setup rootpath

if (!isset($opt['rootpath'])) {
    $opt['rootpath'] = __DIR__ . '/';
}

// chicken-egg problem ...
require_once __DIR__ . '/const.inc.php';

// do all output in HTML format
$opt['gui'] = GUI_HTML;

// include the main library
require_once __DIR__ . '/common.inc.php';


//Bootstrap kernel
$env = 'prod';
// at the moment important for live instance
// has to be analyzed why
$debug = true;

if (isset($opt['debug']) && $opt['debug']) {
    $env = 'dev';
}

$kernel = new AppKernel($env, $debug);
$kernel->boot();

// enforce http or https?
if (!isset($disable_oc_https_redirect) || !$disable_oc_https_redirect) {
    if ($opt['page']['https']['mode'] == HTTPS_DISABLED) {
        if ($opt['page']['https']['active']) {
            $tpl->redirect('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
        }
        $opt['page']['force_https_login'] = false;
    } elseif ($opt['page']['https']['mode'] == HTTPS_ENFORCED) {
        if (!$opt['page']['https']['active']) {
            $tpl->redirect('https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
        }
        $opt['page']['force_https_login'] = true;
    } elseif (!empty($_COOKIE[$opt['session']['cookiename'] . 'https_session']) && !$opt['page']['https']['active']) {
        // during login was https active -> session data is https only -> redirect to https
        $tpl->redirect('https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
    }
}

/**
 * external help embedding
 * pay attention to use only ' quotes in $text (escape other ')
 * see corresponding function in lib/common.inc.php
 *
 * @param $ocpage
 * @return string
 */
function helppageurl($ocpage)
{
    global $opt;

    $help_locale = $opt['template']['locale'];
    $helppage = sql_value(
        "SELECT `helppage` FROM `helppages` WHERE `ocpage`='&1' AND `language`='&2'",
        '',
        $ocpage,
        $help_locale
    );
    if ($helppage == '') {
        $helppage = sql_value(
            "SELECT `helppage` FROM `helppages` WHERE `ocpage`='&1' AND `language`='*'",
            '',
            $ocpage
        );
    }
    if ($helppage == '') {
        $helppage = sql_value(
            "SELECT `helppage` FROM `helppages` WHERE `ocpage`='&1' AND `language`='&2'",
            '',
            $ocpage,
            $opt['template']['default']['fallback_locale']
        );
        if ($helppage != '') {
            $help_locale = $opt['template']['default']['fallback_locale'];
        }
    }

    if ($helppage == '' && isset($opt['locale'][$opt['template']['locale']]['help'][$ocpage])) {
        $helppage = $opt['locale'][$opt['template']['locale']]['help'][$ocpage];
    }

    if (substr($helppage, 0, 1) == '!') {
        substr($helppage, 1);
    } elseif ($helppage != '' && isset($opt['locale'][$help_locale]['helpwiki'])) {
        return $opt['locale'][$help_locale]['helpwiki'] . str_replace(' ', '_', $helppage);
    } else {
        return '';
    }
}

/**
 * @param $ocpage
 * @param string $title
 * @return string
 */
function helppagelink($ocpage, $title = 'Instructions')
{
    global $translate;

    $helpUrl = helppageurl($ocpage);
    if ($helpUrl == '') {
        return '';
    }
    $imgTitle = $translate->t($title, '', basename(__FILE__), __LINE__);
    $imgTitle = "alt='" . $imgTitle . "' title='" . $imgTitle . "'";

    return "<a class='nooutline' href='" . $helpUrl . "' " . $imgTitle . " target='_blank'>";
}
