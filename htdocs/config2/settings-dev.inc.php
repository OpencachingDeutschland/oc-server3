<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Default settings for OC.de developer system. See also
 *    - config2/settings-dist.inc.php for common settings
 *    - config2/settings.inc.php for local settings
 *    - lib/settings* for version-1-code settings
 ***************************************************************************/

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
	$opt['php']['debug'] = PHP_DEBUG_ON;

	/* settings for the template engine
	 *
	 */
	// ... how long a query can take without warning (0 <= disabled)
	$opt['db']['warn']['time'] = 0;
	$opt['db']['warn']['mail'] = 'root';
	$opt['db']['warn']['subject'] = 'sql_warn';
	
	// display mysql error messages on the website - not recommended for productive use!
	$opt['db']['error']['display'] = true;
	$opt['db']['error']['mail'] = 'root';
	$opt['db']['error']['subject'] = 'sql_error';

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
	 * other parameters may be customized
	 */
	$opt['session']['mode'] = SAVE_COOKIE;
	$opt['session']['cookiename'] = 'oc_devel'; // only with SAVE_COOKIE
	$opt['session']['path'] = '/';
	$opt['session']['domain'] = '';    // may be overwritten by $opt['domain'][...]['cookiedomain']

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
	 *  DEBUG_TRANSLATE       = read translate messages (use &trans=1 when calling the site)
	 *  DEBUG_FORCE_TRANSLATE = force read of translate messages
	 *  DEBUG_CLI             = print debug messages of cli scripts
	 */
	$opt['debug'] = DEBUG_DEVELOPER|DEBUG_TEMPLATES|DEBUG_SQLDEBUGGER|DEBUG_TRANSLATE|DEBUG_FORCE_TRANSLATE;
	//$opt['debug'] = DEBUG_DEVELOPER|DEBUG_TEMPLATES|DEBUG_SQLDEBUGGER;
	//$opt['debug'] = DEBUG_DEVELOPER|DEBUG_SQLDEBUGGER;

	// node options
	// see settings-dist.inc.php for known node IDs
	$opt['logic']['node']['id'] = 4;
  $opt['logic']['waypoint_pool']['prefix'] = 'OC';
  
	/* other template options
	 *
	 */
	$opt['page']['absolute_url'] = $dev_baseurl;
	$opt['mail']['from'] = 'root';
	$opt['mail']['subject'] = '[local.opencaching.de] ';
	$opt['page']['headimagepath'] = 'ocde';

	/* location of uploaded images
	 */
	$opt['logic']['pictures']['dir'] = $dev_basepath . $dev_codepath . 'htdocs/images/uploads';
	$opt['logic']['pictures']['url'] = $opt['page']['absolute_url'] . '/images/uploads';
	$opt['logic']['pictures']['thumb_url'] = $opt['logic']['pictures']['url'] . '/thumbs';

	/* cachemaps
	 */
	$opt['logic']['cachemaps']['wmsurl'] = 'http://www.opencaching.de/cachemaps.php?wp={wp_oc}';

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
	$opt['logic']['cachemaps']['url'] = 'http://maps.google.com/maps/api/staticmap?center={latitude},{longitude}&zoom={userzoom}&size=200x200&markers=color:blue|label:|{latitude},{longitude}&sensor=false';  // &key={gmkey}
	$opt['logic']['cachemaps']['iframe'] = false;

 	/* E-Mail for notification about news (newstopic.php)
 	 */
	$opt['news']['mail'] = 'root';
	$opt['mail']['subject'] = '[local.opencaching.de] ';

	/* Purge log files - age in days (0 = keep infinite)
	 */
	$opt['logic']['logs']['purge_email'] = 0;
	$opt['logic']['logs']['purge_userdata'] = 0;

 	/* 3rd party library options
 	 * see https://my.garmin.com/api/communicator/key-generator.jsp
 	 */
 	$opt['lib']['garmin']['key'] = '00112233445566778899AABBCCDDEEFF00';
 	$opt['lib']['garmin']['url'] = 'http://www.site.org/';

	$opt['template']['default']['style'] = 'ocstyle';

	$opt['bin']['cs2cs'] = '/var/www/bin/cs2cs';
	
	// other settings
	$opt['logic']['enableHTMLInUserDescription'] = false;
	$opt['page']['showdonations'] = true;
	$opt['logic']['pictures']['dummy']['replacepic'] = $dev_basepath . $dev_codepath . 'htdocs/images/no_image_license.png';
	
	$opt['template']['locales']['SV']['show'] = false;
	$opt['template']['locales']['NO']['show'] = false;
	$opt['template']['locales']['DA']['show'] = false;
	$opt['template']['locales']['PT']['show'] = false;
	$opt['template']['locales']['JA']['show'] = false;
?>