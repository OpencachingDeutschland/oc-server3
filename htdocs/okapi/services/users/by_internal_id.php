<?php

namespace okapi\services\users\by_internal_id;

use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\BadRequest;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
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
        $internal_id = $request->get_parameter('internal_id');
        if (!$internal_id) throw new ParamMissing('internal_id');
        $fields = $request->get_parameter('fields');

        # There's no need to validate the fields parameter.

        $results = OkapiServiceRunner::call('services/users/by_internal_ids', new OkapiInternalRequest(
            $request->consumer, $request->token, array('internal_ids' => $internal_id,
            'fields' => $fields)));
        $result = $results[$internal_id];
        if ($result == null)
            throw new InvalidParam('internal_id', "There is no user by this internal_id.");
        return Okapi::formatted_response($request, $result);
    }
}
