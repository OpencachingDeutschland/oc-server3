<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  This module contains the main initalisation routine and often used 
 *  functions. It is included by web.inc.php and cli.inc.php.
 *
 *  TODO: accept-language des Browser auswerten
 ***************************************************************************/

function __autoload($class_name)
{
	global $opt;

	if (!preg_match('/^[\w]{1,}$/', $class_name))
		return;
	
	$class_name = str_replace('_', '/', $class_name);
	
	$file = $opt['rootpath'] . '../lib/classes/' . $class_name . '.php';
	if (file_exists($file))
	  require_once($file);
}

	// yepp, we will use UTF-8
	mb_internal_encoding('UTF-8');
	mb_regex_encoding('UTF-8');

	// if magic_quotes is enabled, fix it
	fix_magic_quotes_gpc();

	// set options
	require($opt['rootpath'] . 'config2/settings-dist.inc.php');
	require($opt['rootpath'] . 'config2/settings.inc.php');

	set_domain();

	if (!(isset($_REQUEST['sqldebug']) && $_REQUEST['sqldebug']=='1'))
		$opt['debug'] = $opt['debug'] & ~DEBUG_SQLDEBUGGER;

	if (($opt['debug'] & DEBUG_FORCE_TRANSLATE) != DEBUG_FORCE_TRANSLATE)
	{
		if (($opt['debug'] & DEBUG_TRANSLATE) == DEBUG_TRANSLATE && isset($_REQUEST['trans']) && $_REQUEST['trans']=='1')
			$opt['debug'] = $opt['debug'] | DEBUG_TEMPLATES;
		else
			$opt['debug'] = $opt['debug'] & ~DEBUG_TRANSLATE;
	}

	configure_php();

	require($opt['rootpath'] . 'lib2/cookie.class.php');
	normalize_settings();
	set_language();
	set_usercountry();

	// set stylepath and langpath
	if (isset($opt['template']['style']))
	{
		if (strpos($opt['template']['style'], '.') !== false || 
		    strpos($opt['template']['style'], '/') !== false)
			$opt['template']['style'] = $opt['template']['default']['style'];

		if (!is_dir($opt['rootpath'] . 'templates2/' . $opt['template']['style']))
			$opt['template']['style'] = $opt['template']['default']['style'];
	}
	else
		$opt['template']['style'] = $opt['template']['default']['style'];
	$opt['stylepath'] = $opt['rootpath'] . 'templates2/' . $opt['template']['style'] . '/';

	/* setup smarty
	 *
	 */
	require($opt['rootpath'] . 'lib2/OcSmarty.class.php');
	$tpl = new OcSmarty();

	// include all we need
	require_once($opt['rootpath'] . 'lib2/logic/const.inc.php');
	require_once($opt['rootpath'] . 'lib2/logic/geomath.class.php');
	require_once($opt['rootpath'] . 'lib2/error.inc.php');
	require_once($opt['rootpath'] . 'lib2/util.inc.php');
	require_once($opt['rootpath'] . 'lib2/db.inc.php');
	require_once($opt['rootpath'] . 'lib2/login.class.php');
	require_once($opt['rootpath'] . 'lib2/menu.class.php');
	require_once($opt['rootpath'] . 'lib2/logic/labels.inc.php');
	require_once($opt['rootpath'] . 'lib2/throttle.inc.php');

	// apply post configuration
	if (function_exists('post_config'))
		post_config();

// normalize important settings
function normalize_settings()
{
	global $opt;

	$opt['charset']['iconv'] = strtoupper($opt['charset']['iconv']);
	if (substr($opt['page']['absolute_url'], -1, 1) != '/')
		$opt['page']['absolute_url'] .= '/';
	if (substr($opt['logic']['pictures']['url'], -1, 1) != '/')
		$opt['logic']['pictures']['url'] .= '/';
	if (substr($opt['logic']['pictures']['dir'], -1, 1) != '/')
		$opt['logic']['pictures']['dir'] .= '/';
	if (substr($opt['logic']['podcasts']['url'], -1, 1) != '/')
		$opt['logic']['podcasts']['url'] .= '/';
	if (substr($opt['logic']['podcasts']['dir'], -1, 1) != '/')
		$opt['logic']['podcasts']['dir'] .= '/';
}

function configure_php()
{
	global $opt;

	if ($opt['php']['debug'] == PHP_DEBUG_SKIP)
	{
	}
	if ($opt['php']['debug'] == PHP_DEBUG_ON)
	{
		ini_set('display_errors', true);
		ini_set('error_reporting', E_ALL);
		ini_set('mysql.trace_mode', true);
	}
	else
	{
		ini_set('display_errors', false);
		ini_set('error_reporting', E_ALL & ~E_NOTICE);
		ini_set('mysql.trace_mode', false);
	}
}

