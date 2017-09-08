<?php

namespace okapi\views\method_doc;

use okapi\core\Exception\BadRequest;
use okapi\core\Exception\Http404;
use okapi\core\Okapi;
use okapi\core\OkapiServiceRunner;
use okapi\core\Request\OkapiInternalRequest;
use okapi\core\Response\OkapiHttpResponse;
use okapi\Settings;
use okapi\views\menu\OkapiMenu;

class View
{
    public static function call($methodname)
    {
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
            'okapi_rev' => Okapi::$version_number,
        );

        $response = new OkapiHttpResponse();
        $response->content_type = "text/html; charset=utf-8";
        ob_start();
        require_once __DIR__ . '/method_doc.tpl.php';
        $response->body = ob_get_clean();
        return $response;
    }
}
