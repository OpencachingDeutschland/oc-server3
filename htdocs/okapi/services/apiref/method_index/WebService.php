<?php

namespace okapi\services\apiref\method_index;

use okapi\Cache;
use okapi\Consumer\OkapiInternalConsumer;
use okapi\Okapi;
use okapi\OkapiServiceRunner;
use okapi\Request\OkapiInternalRequest;
use okapi\Request\OkapiRequest;
use okapi\Settings;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;


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
        $cache_key = self::generateCacheKey();
        $results = Cache::get($cache_key);
        if ($results == null)
        {
            $results = array();
            foreach (OkapiServiceRunner::$all_names as $methodname)
            {
                $info = OkapiServiceRunner::call('services/apiref/method', new OkapiInternalRequest(
                    new OkapiInternalConsumer(), null, array('name' => $methodname)));
                $results[] = array(
                    'name' => $info['name'],
                    'short_name' => $info['short_name'],
                    'brief_description' => $info['brief_description'],
                    'infotags' => $info['infotags'],
                    'auth_options' => $info['auth_options'],
                );
            }
            Cache::set($cache_key, $results, 86400);
        }
        return Okapi::formatted_response($request, $results);
    }

    /**
     * This method will generate a best cache key to be used, depending on the environment.
     *
     * We want the method index to return fast, but we also don't want developers to see cached
     * results when they are adding (or changing) methods (in dev-environments).
     */
    private static function generateCacheKey() {

        if (!Settings::get('DEBUG')) {

            /* Production. */

            if (Okapi::$version_number !== null) {
                return "api_ref/method_index#prod#".Okapi::$version_number;
            } else {
                $methodnames = OkapiServiceRunner::$all_names;
                sort($methodnames);
                return "api_ref/method_index#".md5(implode("#", $methodnames));
            }

        } else {

            /* Development. */

            return (
                "api_ref/method_index#dev#".
                self::getDirModDateRecursive($GLOBALS['rootpath']."okapi/services")
            );
        }
    }

    private static function getDirModDateRecursive($absoluteDir) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($absoluteDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        $max_timestamp = 0;
        foreach ($iterator as $item) {
            if($item->isDir()) {
                $pth = $item->getPath()."/.";
            } else {
                $pth = $item->getPathname();
            }
            $timestamp = filemtime($pth);
            if ($timestamp > $max_timestamp) {
                $max_timestamp = $timestamp;
            }
        }
        return $max_timestamp;
    }
}
