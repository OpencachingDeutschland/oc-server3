<?php

namespace okapi\services\oauth\access_token;

use okapi\Exception\ParamMissing;
use okapi\Okapi;
use okapi\Request\OkapiRequest;
use okapi\Response\OkapiHttpResponse;

class WebService
{
    public static function options()
    {
        return array(
            'min_auth_level' => 3,
            'token_type' => 'request'
        );
    }

    public static function call(OkapiRequest $request)
    {
        $verifier = $request->get_parameter('oauth_verifier');
        if (!$verifier)
        {
            # We require the 1.0a flow (throw an error when there is no oauth_verifier).
            throw new ParamMissing("oauth_verifier");
        }

        $new_token = Okapi::$data_store->new_access_token($request->token, $request->consumer, $verifier);

        $response = new OkapiHttpResponse();
        $response->content_type = "text/plain; charset=utf-8";
        $response->body = $new_token;
        return $response;
    }
}
