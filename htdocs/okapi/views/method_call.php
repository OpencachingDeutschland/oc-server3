<?php

namespace okapi\views\method_call;

use okapi\BadRequest;
use okapi\OkapiHttpRequest;
use okapi\OkapiServiceRunner;
use okapi\Settings;

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
