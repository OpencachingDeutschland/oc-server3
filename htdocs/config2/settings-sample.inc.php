<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Sample of settings.inc.php - all lib2 settings needed to run the website.
 *  In addition to this, you must create settings.inc.php files from 
 *  .dist files at the following places:
 *
 *    lib
 *    util/notifications
 *    util/publish_caches
 *    util/watchlist
 *
 *  This file may be outdated and should be reviewed.
 *
 ***************************************************************************/

	/* PHP settings
	 * see settings-dist.inc.php for explanation
	 */
	$opt['php']['debug'] = PHP_DEBUG_ON;
	$opt['php']['timezone'] = 'Europe/Berlin';

	/* database settings
	 */
	$opt['db']['servername'] = 'localhost';
	$opt['db']['username'] = '<user>';
	$opt['db']['password'] = '<pw>';
	$opt['db']['pconnect'] = false;
	$opt['db']['maintenance_user'] = '<priviledged_user>';

	// ... how long a query can take without warning (0 <= disabled)
	$opt['db']['warn']['time'] = 1;
	$opt['db']['warn']['mail'] = '<admin email>';
	$opt['db']['warn']['subject'] = 'sql_warn';
	
	// display mysql error messages on the website - not recommended for productive use!
	$opt['db']['error']['display'] = true;
	$opt['db']['error']['mail'] = '<admin email>';
	$opt['db']['error']['subject'] = 'sql_error';

	// database names
	$opt['db']['placeholder']['db'] = 'ocde';
	$opt['db']['placeholder']['tmpdb'] = 'ocdetmp';

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
	$opt['session']['cookiename'] = '<cookiename>';   // e.g. 'ocde'
	$opt['session']['domain'] = '<do.main>';  // may be overwritten by $opt['domain'][...]['cookiedomain']

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
	$opt['page']['name'] = 'Geocaching mit Opencaching';
	$opt['page']['absolute_url'] = 'http://<domain>';
	$opt['mail']['from'] = '<admin email>';
	$opt['page']['max_logins_per_hour'] = 250;

	/* location of uploaded images
	 */
	$opt['logic']['pictures']['dir'] = '/srv/www/html/images/uploads';
	$opt['logic']['pictures']['url'] = 'http://<domain>/images/uploads';
	$opt['logic']['pictures']['thumb_url'] = $opt['logic']['pictures']['url'] . '/thumbs';

	/* location of uploaded mp3
	 */
	$opt['logic']['podcasts']['url'] = 'http://<domain>/podcasts/uploads';

	/* password authentication method
	 * (true means extra hash on the digested password)
	 */
	$opt['logic']['password_hash'] = false;

 	/* E-Mail for notification about news (newstopic.php)
 	 */
 	$opt['news']['mail'] = '<admin email>';
 	$opt['mail']['subject'] = '[<domain>] ';

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

 	/* 3rd party library options
 	 * see https://my.garmin.com/api/communicator/key-generator.jsp
 	 */
 	$opt['lib']['garmin']['key'] = '00112233445566778899AABBCCDDEEFF00';
 	$opt['lib']['garmin']['url'] = 'http://www.site.org/';

    // developer.what3words.com API Key
    $opt['lib']['w3w']['apikey'] = 'YOURAPIKEY';

	$opt['logic']['node']['id'] = 4;

	// Google Maps API key
	// http://code.google.com/intl/de/apis/maps/signup.html
	// $opt['lib']['google']['mapkey']['<domain>'] = 'EEFFGGHH...';

	// email address for user contact emails
	// has to be an autoresponder informing about wrong mail usage
	$opt['mail']['usermail'] = 'usermail@opencaching.de';

	// contact address
	$opt['mail']['contact'] = 'contact@opencaching.de';

	$opt['page']['showdonations'] = true;
	$opt['page']['showsocialmedia'] = true;


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
