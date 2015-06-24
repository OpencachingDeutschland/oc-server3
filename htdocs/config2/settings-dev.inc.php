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
	$opt['php']['semaphores'] = false;

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

	/* Debug level (combine with OR | )
	 *  DEBUG_NO              = productive use
	 *  DEBUG_DEVELOPER       = developer system
	 *  DEBUG_TEMPLATES       = no template caching; makes some templates very slow!
	 *  DEBUG_OUTOFSERVICE    = only admin login (includes DEBUG_TEMPLATES)
	 *  DEBUG_TESTING         = display warning (includes DEBUG_TEMPLATES)
	 *  DEBUG_SQLDEBUGGER     = sql debugger (use &sqldebug=1 when calling the site)
	 *  DEBUG_TRANSLATE       = read translate messages (use &trans=1 when calling the site)
	 *  DEBUG_FORCE_TRANSLATE = force read of translate messages
	 *  DEBUG_CLI             = print debug messages of cli scripts
	 */
	$opt['debug'] = DEBUG_DEVELOPER|DEBUG_SQLDEBUGGER|DEBUG_TRANSLATE|DEBUG_FORCE_TRANSLATE;

	// node options
	// see settings-dist.inc.php for known node IDs
	$opt['logic']['node']['id'] = 4;
  $opt['logic']['waypoint_pool']['prefix'] = 'OC';
	$opt['logic']['shortlink_domain'] = 'opencaching.de';
  
	/* cachemaps
	*/
	$opt['logic']['cachemaps']['url'] = 'http://maps.google.com/maps/api/staticmap?center={latitude},{longitude}&zoom={userzoom}&size=200x200&maptype=hybrid&markers=color:blue|label:|{latitude},{longitude}&sensor=false';

	/* other template options
	 *
	 */
	$opt['page']['absolute_url'] = $dev_baseurl . "/";
	$opt['page']['develsystem'] = true;
	$opt['mail']['from'] = 'root';
	$opt['mail']['subject'] = '[local.opencaching.de] ';

	/* location of uploaded images
	 */
	$opt['logic']['pictures']['dir'] = $dev_basepath . $dev_codepath . 'htdocs/images/uploads';
	$opt['logic']['pictures']['url'] = $opt['page']['absolute_url'] . 'images/uploads';
	$opt['logic']['pictures']['thumb_url'] = $opt['logic']['pictures']['url'] . '/thumbs';

	/* disable cronjobs which are not needed on devel site
	 */

	$opt['cron']['sitemaps']['generate'] = false;
	$opt['cron']['geokrety']['run'] = false;

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
	$opt['lib']['garmin']['domain'] = 'local.opencaching.de';
	$opt['lib']['garmin']['url'] = 'http://local.opencaching.de/';
	$opt['lib']['garmin']['redirect'] = $opt['lib']['garmin']['url'] . 'garmin.php?redirect=1&cacheid={cacheid}';

    // developer.what3words.com API Key
    $opt['lib']['w3w']['apikey'] = 'YOURAPIKEY';

	// other settings
	$opt['page']['showdonations'] = true;
	$opt['page']['showsocialmedia'] = true;
	$opt['page']['headoverlay'] = 'oc_head_alpha3';

	$opt['logic']['pictures']['dummy']['replacepic'] = $dev_basepath . $dev_codepath . 'htdocs/images/no_image_license.png';
	$opt['logic']['license']['disclaimer'] = true;
	$opt['logic']['admin']['listingadmin_notification'] = 'root';
	
	$opt['template']['locales']['DA']['show'] = false;
	$opt['template']['locales']['JA']['show'] = false;
	$opt['template']['locales']['NL']['show'] = false;
	$opt['template']['locales']['PL']['show'] = false;
	$opt['template']['locales']['PT']['show'] = false;
	$opt['template']['locales']['RU']['show'] = false;
	$opt['template']['locales']['SV']['show'] = false;
	$opt['template']['locales']['NO']['show'] = false;
?>
