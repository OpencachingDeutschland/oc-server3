<?php

namespace okapi\services\users\by_username;

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
        $username = $request->get_parameter('username');
        if (!$username) throw new ParamMissing('username');
        $fields = $request->get_parameter('fields');

        # There's no need to validate the fields parameter.

        $results = OkapiServiceRunner::call('services/users/by_usernames', new OkapiInternalRequest(
            $request->consumer, $request->token, array('usernames' => $username,
            'fields' => $fields)));
        $result = $results[$username];
        if ($result == null)
            throw new InvalidParam('username', "There is no user by this username.");
        return Okapi::formatted_response($request, $result);
    }
}
