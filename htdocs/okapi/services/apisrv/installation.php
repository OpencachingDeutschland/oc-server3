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
        $result['okapi_version_number'] = Okapi::$version_number;
        $result['okapi_revision'] = Okapi::$version_number; /* Important for backward-compatibility! */
        $result['okapi_git_revision'] = Okapi::$git_revision;
        $result['registration_url'] = $result['site_url']."register.php";
        $result['mobile_registration_url'] = null;
        return Okapi::formatted_response($request, $result);
    }
}
