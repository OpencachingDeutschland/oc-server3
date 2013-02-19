<?

namespace okapi;

function get_okapi_settings()
{
	# Settings for OKAPI. See the following URLs for more info:
	#
	# [1] http://code.google.com/p/opencaching-pl/source/browse/trunk/okapi_settings.php
	# [2] http://code.google.com/p/opencaching-api/source/browse/trunk/okapi/settings.php
	# [3] http://code.google.com/p/opencaching-api/issues/detail?id=132
	
	require($GLOBALS['rootpath'].'lib/settings.inc.php');  # (into the *local* scope!)
	
	return array(

		# Settings which ARE NOT present in settings.inc.php:

		'OC_BRANCH' => 'oc.de',  # Tell OKAPI to work in "OCDE mode".
		'SUPPORTS_LOGTYPE_NEEDS_MAINTENANCE' => false,  # OCDE doesn't support it, see [2] for more info.
		'DATA_LICENSE_URL' => 'http://www.opencaching.de/articles.php?page=impressum#datalicense',
		
		# Settings which ARE present in settings.inc.php:
		
		'ADMINS' => array($sql_errormail,'rygielski@mimuw.edu.pl'),
		'FROM_FIELD' => $emailaddr,
		'DEBUG' => $debug_page,
		'DB_SERVER' => $dbserver,
		'DB_NAME' => $dbname,
		'DB_USERNAME' => $dbusername,
		'DB_PASSWORD' => $dbpasswd,
		'SITELANG' => $lang,
		'TIMEZONE' => $timezone,  # BTW, OCPL doesn't have it in settings.inc.php
		'SITE_URL' => $absolute_server_URI,
		'VAR_DIR' => $GLOBALS['rootpath'].'var',
		'IMAGES_DIR' => rtrim($picdir, '/'),
		'OC_NODE_ID' => $oc_nodeid,
		'OC_COOKIE_NAME' =>  $opt['cookie']['name'].'data',
	);
}
