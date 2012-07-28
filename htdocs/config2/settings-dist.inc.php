<?php
/***************************************************************************
 *  You can find the license in the docs directory
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

	/* settings for the template engine
	 *
	 */

	// database connection
	
	/* hostname or IP Address
	 * to connect to mysql socket use ':/path/to/mysql.sock';
	 */
	$opt['db']['servername'] = 'localhost';
	$opt['db']['username'] = '';
	$opt['db']['password'] = '';
	$opt['db']['pconnect'] = false;

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
	$opt['session']['mode'] = SAVE_COOKIE;
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

	/* Debug level (combine with OR | )
	 *  DEBUG_NO              = productive use
	 *  DEBUG_DEVELOPER       = developer system
	 *  DEBUG_TEMPLATES       = no template caching
	 *  DEBUG_OUTOFSERVICE    = only admin login (includes DEBUG_TEMPLATES)
	 *  DEBUG_TESTING         = display warning (includes DEBUG_TEMPLATES)
	 *  DEBUG_SQLDEBUGGER     = sql debugger (use &sqldebug=1 when calling the site)
	 *  DEBUG_TRANSLATE       = read translate messages (use &trans=1 when calling the site, includes DEBUG_TEMPLATES)
	 *  DEBUG_FORCE_TRANSLATE = force read of translate messages (includes DEBUG_TRANSLATE)
	 *  DEBUG_CLI             = print debug messages of cli scripts
	 */
	$opt['debug'] = DEBUG_DEVELOPER;

	/* Default locale and style
	 *
	 */
	$opt['template']['default']['locale'] = 'DE';      // may be overwritten by $opt['domain'][...]['locale']
	$opt['template']['default']['article_locale'] = 'EN';    // may be overwritten by $opt['domain'][...]['article_locale']
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
	$opt['page']['absolute_url'] = 'http://devel.opencaching.de/'; // may be overwritten by $opt['domain'][...]['uri']
	$opt['page']['max_logins_per_hour'] = 25;
 	$opt['page']['showdonations'] = false; // Show donations button

  /* Sponsoring advertisements
   * (plain HTML)
   */

  // example: $opt['page']['sponsor']['topright'] = '<div class="site-slogan" style="background-image: url(resource2/ocstyle/images/darkbluetransparent.png);"><div style="width: 100%; text-align: left;"><p class="search"><a href="http://www.wanderjugend.de" target="_blank"><img border="0" align="right" style="margin-left: 10px;" src="resource2/ocstyle/images/dwj.gif" width="40px" height="20px" alt="... die outdoororientierte Jugendorganisation des Deutschen Wanderverbandes" /></a> Unterst&uuml;tzt und gef&ouml;rdert durch<br />die Deutsche Wanderjugend</p> </div></div>';
  $opt['page']['sponsor']['topright'] = '';
	
	// sponsor link on e.g. print preview and garmin-plugin
	$opt['page']['sponsor']['popup'] = '';

  $opt['page']['sponsor']['bottom'] = 'Driven by the Opencaching Community';

 	/* disable or enable https access to the main site
 	 * if false and connection is https, redirect to $opt['page']['absolute_url']
 	 * access to /xml/ocapi10 (SOAP interface) is allowed nevertheless
 	 */
 	$opt['page']['allowhttps'] = false;

 	// require SSL for SOAP access
 	$opt['page']['nusoap_require_https'] = false;

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

	/* Well known node id's - required for synchronization
	 * 1 Opencaching Deutschland (www.opencaching.de)
	 * 2 Opencaching Polen (www.opencaching.pl)
	 * 3 Opencaching Tschechien (www.opencaching.cz)
	 * 4 Local Development
	 * 5 Opencaching Entwicklung Deutschland (devel.opencaching.de)
	 */
	$opt['logic']['node']['id'] = 4;

	/* location of uploaded images
	 */
	$opt['logic']['pictures']['dir'] = $opt['rootpath'] . 'images/uploads';
	$opt['logic']['pictures']['url'] = 'http://devel.opencaching.de/images/uploads';
	$opt['logic']['pictures']['maxsize'] = 153600;
	$opt['logic']['pictures']['extensions'] = 'jpg;jpeg;gif;png;bmp';

	/* Thumbnail sizes
	 */
	$opt['logic']['pictures']['thumb_max_width'] = 175;
	$opt['logic']['pictures']['thumb_max_height'] = 175;
	$opt['logic']['pictures']['thumb_url'] = $opt['logic']['pictures']['url'] . '/thumbs';
	$opt['logic']['pictures']['thumb_dir'] = $opt['rootpath'] . 'images/uploads/thumbs';

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
	 */
	$opt['logic']['cachemaps']['url'] = 'http://maps.google.com/maps/api/staticmap?center={latitude},{longitude}&zoom={userzoom}&size=200x200&maptype=hybrid&markers=color:blue|label:|{latitude},{longitude}&sensor=false';
	$opt['logic']['cachemaps']['iframe'] = false;
 
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

	/* If the user entered HTML in his description do we
   * display it as HTML or escape it and make it non-functional?
	 */
  $opt['logic']['enableHTMLInUserDescription'] = true;

	/* new lows style
	 */
	$opt['logic']['new_logs_per_country'] = true;

	/* opencaching prefixes in database available to search for
	 */
	$opt['logic']['ocprefixes'] = 'oc';

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
	$opt['cron']['pidfile'] = $opt['rootpath'] . 'cache2/runcron.pid';

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

 	// redirect news.php to the following url
 	$opt['news']['redirect'] = '';

 	// maximum size of the include file
 	$opt['news']['maxsize'] = 25*1024;
 	
 	// E-Mail for notification about news (newstopic.php)
 	$opt['news']['mail'] = 'news@devel.opencaching.de';

	// show news block in start page
	$opt['news']['onstart'] = true;

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
	$opt['map']['maxrecords'] = 180;

	// the full screen mode requires a GIS server at the moment
	// has to be migrated to map2.php
	$opt['map']['disablefullscreen'] = true;

 	/* external binaries
 	 */
 	$opt['bin']['cs2cs'] = 'cs2cs';

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
  $opt['httpd']['stop'] = '/etc/rc.d/init.d/httpd stop';
  $opt['httpd']['start'] = '/etc/rc.d/init.d/httpd start';

  /* owner and group of files created by apache daemon
   * (used to change ownership in shell scripts)
   */
  $opt['httpd']['user'] = 'apache';
  $opt['httpd']['group'] = 'apache';

	/* CMS links for external pages
	 */

	// explanation of common login errors
	$opt['cms']['login'] = 'http://blog.opencaching.de/?page_id=268';

	// explanation of nature protection areas
	$opt['cms']['npa'] = 'http://blog.opencaching.de/?page_id=274';
?>