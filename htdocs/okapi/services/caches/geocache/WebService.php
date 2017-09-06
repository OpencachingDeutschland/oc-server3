<?php

namespace okapi\services\caches\geocache;

use okapi\Db;
use okapi\Exception\InvalidParam;
use okapi\Exception\ParamMissing;
use okapi\Okapi;
use okapi\OkapiServiceRunner;
use okapi\Request\OkapiInternalRequest;
use okapi\Request\OkapiRequest;

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 1
        );
    }

    public static function call(OkapiRequest $request)
    {
        $cache_code = $request->get_parameter('cache_code');
        if (!$cache_code) throw new ParamMissing('cache_code');
        if (strpos($cache_code, "|") !== false) throw new InvalidParam('cache_code');
        $langpref = $request->get_parameter('langpref');
        if (!$langpref) $langpref = "en";
        $fields = $request->get_parameter('fields');
        if (!$fields) $fields = "code|name|location|type|status";
        $log_fields = $request->get_parameter('log_fields');
        if (!$log_fields) $log_fields = "uuid|date|user|type|comment";
        $lpc = $request->get_parameter('lpc');
        if (!$lpc) $lpc = 10;
        $attribution_append = $request->get_parameter('attribution_append');
        if (!$attribution_append) $attribution_append = 'full';
        $params = array(
            'cache_codes' => $cache_code,
            'langpref' => $langpref,
            'fields' => $fields,
            'attribution_append' => $attribution_append,
            'lpc' => $lpc,
            'log_fields' => $log_fields
        );
        $my_location = $request->get_parameter('my_location');
        if ($my_location)
            $params['my_location'] = $my_location;
        $user_uuid = $request->get_parameter('user_uuid');
        if ($user_uuid)
            $params['user_uuid'] = $user_uuid;

        # There's no need to validate the fields/lpc parameters as the 'geocaches'
        # method does this (it will raise a proper exception on invalid values).

        $results = OkapiServiceRunner::call('services/caches/geocaches', new OkapiInternalRequest(
            $request->consumer, $request->token, $params));
        $result = $results[$cache_code];
        if ($result === null)
        {
            # Two errors messages (for OCDE). Makeshift solution for issue #350.

            $exists = Db::select_value("
                select 1
                from caches
                where wp_oc='".Db::escape_string($cache_code)."'
            ");
            if ($exists) {
                throw new InvalidParam('cache_code', "This cache is not accessible via OKAPI.");
            } else {
                throw new InvalidParam('cache_code', "This cache does not exist.");
            }
        }
        return Okapi::formatted_response($request, $result);
    }
}
