<?php

namespace okapi\views\introduction;

use okapi\Okapi;
use okapi\OkapiHttpResponse;
use okapi\OkapiInternalConsumer;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\Settings;
use okapi\views\menu\OkapiMenu;

class View
{
    public static function call()
    {
        require_once __DIR__ . '/menu.inc.php';

        $vars = array(
            'menu' => OkapiMenu::get_menu_html("introduction.html"),
            'okapi_base_url' => Settings::get('SITE_URL')."okapi/",
            'site_url' => Settings::get('SITE_URL'),
            'method_index' => OkapiServiceRunner::call('services/apiref/method_index',
                new OkapiInternalRequest(new OkapiInternalConsumer(), null, array())),
            'installations' => OkapiMenu::get_installations(),
            'okapi_rev' => Okapi::$version_number,
        );

        $response = new OkapiHttpResponse();
        $response->content_type = "text/html; charset=utf-8";
        ob_start();
        require_once __DIR__ . '/introduction.tpl.php';
        $response->body = ob_get_clean();
        return $response;
    }
}
