<?php

namespace okapi\services\attrs\attribute_index;

use ArrayObject;
use okapi\Okapi;
use okapi\OkapiServiceRunner;
use okapi\Request\OkapiInternalRequest;
use okapi\Request\OkapiRequest;
use okapi\services\attrs\AttrHelper;


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

        $langpref = $request->get_parameter('langpref');
        if (!$langpref) $langpref = "en";

        $fields = $request->get_parameter('fields');
        if (!$fields) $fields = "name";

        $only_locally_used = $request->get_parameter('only_locally_used');
        if (!$only_locally_used) $only_locally_used = "false";
        $only_locally_used = ($only_locally_used == "true");

        # Get the list of attributes and filter the A-codes based on the
        # parameters.

        $attrdict = AttrHelper::get_attrdict();
        $acodes = array();
        foreach ($attrdict as $acode => &$attr_ref)
        {
            if ($only_locally_used && ($attr_ref['internal_id'] === null)) {
                /* Skip. */
                continue;
            }

            $acodes[] = $acode;
        }

        # Retrieve the attribute objects and return the results.

        if (count($acodes) > 0) {
            $params = array(
                'acodes' => implode("|", $acodes),
                'langpref' => $langpref,
                'fields' => $fields,
            );
            $results = OkapiServiceRunner::call('services/attrs/attributes',
                new OkapiInternalRequest($request->consumer, $request->token, $params));
        } else {
            $results = new ArrayObject();
        }
        return Okapi::formatted_response($request, $results);
    }
}
