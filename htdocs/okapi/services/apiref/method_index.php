<?php

namespace okapi\services\apiref\method_index;

use okapi\Cache;
use okapi\Okapi;
use okapi\OkapiInternalConsumer;
use okapi\OkapiInternalRequest;
use okapi\OkapiRequest;
use okapi\OkapiServiceRunner;


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
        $methodnames = OkapiServiceRunner::$all_names;
        sort($methodnames);
        $cache_key = "api_ref/method_index#".md5(implode("#", $methodnames));
        $results = Cache::get($cache_key);
        if ($results == null)
        {
            $results = array();
            foreach ($methodnames as $methodname)
            {
                $info = OkapiServiceRunner::call('services/apiref/method', new OkapiInternalRequest(
                    new OkapiInternalConsumer(), null, array('name' => $methodname)));
                $results[] = array(
                    'name' => $info['name'],
                    'brief_description' => $info['brief_description'],
                );
            }
            Cache::set($cache_key, $results, 3600);
        }
        return Okapi::formatted_response($request, $results);
    }
}
