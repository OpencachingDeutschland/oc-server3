<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Inherit Smarty-Class and extend it
 ***************************************************************************/

require_once($opt['rootpath'] . 'lib2/smarty/Smarty.class.php');
require_once($opt['rootpath'] . 'lib2/db.inc.php');
require_once($opt['rootpath'] . 'lib2/logic/labels.inc.php');

class OcSmarty extends Smarty
{
	var $name = 'sys_nothing';
	var $main_template = 'sys_main';
	var $bench = null;
	var $compile_id = null;
	var $cache_id = null;
	var $title = '';
	var $menuitem = null;
	var $nowpsearch = false;

	// no header, menu or footer
	var $popup = false;

	// show a thin border when using popup
	// disable popupmargin to appear fullscreen
	var $popupmargin = true;

	// url to call if login is required
	var $target = '';

	var $header_javascript = array();
	var $body_load = array();
	var $body_unload = array();

	function OcSmarty()
	{
		global $opt, $sqldebugger;
		require_once($opt['rootpath'] . 'lib2/bench.inc.php');
		$this->bench = new CBench();
		$this->bench->start();

		// configuration
		$this->template_dir = $opt['stylepath'];
		$this->compile_dir = $opt['rootpath'] . 'cache2/smarty/compiled/';
		$this->cache_dir = $opt['rootpath'] . 'cache2/smarty/cache/';
		$this->plugins_dir = array('plugins', 'ocplugins');
		
		// disable caching ... if caching is enabled, 1 hour is default
		$this->caching = false;
		$this->cache_lifetime = 3600; // default

		// register additional functions
		require_once($opt['rootpath'] . 'lib2/smarty/ocplugins/block.nocache.php');
		$this->register_block('nocache', 'smarty_block_nocache', false);
		$this->load_filter('pre', 't');

		if ($opt['session']['mode'] == SAVE_SESSION)
			$this->load_filter('output', 'session');

		// cache control
		if (($opt['debug'] & DEBUG_TEMPLATES) == DEBUG_TEMPLATES)
			$this->force_compile = true;

		// process debug level
		if (($opt['debug'] & DEBUG_SQLDEBUGGER) == DEBUG_SQLDEBUGGER)
		{
			require_once($opt['rootpath'] . 'lib2/sqldebugger.class.php');
		}
		else if (($opt['debug'] & DEBUG_OUTOFSERVICE) == DEBUG_OUTOFSERVICE)
		{
			$this->name = 'sys_outofservice';
			$this->display();
		}

		/* set login target
		 */
		if (isset($_REQUEST['target']))
		{
			$this->target = trim($_REQUEST['target']);
			if (strtolower(substr($this->target, 0, 7)) == 'http://')
				$this->target = '';
		}
		else
		{
			$target = basename($_SERVER['PHP_SELF']) . '?';

			// REQUEST-Variablen durchlaufen und an target anhaengen
			reset($_REQUEST);
			while (list($varname, $varvalue) = each($_REQUEST))
				if (in_array($varname, $opt['logic']['targetvars']))
					$target .= urlencode($varname) . '=' . urlencode($varvalue) . '&';
			reset($_REQUEST);

			if (mb_substr($target, -1) == '?' || mb_substr($target, -1) == '&')
				$target = mb_substr($target, 0, -1);

			$this->target = $target;
		}
	}

	/* ATTENTION: copied from internal implementation!
	 */
	function compile($resource_name, $compile_id = null)
	{
    if (!isset($compile_id)) {
        $compile_id = $this->compile_id;
    }

    $this->_compile_id = $compile_id;

    // load filters that are marked as autoload
    if (count($this->autoload_filters)) {
        foreach ($this->autoload_filters as $_filter_type => $_filters) {
            foreach ($_filters as $_filter) {
                $this->load_filter($_filter_type, $_filter);
            }
        }
    }
 
		$_smarty_compile_path = $this->_get_compile_path($resource_name);
		
		// if we just need to display the results, don't perform output
		// buffering - for speed
		$_cache_including = $this->_cache_including;
		$this->_cache_including = false;

		// compile the resource
		if (!$this->_is_compiled($resource_name, $_smarty_compile_path))
			$this->_compile_resource($resource_name, $_smarty_compile_path);

		$this->_cache_including = $_cache_including;
	}

