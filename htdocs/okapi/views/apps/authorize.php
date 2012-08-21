<?php

namespace okapi\views\apps\authorize;

use Exception;
use okapi\Okapi;
use okapi\Db;
use okapi\OkapiHttpResponse;
use okapi\OkapiHttpRequest;
use okapi\OkapiRedirectResponse;
use okapi\Settings;
use okapi\Locales;

class View
{
	public static function call()
	{
		$token_key = isset($_GET['oauth_token']) ? $_GET['oauth_token'] : '';
		$langpref = isset($_GET['langpref']) ? $_GET['langpref'] : Settings::get('SITELANG');
		$langprefs = explode("|", $langpref);
		$locales = array();
		foreach (Locales::$languages as $lang => $attrs)
			$locales[$attrs['locale']] = $attrs;
		
		# Current implementation of the "interactivity" parameter is: If developer
		# wants to "confirm_user", then just log out the current user before we
		# continue.
		
		$force_relogin = (isset($_GET['interactivity']) && $_GET['interactivity'] == 'confirm_user');
		
		$token = Db::select_row("
			select
				t.`key` as `key`,
				c.`key` as consumer_key,
				c.name as consumer_name,
				c.url as consumer_url,
				t.callback,
				t.verifier
			from
				okapi_consumers c,
				okapi_tokens t
			where
				t.`key` = '".mysql_real_escape_string($token_key)."'
				and t.consumer_key = c.`key`
				and t.user_id is null
		");
		
		$callback_concat_char = (strpos($token['callback'], '?') === false) ? "?" : "&";
		
		if (!$token)
		{
			# Probably Request Token has expired. This will be usually viewed
			# by the user, who knows nothing on tokens and OAuth. Let's be nice then!
			
			$vars = array(
				'okapi_base_url' => $GLOBALS['absolute_server_URI']."okapi/",
				'token' => $token,
				'token_expired' => true,
				'site_name' => Okapi::get_normalized_site_name(),
				'locales' => $locales,
			);
			$response = new OkapiHttpResponse();
			$response->content_type = "text/html; charset=utf-8";
			ob_start();
			$vars['locale_displayed'] = Okapi::gettext_domain_init($langprefs);
			include 'authorize.tpl.php';
			$response->body = ob_get_clean();
			Okapi::gettext_domain_restore();
			return $response;
		}
		
		# Ensure a user is logged in (or force re-login).
	
		if ($force_relogin || ($GLOBALS['usr'] == false))
		{
			if ($force_relogin)
			{
				# OC uses REAL MAGIC for session handling. I don't get ANY of it.
				# The logout.php DOES NOT support the "target" parameter, so we
				# can't just call it. The only thing that comes to mind is...
				# Destroy EVERYTHING.
				
				$past = time() - 86400;
				foreach ($_COOKIE as $key => $value)
					setcookie($key, $value, $past, '/');
			}
			
			# We should be logged out now. Let's login again.
			
			$after_login = "okapi/apps/authorize?oauth_token=$token_key".(($langpref != Settings::get('SITELANG'))?"&langpref=".$langpref:"");
			$login_url = $GLOBALS['absolute_server_URI']."login.php?target=".urlencode($after_login)
				."&langpref=".$langpref;
			return new OkapiRedirectResponse($login_url);
		}

		# Check if this user has already authorized this Consumer. If he did,
		# then we will automatically authorize all subsequent Request Tokens
		# from this Consumer.

		$authorized = Db::select_value("
			select 1
			from okapi_authorizations
			where
				user_id = '".mysql_real_escape_string($GLOBALS['usr']['userid'])."'
				and consumer_key = '".mysql_real_escape_string($token['consumer_key'])."'
		", 0);

		if (!$authorized)
		{
			if (isset($_POST['authorization_result']))
			{
				# Not yet authorized, but user have just submitted the authorization form.
				# WRTODO: CSRF protection
				
				if ($_POST['authorization_result'] == 'granted')
				{
					Db::execute("
						insert into okapi_authorizations (consumer_key, user_id)
						values (
							'".mysql_real_escape_string($token['consumer_key'])."',
							'".mysql_real_escape_string($GLOBALS['usr']['userid'])."'
						);
					");
					$authorized = true;
				}
				else
				{
					# User denied access. Nothing sensible to do now. Will try to report
					# back to the Consumer application with an error.
					
					if ($token['callback']) {
						return new OkapiRedirectResponse($token['callback'].$callback_concat_char."error=access_denied");
					} else {
						# Consumer did not provide a callback URL (oauth_callback=oob).
						# We'll have to redirect to the OpenCaching main page then...
						return new OkapiRedirectResponse($GLOBALS['absolute_server_URI']."index.php");
					}
				}
			}
			else
			{
				# Not yet authorized. Display an authorization request.
				$vars = array(
					'okapi_base_url' => $GLOBALS['absolute_server_URI']."okapi/",
					'token' => $token,
					'site_name' => Okapi::get_normalized_site_name(),
					'locales' => $locales,
				);
				$response = new OkapiHttpResponse();
				$response->content_type = "text/html; charset=utf-8";
				ob_start();
				$vars['locale_displayed'] = Okapi::gettext_domain_init($langprefs);
				include 'authorize.tpl.php';
				$response->body = ob_get_clean();
				Okapi::gettext_domain_restore();
				return $response;
			}
		}
		
		# User granted access. Now we can authorize the Request Token.
		
		Db::execute("
			update okapi_tokens
			set user_id = '".mysql_real_escape_string($GLOBALS['usr']['userid'])."'
			where `key` = '".mysql_real_escape_string($token_key)."';
		");
		
		# Redirect to the callback_url.
		
		if ($token['callback']) {
			return new OkapiRedirectResponse($token['callback'].$callback_concat_char."oauth_token=".$token_key."&oauth_verifier=".$token['verifier']);
		} else {
			# Consumer did not provide a callback URL (probably the user is using a desktop
			# or mobile application). We'll just have to display the verifier to the user.
			return new OkapiRedirectResponse($GLOBALS['absolute_server_URI']."okapi/apps/authorized?oauth_token=".$token_key
				."&oauth_verifier=".$token['verifier']."&langpref=".$langpref);
		}
	}
}
