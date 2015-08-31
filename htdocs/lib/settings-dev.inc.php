<?php
/****************************************************************************
														 ./lib/settings.inc.php
															-------------------
		begin                : Mon June 14 2004

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************
 *
 *	Unicode Reminder メモ
 *	                                         				                                
 *  Default settings for OC.de developer system. See also
 *    - settings-dist.inc.php for sample settings
 *    - settings.inc.php for local settings
 *    - config2/settings* for version-2-code settings
	
 ****************************************************************************/
 
 	//relative path to the root directory
	if (!isset($rootpath)) $rootpath = dirname(__FILE__) . '/../';
	
	//default used language
	if (!isset($lang)) $lang = 'de';
	
	//default timezone
	if (!isset($timezone)) $timezone = 'Europe/Berlin';

	//default used style
	if (!isset($style)) $style = 'ocstyle';

	// include common settings of lib1 and lib2
	require_once($rootpath . 'config2/common-settings.inc.php');

	//id of the node
	$oc_nodeid = $opt['logic']['node']['id'];
	
	//name of the cookie
	$opt['cookie']['name'] = 'oc_devel';
	$opt['cookie']['path'] = '/';
	$opt['cookie']['domain'] = '';

	//Debug?
	if (!isset($debug_page)) $debug_page = true;
	$develwarning = '<div id="debugoc"><font size="5" face="arial" color="red"><center>Entwicklersystem - nur Testdaten</center></font></div>';
	
	//site in service? Set to false when doing bigger work on the database to prevent error's
	if (!isset($site_in_service)) $site_in_service = true;
	
	//if you are running this site on a other domain than staging.opencaching.de, you can set
	//this in private_db.inc.php, but don't forget the ending /
	$absolute_server_URI = $dev_baseurl . '/';
	
	// EMail address of the sender
	if (!isset($maildomain)) $maildomain  = 'local.opencaching.de';
	if (!isset($emailaddr)) $emailaddr = 'root@' . $maildomain;
	
	// location of cache images
	if (!isset($picdir)) $picdir = $rootpath . 'images/uploads';
	if (!isset($picurl)) $picurl = $absolute_server_URI . 'images/uploads';

	// Thumbsize
	$thumb_max_width = 175;
	$thumb_max_height = 175;

	// maximal size of images
	if (!isset($maxpicsize)) $maxpicsize = 153600;
	
	// allowed extensions of images
	if (!isset($picextensions)) $picextensions = ';jpg;jpeg;gif;png;bmp;';
	
	// news settings
	$use_news_approving = true;
	$news_approver_email = 'root';

	$opt['page']['showdonations'] = true;
	$opt['page']['showsocialmedia'] = true;
    
	// date format
	$opt['db']['dateformat'] = 'Y-m-d H:i:s';

	// warnlevel for sql-execution
	$sql_errormail = 'root';
	$dberrormail = $sql_errormail;
	$sql_warntime = 100000;

	$sql_allow_debug = 0;
	
	// minimum of 24 chars
	$sql_debug_cryptkey = 'this is my very, very secret \'secret key\'';

	// replacements for sql()
	$sql_replacements['db'] = $dbname;
	$sql_replacements['tmpdb'] = $tmpdbname;

	// safemode_zip-binary
	$safemode_zip = '/var/www/bin/phpzip.php';
	$zip_basedir = $dev_basepath . $dev_codepath . 'htdocs/download/zip/';
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
	$opt['page']['max_logins_per_hour'] = 1000;    // for development ... 

  // data license
	$opt['logic']['license']['disclaimer'] = true;   // also in lib2/settings-dist.inc.php
	$opt['logic']['license']['terms'] = $absolute_server_URI . 'articles.php?page=impressum#datalicense';

	$opt['logic']['admin']['listingadmin_notification'] = 'root';

  // include all locale settings
  require_once($rootpath . 'config2/locale.inc.php');

	/* replicated slave databases
	 * use same config as in config2/settings.inc.php (!)
	 */
	$opt['db']['slaves'] = array();
	$opt['db']['slave']['max_behind'] = 180;

	// use this slave when a specific slave must be connected
	// (e.g. xml-interface and mapserver-results)
	// you can use -1 to use the master (not recommended, because replicated to slaves)
	$opt['db']['slave']['primary'] = -1;

	$opt['template']['locales']['DA']['show'] = false;
	$opt['template']['locales']['JA']['show'] = false;
	$opt['template']['locales']['NL']['show'] = false;
	$opt['template']['locales']['PL']['show'] = false;
	$opt['template']['locales']['PT']['show'] = false;
	$opt['template']['locales']['RU']['show'] = false;
	$opt['template']['locales']['SV']['show'] = false;
	$opt['template']['locales']['NO']['show'] = false;

?>
