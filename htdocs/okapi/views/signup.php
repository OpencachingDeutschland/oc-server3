<?php

namespace okapi\views\signup;

use Exception;
use okapi\Okapi;
use okapi\Settings;
use okapi\OkapiRequest;
use okapi\OkapiHttpResponse;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use okapi\views\menu\OkapiMenu;
use okapi\OkapiHttpRequest;

class View
{
	public static function call()
	{
		if (isset($_REQUEST['posted']))
		{
			$appname = isset($_REQUEST['appname']) ? $_REQUEST['appname'] : "";
			$appname = trim($appname);
			$appurl = isset($_REQUEST['appurl']) ? $_REQUEST['appurl'] : "";
			$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : "";
			$accepted_terms = isset($_REQUEST['terms']) ? $_REQUEST['terms'] : "";
			$ok = false;
			if (!$appname)
				$notice = "Please provide a valid application name.";
			elseif (mb_strlen($appname) > 100)
				$notice = "Application name should be less than 100 characters long.";
			elseif (mb_strlen($appurl) > 250)
				$notice = "Application URL should be less than 250 characters long.";
			elseif ($appurl && (substr($appurl, 0, 7) != "http://") && (substr($appurl, 0, 8) != "https://"))
				$notice = "Application homepage URL should start with http(s)://. (Note: this URL is OPTIONAL and it is NOT for OAuth callback.)";
			elseif (!$email)
				$notice = "Please provide a valid email address.";
			elseif (mb_strlen($email) > 70)
				$notice = "Email address should be less than 70 characters long.";
			elseif (!$accepted_terms)
				$notice = "You have to read and accept OKAPI Terms of Use.";
			else
			{
				$ok = true;
				Okapi::register_new_consumer($appname, $appurl, $email);
				$notice = "Consumer Key generated successfully.\nCheck your email!";
			}
			$response = new OkapiHttpResponse();
			$response->content_type = "application/json; charset=utf-8";
			$response->body = json_encode(array(
				'ok' => $ok,
				'notice' => $notice
			));
			return $response;
		}

		require_once($GLOBALS['rootpath'].'okapi/service_runner.php');
		require_once($GLOBALS['rootpath'].'okapi/views/menu.inc.php');

		$vars = array(
			'menu' => OkapiMenu::get_menu_html("signup.html"),
			'okapi_base_url' => Settings::get('SITE_URL')."okapi/",
			'site_url' => Settings::get('SITE_URL'),
			'site_name' => Okapi::get_normalized_site_name(),
			'installations' => OkapiMenu::get_installations(),
			'okapi_rev' => Okapi::$revision,
			'data_license_html' => Settings::get('DATA_LICENSE_URL')
				? "<a href='".Settings::get('DATA_LICENSE_URL')."'>Data License</a>"
				: "Data License",
		);

		$response = new OkapiHttpResponse();
		$response->content_type = "text/html; charset=utf-8";
		ob_start();
		include 'signup.tpl.php';
		$response->body = ob_get_clean();
		return $response;
	}
}
