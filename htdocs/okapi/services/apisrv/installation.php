<?php

namespace okapi\services\apisrv\installation;

use Exception;
use okapi\Okapi;
use okapi\Settings;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 0
        );
    }

    public static function call(OkapiRequest $request)
    {
        $result = array();
        $result['site_url'] = Settings::get('SITE_URL');
        $result['okapi_base_url'] = $result['site_url']."okapi/";
        $result['site_name'] = Okapi::get_normalized_site_name();
        $result['okapi_revision'] = Okapi::$revision;
        return Okapi::formatted_response($request, $result);
    }
}
