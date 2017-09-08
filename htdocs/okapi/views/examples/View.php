<?php

namespace okapi\views\examples;

use okapi\core\Okapi;
use okapi\core\Response\OkapiHttpResponse;
use okapi\Settings;
use okapi\views\menu\OkapiMenu;

class View
{
    public static function call()
    {
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
        require_once __DIR__ . '/examples.tpl.php';
        $response->body = ob_get_clean();
        return $response;
    }
}
