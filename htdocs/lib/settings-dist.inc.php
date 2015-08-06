<?php
/****************************************************************************
														 ./lib/settings.inc.php
															-------------------
		begin                : Mon June 14 2004

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************

		Unicode Reminder メモ
	                                         				                                
		server specific settings
	
		this file may be outdated and should be reviewed
 ****************************************************************************/
 
 	//relative path to the root directory
	if (!isset($rootpath)) $rootpath = './';

	//default used language
	if (!isset($lang)) $lang = 'de';

	//default timezone
	if (!isset($timezone)) $timezone = 'Europe/Berlin';

	//default used style
	if (!isset($style)) $style = 'ocstyle';

	//pagetitle
	if (!isset($pagetitle)) $pagetitle = 'www.opencaching.de';
	
	//id of the node
	$oc_nodeid = 4;
	
	//name of the cookie
	$opt['cookie']['name'] = 'oc_devel';
	$opt['cookie']['path'] = '/';
	$opt['cookie']['domain'] = '';

	//Debug?
	if (!isset($debug_page)) $debug_page = true;
	$develwarning = '<div id="debugoc"><font size="5" face="arial" color="red"><center>Entwicklersystem - nur Testdaten!</center></font></div>';
	
	//site in service? Set to false when doing bigger work on the database to prevent error's
	if (!isset($site_in_service)) $site_in_service = true;
	
	//if you are running this site on a other domain than staging.opencaching.de, you can set
	//this in private_db.inc.php, but don't forget the ending /
	$absolute_server_URI = 'http://www.opencaching.de/';
	
	// 'From' EMail address for admin error messages and log removals
	if (!isset($emailaddr)) $emailaddr = 'noreply@opencaching.de';
	
	// location of cache images
	if (!isset($picdir)) $picdir = $rootpath . 'images/uploads';  // Ocprop
	if (!isset($picurl)) $picurl = 'http://www.opencaching.de/images/uploads';

	// Thumbsize
	$thumb_max_width = 175;
	$thumb_max_height = 175;

	// maximal size of images
	if (!isset($maxpicsize)) $maxpicsize = 153600;
	
	// allowed extensions of images
	if (!isset($picextensions)) $picextensions = ';jpg;jpeg;gif;png;bmp;';
	
	// news settings
	$use_news_approving = true;
	$news_approver_email = 'news-approver@devel.opencaching.de';

	$opt['page']['showdonations'] = false;
	$opt['page']['showsocialmedia'] = false;
	$opt['page']['headoverlay'] = 'oc_head_alpha3_generic';

	//local database settings
	$dbusername = 'username';
	$dbname = 'database';
	$dbserver = 'server';
	$dbpasswd = 'password';
	$dbpconnect = false;

	$tmpdbname = 'test'; // empty db with CREATE and DROP priviledges

	// date format
	$opt['db']['dateformat'] = 'Y-m-d H:i:s';

	// warnlevel for sql-execution
	$sql_errormail = 'sqlerror@somewhere.net';
	$dberrormail = $sql_errormail;
	$sql_warntime = 0.1;

	$sql_allow_debug = 0;
	
	// minimum of 24 chars
	$sql_debug_cryptkey = 'this is my very, very secret \'secret key\'';

	// replacements for sql()
	$sql_replacements['db'] = $dbname;
	$sql_replacements['tmpdb'] = $tmpdbname;

	// safemode_zip-binary
	$safemode_zip = '/path/to/phpzip.php';
	$zip_basedir = '/path/to/html/download/zip/';
	$zip_wwwdir = 'download/zip/';

	$googlemap_key = "<key>";
	$googlemap_type = "G_MAP_TYPE"; // alternativ: _HYBRID_TYPE

  // cache_maps-settings
  //$cachemap_wms_url = 'http://www.top-sectret.oc/{min_lat},{min_lon},{max_lat},{max_lon}';
  $cachemap_wms_url = 'http://www.opencaching.de/cachemaps.php?wp={wp_oc}';
  $cachemap_size_lat = 0.2;
  $cachemap_size_lon = 0.2;
  $cachemap_pixel_x = 200;
  $cachemap_pixel_y = 200;
  $cachemap_url = 'images/cachemaps/';
  $cachemap_dir = $rootpath . $cachemap_url;

	$opt['translate']['debug'] = false;
  
  /* maximum number of failed logins per hour before that IP address is blocked
   * (used to prevent brute-force-attacks)
   */
	$opt['page']['max_logins_per_hour'] = 25;

	// block troublemakers
	$opt['page']['banned_user_agents'] = array();

  // copy of config2/settings-dist.inc.php
  /* pregenerated waypoint list for new caches
   * - Waypoint prefix (OC, OP, OZ etc.)
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

	// admin may use OC-team-comment log flag only when processing a cache report
	// see also setting in config2/settings.inc.php!
	$opt['logic']['admin']['team_comments_only_for_reports'] = true;
	$opt['logic']['admin']['enable_listing_admins'] = false;
	$opt['logic']['admin']['listingadmin_notification'] = 'contact@opencaching.xx';

	/*
	 * html purifier
	 */
	$opt['html_purifier']['cache_path'] = dirname(__FILE__).'/../cache2/html_purifier/';

  // see config2/settings-dist.inc.php
	$opt['template']['default']['locale'] = 'DE';      // may be overwritten by $opt['domain'][...]['locale']
	$opt['template']['default']['country'] = 'DE';     // may be overwritten by $opt['domain'][...]['country']
	$opt['template']['default']['fallback_locale'] = 'EN';   // may be overwritten by $opt['domain'][...]['article_locale']

  // include all locale settings
  require_once($rootpath . 'config2/locale.inc.php');

	$opt['page']['title'] = 'OPENCACHING';
	$opt['page']['subtitle1'] = 'Geocaching with Opencaching';
	$opt['page']['subtitle2'] = '';
	$opt['page']['headimagepath'] = '';

  /* Sponsoring advertisements
   * (plain HTML)
   */

  $opt['page']['sponsor']['bottom'] = 'Driven by the Opencaching Community';

	/* multi-domain settings
	 *
	 * if one of the domains matches $_SERVER['SERVER_NAME'], the default values will be overwritten
	 * can be used to host more than one locale on one server with multiple default-locales
	 */
	//$opt['domain']['www.opencaching.de']['url'] = 'http://www.opencaching.de/';
	//$opt['domain']['www.opencaching.de']['locale'] = 'DE';
	//$opt['domain']['www.opencaching.de']['style'] = 'ocstyle';
	//$opt['domain']['www.opencaching.de']['cookiedomain'] = '.opencaching.de';
	//$opt['domain']['www.opencaching.de']['country'] = 'DE';
	//$opt['domain']['www.opencaching.pl']['url'] = 'http://www.opencaching.pl/';
	//$opt['domain']['www.opencaching.pl']['locale'] = 'PL';
	//$opt['domain']['www.opencaching.pl']['style'] = 'ocstyle';
	//$opt['domain']['www.opencaching.pl']['cookiedomain'] = '.opencaching.pl';
	//$opt['domain']['www.opencaching.pl']['country'] = 'PL';

	/* replicated slave databases
	 * use same config as in config2/settings.inc.php (!)
	 */
	$opt['db']['slaves'] = array();

	/*
		$opt['db']['slaves'][0]['server'] = 'slave-ip-or-socket';
		
		// if a slave is no active, the slave will not be tracked
		// by online-check or purge of master logs!
		// Therefore you might have to initialize the replication again,
		// after activating a slave.
		$opt['db']['slaves'][0]['active'] = true;
		
		// relative weight compared to other slaves
		// see doc2/replicaiton.txt (!)
		$opt['db']['slaves'][0]['weight'] = 100;
		$opt['db']['slaves'][0]['username'] = '';
		$opt['db']['slaves'][0]['password'] = '';

		$opt['db']['slaves'][1]...
	*/

	// maximum time (sec) a slave is allowed to be behind
	// the state of the master database before no connection
	// is redirected to this slave
	$opt['db']['slave']['max_behind'] = 180;

	// use this slave when a specific slave must be connected
	// (e.g. xml-interface and mapserver-results)
	// you can use -1 to use the master (not recommended, because replicated to slaves)
	$opt['db']['slave']['primary'] = -1;


/* post_config() is invoked directly before the first HTML line of the main.tpl.php is sent to the client.
 */
function post_config()
{
	global $menu;

	$menu[] = array(
		'title' => t('Geokrety'),
		'menustring' => t('Geokrety'),
		'siteid' => 'geokrety',
		'visible' => true,
		'filename' => 'http://geokrety.org/index.php?lang=de_DE.UTF-8'
	);

	$menu[] = array(
		'title' => 'API',
		'menustring' => 'API',
		'siteid' => 'API',
		'visible' => true,
		'filename' => 'okapi'
	);
}

?>
