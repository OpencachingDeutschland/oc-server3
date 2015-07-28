<?php

namespace okapi\views\examples;

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

class View
{
    public static function call()
    {
        require_once($GLOBALS['rootpath'].'okapi/service_runner.php');
        require_once($GLOBALS['rootpath'].'okapi/views/menu.inc.php');

        $vars = array(
            'menu' => OkapiMenu::get_menu_html("examples.html"),
            'okapi_base_url' => Settings::get('SITE_URL')."okapi/",
            'site_url' => Settings::get('SITE_URL'),
            'installations' => OkapiMenu::get_installations(),
            'okapi_rev' => Okapi::$version_number,
            'site_name' => Okapi::get_normalized_site_name(),
        );

        $response = new OkapiHttpResponse();
        $response->content_type = "text/html; charset=utf-8";
        ob_start();
        include 'examples.tpl.php';
        $response->body = ob_get_clean();
        return $response;
    }
}
