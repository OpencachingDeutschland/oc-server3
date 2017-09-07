<?php

# This method is the simplest of all. It just returns all cashes, in any order.
# Results might be limited only with the "standard filtering arguments",
# implemented in the OkapiSearchAssistant::get_common_search_params.
#
# Its existance is intentional - though a bit inpractical, it serves as a
# reference base for every other search method which might use "standard
# filters" (those defined in OkapiSearchAssistant::get_common_search_params).

namespace okapi\services\caches\search\all;

use okapi\Okapi;
use okapi\Request\OkapiRequest;
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
        $search_assistant = new SearchAssistant($request);
        $search_assistant->prepare_common_search_params();
        $result = $search_assistant->get_common_search_result();
        return Okapi::formatted_response($request, $result);
    }
}
