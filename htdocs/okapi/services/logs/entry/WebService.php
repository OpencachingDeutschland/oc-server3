<?php

namespace okapi\services\logs\entry;

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
        $log_uuid = $request->get_parameter('log_uuid');
        if (!$log_uuid) throw new ParamMissing('log_uuid');
        $fields = $request->get_parameter('fields');
        if (!$fields) $fields = "date|user|type|comment";

        $results = OkapiServiceRunner::call('services/logs/entries', new OkapiInternalRequest(
            $request->consumer, $request->token, array('log_uuids' => $log_uuid,
            'fields' => $fields)));
        $result = $results[$log_uuid];
        if ($result == null)
            throw new InvalidParam('log_uuid', "This log entry does not exist.");
        return Okapi::formatted_response($request, $result);
    }
}