	function display($dummy1=null, $dummy2=null, $dummy3=null)
	{
		global $opt, $db, $cookie, $login, $menu, $sqldebugger, $translate;
		global $useragent_msie;
		$cookie->close();

		// if the user is an admin, dont cache the content
		if (isset($login))
			if ($login->admin)
				$this->caching = false;

    //Give Smarty access to the whole options array.
    $this->assign('siteSettings', $opt);

    //Should we remove this whole block since we now have
    //access using the siteSettings above?
		// assign main template vars
		// ... and some of the $opt
		$locale = $opt['template']['locale'];

		$optn['debug'] = $opt['debug'];
		$optn['template']['locales'] = $opt['template']['locales'];
		$optn['template']['locale'] = $opt['template']['locale'];
		$optn['template']['style'] = $opt['template']['style'];
		$optn['template']['country'] = $login->getUserCountry();
		$optn['page']['subtitle1'] = isset($opt['locale'][$locale]['page']['subtitle1']) ? $opt['locale'][$locale]['page']['subtitle1'] : $opt['page']['subtitle1'];
		$optn['page']['subtitle2'] = isset($opt['locale'][$locale]['page']['subtitle2']) ? $opt['locale'][$locale]['page']['subtitle2'] : $opt['page']['subtitle2'];
		$optn['page']['headimagepath'] = $opt['page']['headimagepath'];
		$optn['page']['headoverlay'] = $opt['page']['headoverlay'];
		$optn['page']['max_logins_per_hour'] = $opt['page']['max_logins_per_hour'];
		$optn['page']['absolute_url'] = $opt['page']['absolute_url'];
		$optn['page']['absolute_urlpath'] = parse_url($opt['page']['absolute_url'], PHP_URL_PATH);
		$optn['page']['target'] = $this->target;
		$optn['page']['showdonations'] = $opt['page']['showdonations'];
		$optn['page']['title'] = $opt['page']['title'];
		$optn['page']['nowpsearch'] = $this->nowpsearch;
		$optn['page']['header_javascript'] = $this->header_javascript;
		$optn['page']['body_load'] = $this->body_load;
		$optn['page']['body_unload'] = $this->body_unload;
		$optn['page']['sponsor'] = $opt['page']['sponsor'];
		$optn['page']['showsocialmedia'] = $opt['page']['showsocialmedia'];
		$optn['template']['title'] = $this->title;
		$optn['template']['caching'] = $this->caching;
		$optn['template']['popup'] = $this->popup;
		$optn['template']['popupmargin'] = $this->popupmargin;
		$optn['format'] = $opt['locale'][$opt['template']['locale']]['format'];
		$optn['mail'] = $opt['mail'];
		$optn['lib'] = $opt['lib'];
		$optn['geokrety'] = $opt['geokrety'];
		$optn['template']['usercountrieslist'] = labels::getLabels('usercountrieslist');
		$optn['help']['oconly'] = helppagelink('oconly','OConly');
		$optn['msie'] = $useragent_msie;

		// url-sessions? (for session timout display)
		$optn['session']['url'] = false;
		if ($opt['session']['mode']==SAVE_SESSION && $login->userid!=0)
		{
			if (isset($_GET['SESSION']) || isset($_POST['SESSION']))
			{
				$optn['session']['url'] = true;
			}

			$optn['session']['id'] = session_id();
		}

		if (isset($login))
		{
			$loginn['username'] = $login->username;
			$loginn['userid'] = $login->userid;
			$loginn['admin'] = $login->admin;
		}
		else
		{
			$loginn['username'] = '';
			$loginn['userid'] = '';
			$loginn['admin'] = '';
		}

		// build menu
		if ($this->menuitem == null)
			$menu->SetSelectItem(MNU_ROOT);
		else
			$menu->SetSelectItem($this->menuitem);

		$this->assign('topmenu', $menu->getTopMenu());
		$this->assign('submenu', $menu->getSubMenu());
		$this->assign('breadcrumb', $menu->getBreadcrumb());
		$this->assign('menucolor', $menu->getMenuColor());
		$this->assign('helplink', helppagelink($this->name));

		if ($this->title == '')
			$optn['template']['title'] = $menu->GetMenuTitle();

		// build address for switching locales
		$locale_pageadr = $_SERVER['REQUEST_URI'];
		// workaround for http://redmine.opencaching.de/issues/703
		$strange_things_pos = strpos($locale_pageadr,".php/");
		if ($strange_things_pos)
			$locale_pageadr = substr($locale_pageadr,0,$strange_things_pos + 4);
		$lpos = strpos($locale_pageadr,"locale=");
		if ($lpos)
			$locale_pageadr = substr($locale_pageadr,0,$lpos);
		else
		{
			$urx = explode('#', $locale_pageadr);
			$locale_pageadr = $urx[0];
			if (strpos($locale_pageadr,'?') == 0)
				$locale_pageadr .= '?';
			else
				$locale_pageadr .= '&';
		}
		$this->assign('locale_pageadr', $locale_pageadr);

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

			$this->assign('license_disclaimer', mb_ereg_replace('%1', $lurl, $ltext));
		}
		else
			$this->assign('license_disclaimer','');

