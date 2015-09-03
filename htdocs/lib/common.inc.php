<?php
/****************************************************************************
													    ./lib/common.inc.php
															--------------------
		begin                : Mon June 14 2004

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************

		Unicode Reminder メモ

	sets up all neccessary variables and handle template and database-things
	also useful functions

	parameter: lang       get/post/cookie   used language
	           style      get/post/cookie   used style

 ****************************************************************************/

function __autoload($class_name)
{
	global $opt;

	if (!preg_match('/^[\w]{1,}$/', $class_name))
		return;
	
	$class_name = str_replace('_', '/', $class_name);
	
	$file = $opt['rootpath'] . 'libse/' . $class_name . '.php';
	if (file_exists($file))
	  require_once($file);
}



	if (isset($opt['rootpath']))
		$rootpath = $opt['rootpath'];
	else if (isset($rootpath))
		$opt['rootpath'] = $rootpath;
	else
	{
		$rootpath = './';
		$opt['rootpath'] = $rootpath;
	}

	// we are in HTML-mode ... maybe plain (for CLI scripts)
	global $interface_output, $bScriptExecution;
	$interface_output = 'html';

	// set default CSS
	tpl_set_var('css', 'main.css');

	//detecting errors
	$error = false;

	//no slashes in variables! originally from phpBB2 copied
	@set_magic_quotes_runtime(0); // Disable magic_quotes_runtime

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

		if (is_array($HTTP_COOKIE_VARS))
		{
			while (list($k, $v) = each($HTTP_COOKIE_VARS))
			{
				if (is_array($HTTP_COOKIE_VARS[$k]))
				{
					while (list($k2, $v2) = each($HTTP_COOKIE_VARS[$k]))
					{
						$HTTP_COOKIE_VARS[$k][$k2] = stripslashes($v2);
					}
					@reset($HTTP_COOKIE_VARS[$k]);
				}
				else
				{
					$HTTP_COOKIE_VARS[$k] = stripslashes($v);
				}
			}
			@reset($HTTP_COOKIE_VARS);
		}
	}

	if (!isset($rootpath)) $rootpath = './';
	require_once($rootpath . 'lib/clicompatbase.inc.php');

	// load domain specific settings
	load_domain_settings();

	// load HTML specific includes
	require_once($rootpath . 'lib/cookie.class.php');

	//site in service?
	if ($site_in_service == false)
	{
		header('Content-type: text/html; charset=utf-8');
		$page_content = read_file($rootpath . 'html/outofservice.tpl.php');
		die($page_content);
	}

	//by default, use start template
	if (!isset($tplname)) $tplname = 'start';

	//restore cookievars[]
	load_cookie_settings();

	//language changed?
	if (isset($_POST['lang']))
	{
		$lang = $_POST['lang'];
	}
	if (isset($_GET['lang']))
	{
		$lang = $_GET['lang'];
	}

	//are there files for this language?
	if (!file_exists($rootpath . 'lang/'. $lang . '/'))
	{
		die('Critical Error: The specified language does not exist!');
	}

	//style changed?
	if (isset($_POST['style']))
	{
		$style = $_POST['style'];
	}
	if (isset($_GET['style']))
	{
		$style = $_GET['style'];
	}

	//does the style exist?
	if (!file_exists($rootpath . 'lang/'. $lang . '/' . $style . '/'))
		$style = 'ocstyle';

	if (!file_exists($rootpath . 'lang/'. $lang . '/' . $style . '/'))
	{
		die('Critical Error: The specified style does not exist!');
	}

	//set up the language path
	if (!isset($langpath)) $langpath = $rootpath . 'lang/' . $lang;

	//set up the style path
	if (!isset($stylepath)) $stylepath = $langpath . '/' . $style;

	//load gettext translation
	load_gettext();

	// thumbs-dir/url
	if (!isset($thumbdir)) $thumbdir = $picdir . '/thumbs';
	if (!isset($thumburl)) $thumburl = $picurl . '/thumbs';

	//open a databse connection
	db_connect();

	require($opt['rootpath'] . 'lib/auth.inc.php');
	require_once($opt['rootpath'] . 'lib2/translate.class.php');

	//load language specific strings
	require_once($langpath . '/expressions.inc.php');

	//set up the defaults for the main template
	require_once($stylepath . '/varset.inc.php');

	if ($dblink === false)
	{
		//error while connecting to the database
		$error = true;

		//set up error report
		tpl_set_var('error_msg', htmlspecialchars(mysql_error(), ENT_COMPAT, 'UTF-8'));
		tpl_set_var('tplname', $tplname);
		$tplname = 'error';
	}
	else
	{
		//user authenification from cookie
		auth_user();
		if ($usr == false)
		{
			//no user logged in
			if (isset($_POST['target']))
			{
				$target = $_POST['target'];
			}
			elseif (isset($_REQUEST['target']))
			{
				$target = $_REQUEST['target'];
			}
			elseif (isset($_GET['target']))
			{
				$target = $_GET['target'];
			}
			else
			{
				$target = '{target}';
			}
			$sLoggedOut = mb_ereg_replace('{target}', $target, $sLoggedOut);
			tpl_set_var('loginbox', $sLoggedOut);
		}
		else
		{
			//user logged in
			$sTmpString = mb_ereg_replace('{username}', $usr['username'], $sLoggedIn);
			tpl_set_var('loginbox', $sTmpString);
			unset($sTmpString);
		}
	}

	// are we Ocprop?
	$ocpropping = isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],"Ocprop/");

	// zeitmessung
	require_once($rootpath . 'lib/bench.inc.php');
	$bScriptExecution = new Cbench;
	$bScriptExecution->start();

	function load_domain_settings()
	{
		global $opt, $style, $absolute_server_URI;

		$sHost = strtolower($_SERVER['HTTP_HOST']);

		if (isset($opt['domain'][$sHost]))
		{
			if (isset($opt['domain'][$sHost]['style']))
				$style = $opt['domain'][$sHost]['style'];

			if (isset($opt['domain'][$sHost]['cookiedomain']))
				$opt['cookie']['domain'] = $opt['domain'][$sHost]['cookiedomain'];

			if (isset($opt['domain'][$sHost]['url']))
				$absolute_server_URI = $opt['domain'][$sHost]['url'];

			if (isset($opt['domain'][$sHost]['locale']))
				$opt['template']['default']['locale'] = $opt['domain'][$sHost]['locale'];

			if (isset($opt['domain'][$sHost]['country']))
				$opt['template']['default']['country'] = $opt['domain'][$sHost]['country'];
		}
	}

	// get the language from a given shortage
	// on success return the name, otherwise false
	function db_LanguageFromShort($langcode)
	{
 		global $dblink, $locale;

		//no databse connection?
		if ($dblink === false) return false;

		//select the right record
		$rs = sql("SELECT IFNULL(`sys_trans_text`.`text`, `languages`.`name`) AS `text` FROM `languages` LEFT JOIN `sys_trans` ON `languages`.`trans_id`=`sys_trans`.`id` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' WHERE `languages`.`short`='&2'", $locale, $langcode);
		if (mysql_num_rows($rs) > 0)
		{
			$record = sql_fetch_array($rs);

			//return the language
			return $record['text'];
		}
		else
		{
			//language not found
			return false;
		}
	}

	//get the stored settings and authentification data from the cookie
	function load_cookie_settings()
	{
		global $cookie, $lang, $style;

		//speach
		if ($cookie->is_set('lang'))
		{
			$lang = $cookie->get('lang');
		}

		//style
		if ($cookie->is_set('style'))
		{
			$style = $cookie->get('style');
		}
	}

	//store the cookie vars
	function write_cookie_settings()
	{
		global $cookie, $lang, $style;

		//language
		$cookie->set('lang', $lang);

		//style
		$cookie->set('style', $style);

		//send cookie
		$cookie->header();
	}

	//returns the cookie value, otherwise false
	function get_cookie_setting($name)
	{
		global $cookie;

		if ($cookie->is_set($name))
		{
			return $cookie->get($name);
		}
		else
		{
			return false;
		}
	}

	//sets the cookie value
	function set_cookie_setting($name, $value)
	{
		global $cookie;
		$cookie->set($name, $value);
	}

	//set a template replacement
	//set no_eval true to prevent this contents from php-parsing.
	//Important when replacing something that the user has posted
	//in HTML code and could contain \<\? php-Code \?\>
	function tpl_set_var($name, $value, $no_eval = true)
	{
		global $vars, $no_eval_vars;
		$vars[$name] = $value;
		$no_eval_vars[$name] = $no_eval;
	}

	//get a template replacement, otherwise false
	function tpl_get_var($name)
	{
		global $vars;

		if (isset($vars[$name]))
		{
			return $vars[$name];
		}
		else
		{
			return false;
		}
	}

	//clear all template vars
	function tpl_clear_vars()
	{
		unset($GLOBALS['vars']);
		unset($GLOBALS['no_eval_vars']);
	}

	//page function replaces {functionsbox} in main template
	function tpl_set_page_function($id, $html_code)
	{
		global $page_functions;

		$page_functions[$id] = $html_code;
	}

	function tpl_unset_page_function($id)
	{
		global $page_functions;

		unset($page_functions[$id]);
	}

	function tpl_clear_page_functions()
	{
		unset($GLOBALS['page_functions']);
	}

	//read the templates and echo it to the user
	function tpl_BuildTemplate($dbdisconnect=true)
	{
		//template handling vars
		global $style, $stylepath, $tplname, $vars, $langpath, $locale, $opt, $oc_nodeid, $translate, $usr;
		//language specific expression
		global $error_pagenotexist;
		//only for debbuging
		global $b, $bScriptExecution;
		// country dropdown
		global $tpl_usercountries;

		tpl_set_var('screen_css_time',filemtime($opt['rootpath'] . "resource2/" . $style . "/css/style_screen.css"));
		tpl_set_var('screen_msie_css_time',filemtime($opt['rootpath'] . "resource2/" . $style . "/css/style_screen_msie.css"));
		tpl_set_var('print_css_time',filemtime($opt['rootpath'] . "resource2/" . $style . "/css/style_print.css"));

		if (isset($bScriptExecution))
		{
			$bScriptExecution->Stop();
			tpl_set_var('scripttime', sprintf('%1.3f', $bScriptExecution->Diff()));
		}
		else
			tpl_set_var('scripttime', sprintf('%1.3f', 0));

		tpl_set_var('sponsorbottom', $opt['page']['sponsor']['bottom']);

		if (isset($opt['locale'][$locale]['page']['subtitle1'])) $opt['page']['subtitle1'] = $opt['locale'][$locale]['page']['subtitle1'];
		if (isset($opt['locale'][$locale]['page']['subtitle2'])) $opt['page']['subtitle2'] = $opt['locale'][$locale]['page']['subtitle2'];
		tpl_set_var('opt_page_subtitle1', $opt['page']['subtitle1']);
		tpl_set_var('opt_page_subtitle2', $opt['page']['subtitle2']);
		tpl_set_var('opt_page_title', $opt['page']['title']);

		if ($opt['logic']['license']['disclaimer'])
		{
			if (isset($opt['locale'][$locale]['page']['license_url']))
				$lurl = $opt['locale'][$locale]['page']['license_url'];
			else
				$lurl = $opt['locale']['EN']['page']['license_url'];

			if (isset($opt['locale'][$locale]['page']['license']))
				$ltext = $opt['locale'][$locale]['page']['license'];
			else
				$ltext = $opt['locale']['EN']['page']['license'];

			$ld = '<p class="sidebar-maintitle">' .
			      $translate->t('Datalicense', '', '', 0) .
						'</p>' .
            '<div style="margin:20px 0 16px 0; width:100%; text-align:center;">' .
            mb_ereg_replace('%1', $lurl, $ltext) .
						'</div>';
			tpl_set_var('license_disclaimer', $ld);
		}
		else
			tpl_set_var('license_disclaimer','');

		$bTemplateBuild = new Cbench;
		$bTemplateBuild->Start();

		//set {functionsbox}
		global $page_functions, $functionsbox_start_tag, $functionsbox_middle_tag, $functionsbox_end_tag;

		if (isset($page_functions))
		{
			$functionsbox = $functionsbox_start_tag;
			foreach ($page_functions AS $func)
			{
				if ($functionsbox != $functionsbox_start_tag)
				{
					$functionsbox .= $functionsbox_middle_tag;
				}
				$functionsbox .= $func;
			}
			$functionsbox .= $functionsbox_end_tag;

			tpl_set_var('functionsbox', $functionsbox);
		}

		/* prepare user country selection
		 */
		$tpl_usercountries = array();
		$rsUserCountries = sql("SELECT `countries_options`.`country`, 
		                  IF(`countries_options`.`nodeId`='&1', 1, IF(`countries_options`.`nodeId`!=0, 2, 3)) AS `group`, 
		                  IFNULL(`sys_trans_text`.`text`, `countries`.`name`) AS `name` 
		             FROM `countries_options` 
		       INNER JOIN `countries` ON `countries_options`.`country`=`countries`.`short`
		        LEFT JOIN `sys_trans` ON `countries`.`trans_id`=`sys_trans`.`id` 
		        LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&2' 
		            WHERE `countries_options`.`display`=1 
		         ORDER BY `group` ASC,
		                  IFNULL(`sys_trans_text`.`text`, `countries`.`name`) ASC", $oc_nodeid, $locale);
		while ($rUserCountries = sql_fetch_assoc($rsUserCountries))
			$tpl_usercountries[] = $rUserCountries;
		sql_free_result($rsUserCountries);

		//include language specific expressions, so that they are available in the template code
		include $langpath . '/expressions.inc.php';

		//load main template
		tpl_set_var('backgroundimage','<div id="bg1">&nbsp;</div><div id="bg2">&nbsp;</div>');
		tpl_set_var('bodystyle','');

		if (isset($_REQUEST['print']) && $_REQUEST['print'] == 'y')
			$sCode = read_file($stylepath . '/main_print.tpl.php');
		else if (isset($_REQUEST['popup']) && $_REQUEST['popup'] == 'y')
			$sCode = read_file($stylepath . '/popup.tpl.php');
		else
			$sCode = read_file($stylepath . '/main.tpl.php');
		$sCode = '?>' . $sCode;

		//does template exist?
		if (!file_exists($stylepath . '/' . $tplname . '.tpl.php'))
		{
			//set up the error template
			$error = true;
			tpl_set_var('error_msg', htmlspecialchars($error_pagenotexist, ENT_COMPAT, 'UTF-8'));
			tpl_set_var('tplname', $tplname);
			$tplname = 'error';
		}

		//read the template
		$sTemplate = read_file($stylepath . '/' . $tplname . '.tpl.php');
		$sCode = mb_ereg_replace('{template}', $sTemplate, $sCode);

		//process translations
		$sCode = tpl_do_translation($sCode);

		//process the template replacements
		$sCode = tpl_do_replace($sCode);

		//store the cookie
		write_cookie_settings();

		//send http-no-caching-header
		http_write_no_cache();

		// write UTF8-Header
		header('Content-type: text/html; charset=utf-8');

		//run the template code
		eval($sCode);

		//disconnect the database
		if ($dbdisconnect) db_disconnect();
	}

	function http_write_no_cache()
	{
		// HTTP/1.1
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		// HTTP/1.0
		header("Pragma: no-cache");
		// Date in the past
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		// always modified
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	}

	//redirect to another site to display, i.e. to view a cache after logging
	function tpl_redirect($page)
	{
		global $absolute_server_URI;

		//page has to be the filename without domain i.e. 'viecache.php?cacheid=1'
		write_cookie_settings();
		http_write_no_cache();

		header("Location: " . $absolute_server_URI . $page);
		exit;
	}

	//redirect to another absolute url
	function tpl_redirect_absolute($absolute_server_URI)
	{
		//page has to be the filename with domain i.e. 'http://abc.de/viecache.php?cacheid=1'
		write_cookie_settings();
		http_write_no_cache();

		header("Location: " . $absolute_server_URI);
		exit;
	}

	//process the template replacements
	//no_eval_replace - if true, variables will be replaced that are
	//                  marked as "no_eval"
	function tpl_do_replace($str)
	{
		global $vars, $no_eval_vars;

		if (is_array($vars))
		{
			foreach ($vars as $varname=>$varvalue)
			{
				if ($no_eval_vars[$varname] == false)
				{
					$str = mb_ereg_replace('{' . $varname . '}', $varvalue, $str);
				}
				else
				{
					$replave_var_name = 'tpl_replace_var_' . $varname;

					global $$replave_var_name;
					$$replave_var_name = $varvalue;

					//replace using php-echo
					$str = mb_ereg_replace('{' . $varname . '}', '<?php global $' . $replave_var_name . '; echo $tpl_replace_var_' . $varname . '; ?>', $str);
				}
			}
		}

		return $str;
	}

	function tpl_errorMsg($tplnameError, $msg)
	{
		global $tplname;

		$tplname = 'error';
		tpl_set_var('error_msg', $msg);
		tpl_set_var('tplname', $tplnameError);

		tpl_BuildTemplate();
		exit;
	}


	function load_gettext()
	{
		global $cookie, $opt, $rootpath, $locale;

		$locale = $cookie->get('locale');
		if (!isset($opt['locale'][$locale]))
			$locale = $opt['template']['default']['locale'];
		$opt['template']['locale'] = $locale;

		bindtextdomain('messages', $rootpath . '/cache2/translate');
		set_php_locale();
		textdomain('messages');
	}

	function tpl_do_translation($sCode)
	{
		global $opt, $style, $tplname;

		$sResultCode = '';
		$nCurrentPos = 0;
		while ($nCurrentPos < mb_strlen($sCode))
		{
			$nStartOfHTML = mb_strpos($sCode, '?>', $nCurrentPos);
			if ($nStartOfHTML === false)
			{
				$sResultCode .= mb_substr($sCode, $nCurrentPos, mb_strlen($sCode) - $nCurrentPos);
				$nCurrentPos = mb_strlen($sCode);
			}
			else
			{
				$nEndOfHTML = mb_strpos($sCode, '<?', $nStartOfHTML);
				if ($nEndOfHTML === false) $nEndOfHTML = mb_strlen($sCode);

				$sResultCode .= mb_substr($sCode, $nCurrentPos, $nStartOfHTML - $nCurrentPos);
				$sHTMLCode = mb_substr($sCode, $nStartOfHTML, $nEndOfHTML - $nStartOfHTML);
				$sResultCode .= gettext_do_html($sHTMLCode);

				$nCurrentPos = $nEndOfHTML;
			}
		}

		return $sResultCode;
	}

	function gettext_do_html($sCode)
	{
		$sResultCode = '';
		$nCurrentPos = 0;
		while ($nCurrentPos < mb_strlen($sCode))
		{
			$nStartOf = mb_strpos($sCode, '{'.'t}', $nCurrentPos);
			if ($nStartOf === false)
			{
				$sResultCode .= mb_substr($sCode, $nCurrentPos, mb_strlen($sCode) - $nCurrentPos);
				$nCurrentPos = mb_strlen($sCode);
			}
			else
			{
				$nEndOf = mb_strpos($sCode, '{/t}', $nStartOf);
				if ($nEndOf === false)
					$nEndOf = mb_strlen($sCode);
				else
					$nEndOf += 4;

				$sResultCode .= mb_substr($sCode, $nCurrentPos, $nStartOf - $nCurrentPos);
				$sTransString = mb_substr($sCode, $nStartOf+3, $nEndOf - $nStartOf-3-4);

				$sResultCode .= t($sTransString);

				$nCurrentPos = $nEndOf;
			}
		}

		return $sResultCode;
	}
	
	function t($str)
	{
		global $translate;

		$str = $translate->t($str, '', basename(__FILE__), __LINE__);
		$args = func_get_args();
		for ($nIndex=count($args)-1; $nIndex>0; $nIndex--)
			$str = str_replace('%' . $nIndex, $args[$nIndex], $str);

		return $str;
	}

	function t_prepare_text($text)
	{
		$text = mb_ereg_replace("\t", ' ', $text);
		$text = mb_ereg_replace("\r", ' ', $text);
		$text = mb_ereg_replace("\n", ' ', $text);
		while (mb_strpos($text, '  ') !== false)
			$text = mb_ereg_replace('  ', ' ', $text);

		return $text;
	}

	function getUserCountry()
	{
		global $opt, $cookie, $usr;

		// language specified in cookie?
		if ($cookie->is_set('usercountry'))
		{
			$sCountry = $cookie->get('usercountry', null);
			if ($sCountry != null)
				return $sCountry;
		}

		// user specified a language?
		if (isset($usr) && ($usr !== false))
		{
			$sCountry = sqlValue("SELECT `country` FROM `user` WHERE `user_id`='" . ($usr['userid']+0) . "'", null);
			if ($sCountry != null)
				return $sCountry;
		}

		// default country of this language
		if (isset($opt['template']['locale']) && isset($opt['locale'][$opt['template']['locale']]['country']))
			return $opt['locale'][$opt['template']['locale']]['country'];

		// default country of installation (or domain)
		if (isset($opt['template']['default']['country']))
			return $opt['template']['default']['country'];
		
		// country could not be determined by the above checks -> return "GB"
		return 'GB';
	}


// external help embedding
// pay attention to use only ' quotes in $text (escape other ')
//
// see corresponding function in lib2/common.inc.php
function helppagelink($ocpage)
{
	global $opt, $locale, $translate;

	$help_locale = $locale;
	$rs = sql("SELECT `helppage` FROM `helppages` WHERE `ocpage`='&1' AND `language`='&2'",
             $ocpage, $help_locale);
	if (mysql_num_rows($rs) == 0)
	{
		mysql_free_result($rs);
		$rs = sql("SELECT `helppage` FROM `helppages` WHERE `ocpage`='&1' AND `language`='*'",
		          $ocpage);
	}
	if (mysql_num_rows($rs) == 0)
	{
		mysql_free_result($rs);
		$rs = sql("SELECT `helppage` FROM `helppages` WHERE `ocpage`='&1' AND `language`='&2'",
		          $ocpage, $opt['template']['default']['fallback_locale']);
		if (mysql_num_rows($rs) > 0)
			$help_locale = $opt['template']['default']['fallback_locale'];
	}

	if (mysql_num_rows($rs) > 0)
	{
		$record = sql_fetch_array($rs);
		$helppage = $record['helppage'];
	}
	else
		$helppage = "";
	mysql_free_result($rs);

	$imgtitle = $translate->t('Instructions', '', basename(__FILE__), __LINE__);
	$imgtitle = "alt='" . $imgtitle . "' title='" . $imgtitle  . "'";

	if (substr($helppage,0,1) == "!")
		return "<a class='nooutline' href='" . substr($helppage,1) . "' " . $imgtitle . " target='_blank'>";
	else
		if ($helppage != "" && isset($opt['locale'][$help_locale]['helpwiki']))
			return "<a class='nooutline' href='" . $opt['locale'][$help_locale]['helpwiki'] .
			       str_replace(' ','_',$helppage) . "' " . $imgtitle . " target='_blank'>";

	return "";
}

?>
