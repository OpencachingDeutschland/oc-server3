<?php
/***************************************************************************
														 ./lib/settings.inc.php
															-------------------
		begin                : Mon June 14 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*                                         				                                
	*   This program is free software; you can redistribute it and/or modify  	
	*   it under the terms of the GNU General Public License as published by  
	*   the Free Software Foundation; either version 2 of the License, or	    	
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

		Unicode Reminder メモ
	                                         				                                
	server specific settings
	
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
	
	// waypoint prefix of the node
	// OC = oc.de, OP = oc.pl, ... AA = local development
	$oc_waypoint_prefix = 'AA';
	
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
	
	// EMail address of the sender
	if (!isset($emailaddr)) $emailaddr = 'contact@opencaching.de';
	
	// location of cache images
	if (!isset($picdir)) $picdir = $rootpath . 'images/uploads';
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

	// see config2/settings-dist.inc.php
	$opt['template']['default']['locale'] = 'DE';      // may be overwritten by $opt['domain'][...]['locale']
	$opt['template']['default']['country'] = 'DE';     // may be overwritten by $opt['domain'][...]['country']

	$opt['template']['locales']['DE']['show'] = true;
	$opt['template']['locales']['DE']['flag'] = 'images/flag/DE.gif';
	$opt['template']['locales']['DE']['name'] = 'Deutsch';
	$opt['template']['locales']['FR']['show'] = true;
	$opt['template']['locales']['FR']['flag'] = 'images/flag/FR.gif';
	$opt['template']['locales']['FR']['name'] = 'Français';
	$opt['template']['locales']['NL']['show'] = true;
	$opt['template']['locales']['NL']['flag'] = 'images/flag/NL.gif';
	$opt['template']['locales']['NL']['name'] = 'Nederlands';
	$opt['template']['locales']['EN']['show'] = true;
	$opt['template']['locales']['EN']['flag'] = 'images/flag/EN.gif';
	$opt['template']['locales']['EN']['name'] = 'English';
	$opt['template']['locales']['PL']['show'] = true;
	$opt['template']['locales']['PL']['flag'] = 'images/flag/PL.gif';
	$opt['template']['locales']['PL']['name'] = 'Polski';
	$opt['template']['locales']['IT']['show'] = true;
	$opt['template']['locales']['IT']['flag'] = 'images/flag/IT.gif';
	$opt['template']['locales']['IT']['name'] = 'Italiano';
	$opt['template']['locales']['RU']['show'] = true;
	$opt['template']['locales']['RU']['flag'] = 'images/flag/RU.gif';
	$opt['template']['locales']['RU']['name'] = 'Russian';

	$opt['locale']['EN']['locales'] = array('en_US.utf8', 'en_US', 'en');
	$opt['locale']['EN']['format']['date'] = '%x';
	$opt['locale']['EN']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['EN']['format']['datetime'] = '%x %I:%M %p';
	$opt['locale']['EN']['format']['datetimesec'] = '%x %X';
	$opt['locale']['EN']['format']['time'] = '%I:%M %p';
	$opt['locale']['EN']['format']['timesec'] = '%X';

	$opt['locale']['DE']['locales'] = array('de_DE.utf8', 'de_DE@euro', 'de_DE', 'de', 'ge');
	$opt['locale']['DE']['format']['date'] = '%x';
	$opt['locale']['DE']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['DE']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['DE']['format']['datetimesec'] = '%x %X';
	$opt['locale']['DE']['format']['time'] = '%H:%M';
	$opt['locale']['DE']['format']['timesec'] = '%X';
	$opt['locale']['DE']['page']['subtitle1'] = 'Geocaching mit Opencaching';
	$opt['locale']['DE']['page']['subtitle2'] = '';

	$opt['locale']['PL']['locales'] = array('pl_PL.utf8', 'pl_PL', 'pl');
	$opt['locale']['PL']['format']['date'] = '%x';
	$opt['locale']['PL']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['PL']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['PL']['format']['datetimesec'] = '%x %X';
	$opt['locale']['PL']['format']['time'] = '%H:%M';
	$opt['locale']['PL']['format']['timesec'] = '%X';

	$opt['locale']['NL']['locales'] = array('nl_NL.utf8', 'nl_NL', 'nl');
	$opt['locale']['NL']['format']['date'] = '%x';
	$opt['locale']['NL']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['NL']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['NL']['format']['datetimesec'] = '%x %X';
	$opt['locale']['NL']['format']['time'] = '%H:%M';
	$opt['locale']['NL']['format']['timesec'] = '%X';
	$opt['locale']['NL']['page']['subtitle1'] = 'Geocaching met Opencaching';
	$opt['locale']['NL']['page']['subtitle2'] = '';

	$opt['locale']['IT']['locales'] = array('it_IT.utf8', 'it_IT', 'it');
	$opt['locale']['IT']['format']['date'] = '%x';
	$opt['locale']['IT']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['IT']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['IT']['format']['datetimesec'] = '%x %X';
	$opt['locale']['IT']['format']['time'] = '%H:%M';
	$opt['locale']['IT']['format']['timesec'] = '%X';
	$opt['locale']['IT']['page']['subtitle1'] = 'Geocaching con Opencaching';
	$opt['locale']['IT']['page']['subtitle2'] = '';

	$opt['locale']['FR']['locales'] = array('fr_FR.utf8', 'fr_FR@euro', 'fr_FR', 'french', 'fr');
	$opt['locale']['FR']['format']['date'] = '%x';
	$opt['locale']['FR']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['FR']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['FR']['format']['datetimesec'] = '%x %X';
	$opt['locale']['FR']['format']['time'] = '%H:%M';
	$opt['locale']['FR']['format']['timesec'] = '%X';

	$opt['locale']['RU']['locales'] = array('ru_RU.utf8', 'ru_RU', 'ru');
	$opt['locale']['RU']['format']['date'] = '%x';
	$opt['locale']['RU']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['RU']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['RU']['format']['datetimesec'] = '%x %X';
	$opt['locale']['RU']['format']['time'] = '%H:%M';
	$opt['locale']['RU']['format']['timesec'] = '%X';

	$opt['locale']['ES']['locales'] = array('es_ES.utf8', 'es_ES', 'es');
	$opt['locale']['ES']['format']['date'] = '%x';
	$opt['locale']['ES']['format']['datelong'] = '%d. %B %Y';
	$opt['locale']['ES']['format']['datetime'] = '%x %H:%M';
	$opt['locale']['ES']['format']['datetimesec'] = '%x %X';
	$opt['locale']['ES']['format']['time'] = '%H:%M';
	$opt['locale']['ES']['format']['timesec'] = '%X';
	$opt['locale']['ES']['country'] = 'ES';
	$opt['locale']['ES']['page']['subtitle1'] = 'Geocaching con Opencaching';
	$opt['locale']['ES']['page']['subtitle2'] = '';

	$opt['page']['title'] = 'OPENCACHING';
	$opt['page']['subtitle1'] = 'Geocaching with Opencaching';
	$opt['page']['subtitle2'] = '';

/* Sponsoring advertisements
   * (plain HTML)
   */

  // example: $opt['page']['sponsor']['topright'] = '<div class="site-slogan" style="background-image: url(resource2/ocstyle/images/darkbluetransparent.png);"><div style="width: 100%; text-align: left;"><p class="search"><a href="http://www.wanderjugend.de" target="_blank"><img border="0" align="right" style="margin-left: 10px;" src="resource2/ocstyle/images/dwj.gif" width="40px" height="20px" alt="... die outdoororientierte Jugendorganisation des Deutschen Wanderverbandes" /></a> Unterst&uuml;tzt und gef&ouml;rdert durch<br />die Deutsche Wanderjugend</p> </div></div>';
  $opt['page']['sponsor']['topright'] = '<div class="site-slogan" style="border-width:0px;"><div style="width: 100%; text-align: left;"><p class="search">&nbsp;<br />&nbsp;</p></div></div>';

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
}
?>
