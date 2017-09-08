<?php

namespace okapi\views\method_call;

use okapi\core\Exception\BadRequest;
use okapi\core\OkapiServiceRunner;
use okapi\core\Request\OkapiHttpRequest;
use okapi\Settings;

class View
{
    public static function call($methodname)
    {
        if (!OkapiServiceRunner::exists($methodname))
            throw new BadRequest("Method '$methodname' does not exist. ".
                "See OKAPI docs at ".Settings::get('SITE_URL')."okapi/");
        $options = OkapiServiceRunner::options($methodname);
        $request = new OkapiHttpRequest($options);
        return OkapiServiceRunner::call($methodname, $request);
    }
}
