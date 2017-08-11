<?php

namespace okapi\services\logs\logs;

use okapi\Db;
use okapi\InvalidParam;
use okapi\Okapi;
use okapi\OkapiInternalRequest;
use okapi\OkapiRequest;
use okapi\OkapiServiceRunner;
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
    public static function call(OkapiRequest $request)
    {
        $cache_code = $request->get_parameter('cache_code');
        if (!$cache_code) throw new ParamMissing('cache_code');
        $fields = $request->get_parameter('fields');
        if (!$fields) $fields = "uuid|date|user|type|comment";

        $offset = $request->get_parameter('offset');
        if (!$offset) $offset = "0";
        if ((((int)$offset) != $offset) || ((int)$offset) < 0)
            throw new InvalidParam('offset', "Expecting non-negative integer.");
        $limit = $request->get_parameter('limit');
        if (!$limit) $limit = "none";
        if ($limit == "none") $limit = "999999999";
        if ((((int)$limit) != $limit) || ((int)$limit) < 0)
            throw new InvalidParam('limit', "Expecting non-negative integer or 'none'.");

        # Check if code exists and retrieve cache ID (this will throw
        # a proper exception on invalid code).

        $cache = OkapiServiceRunner::call('services/caches/geocache', new OkapiInternalRequest(
            $request->consumer, null, array('cache_code' => $cache_code, 'fields' => 'internal_id')));

        # Cache exists. Getting the uuids of its logs.

        $log_uuids = Db::select_column("
            select uuid
            from cache_logs
            where
                cache_id = '".Db::escape_string($cache['internal_id'])."'
                and ".((Settings::get('OC_BRANCH') == 'oc.pl') ? "deleted = 0" : "true")."
            order by date desc
            limit $offset, $limit
        ");

        # Getting the logs themselves. Formatting as an ordered list.

        $internal_request = new OkapiInternalRequest(
            $request->consumer, $request->token, array('log_uuids' => implode("|", $log_uuids),
            'fields' => $fields));
        $internal_request->skip_limits = true;
        $logs = OkapiServiceRunner::call('services/logs/entries', $internal_request);
        $results = array();
        foreach ($log_uuids as $log_uuid)
            $results[] = $logs[$log_uuid];

        /* Handle OCPL's "access logs" feature. */

        if (
            (Settings::get('OC_BRANCH') == 'oc.pl')
            && Settings::get('OCPL_ENABLE_GEOCACHE_ACCESS_LOGS')
            && (count($log_uuids) > 0)
        ) {
            \okapi\lib\OCPLAccessLogs::log_geocache_access($request, $cache['internal_id']);
        }

        return Okapi::formatted_response($request, $results);
    }
}
