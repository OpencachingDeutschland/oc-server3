<?php

namespace okapi;

use Exception;
use okapi\Okapi;
use okapi\views\menu\OkapiMenu;

#
# All HTTP requests within the /okapi/ path are redirected through this
# controller. From here we'll pass them to the right entry point (or
# display an appropriate error message). 
#
# To learn more about OKAPI, see core.php.
#

$GLOBALS['rootpath'] = '../'; # this is for OC-code compatibility, OC requires this
$GLOBALS['no-session'] = true; # turn off OC-code session starting
$GLOBALS['no-ob'] = true; # turn off OC-code GZIP output buffering

require_once($GLOBALS['rootpath'].'okapi/core.php');
OkapiErrorHandler::$treat_notices_as_errors = true;
require_once($GLOBALS['rootpath'].'okapi/urls.php');

# OKAPI does not use sessions. The following statement will allow concurrent
# requests to be fired from browser.
if (session_id())
{
	# WRTODO: Move this to some kind of cronjob, to prevent admin-spamming in case on an error.
	throw new Exception("Session started when should not be! You have to patch your OC installation. ".
		"You have to check \"if ((!isset(\$GLOBALS['no-session'])) || (\$GLOBALS['no-session'] == false))\" ".
		"before executing session_start.");
}

# Make sure OC did not start anything suspicious, like ob_start('ob_gzhandler').
# OKAPI makes it's own decisions whether "to gzip or not to gzip".
if (ob_list_handlers() == array('default output handler'))
{
	# We will assume that this one comes from "output_buffering" being turned on
	# in PHP config. This is very common and probably is good for most other OC
	# pages. But we don't need it in OKAPI. We will just turn this off.
	
	ob_end_clean();
}

if (count(ob_list_handlers()) > 0)
{
	# WRTODO: Move this to some kind of cronjob, to prevent admin-spamming in case on an error.
	throw new Exception("Output buffering started while it should not be! You have to patch you OC ".
		"installation (probable lib/common.inc.php file). You have to check \"if ((!isset(\$GLOBALS['no-ob'])) ".
		"|| (\$GLOBALS['no-ob'] == false))\" before executing ob_start. Refer to installation docs.");
}

class OkapiScriptEntryPointController
{
	public static function dispatch_request($uri)
	{
		# Chop off the ?args=... part.
		
		if (strpos($uri, '?') !== false)
			$uri = substr($uri, 0, strpos($uri, '?'));
		
		# Chop off everything before "/okapi/". This should work for okay for most "weird"
		# server configurations. It will also address a more subtle issue described here:
		# http://stackoverflow.com/questions/8040461/request-uri-unexpectedly-contains-fqdn
		
		if (strpos($uri, "/okapi/") !== false)
			$uri = substr($uri, strpos($uri, "/okapi/"));
		
		# Make sure we're in the right directory (.htaccess should make sure of that).
		
		if (strpos($uri, "/okapi/") !== 0)
			throw new Exception("'$uri' is outside of the /okapi/ path.");
		$uri = substr($uri, 7);
		
		# Initializing internals and running pre-request cronjobs (we don't want
		# cronjobs to be run before "okapi/update", for example before database
		# was installed).
		
		$allow_cronjobs = ($uri != "update");
		Okapi::init_internals($allow_cronjobs);
		
		# Checking for allowed patterns...
		
		try
		{
			foreach (OkapiUrls::$mapping as $pattern => $namespace)
			{
				$matches = null;
				if (preg_match("#$pattern#", $uri, $matches))
				{
					# Pattern matched! Moving on to the proper View...
					
					array_shift($matches);
					require_once "views/$namespace.php";
					$response = call_user_func_array(array('\\okapi\\views\\'.
						str_replace('/', '\\', $namespace).'\\View', 'call'), $matches);
					if ($response)
						$response->display();
					return;
				}
			}
		}
		catch (Http404 $e)
		{
			/* pass */
		}
		
		# None of the patterns matched OR method threw the Http404 exception.
		
		require_once "views/http404.php";
		$response = \okapi\views\http404\View::call();
		$response->display();
	}
}

Okapi::gettext_domain_init();
OkapiScriptEntryPointController::dispatch_request($_SERVER['REQUEST_URI']);
Okapi::gettext_domain_restore();
