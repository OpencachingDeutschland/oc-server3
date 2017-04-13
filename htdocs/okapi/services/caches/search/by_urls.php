<?php

namespace okapi\services\caches\search\by_urls;

require_once('searching.inc.php');

use okapi\Db;
use okapi\InvalidParam;
use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\Settings;

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 1
        );
    }

    /**
     * Returns one of: array('cache_code', 'OPXXXX'), array('internal_id', '12345'),
     * array('uuid', 'A408C3...') or null.
     */
    private static function get_cache_key($url)
    {
        # Determine our own domain.

        static $host = null;
        static $length = null;
        if ($host == null)
        {
            $host = parse_url(Settings::get('SITE_URL'), PHP_URL_HOST);
            if (strpos($host, "www.") === 0)
                $host = substr($host, 4);
            $length = strlen($host);
        }

        # Parse the URL

        $uri = parse_url($url);
        if ($uri == false)
            return null;
        if ((!isset($uri['scheme'])) || (!in_array($uri['scheme'], array('http', 'https'))))
            return null;
        if ((!isset($uri['host'])) || (substr($uri['host'], -$length) != $host))
            return null;
        if (!isset($uri['path']))
            return null;
        if (preg_match("#^/(O[A-Z][A-Z0-9]{4,5})$#", $uri['path'], $matches))
        {
            # Some servers allow "http://oc.xx/<cache_code>" shortcut.
            return array('cache_code', $matches[1]);
        }
        $parts = array();
        if (isset($uri['query']))
            $parts = array_merge($parts, explode('&', $uri['query']));
        if (isset($uri['fragment']))
            $parts = array_merge($parts, explode('&', $uri['fragment']));
        foreach ($parts as $param)
        {
            $item = explode('=', $param, 2);
            if (count($item) != 2)
                continue;
            $key = $item[0];
            $value = $item[1];
            if ($key == 'wp')
                return array('cache_code', $value);
            if ($key == 'cacheid')
                return array('internal_id', $value);
            if ($key == 'uuid')
                return array('uuid', $value);
        }
        return null;
    }

    public static function call(OkapiRequest $request)
    {
        # Retrieve the list of URLs to check.

        $tmp = $request->get_parameter('urls');
        if (!$tmp)
            throw new ParamMissing('urls');
        $urls = explode('|', $tmp);
        $as_dict = $request->get_parameter('as_dict');
        if (!$as_dict) $as_dict = 'false';
        if (!in_array($as_dict, array('true', 'false')))
            throw new InvalidParam('as_dict');
        $as_dict = ($as_dict == 'true');

        # Generate the lists of keys.

        $results = array();
        $urls_with = array(
            'cache_code' => array(),
            'internal_id' => array(),
            'uuid' => array()
        );
        foreach ($urls as &$url_ref)
        {
            $key = self::get_cache_key($url_ref);
            if ($key != null)
                $urls_with[$key[0]][$url_ref] = $key[1];
            else
                $results[$url_ref] = null;
        }

        # Include 'cache_code' references.

        foreach ($urls_with['cache_code'] as $url => $cache_code)
            $results[$url] = $cache_code;

        # Include 'internal_id' references.

        $internal_ids = array_values($urls_with['internal_id']);
        if (count($internal_ids) > 0)
        {
            $rs = Db::query("
                select cache_id, wp_oc
                from caches
                where
                    cache_id in ('".implode("','", array_map('\okapi\Db::escape_string', $internal_ids))."')
                    and status in (1,2,3)
            ");
            $dict = array();
            while ($row = Db::fetch_assoc($rs))
                $dict[$row['cache_id']] = $row['wp_oc'];
            foreach ($urls_with['internal_id'] as $url => $internal_id)
            {
                if (isset($dict[$internal_id]))
                    $results[$url] = $dict[$internal_id];
                else
                    $results[$url] = null;
            }
        }

        # Include 'uuid' references.

        $uuids = array_values($urls_with['uuid']);
        if (count($uuids) > 0)
        {
            $rs = Db::query("
                select uuid, wp_oc
                from caches
                where
                    uuid in ('".implode("','", array_map('\okapi\Db::escape_string', $uuids))."')
                    and status in (1,2,3)
            ");
            $dict = array();
            while ($row = Db::fetch_assoc($rs))
                $dict[$row['uuid']] = $row['wp_oc'];
            foreach ($urls_with['uuid'] as $url => $uuid)
            {
                if (isset($dict[$uuid]))
                    $results[$url] = $dict[$uuid];
                else
                    $results[$url] = null;
            }
        }

        # Format the results according to the 'as_dict' parameter.

        if ($as_dict)
            return Okapi::formatted_response($request, $results);
        else
        {
            $cache_codes = array();
            foreach ($results as $url => $cache_code)
                if ($cache_code != null)
                    $cache_codes[$cache_code] = true;
            $flattened = array('results' => array_keys($cache_codes));
            return Okapi::formatted_response($request, $flattened);
        }
    }
}
