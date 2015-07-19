<?php
/***************************************************************************
 *  For license information see doc/license.txt
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
	
	$file1 = $opt['rootpath'] . 'lib2/' . $class_name . '.class.php';
	$file2 = $opt['rootpath'] . 'lib2/logic/' . $class_name . '.class.php';
	$file3 = $opt['rootpath'] . 'libse/' . str_replace('_', '/', $class_name) . '.php';
	if (file_exists($file1))
		require_once($file1);
	elseif (file_exists($file2)) 
		require_once($file2);
	elseif (file_exists($file3))
		require_once($file3);
}


	// check for broken browsers
	$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
	$useragent_msie = preg_match('/MSIE ([1-9]+).[0-9]+/',$useragent,$ua_matches) && !strpos($useragent,"Opera");
	if (count($ua_matches)>1) $useragent_msie_version = $ua_matches[1];
	else $useragent_msie_version = null;

	// yepp, we will use UTF-8
	mb_internal_encoding('UTF-8');
	mb_regex_encoding('UTF-8');

	// if magic_quotes is enabled, fix it
	fix_magic_quotes_gpc();

	// set options
	require_once($opt['rootpath'] . 'config2/settings-dist.inc.php');
	require_once($opt['rootpath'] . 'config2/settings.inc.php');
	require_once($opt['rootpath'] . 'config2/verify-settings.inc.php');

	foreach ($opt['page']['banned_user_agents'] as $ua)
		if (strpos($useragent, $ua) !== false)
			die();

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

	require_once($opt['rootpath'] . 'lib2/errorhandler.inc.php');
	configure_php();

	require($opt['rootpath'] . 'lib2/cookie.class.php');
	normalize_settings();
	set_language();
	set_usercountry();
	set_timezone();
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

	check_useragent();

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
	// require_once($opt['rootpath'] . 'lib2/throttle.inc.php');

	// apply post configuration
	if (function_exists('post_config'))
		post_config();

	// check for email address problems
	// use direct database access instead of user class for performance reasons - need not
	//	to include user.class.php in any script
	if (!isset($disable_verifyemail) &&
			$login->userid > 0 &&
			sql_value("SELECT `email_problems` FROM `user` WHERE `user_id`='&1'", 0, $login->userid) != 0)
	{
		header("Location: verifyemail.php?page=" . basename($_SERVER['REQUEST_URI']));
		exit;
	}

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
			// SQL_CALC_FOUND_ROWS will not work with trace_mode on!
			// Use the next two functions below as workaround.

		// not for production use yet (has to be tested thoroughly)
		register_errorhandlers();
	}
	else
	{
		ini_set('display_errors', false);
		ini_set('error_reporting', E_ALL & ~E_NOTICE);
		ini_set('mysql.trace_mode', false);
	}
}

function sql_enable_foundrows()
{
	ini_set('mysql.trace_mode', false);
}

function sql_foundrows_done()
{
	global $opt;

	if ($opt['php']['debug'] == PHP_DEBUG_ON)
		ini_set('mysql.trace_mode', true);
}

function set_domain()
{
	global $opt;

	$domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
	$opt['page']['domain'] = $domain;

	if (!isset($opt['domain']) || $domain == '')
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

function set_timezone()
{
	global $opt;
	
	date_default_timezone_set($opt['php']['timezone']);
}

function check_useragent()
{
	global $_SERVER, $ocpropping;

	// are we Ocprop?
	$ocpropping = isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],"Ocprop/");
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

// external help embedding
// pay attention to use only ' quotes in $text (escape other ')
//
// see corresponding function in lib/common.inc.php
function helppageurl($ocpage)
{
	global $opt;

	$help_locale = $opt['template']['locale'];
	$helppage = sql_value("SELECT `helppage` FROM `helppages`
	                        WHERE `ocpage`='&1' AND `language`='&2'",
                         "", $ocpage, $help_locale);
	if ($helppage == "")
		$helppage = sql_value("SELECT `helppage` FROM `helppages`
		                        WHERE `ocpage`='&1' AND `language`='*'",
                          "", $ocpage);
	if ($helppage == "")
	{
		$helppage = sql_value("SELECT `helppage` FROM `helppages`
		                        WHERE `ocpage`='&1' AND `language`='&2'",
                          "", $ocpage, $opt['template']['default']['fallback_locale']);
		if ($helppage != "")
			$help_locale = $opt['template']['default']['fallback_locale'];
	}

	if ($helppage == "" && isset($opt['locale'][$opt['template']['locale']]['help'][$ocpage]))
		$helppage = $opt['locale'][$opt['template']['locale']]['help'][$ocpage];

	if (substr($helppage,0,1) == "!")
		substr($helppage,1);
	else if ($helppage != "" && isset($opt['locale'][$help_locale]['helpwiki']))
		return $opt['locale'][$help_locale]['helpwiki'] . str_replace(' ','_',$helppage);
	else
		return "";
}

function helppagelink($ocpage, $title='Instructions')
{
	global $translate;

	$helpurl = helppageurl($ocpage);
	if ($helpurl == "")
		return "";
	else
	{
		$imgtitle = $translate->t($title, '', basename(__FILE__), __LINE__);
		$imgtitle = "alt='" . $imgtitle . "' title='" . $imgtitle  . "'";
		return "<a class='nooutline' href='" . $helpurl . "' " . $imgtitle . " target='_blank'>";
	}
}


// format number with 1000er dots

function number1000($n)
{
	global $opt;

	if (isset($opt['locale'][$opt['template']['locale']]['format']['dot1000']) &&
	    $opt['locale'][$opt['template']['locale']]['format']['dot1000'] == ',')
		return number_format($n);
	else
		return str_replace(',', '.', number_format($n));
}

?>