<?php

namespace okapi\views\method_call;

use Exception;
use okapi\Okapi;
use okapi\Settings;
use okapi\OkapiHttpRequest;
use okapi\OkapiHttpResponse;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\BadRequest;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use okapi\views\menu\OkapiMenu;

class View
{
    public static function call($methodname)
    {
        require_once($GLOBALS['rootpath'].'okapi/service_runner.php');

        if (!OkapiServiceRunner::exists($methodname))
            throw new BadRequest("Method '$methodname' does not exist. ".
                "See OKAPI docs at ".Settings::get('SITE_URL')."okapi/");
        $options = OkapiServiceRunner::options($methodname);
        $request = new OkapiHttpRequest($options);
        return OkapiServiceRunner::call($methodname, $request);
    }
}
