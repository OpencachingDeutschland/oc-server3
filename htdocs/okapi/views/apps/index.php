<?php

namespace okapi\views\apps\index;

use Exception;
use okapi\Okapi;
use okapi\Db;
use okapi\OkapiHttpResponse;
use okapi\OkapiHttpRequest;
use okapi\OkapiRedirectResponse;
use okapi\Settings;

class View
{
	public static function call()
	{
		$langpref = isset($_GET['langpref']) ? $_GET['langpref'] : Settings::get('SITELANG');
		$langprefs = explode("|", $langpref);
		
		# Ensure a user is logged in.
	
		if ($GLOBALS['usr'] == false)
		{
			$after_login = "okapi/apps/".(($langpref != Settings::get('SITELANG'))?"?langpref=".$langpref:"");
			$login_url = $GLOBALS['absolute_server_URI']."login.php?target=".urlencode($after_login);
			return new OkapiRedirectResponse($login_url);
		}
		
		# Get the list of authorized apps.
		
		$rs = Db::query("
			select c.`key`, c.name, c.url
			from
				okapi_consumers c,
				okapi_authorizations a
			where
				a.user_id = '".mysql_real_escape_string($GLOBALS['usr']['userid'])."'
				and c.`key` = a.consumer_key
			order by c.name
		");
		$vars = array();
		$vars['okapi_base_url'] = $GLOBALS['absolute_server_URI']."okapi/";
		$vars['site_name'] = Okapi::get_normalized_site_name();
		$vars['apps'] = array();
		while ($row = mysql_fetch_assoc($rs))
			$vars['apps'][] = $row;
		mysql_free_result($rs);
		
		$response = new OkapiHttpResponse();
		$response->content_type = "text/html; charset=utf-8";
		ob_start();
		Okapi::gettext_domain_init($langprefs);
		include 'index.tpl.php';
		$response->body = ob_get_clean();
		Okapi::gettext_domain_restore();
		return $response;
	}
}
