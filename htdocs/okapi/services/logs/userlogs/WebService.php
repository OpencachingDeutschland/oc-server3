<?php

namespace okapi\services\logs\userlogs;

use okapi\Db;
use okapi\Exception\InvalidParam;
use okapi\Exception\ParamMissing;
use okapi\Okapi;
use okapi\OkapiServiceRunner;
use okapi\Request\OkapiInternalRequest;
use okapi\Request\OkapiRequest;
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
        $user_uuid = $request->get_parameter('user_uuid');
        if (!$user_uuid) throw new ParamMissing('user_uuid');
        $limit = $request->get_parameter('limit');
        if (!$limit) $limit = "20";
        if (!is_numeric($limit))
            throw new InvalidParam('limit', "'$limit'");
        $limit = intval($limit);
        if (($limit < 1) || ($limit > 1000))
            throw new InvalidParam('limit', "Has to be in range 1..1000.");
        $offset = $request->get_parameter('offset');
        if (!$offset) $offset = "0";
        if (!is_numeric($offset))
            throw new InvalidParam('offset', "'$offset'");
        $offset = intval($offset);
        if ($offset < 0)
            throw new InvalidParam('offset', "'$offset'");

        # Check if user exists and retrieve user's ID (this will throw
        # a proper exception on invalid UUID).
        $user = OkapiServiceRunner::call('services/users/user', new OkapiInternalRequest(
            $request->consumer, null, array('user_uuid' => $user_uuid, 'fields' => 'internal_id')));

        # User exists. Retrieving logs.

        $rs = Db::query("
            select cl.id, cl.uuid, cl.type, unix_timestamp(cl.date) as date, cl.text,
                c.wp_oc as cache_code
            from cache_logs cl, caches c
            where
                cl.user_id = '".Db::escape_string($user['internal_id'])."'
                and ".((Settings::get('OC_BRANCH') == 'oc.pl') ? "cl.deleted = 0" : "true")."
                and c.status in (1,2,3)
                and cl.cache_id = c.cache_id
            order by cl.date desc
            limit $offset, $limit
        ");
        $results = array();
        while ($row = Db::fetch_assoc($rs))
        {
            $results[] = array(
                'uuid' => $row['uuid'],
                'date' => date('c', $row['date']),
                'cache_code' => $row['cache_code'],
                'type' => Okapi::logtypeid2name($row['type']),
                'comment' => $row['text']
            );
        }

        return Okapi::formatted_response($request, $results);
    }
}
