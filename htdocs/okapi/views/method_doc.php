<?php

namespace okapi\views\method_doc;

use Exception;
use okapi\Okapi;
use okapi\Settings;
use okapi\OkapiRequest;
use okapi\OkapiHttpResponse;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\BadRequest;
use okapi\Http404;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use okapi\views\menu\OkapiMenu;

class View
{
    public static function call($methodname)
    {
        require_once($GLOBALS['rootpath'].'okapi/service_runner.php');
        require_once($GLOBALS['rootpath'].'okapi/views/menu.inc.php');

        try
        {
            $method = OkapiServiceRunner::call('services/apiref/method', new OkapiInternalRequest(
                null, null, array('name' => $methodname)));
        }
        catch (BadRequest $e)
        {
            throw new Http404();
        }
        $vars = array(
            'method' => $method,
            'menu' => OkapiMenu::get_menu_html($methodname.".html"),
            'okapi_base_url' => Settings::get('SITE_URL')."okapi/",
            'installations' => OkapiMenu::get_installations(),
            'okapi_rev' => Okapi::$revision,
        );

        $response = new OkapiHttpResponse();
        $response->content_type = "text/html; charset=utf-8";
        ob_start();
        include 'method_doc.tpl.php';
        $response->body = ob_get_clean();
        return $response;
    }
}
