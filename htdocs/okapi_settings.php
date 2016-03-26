<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace okapi;

function get_okapi_settings()
{
	# Settings for OKAPI. See the following URLs for more info:
	#
	# [1] http://code.google.com/p/opencaching-pl/source/browse/trunk/okapi_settings.php
	# [2] http://code.google.com/p/opencaching-api/source/browse/trunk/okapi/settings.php
	# [3] http://code.google.com/p/opencaching-api/issues/detail?id=132

	$opt['rootpath'] = $GLOBALS['rootpath'];
	require($opt['rootpath'].'lib2/const.inc.php');   # (into the *local* scope!)
	require($opt['rootpath'].'config2/settings-dist.inc.php');
	require($opt['rootpath'].'config2/settings.inc.php');
	
	return array(

		# Settings which ARE NOT present in settings.inc.php:

		'OC_BRANCH' => 'oc.de',  # Tell OKAPI to work in "OCDE mode".

		# Settings which ARE present in settings.inc.php:

		'ADMINS'           => array($opt['db']['warn']['mail'],'rygielski@mimuw.edu.pl'),
		'FROM_FIELD'       => $opt['mail']['contact'],
		'DATA_LICENSE_URL' => $opt['page']['absolute_url'] . $opt['logic']['license']['terms'],
		'DEBUG'            => ($opt['debug'] & DEBUG_DEVELOPER != 0),
		'DEBUG_PREVENT_SEMAPHORES'
		                   => !$opt['php']['semaphores'],  # not available on current developer system
		'DB_SERVER'        => $opt['db']['servername'],
		'DB_NAME'          => $opt['db']['placeholder']['db'],
		'DB_USERNAME'      => $opt['db']['username'],
		'DB_PASSWORD'      => $opt['db']['password'],
		'DB_CHARSET'       => $opt['charset']['mysql'],
		'SITELANG'         => strtolower($opt['template']['default']['locale']),
		'TIMEZONE'         => $opt['php']['timezone'],  # BTW, OCPL doesn't have it in settings.inc.php
		'SITE_URL'         => $opt['page']['absolute_url'],
		'ORIGIN_URL'       => $opt['page']['origin_url'],
		'HTTPS'            => $opt['page']['https']['mode'] == HTTPS_DISABLED ? 'unavailable' : ($opt['page']['https']['is_default'] ? 'recommended' : 'available'),
		'VAR_DIR'          => $opt['okapi']['var_dir'],
		'IMAGES_DIR'       => rtrim($opt['logic']['pictures']['dir'], '/'),
		'SITE_LOGO'        => $opt['page']['absolute_url'] . 'resource2/' . $opt['template']['default']['style'] . '/images/oclogo/oc_logo_alpha3.png',
		'OC_NODE_ID'       => $opt['logic']['node']['id'],
		'OC_COOKIE_NAME'   => $opt['session']['cookiename'] . 'data',
		'OCDE_HTML_PURIFIER_SETTINGS'
		                   => $opt['html_purifier'],
	);
}
