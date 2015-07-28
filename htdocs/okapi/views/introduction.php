<?php

namespace okapi\views\introduction;

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
use okapi\OkapiInternalConsumer;

class View
{
    public static function call()
    {
        require_once($GLOBALS['rootpath'].'okapi/service_runner.php');
        require_once($GLOBALS['rootpath'].'okapi/views/menu.inc.php');

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
        include 'introduction.tpl.php';
        $response->body = ob_get_clean();
        return $response;
    }
}