		$this->assign('opt', $optn);
		$this->assign('login', $loginn);

		if ($db['connected'] == true)
			$this->assign('sys_dbconnected', true);
		else
			$this->assign('sys_dbconnected', false);
		$this->assign('sys_dbslave', ($db['slave_id'] != -1));

		if ($this->template_exists($this->name . '.tpl'))
			$this->assign('template', $this->name);
		else if ($this->name != 'sys_error')
			$this->error(ERROR_TEMPLATE_NOT_FOUND);

		$this->bench->stop();
		$this->assign('sys_runtime', $this->bench->diff());

		$this->assign('screen_css_time',filemtime($opt['rootpath'] . "resource2/" . $opt['template']['style'] . "/css/style_screen.css"));
		$this->assign('screen_msie_css_time',filemtime($opt['rootpath'] . "resource2/" . $opt['template']['style'] . "/css/style_screen_msie.css"));
		$this->assign('print_css_time',filemtime($opt['rootpath'] . "resource2/" . $opt['template']['style'] . "/css/style_print.css"));

		// check if the template is compiled
		// if not, check if translation works correct
    $_smarty_compile_path = $this->_get_compile_path($this->name);
    if (!$this->_is_compiled($this->name, $_smarty_compile_path) && $this->name != 'error')
    {
			$internal_lang = $translate->t('INTERNAL_LANG', 'all', 'OcSmarty.class.php', '');
			if (($internal_lang != $opt['template']['locale']) && ($internal_lang != 'INTERNAL_LANG'))
				$this->error(ERROR_COMPILATION_FAILED);
    }

		if ($this->is_cached() == true)
			$this->assign('sys_cached', true);
		else
			$this->assign('sys_cached', false);

		if (($opt['debug'] & DEBUG_SQLDEBUGGER) == DEBUG_SQLDEBUGGER)
		{
			require_once($opt['rootpath'] . 'lib2/sqldebugger.class.php');

			parent::fetch($this->main_template . '.tpl', $this->get_cache_id(), $this->get_compile_id());

			$this->clear_all_assign();
			$this->main_template = 'sys_sqldebugger';
			$this->assign('commands', $sqldebugger->getCommands());
			$this->assign('cancel', $sqldebugger->getCancel());
			unset($sqldebugger);

			$this->assign('opt', $optn);
			$this->assign('login', $loginn);

			$this->caching = false;

			// unset sqldebugger to allow proper translation of sqldebugger template
			$opt['debug'] = $opt['debug'] & ~DEBUG_SQLDEBUGGER;

			$this->header();
			parent::display($this->main_template . '.tpl');
		}
		else
		{
			$this->header();
			parent::display($this->main_template . '.tpl', $this->get_cache_id(), $this->get_compile_id());
		}

