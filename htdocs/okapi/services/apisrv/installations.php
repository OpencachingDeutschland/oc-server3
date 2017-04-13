<?php

namespace okapi\services\apisrv\installations;

use ErrorException;
use okapi\Cache;
use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\Settings;

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
        # The list of installations is periodically refreshed by contacting OKAPI
        # repository. This method usually displays the cached version of it.

        $VERSION = "2";
        $cachekey = 'apisrv/installations-v'.$VERSION;
        $backupkey = 'apisrv/installations-v'.$VERSION.'-backup';
        $results = Cache::get($cachekey);
        if (!$results)
        {
            # Download the current list of OKAPI servers.

            try
            {
                $opts = array(
                    'http' => array(
                        'method' => "GET",
                        'timeout' => 5.0
                    )
                );
                $context = stream_context_create($opts);
                $xml = file_get_contents("https://raw.githubusercontent.com/opencaching/okapi/master/etc/installations.xml",
                    false, $context);
                $doc = simplexml_load_string($xml);
                if (!$doc) {
                    throw new ErrorException(); # just to get to the catch block
                }
            }
            catch (ErrorException $e)
            {
                # GitHub failed on us. Try to respond with a backup list.

                $results = Cache::get($backupkey);
                if ($results)
                {
                    Cache::set($cachekey, $results, 12 * 3600); # so to retry no earlier than after 12 hours
                    return Okapi::formatted_response($request, $results);
                }

                # Backup has expired (or have never been cached). If we're on a development
                # server then probably it's okay. In production this SHOULD NOT happen.

                $results = array(
                    array(
                        'site_url' => Settings::get('SITE_URL'),
                        'site_name' => "Unable to retrieve!",
                        'okapi_base_url' => Settings::get('SITE_URL')."okapi/",
                    )
                );
                Cache::set($cachekey, $results, 12 * 3600); # so to retry no earlier than after 12 hours
                return Okapi::formatted_response($request, $results);
            }

            $results = array();
            $i_was_included = false;
            foreach ($doc->installation as $inst)
            {
                $site_url = (string)$inst[0]['site_url'];
                if ($inst[0]['okapi_base_url'])
                    $okapi_base_url = (string)$inst[0]['okapi_base_url'];
                else
                    $okapi_base_url = $site_url."okapi/";
                if ($inst[0]['site_name'])
                    $site_name = (string)$inst[0]['site_name'];
                else
                    $site_name = Okapi::get_normalized_site_name($site_url);
                $results[] = array(
                    'site_url' => $site_url,
                    'site_name' => $site_name,
                    'okapi_base_url' => $okapi_base_url,
                );
                if (in_array($okapi_base_url, Okapi::get_allowed_base_urls()))
                    $i_was_included = true;
            }

            # If running on a local development installation, then include the local
            # installation URL.

            if (!$i_was_included)
            {
                $results[] = array(
                    'site_url' => Settings::get('SITE_URL'),
                    'site_name' => "DEVELSITE",
                    'okapi_base_url' => Settings::get('SITE_URL')."okapi/",
                );
                # Contact OKAPI developers in order to get added to the official sites list!
            }

            # Cache it for one day. Also, save a backup (valid for 30 days).

            Cache::set($cachekey, $results, 86400);
            Cache::set($backupkey, $results, 86400*30);
        }

        return Okapi::formatted_response($request, $results);
    }
}