function set_domain()
{
	global $opt;
	if (!isset($opt['domain']))
		return;

	$domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
	if ($domain == '')
		return;

	if (isset($opt['domain'][$domain]))
	{
		if (isset($opt['domain'][$domain]['url']))
			$opt['page']['absolute_url'] = $opt['domain'][$domain]['url'];

		if (isset($opt['domain'][$domain]['locale']))
			$opt['template']['default']['locale'] = $opt['domain'][$domain]['locale'];

		if (isset($opt['domain'][$domain]['country']))
			$opt['template']['default']['country'] = $opt['domain'][$domain]['country'];

		if (isset($opt['domain'][$domain]['style']))
			$opt['template']['default']['style'] = $opt['domain'][$domain]['style'];

		if (isset($opt['domain'][$domain]['cookiedomain']))
			$opt['session']['domain'] = $opt['domain'][$domain]['cookiedomain'];
	}
}

function set_language()
{
	global $opt, $cookie;

	if (isset($_REQUEST['locale']))
		$opt['template']['locale'] = strtoupper($_REQUEST['locale']);
	else
		$opt['template']['locale'] = strtoupper($cookie->get('locale', $opt['template']['default']['locale']));

	if (isset($opt['template']['locale']) && $opt['template']['locale'] != '')
	{
		if (strpos($opt['template']['locale'], '.') !== false || 
		    strpos($opt['template']['locale'], '/') !== false)
			$opt['template']['locale'] = $opt['template']['default']['locale'];

		if (!isset($opt['locale'][$opt['template']['locale']]))
			$opt['template']['locale'] = $opt['template']['default']['locale'];
	}
	else
		$opt['template']['locale'] = $opt['template']['default']['locale'];

	$cookie->set('locale', $opt['template']['locale'], $opt['template']['default']['locale']);

	bindtextdomain('messages', $opt['rootpath'] . 'cache2/translate');

	// setup the PHP locale
	setlocale(LC_MONETARY, $opt['locale'][$opt['template']['locale']]['locales']);
	setlocale(LC_TIME, $opt['locale'][$opt['template']['locale']]['locales']);
	if (defined('LC_MESSAGES'))
		setlocale(LC_MESSAGES, $opt['locale'][$opt['template']['locale']]['locales']);

	// no localisation!
	setlocale(LC_COLLATE, $opt['locale']['EN']['locales']);
	setlocale(LC_CTYPE, $opt['locale']['EN']['locales']);
	setlocale(LC_NUMERIC, $opt['locale']['EN']['locales']); // important for mysql-queries!

  textdomain('messages');
}

function set_usercountry()
{
	global $cookie;

	if (isset($_REQUEST['usercountry']))
		$cookie->set('usercountry', $_REQUEST['usercountry']);
}

function fix_magic_quotes_gpc()
{
	// Disable magic_quotes_runtime
	@set_magic_quotes_runtime(0);

	if (get_magic_quotes_gpc())
	{
		if (is_array($_GET))
		{
			while (list($k, $v) = each($_GET))
			{
				if (is_array($_GET[$k]))
				{
					while (list($k2, $v2) = each($_GET[$k]))
					{
						$_GET[$k][$k2] = stripslashes($v2);
					}
					@reset($_GET[$k]);
				}
				else
				{
					$_GET[$k] = stripslashes($v);
				}
			}
			@reset($_GET);
		}

		if (is_array($_POST))
		{
			while (list($k, $v) = each($_POST))
			{
				if (is_array($_POST[$k]))
				{
					while (list($k2, $v2) = each($_POST[$k]))
					{
						$_POST[$k][$k2] = stripslashes($v2);
					}
					@reset($_POST[$k]);
				}
				else
				{
					$_POST[$k] = stripslashes($v);
				}
			}
			@reset($_POST);
		}

		if (is_array($_REQUEST))
		{
			while (list($k, $v) = each($_REQUEST))
			{
				if (is_array($_REQUEST[$k]))
				{
					while (list($k2, $v2) = each($_REQUEST[$k]))
					{
						$_REQUEST[$k][$k2] = stripslashes($v2);
					}
					@reset($_REQUEST[$k]);
				}
				else
				{
					$_REQUEST[$k] = stripslashes($v);
				}
			}
			@reset($_REQUEST);
		}

		if (is_array($_COOKIE))
		{
			while (list($k, $v) = each($_COOKIE))
			{
				if (is_array($_COOKIE[$k]))
				{
					while (list($k2, $v2) = each($_COOKIE[$k]))
					{
						$_COOKIE[$k][$k2] = stripslashes($v2);
					}
					@reset($_COOKIE[$k]);
				}
				else
				{
					$_COOKIE[$k] = stripslashes($v);
				}
			}
			@reset($_COOKIE);
		}
	}
}
?>