		exit;
	}

	// show an error dialog
	function error($id)
	{
		$this->clear_all_assign();
		$this->caching = false;

		$this->assign('page', $this->name);
		$this->assign('id', $id);

		if ($this->menuitem == null)
			$this->menuitem = MNU_ERROR;

		$args = func_get_args();
		unset($args[0]);
		for ($i = 1; isset($args[$i]); $i++)
			$this->assign('p' . $i, $args[$i]);

		$this->name = 'error';
		$this->display();
	}

	// check if this template is valid
	function is_cached($dummy1=null, $dummy2=null, $dummy3=null)
	{
		global $login;

		// if the user is an admin, dont cache the content
		if (isset($login))
			if ($login->admin)
				return false;

		return parent::is_cached($this->main_template . '.tpl', $this->get_cache_id(), $this->get_compile_id());
	}
	
	function get_cache_id()
	{
		global $opt;
		return $this->name . '|' . $this->cache_id;
	}
	
	function get_compile_id()
	{
		global $opt;
		return $opt['template']['style'] . '|' . $opt['template']['locale'] . '|' . $this->compile_id;
	}
	
	function redirect($page)
	{
		global $cookie, $opt;
		$cookie->close();

		// close db-connection
		sql_disconnect();

		$this->header();

		if (strpos($page, "\n") !== false)
			$page = substr($page, 0, strpos($page, "\n"));

		// redirect
		if (substr($page, 0, 7) != 'http://')
		{
			if (substr($page, 0, 1) == '/') $page = substr($page, 1);
			$page = $opt['page']['absolute_url'] . $page;
		}

		if ($opt['session']['mode'] == SAVE_SESSION)
		{
			if (defined('SID') && SID != '' && session_id() != '')
			{
				if (strpos($page, '?') === false)
					header("Location: " . $page . '?' . urlencode(session_name()) . '=' . urlencode(session_id()));
				else
					header("Location: " . $page . '&' . urlencode(session_name()) . '=' . urlencode(session_id()));
			}
			else
				header("Location: " . $page);
		}
		else
			header("Location: " . $page);
		exit;
	}

	function redirect_login()
	{
		// we cannot redirect the POST-data
		if (count($_POST) > 0)
			$this->error(ERROR_LOGIN_REQUIRED);

		// ok ... redirect the get-data
		$target = 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$this->redirect('login.php?target=' . urlencode($target));
	}

	function assign_rs($name, $rs)
	{
		$items = array();
		while ($r = sql_fetch_assoc($rs))
			$items[] = $r;
		$this->assign($name, $items);
	}

	function add_header_javascript($src)
	{
		$this->header_javascript[] = $src;
	}

	function add_body_load($script)
	{
		$this->body_load[] = $script;
	}

	function add_body_unload($script)
	{
		$this->body_unload[] = $script;
	}

	function header()
	{
		global $opt;
		global $cookie;

		if ($opt['gui'] == GUI_HTML)
		{
			// charset setzen
			header('Content-type: text/html; charset=utf-8');

			// HTTP/1.1
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			// HTTP/1.0
			header("Pragma: no-cache");
			// Date in the past
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			// always modified
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

			// set the cookie
			$cookie->header();
		}
	}

	/* - trim target and strip newlines
	 * - use sDefault if sTarget is absolute and sDefault!=null
	 */
	function checkTarget($sTarget, $sDefault=null)
	{
		if (mb_strpos($sTarget, "\n") !== false)
			$sTarget = mb_substr($sTarget, 0, mb_strpos($sTarget, "\n"));

		$sTarget = mb_trim($sTarget);

		if (mb_strtolower(mb_substr($sTarget, 0, 7)) == 'http://' || $sTarget=='')
			if ($sDefault != null)
				return $sDefault;

		return $sTarget;
	}
}
?>