<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  All settings to run the website.
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
	$opt['db']['servername'] = 'localhost';
	$opt['db']['username'] = '<db>';
	$opt['db']['password'] = '<pw>';
	$opt['db']['pconnect'] = true;

	// ... how long a query can take without warning (0 <= disabled)
	$opt['db']['warn']['time'] = 1;
	$opt['db']['warn']['mail'] = '<admin email>';
	$opt['db']['warn']['subject'] = 'sql_warn';
	
	// display mysql error messages on the website - not recommended for productive use!
	$opt['db']['error']['display'] = true;
	$opt['db']['error']['mail'] = '<admin email>';
	$opt['db']['error']['subject'] = 'sql_error';

	// database placeholder
	$opt['db']['placeholder']['db'] = 'ocde';
	$opt['db']['placeholder']['tmpdb'] = 'ocdetmp';
	$opt['db']['placeholder']['hist'] = 'ocdehist';

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
	$opt['session']['cookiename'] = '<cookiename>'; // only with SAVE_COOKIE
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
	$opt['page']['absolute_url'] = 'http://<domain>';
	$opt['mail']['from'] = '<admin email>';

	/* location of uploaded images
	 */
	$opt['logic']['pictures']['dir'] = '/srv/www/html/images/uploads';
	$opt['logic']['pictures']['url'] = 'http://<domain>/images/uploads';
	$opt['logic']['pictures']['thumb_url'] = $opt['logic']['pictures']['url'] . '/thumbs';

	/* location of uploaded mp3
	 */
	$opt['logic']['podcasts']['url'] = 'http://<domain>/podcasts/uploads';

	/* cachemaps
	 */
	$opt['logic']['cachemaps']['wmsurl'] = 'http://www.opencaching.de/cachemaps.php?wp={wp_oc}';

	/* password authentication method
	 * (true means extra hash on the digested password)
	 */
	$opt['logic']['password_hash'] = false;

 	/* E-Mail for notification about news (newstopic.php)
 	 */
 	$opt['news']['mail'] = '<admin email>';
 	$opt['mail']['subject'] = '[<domain>] ';

 	/* 3rd party library options
 	 * see https://my.garmin.com/api/communicator/key-generator.jsp
 	 */
 	$opt['lib']['garmin']['key'] = '00112233445566778899AABBCCDDEEFF00';
 	$opt['lib']['garmin']['url'] = 'http://www.site.org/';

	$opt['logic']['node']['id'] = 4;

	$opt['logic']['theme'] = 'seasons'; // leave blank to disable theme
	$opt['logic']['lowresfriendly'] = false;

function post_config()
{
	global $opt, $menuitem, $tpl;

	$domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
	if ($domain == '')
		return;

	switch (mb_strtolower($domain))
	{
		case 'www.opencaching.it':
			config_domain_www_opencaching_it();
			break;
		case 'www.opencachingspain.es':
			config_domain_www_opencachingspain_es();
			break;
		default:
			$tpl->redirect($opt['page']['absolute_url'] . 'index.php');
	}
}
?>