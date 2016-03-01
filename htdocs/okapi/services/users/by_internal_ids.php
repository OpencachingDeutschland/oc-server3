<?php

namespace okapi\services\users\by_internal_ids;

use okapi\Okapi;
use okapi\Db;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\services\caches\search\SearchAssistant;

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
        $internal_ids = $request->get_parameter('internal_ids');
        if (!$internal_ids) throw new ParamMissing('internal_ids');
        $internal_ids = explode("|", $internal_ids);
        if (count($internal_ids) > 500)
            throw new InvalidParam('internal_ids', "Maximum allowed number of referenced users ".
                "is 500. You provided ".count($internal_ids)." references.");
        $fields = $request->get_parameter('fields');
        if (!$fields)
            throw new ParamMissing('fields');

        # There's no need to validate the fields parameter as the 'users'
        # method does this (it will raise a proper exception on invalid values).

        $rs = Db::query("
            select user_id, uuid
            from user
            where user_id in ('".implode("','", array_map('\okapi\Db::escape_string', $internal_ids))."')
        ");
        $internalid2useruuid = array();
        while ($row = Db::fetch_assoc($rs))
        {
            $internalid2useruuid[$row['user_id']] = $row['uuid'];
        }
        Db::free_result($rs);

        # Retrieve data on given user_uuids.
        $id_results = OkapiServiceRunner::call('services/users/users', new OkapiInternalRequest(
            $request->consumer, $request->token, array('user_uuids' => implode("|", array_values($internalid2useruuid)),
            'fields' => $fields)));

        # Map user_uuids to internal_ids. Also check which internal_ids were not found
        # and mark them with null.
        $results = array();
        foreach ($internal_ids as $internal_id)
        {
            if (!isset($internalid2useruuid[$internal_id]))
                $results[$internal_id] = null;
            else
                $results[$internal_id] = $id_results[$internalid2useruuid[$internal_id]];
        }

        return Okapi::formatted_response($request, $results);
    }
}
