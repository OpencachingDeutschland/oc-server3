<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Default settings for developer system. See also
 *    - config2/settings-dist.inc.php for global default settings
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
	$opt['db']['servername'] = ':/var/lib/mysql/mysql.sock';
	$opt['db']['username'] = 'oc';
	$opt['db']['password'] = 'developer';
	$opt['db']['pconnect'] = true;

	// ... how long a query can take without warning (0 <= disabled)
	$opt['db']['warn']['time'] = 0;
	$opt['db']['warn']['mail'] = 'root';
	$opt['db']['warn']['subject'] = 'sql_warn';
	
	// display mysql error messages on the website - not recommended for productive use!
	$opt['db']['error']['display'] = true;
	$opt['db']['error']['mail'] = 'root';
	$opt['db']['error']['subject'] = 'sql_error';

	// database placeholder
	$opt['db']['placeholder']['db'] = 'opencaching';
	$opt['db']['placeholder']['tmpdb'] = 'octmp';
	$opt['db']['placeholder']['hist'] = 'ochist';

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

	/* other template options
	 *
	 */
	$opt['page']['absolute_url'] = 'http://local.opencaching.de/oc-server/server-3.0/htdocs';
	$opt['mail']['from'] = '<admin email>';

	/* location of uploaded images
	 */
	$opt['logic']['pictures']['dir'] = '/var/www/html/oc-server/server-3.0/htdocs/images/uploads';
	$opt['logic']['pictures']['url'] = 'http://local.opencaching.de/oc-server/server-3.0/htdocs/images/uploads';
	$opt['logic']['pictures']['thumb_url'] = $opt['logic']['pictures']['url'] . '/thumbs';

	/* cachemaps
	 */
	$opt['logic']['cachemaps']['wmsurl'] = 'http://www.opencaching.de/cachemaps.php?wp={wp_oc}';

 	/* E-Mail for notification about news (newstopic.php)
 	 */
 	$opt['news']['mail'] = '<admin email>';
 	$opt['mail']['subject'] = '[<domain>] ';

 	/* 3rd party library options
 	 * see https://my.garmin.com/api/communicator/key-generator.jsp
 	 */
 	$opt['lib']['garmin']['key'] = '00112233445566778899AABBCCDDEEFF00';
 	$opt['lib']['garmin']['url'] = 'http://www.site.org/';

	$opt['template']['default']['style'] = 'ocstyle';

 	// Google maps key
        $opt['lib']['google']['mapkey']['local.opencaching.de'] = 'ABQIAAAAwY6rAeeTA2cLnBDnf5FWGhQ9ZMVXtHZ4yn114tr66PefbkFZhBQXKkJK_k96Ci1JimzyaUwfhFhGrQ';

	$opt['bin']['cs2cs'] = '/var/www/bin/cs2cs';
	
	// Flag paths
	$opt['template']['locales']['DE']['flag'] = 'images/flag/DE.gif';
	$opt['template']['locales']['FR']['flag'] = 'images/flag/FR.gif';
	$opt['template']['locales']['NL']['flag'] = 'images/flag/NL.gif';
	$opt['template']['locales']['EN']['flag'] = 'images/flag/EN.gif';
	$opt['template']['locales']['PL']['flag'] = 'images/flag/PL.gif';
	$opt['template']['locales']['IT']['flag'] = 'images/flag/IT.gif';
	$opt['template']['locales']['RU']['flag'] = 'images/flag/RU.gif';
	$opt['template']['locales']['ES']['flag'] = 'images/flag/ES.png';
	$opt['template']['locales']['JA']['flag'] = 'images/flag/JP.gif';
	
	$opt['template']['locales']['SV']['show'] = false;
	$opt['template']['locales']['NO']['show'] = false;
	$opt['template']['locales']['DA']['show'] = false;
	$opt['template']['locales']['PT']['show'] = false;
	$opt['template']['locales']['JA']['show'] = false;

	
?>
