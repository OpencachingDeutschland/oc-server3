<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 * Settings shared by all configurations of lib1 and lib2.
 * See also locale.inc.php, which is included in both lib1 and lib2.
 ***************************************************************************/

	// page title
	$opt['page']['title'] = 'OPENCACHING';
	$opt['page']['subtitle1'] = 'Geocaching with Opencaching';
	$opt['page']['subtitle2'] = '';
	$opt['page']['headoverlay'] = 'oc_head_alpha3_generic';

	// directory of rotator pictures and script, relative to head images dir
	$opt['page']['headimagepath'] = '';

	// sponsor link on e.g. print preview and garmin-plugin
	$opt['page']['sponsor']['popup'] = '';
	$opt['page']['sponsor']['bottom'] = 'Driven by the Opencaching Community';

	$opt['page']['showdonations'] = false; // Show donations button
	$opt['page']['showsocialmedia'] = false;

	/* maximum number of failed logins per hour before that IP address is blocked
	 * (used to prevent brute-force-attacks)
	 */
	$opt['page']['max_logins_per_hour'] = 25;

	// block troublemakers
	$opt['page']['banned_user_agents'] = array();

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

	/* admin functions
	 */
	// admin may use OC-team-comment log flag only when processing a cache report 
	// see also setting in lib/settings.inc.php!
	$opt['logic']['admin']['team_comments_only_for_reports'] = true;
	$opt['logic']['admin']['enable_listing_admins'] = false;
	$opt['logic']['admin']['listingadmin_notification'] = '';  // Email address(es), comma separated

	/*
	 * html purifier
	 */
	$opt['html_purifier']['cache_path'] = dirname(__FILE__).'/../cache2/html_purifier/';

	/* CMS links for external pages
	 */

	// explanation of common login errors
	$opt['cms']['login'] = 'http://wiki.opencaching.de/index.php/Login_auf_Opencaching.de';

	// explanation of nature protection areas
	$opt['cms']['npa'] = 'http://wiki.opencaching.de/index.php/Schutzgebiete';

?>
