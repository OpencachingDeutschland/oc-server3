<?php

namespace okapi\services\users\user;

use okapi\BadRequest;

use okapi\OkapiInternalRequest;

use okapi\OkapiServiceRunner;

use okapi\Okapi;
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
        $user_uuid = $request->get_parameter('user_uuid');
        if (!$user_uuid)
        {
            if ($request->token)
            {
                $tmp = OkapiServiceRunner::call('services/users/by_internal_id', new OkapiInternalRequest(
                    $request->consumer, null, array('internal_id' => $request->token->user_id, 'fields' => 'uuid')));
                $user_uuid = $tmp['uuid'];
            }
            else
            {
                throw new BadRequest("You must either: 1. supply the user_uuid argument, or "
                    ."2. sign your request with an Access Token.");
            }
        }
        $fields = $request->get_parameter('fields');

        # There's no need to validate the fields parameter as the 'users'
        # method does this (it will raise a proper exception on invalid values).

        $results = OkapiServiceRunner::call('services/users/users', new OkapiInternalRequest(
            $request->consumer, $request->token, array('user_uuids' => $user_uuid,
            'fields' => $fields)));
        $result = $results[$user_uuid];
        if ($result == null)
            throw new InvalidParam('user_uuid', "There is no user by this ID.");
        return Okapi::formatted_response($request, $result);
    }
}
