<?php

namespace okapi\services\attrs\attribute;

use okapi\InvalidParam;
use okapi\Okapi;
use okapi\OkapiInternalRequest;
use okapi\OkapiRequest;
use okapi\OkapiServiceRunner;
use okapi\ParamMissing;


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
        # Read the parameters.

        $acode = $request->get_parameter('acode');
        if ($acode === null) throw new ParamMissing('acode');
        if (strstr($acode,'|')) throw new InvalidParam('acode', "Only a single A-code must be supplied.");

        $langpref = $request->get_parameter('langpref');
        if (!$langpref) $langpref = "en";

        $fields = $request->get_parameter('fields');
        if (!$fields) $fields = "name";

        $forward_compatible = $request->get_parameter('forward_compatible');
        if (!$forward_compatible) $forward_compatible = "true";

        # Pass them all to the attributes method.

        $params = array(
            'acodes' => $acode,
            'langpref' => $langpref,
            'fields' => $fields,
            'forward_compatible' => $forward_compatible
        );
        $results = OkapiServiceRunner::call('services/attrs/attributes',
            new OkapiInternalRequest($request->consumer, $request->token, $params));
        $result = $results[$acode];
        if ($result === null)
        {
            /* Note, this can happen only when $forward_compatible is false. */
            throw new InvalidParam('acode', "Unknown A-code.");
        }
        return Okapi::formatted_response($request, $result);
    }
}
