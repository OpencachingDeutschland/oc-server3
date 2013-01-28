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
	if (!isset($rootpath)) $rootpath = './';
	
	//default used language
	if (!isset($lang)) $lang = 'de';
	
	//default timezone
	if (!isset($timezone)) $timezone = 'Europe/Berlin';

	//default used style
	if (!isset($style)) $style = 'ocstyle';

	//pagetitle
	if (!isset($pagetitle)) $pagetitle = 'www.opencaching.de';
	$opt['page']['title'] = 'OPENCACHING';
	$opt['page']['subtitle1'] = 'Geocaching with Opencaching';
	$opt['page']['subtitle2'] = '';
	
	//id of the node
	$oc_nodeid = 4;
	$opt['logic']['node']['id'] = 4;
	
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
	if (!isset($emailaddr)) $emailaddr = 'noreply@opencaching.de';
	
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

  // see config2/settings-dist.inc.php
	$opt['template']['default']['locale'] = 'DE';      // may be overwritten by $opt['domain'][...]['locale']

  // include all locale settings
  require_once($rootpath . 'config2/locale.inc.php');

	/* Sponsoring advertisements
	 * (plain HTML)
	 */
	//$opt['page']['sponsor']['topright'] = '<div class="site-slogan" style="background-image: url(resource2/ocstyle/images/darkbluetransparent.png);"><div style="width: 100%; text-align: left;"><p class="search"><a href="http://www.wanderjugend.de" target="_blank"><img border="0" align="right" style="margin-left: 10px;" src="resource2/ocstyle/images/dwj.gif" width="40px" height="20px" alt="... die outdoororientierte Jugendorganisation des Deutschen Wanderverbandes" /></a> Unterst&uuml;tzt und gef&ouml;rdert durch<br />die Deutsche Wanderjugend</p> </div></div>';
	//$opt['page']['sponsor']['bottom'] = 'Hosting by <a target="_blank" href="http://www.schwarzwald.de">www.schwarzwald.de</a>';
	$opt['page']['sponsor']['topright'] = '';
	$opt['page']['sponsor']['bottom'] = '';

	/* replicated slave databases
	 * use same config as in config2/settings.inc.php (!)
	 */
	$opt['db']['slaves'] = array();
	$opt['db']['slave']['max_behind'] = 180;

	// use this slave when a specific slave must be connected
	// (e.g. xml-interface and mapserver-results)
	// you can use -1 to use the master (not recommended, because replicated to slaves)
	$opt['db']['slave']['primary'] = -1;
?>