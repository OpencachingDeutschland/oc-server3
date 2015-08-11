<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Default settings for all options in settings.inc.php
 *  Do not modify this file - use settings.inc.php!
 ***************************************************************************/

	require('locale.inc.php');

	/* PHP settings
	 *
	 * PHP_DEBUG_SKIP
	 *
	 *  dont use ini_set()
	 *
	 * PHP_DEBUG_OFF
	 *
	 *  use the following php.ini-settings
	 *    display_errors = On
	 *    error_reporting = E_ALL & ~E_NOTICE
	 *    mysql.trace_mode = Off
	 *
	 *  strongly recommended settings
	 *    register_globals = Off
	 *
	 * PHP_DEBUG_ON 
	 *
	 *  use the following php.ini-settings
	 *    display_errors = On
	 *    error_reporting = E_ALL
	 *    mysql.trace_mode = On
	 */
	$opt['php']['debug'] = PHP_DEBUG_SKIP;
	$opt['php']['timezone'] = 'Europe/Berlin';
	$opt['php']['semaphores'] = true;

	// database connection
	
	/* hostname or IP Address
	 * to connect to mysql socket use ':/path/to/mysql.sock';
	 */
	$opt['db']['servername'] = 'localhost';
	$opt['db']['username'] = '';
	$opt['db']['password'] = '';
	$opt['db']['pconnect'] = false;

	/**
	 * user for manual maintenance functions
	 * needs all privileges except for GRANT
	 *
	 * Set the password ONLY ON DEVELOPER SYSTEMS !!
	 */
	$opt['db']['maintenance_user'] = '';
	$opt['db']['maintenance_password'] = '';

	// begin throotling when more than 80%
	// of max_connections is reached on db server
	$opt['db']['throttle_connection_count'] = 240;

	// log the last N seconds for throttling
	$opt['db']['throttle_access_time'] = 300;

	// throttle users that have more than N access log
	// entries in the last [throttle_access_time] seconds
	$opt['db']['throttle_access_count'] = 200;

	/* replicated slave databases
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

	// TODO: use this slave when a specific slave must be connected
	// (e.g. xml-interface and mapserver-results)
	// you can use -1 to use the master (not recommended, because replicated to slaves)
	$opt['db']['slave']['primary'] = -1;

	// ... how long a query can take without warning (0 <= disabled)
	$opt['db']['warn']['time'] = 0;
	$opt['db']['warn']['mail'] = 'developer@devel.opencaching.de'; // set '' to disable
	$opt['db']['warn']['subject'] = 'sql_warn';
	
	// display mysql error messages on the website - not recommended for productive use!
	$opt['db']['error']['display'] = false;
	$opt['db']['error']['mail'] = 'developer@devel.opencaching.de'; // set '' to disable
	$opt['db']['error']['subject'] = 'sql_error';

	// database placeholder

	// productive database with opencaching-tables
	$opt['db']['placeholder']['db'] = '';    // selected by default

	// empty database for temporary table creation
	$opt['db']['placeholder']['tmpdb'] = '';

	// date format
	$opt['db']['dateformat'] = 'Y-m-d H:i:s';

	// email delivery processing from syslog-ng eventlog DB
	$opt['system']['maillog']['syslog_db_host'] = '';
	$opt['system']['maillog']['syslog_db_name'] = '';
	$opt['system']['maillog']['syslog_db_user'] = '';
	$opt['system']['maillog']['syslog_db_password'] = '';
	$opt['system']['maillog']['syslog_oc_host'] = '';  // 'host_name' column in syslog DB
	$opt['system']['maillog']['syslog_mta'] = 'postfix/smtp';  // 'program' column in syslog DB 

	/* cookie or session
	 *
	 * SAVE_COOKIE            = only use cookies
	 * SAVE_SESSION           = use php sessions
	 *
	 * to use SESSIONS set php.ini to session default values:
	 *
	 * session.auto_start = 0
	 * session.use_cookies = 1
	 * session.use_only_cookies = 0
	 * session.cookie_lifetime = 0
	 * session.cookie_path = "/"
	 * session.cookie_domain = ""
	 * session.cookie_secure = off
	 * session.use_trans_sid = 0
	 *
	 * set session.safe_path to a secure place
	 * 
	 * other parameters may be customized
	 */
	$opt['session']['mode'] = SAVE_COOKIE;	 // don't change - other option "SAVE_SESSION" is not implemented properly
	$opt['session']['cookiename'] = 'oc_devel'; // only with SAVE_COOKIE
	$opt['session']['path'] = '/';
	$opt['session']['domain'] = '';    // may be overwritten by $opt['domain'][...]['cookiedomain']

	/* maximum session lifetime
	 */
	$opt['session']['expire']['cookie'] = 31536000; // when cookies used (default 1 year)
	$opt['session']['expire']['url'] = 1800;        // when no cookies used (default 30 min since last call), attention to session.js

	/* If the Referer was sent by the client and the substring was not found,
	 * the embedded session id will be marked as invalid.
	 * Only used with session.mode = SAVE_SESSION
	 */
	$opt['session']['check_referer'] = true;

	$opt['session']['login_statistics'] = false;

	/* Debug level (combine with OR | )
	 *  DEBUG_NO              = productive use
	 *  DEBUG_DEVELOPER       = developer system
	 *  DEBUG_TEMPLATES       = no template caching; makes some templates very slow!
	 *  DEBUG_OUTOFSERVICE    = only admin login (includes DEBUG_TEMPLATES)
	 *  DEBUG_TESTING         = display warning (includes DEBUG_TEMPLATES)
	 *  DEBUG_SQLDEBUGGER     = sql debugger (use &sqldebug=1 when calling the site)
	 *  DEBUG_TRANSLATE       = read translate messages (use &trans=1 when calling the site, includes DEBUG_TEMPLATES)
	 *  DEBUG_FORCE_TRANSLATE = force read of translate messages (includes DEBUG_TRANSLATE)
	 *  DEBUG_CLI             = print debug messages of cli scripts
	 */
	$opt['debug'] = DEBUG_DEVELOPER;

	/* Main locale and style: The country and language with most content on this site.
	 *
	 */
	$opt['page']['main_country'] = 'DE';
	$opt['page']['main_locale'] = 'DE';

	/* Default locale and style
	 *
	 */
	$opt['template']['default']['locale'] = 'DE';      // may be overwritten by $opt['domain'][...]['locale']
	$opt['template']['default']['article_locale'] = 'EN';    // may be overwritten by $opt['domain'][...]['article_locale']
	$opt['template']['default']['fallback_locale'] = 'EN';   // may be overwritten by $opt['domain'][...]['article_locale']
	$opt['template']['default']['style'] = 'ocstyle';  // may be overwritten by $opt['domain'][...]['style']
	$opt['template']['default']['country'] = 'DE';     // may be overwritten by $opt['domain'][...]['country']

	// smiley path
	$opt['template']['smiley'] = 'resource2/tinymce/plugins/emotions/img/';

	/* other template options
	 *
	 */
	$opt['page']['subtitle1'] = 'Geocaching with Opencaching';
	$opt['page']['subtitle2'] = '';
	$opt['page']['title'] = 'OPENCACHING';
	$opt['page']['showdonations'] = false; // Show donations button
	$opt['page']['showsocialmedia'] = false;

  $opt['page']['absolute_url'] = 'http://devel.opencaching.de/'; // may be overwritten by $opt['domain'][...]['uri']
	$opt['page']['develsystem'] = false;
	$opt['page']['origin_url'] = 'http://www.opencaching.de/';  // reference productive installation for this OC code fork

  /* maximum number of failed logins per hour before that IP address is blocked
   * (used to prevent brute-force-attacks)
   */
	$opt['page']['max_logins_per_hour'] = 25;

	// sponsor link on e.g. print preview and garmin-plugin
	$opt['page']['sponsor']['popup'] = '';
  $opt['page']['sponsor']['bottom'] = 'Driven by the Opencaching Community';
  
  // directory of rotator pictures and script, relative to head images dir
  $opt['page']['headimagepath'] = '';
  $opt['page']['headoverlay'] = 'oc_head_alpha3_generic';

 	/* disable or enable https access to the main site
 	 * if false and connection is https, redirect to $opt['page']['absolute_url']
 	 * access to /xml/ocapi10 (SOAP interface) is allowed nevertheless
 	 */
 	$opt['page']['allowhttps'] = false;

 	// require SSL for SOAP access
 	$opt['page']['nusoap_require_https'] = false;

	// block troublemakers
	$opt['page']['banned_user_agents'] = array();
 	
 	/*
 	 * configure infos on 404.php
 	 */
 	$opt['page']['404']['www.opencaching.de'] = array(
 			'blog' => array(
 					'show' => false,
 					'feedurl'  => '',
 					'url' => '',
 					'urlname' => '',
 				),
 			'forum' => array(
 					'show' => false,
 					'feedurl'  => '',
 					'url' => '',
 					'urlname' => '',
 				),
 			'wiki' => array(
 					'show' => false,
 					'feedurl'  => '',
 					'url' => '',
 					'urlname' => '',
 				),
 			
			'newcaches' => array(
					'show' => true,
					'url' => 'http://www.opencaching.de',
					'urlname' => '',  // optional: show other name than the url-domain
				),
 		);

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

	/* settings for business layer
	 *
	 */
	$opt['logic']['rating']['percentageOfFounds'] = 10;
	$opt['logic']['rating']['topdays_mainCountry'] = 30;
	$opt['logic']['rating']['topdays_otherCountry'] = 180;
	
	/*
	 * count of identical logs (date and text) that shows a warning message on
	 * next log 
	 */
	$opt['logic']['masslog']['count'] = 20;

	/* Well known node id's - required for synchronization
	 *  1 Opencaching Deutschland (www.opencaching.de)
	 *  2 Opencaching Polen (www.opencaching.pl)
	 *  3 Opencaching Tschechien (www.opencaching.cz)
	 *  4 Local Development
	 *  5 Opencaching Entwicklung Deutschland (devel.opencaching.de)
	 *  6 Opencaching Schweden (www.opencaching.se)
	 *  7 Opencaching Großbritannien (www.opencacing.org.uk)
	 *  8 Opencaching Norwegen (www.opencaching.no)
	 *  9 Opencaching Lettland (?)
	 * 10 Opencaching USA (www.opencaching.us)
	 * 11 Opencaching Japan (eingestellt)
	 * 12 Opencaching Russland  (?)
	 * 13 Garmin (www.opencaching.com)
	 * 14 Opencaching Niederlande (www.opencaching.nl)
	 * 16 Opencaching Rumänien (www.opencaching.ro)
	 */
	$opt['logic']['node']['id'] = 4;

	/* location of uploaded images
	 */
	$opt['logic']['pictures']['dir'] = $opt['rootpath'] . 'images/uploads';  // Ocprop
	$opt['logic']['pictures']['url'] = 'http://devel.opencaching.de/images/uploads';
	$opt['logic']['pictures']['maxsize'] = 6000*1024;
	$opt['logic']['pictures']['unchg_size'] = 250*1024;
    if (extension_loaded('imagick'))
    $opt['logic']['pictures']['extensions'] = 'jpg;jpeg;gif;png;bmp;tif;psd;pcx;svg;xpm';
    else
    $opt['logic']['pictures']['extensions'] = 'jpg;jpeg;gif;png';

	/* Thumbnail sizes
	 */
	$opt['logic']['pictures']['thumb_max_width'] = 175;
	$opt['logic']['pictures']['thumb_max_height'] = 175;
	$opt['logic']['pictures']['thumb_url'] = $opt['logic']['pictures']['url'] . '/thumbs';
	$opt['logic']['pictures']['thumb_dir'] = $opt['rootpath'] . 'images/uploads/thumbs';

	/* Defaults for picture replacement on declined license
	 *
	 * replacement picture must be square sized
	 */
	$opt['logic']['pictures']['dummy']['bgcolor'] = array(255,255,255);
	$opt['logic']['pictures']['dummy']['text'] = '';
	$opt['logic']['pictures']['dummy']['textcolor'] = array(0,0,0);
	$opt['logic']['pictures']['dummy']['replacepic'] = $opt['rootpath'] . 'images/';  	   

	/* location of uploaded podcasts
	 */
	$opt['logic']['podcasts']['dir'] = $opt['rootpath'] . 'podcasts/uploads';
	$opt['logic']['podcasts']['url'] = 'http://devel.opencaching.de/podcasts/uploads';
	$opt['logic']['podcasts']['maxsize'] = 1536000;
	$opt['logic']['podcasts']['extensions'] = 'mp3';

	/* cachemaps (old, see cachemaps.php)
	 */
	$opt['logic']['cachemaps']['url'] = 'images/cachemaps/';
	$opt['logic']['cachemaps']['dir'] = $opt['rootpath'] . $opt['logic']['cachemaps']['url'];
	$opt['logic']['cachemaps']['wmsurl'] = 'http://www.opencaching.de/cachemaps.php?wp={wp_oc}';
	$opt['logic']['cachemaps']['size']['lat'] = 0.2;
	$opt['logic']['cachemaps']['size']['lon'] = 0.2;
	$opt['logic']['cachemaps']['pixel']['y'] = 200;
	$opt['logic']['cachemaps']['pixel']['x'] = 200;

	/* cachemaps (new)
	 * how to display the cache map on viewcache.php (200x200 pixel)
	 *
	 * option 1) via <img> tag (e.g. google maps)
	 *        2) via <iframe> tag (e.g. own mapserver)
	 *
	 * placeholders:
	 * {userzoom} = user zoomlevel (see myprofile.php)
	 * {latitude} = latitude of the cache
	 * {longitude} = longitude of the cache
   * {gmkey} = google maps key for current domain
	 */
	$opt['logic']['cachemaps']['url'] = 'http://maps.google.com/maps/api/staticmap?center={latitude},{longitude}&zoom={userzoom}&size=200x200&maptype=hybrid&markers=color:blue|label:|{latitude},{longitude}&sensor=false&key={gmkey}';
	$opt['logic']['cachemaps']['iframe'] = false;
	
	/* Minimap for the new-caches list on the front page.
	 * If the url string is empty, no minimap is displayed on the front page. 
	 * 
	 * Coordinates of new caches are appended to the url.
	 */ 
	$opt['logic']['minimapurl'] = 'http://maps.googleapis.com/maps/api/staticmap?sensor=false&key={gmkey}&size=220x220&maptype=roadmap&markers=color:blue|size:small';
	
	/* target vars
	 * all _REQUEST-vars that identifiy the current page for target redirection after login
	 */
	$opt['logic']['targetvars'] = array('cacheid', 'userid', 'logid', 'desclang', 'descid', 'wp', 'uuid', 'id', 'action', 'rid', 'ownerid');

	/* cracklib-check for users passwords enabled?
	 * (requires php extension crack_check)
	 */
	$opt['logic']['cracklib'] = false;

	/* password authentication method
	 * (true means extra hash on the digested password)
	 */
	$opt['logic']['password_hash'] = false;

	/* password salt
	 * is a random generated String that is appended to the password
	 */
	$opt['logic']['password_salt'] = '';

	/* new lows style
	 */
	$opt['logic']['new_logs_per_country'] = true;

	/* search engines
	 * will be excluded from cache visitor count
	 * current active bots on www.opencaching.de in 03/2013:
	 *
	 * (I added this and then noticed that is may be unnecessary, as the
	 * visit counter function is special javascript code which probably is not
	 * executed by search engines.  -- following)
	 */
	$opt['logic']['search_engines'] = 'AcoonBot;AhrefsBot;Baiduspider;bingbot;Exabot;Ezooms;Googlebot;Googlebot-mobile;ia_archiver,Linguee Bot;Mail.RU_Bot;MJ12bot;msnbot;SISTRIX Crawler;Sophora Linkchecker;TweetmemeBot;WBSearchBot;Yahoo! Slurp;YandexBot';

	/* default maximum of OConly-81 ranklist members
	 */
	$opt['logic']['oconly81']['default_maxusers'] = 60;

	/* opencaching prefixes in database available to search for
	 */
	$opt['logic']['ocprefixes'] = 'oc';
	$opt['logic']['shortlink_domain'] = false;

  /* pregenerated waypoint list for new caches
   * - Waypoint prefix (OC, OP, OZ ... AA=local development)
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

  /* Username for cronjobs or CLI tools
   * is used e.g. for cache auto-archiving and auto-publishing
   */
	$opt['logic']['systemuser']['user'] = '';

	/* Purge log files - age in days (0 = keep infinite)
	 */
	$opt['logic']['logs']['purge_email'] = 30;
	$opt['logic']['logs']['purge_userdata'] = 14;

	/* license-related functions
	 */
	$opt['logic']['license']['newusers'] = 2;  // see license constants in lib2/logic/const.inc.php
	$opt['logic']['license']['admin'] = true;
	$opt['logic']['license']['disclaimer'] = false;
	$opt['logic']['license']['terms'] = 'articles.php?page=impressum#datalicense';
	   // 'disclaimer' and 'terms' also in lib/settings.inc.php

	/* admin functions
	 */
	// admin may use OC-team-comment log flag only when processing a cache report 
	// see also setting in lib/settings.inc.php!
	$opt['logic']['admin']['team_comments_only_for_reports'] = true;
	$opt['logic']['admin']['enable_listing_admins'] = false;
	$opt['logic']['admin']['listingadmin_notification'] = '';  // Email address(es), comma separated

	/* optional APIs
	 */
	$opt['logic']['api']['email_problems']['key'] = '';   // must be set to enable

  /* Database charset
	 *   frontend and php charsets are UTF-8
	 *   here you can set a different charset for the MySQL-Engine
	 *   usefull if your database is not UTF-8.
	 *   Should only be used for step by step migration.
	 *
	 *   Both charsets must be the same!
	 */
	$opt['charset']['iconv'] = 'UTF-8'; // 'ISO-8859-1'; // use iconv compatible charset-name
	$opt['charset']['mysql'] = 'utf8';     // use mysql compatible charset-name

	/* cronjob
	 */
	$opt['cron']['username'] = 'apache';   // system username for cronjobs

	/* phpbb news integration (index.php)
	 *
	 * Set url='' to disable the cronjob task and hide the section on start page.
	 * Topics from different subforum will be merged and sorted by date.
	 * forumids defines what subforums to query
	 * count defines number of postings shown
	 * maxcontentlength defines where to strip to content
	 */
	
	/* example   $opt['cron']['phpbbtopics']['url'] = 'http://www.geoclub.de/feed.php?f={id}';
	             $opt['cron']['phpbbtopics']['forumids'] = array(125, 126, 127);
	             $opt['cron']['phpbbtopics']['name'] = 'geoclub.de';
	             $opt['cron']['phpbbtopics']['link'] = 'http://www.geoclub.de/viewforum.php?f=52';
   */
	$opt['cron']['phpbbtopics']['url'] = '';
	$opt['cron']['phpbbtopics']['forumids'] = array();
	$opt['cron']['phpbbtopics']['name'] = '';
	$opt['cron']['phpbbtopics']['link'] = '';
	$opt['cron']['phpbbtopics']['count'] = 5;
	$opt['cron']['phpbbtopics']['maxcontentlength'] = 230;

	/* generate sitemap.xml and upload to search engines
	 *
	 * NOTE
	 *
	 * testing server: disbale submit and add OC-source-directory to robots.txt (disallow /)
	 * productive server: enable submit and add "Sitemap: sitemap.xml" to you robots.txt
	 */
	$opt['cron']['sitemaps']['generate'] = true;
	$opt['cron']['sitemaps']['submit'] = false;

	/* other cronjobs
	 */

	$opt['cron']['geokrety']['run'] = true;
	$opt['cron']['geokrety']['xml_archive'] = false;
	$opt['cron']['autoarchive']['run'] = false;
	$opt['cron']['gcwp']['sources'] = array();

	/* E-Mail settings
	 *
	 */

	// outgoing mails
 	$opt['mail']['from'] = 'noreply@devel.opencaching.de';
 	$opt['mail']['subject'] = '[devel.opencaching.de] ';

 	// email address for user contact emails
 	// has to be an autoresponder informing about wrong mail usage
 	$opt['mail']['usermail'] = 'usermail@devel.opencaching.de';

	// contact address
	$opt['mail']['contact'] = 'contact@devel.opencaching.de';

 	/* News configuration
 	 *
 	 * filename to the include file containing the newscontent
 	 * (e.g. prepared blog-feed in HTML format)
 	 * if no filename is given, the own news-code is used
 	 * (table news and newstopic.php)
 	 * You can use '{style}' as placeholder for the current style-name
 	 */
 	$opt['news']['include'] = '';
  $opt['news']['count'] = 3;

 	// redirect news.php to the following url
 	$opt['news']['redirect'] = '';

 	// maximum size of the include file
 	$opt['news']['maxsize'] = 25*1024;
 	
 	// E-Mail for notification about news (newstopic.php)
 	$opt['news']['mail'] = 'news@devel.opencaching.de';

	// show news block in start page
	$opt['news']['onstart'] = true;

  /* current forum topcis on start page
   * requires url to be a vaild rss feed
   * -> show the number of 'count' topics from rss feed
   */
  $opt['forum']['url'] = '';
  $opt['forum']['count'] = 5;

  // settings for webchat.php
  $opt['chat']['url'] = 'http://webchat.freenode.net/?nick={chatusername}&amp;channels=opencaching.de&amp;prompt=1';
  $opt['chat']['width'] = 720;
  $opt['chat']['height'] = 480;

	/* 3rd party library options
 	 */

	// key provided from garmin (communicator api)
 	$opt['lib']['garmin']['key'] = '00112233445566778899AABBCCDDEEFF00';

 	// domain registered to this key. If the domain does not match the request
 	// a redirect to redirect-setting will be done
 	// (use exact same url with slashes etc. as registered by garmin)
 	$opt['lib']['garmin']['domain'] = 'www.site.org';
 	$opt['lib']['garmin']['url'] = 'http://' . $opt['lib']['garmin']['domain'] . '/';

 	// if the plugin is not called from the correct domain, redirect to this site
 	// (e.g. domain called without www. prefix) - must match domain of $opt['lib']['garmin']['url']
 	$opt['lib']['garmin']['redirect'] = 'http://www.site.org/garmin.php?redirect=1&cacheid={cacheid}';

    // developer.what3words.com API key
    $opt['lib']['w3w']['apikey'] = 'YOURAPIKEY';

	// Google Maps API key
	// http://code.google.com/intl/de/apis/maps/signup.html
	$opt['lib']['google']['mapkey'] = array();
	//$opt['lib']['google']['mapkey']['www.opencaching.xy'] = 'EEFFGGHH...';

	/* config of map.php
	 */

	// search result cache behaviour
	$opt['map']['maxcacheage'] = 3600;

	// execute cleanup when the size of table map2_data is greater than maxcachesize (in bytes)
	$opt['map']['maxcachesize'] = 20 * 1048576; // = 20MB

	// cache size after deleting old entries
	$opt['map']['maxcachereducedsize'] = 10 * 1048576; // = 10MB

	// max number of caches displayed in google maps
	if (isset($_SERVER['HTTP_USER_AGENT']))
	{
		$user_agent = " " . $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent,"MSIE") && !strpos($user_agent,"Opera"))
			$opt['map']['maxrecords'] = 200;
		else
			$opt['map']['maxrecords'] = 2500;
	}
	else
		$opt['map']['maxrecords'] = 200;
			// ... selectable by user:
	$opt['map']['min_maxrecords'] = 100;
	$opt['map']['max_maxrecords'] = 4000;

 	/*
	 * html purifier
	 */
	$opt['html_purifier']['cache_path'] = dirname(__FILE__).'/../cache2/html_purifier/';

	/*
	 * OKAPI
	 */
	$opt['okapi']['var_dir'] = $opt['rootpath'] . 'var/okapi';

	/* Opencaching Node Daemon
	 *
	 */
	// temporary file to collect status info of all child forks
	$opt['ocnd']['statusfile'] = '/tmp/ocndaemon.tmp';

	// polling behaviour of status option
	$opt['ocnd']['timeout'] = 10; // seconds

	// IP address to listen
	$opt['ocnd']['ip'] = '0.0.0.0';
	// TCP port to listen
	$opt['ocnd']['port'] = 15000;
	// maximum connects buffer (see php manual of socket_listen() )
	$opt['ocnd']['connectbuffer'] = 10;
	// print out every line sent and received
	$opt['ocnd']['debugtcp'] = true;
	// do not check openssl version (version check is available in php 5.2+)
	$opt['ocnd']['noopensslcheck'] = false;

  /* commands to start and stop apache process
   * required to clear the webcache
   */
  $opt['httpd']['stop'] = 'service httpd stop';
  $opt['httpd']['start'] = 'service httpd start';

  /* owner and group of files created by apache daemon
   * (used to change ownership in shell scripts)
   */
  $opt['httpd']['user'] = 'apache';
  $opt['httpd']['group'] = 'apache';

?>
