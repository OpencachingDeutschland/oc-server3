<?php

namespace okapi\views\http404;

use Exception;
use okapi\Okapi;
use okapi\OkapiHttpResponse;
use okapi\views\menu\OkapiMenu;

class View
{
	public static function call()
	{
		require_once 'menu.inc.php';
		
		$vars = array(
			'okapi_base_url' => $GLOBALS['absolute_server_URI']."okapi/",
			'menu' => OkapiMenu::get_menu_html(),
			'installations' => OkapiMenu::get_installations(),
			'okapi_rev' => Okapi::$revision,
		);
		
		$response = new OkapiHttpResponse();
		$response->status = "404 Not Found";
		$response->content_type = "text/html; charset=utf-8";
		ob_start();
		include 'http404.tpl.php';
		$response->body = ob_get_clean();
		return $response;
	}
}
