<?php

namespace okapi\views\apps\revoke_access;

use Exception;
use okapi\Okapi;
use okapi\Db;
use okapi\OkapiHttpResponse;
use okapi\OkapiHttpRequest;
use okapi\OkapiRedirectResponse;

class View
{
	public static function call()
	{
		# Ensure a user is logged in.
	
		if ($GLOBALS['usr'] == false)
		{
			$after_login = "okapi/apps/"; # it is correct, if you're wondering
			$login_url = $GLOBALS['absolute_server_URI']."login.php?target=".urlencode($after_login);
			return new OkapiRedirectResponse($login_url);
		}
		
		$consumer_key = isset($_REQUEST['consumer_key']) ? $_REQUEST['consumer_key'] : '';
		
		# Just remove app (if it doesn't exist - nothing wrong will happen anyway).
		
		Db::execute("
			delete from okapi_tokens
			where
				user_id = '".mysql_real_escape_string($GLOBALS['usr']['userid'])."'
				and consumer_key = '".mysql_real_escape_string($consumer_key)."'
		");
		Db::execute("
			delete from okapi_authorizations
			where
				user_id = '".mysql_real_escape_string($GLOBALS['usr']['userid'])."'
				and consumer_key = '".mysql_real_escape_string($consumer_key)."'
		");
		
		# Redirect back to the apps page.
		
		return new OkapiRedirectResponse($GLOBALS['absolute_server_URI']."okapi/apps/");
	}
}
