<?php

namespace okapi\services\caches\shortcuts\search_and_retrieve;

use okapi\BadRequest;
use okapi\InvalidParam;
use okapi\Okapi;
use okapi\OkapiHttpResponse;
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
        # Check search method
        $search_method = $request->get_parameter('search_method');
        if (!$search_method)
            throw new ParamMissing('search_method');
        if (strpos($search_method, "services/caches/search/") !== 0)
            throw new InvalidParam('search_method', "Should begin with 'services/caches/search/'.");
        if (!OkapiServiceRunner::exists($search_method))
            throw new InvalidParam('search_method', "Method does not exist: '$search_method'");
        $search_params = $request->get_parameter('search_params');
        if (!$search_params)
            throw new ParamMissing('search_params');
        $search_params = json_decode($search_params, true);
        if (!is_array($search_params))
            throw new InvalidParam('search_params', "Should be a JSON-encoded dictionary");

        # Check retrieval method
        $retr_method = $request->get_parameter('retr_method');
        if (!$retr_method)
            throw new ParamMissing('retr_method');
        if (!OkapiServiceRunner::exists($retr_method))
            throw new InvalidParam('retr_method', "Method does not exist: '$retr_method'");
        $retr_params = $request->get_parameter('retr_params');
        if (!$retr_params)
            throw new ParamMissing('retr_params');
        $retr_params = json_decode($retr_params, true);
        if (!is_array($retr_params))
            throw new InvalidParam('retr_params', "Should be a JSON-encoded dictionary");

        self::map_values_to_strings($search_params);
        self::map_values_to_strings($retr_params);

        # Wrapped?
        $wrap = $request->get_parameter('wrap');
        if ($wrap == null) throw new ParamMissing('wrap');
        if (!in_array($wrap, array('true', 'false')))
            throw new InvalidParam('wrap');
        $wrap = ($wrap == 'true');

        # Run search method
        try
        {
            $search_result = OkapiServiceRunner::call($search_method, new OkapiInternalRequest(
                $request->consumer, $request->token, $search_params));
        }
        catch (BadRequest $e)
        {
            throw new InvalidParam('search_params', "Search method responded with the ".
                "following error message: ".$e->getMessage());
        }

        # Run retrieval method
        try
        {
            $retr_result = OkapiServiceRunner::call($retr_method, new OkapiInternalRequest(
                $request->consumer, $request->token, array_merge($retr_params,
                array('cache_codes' => implode("|", $search_result['results'])))));
        }
        catch (BadRequest $e)
        {
            throw new InvalidParam('retr_params', "Retrieval method responded with the ".
                "following error message: ".$e->getMessage());
        }

        if ($wrap)
        {
            # $retr_result might be a PHP object, but also might be a binary response
            # (e.g. a GPX file).
            if ($retr_result instanceof OkapiHttpResponse)
                $result = array('results' => $retr_result->get_body());
            else
                $result = array('results' => $retr_result);
            foreach ($search_result as $key => &$value_ref)
                if ($key != 'results')
                    $result[$key] = $value_ref;
            return Okapi::formatted_response($request, $result);
        }
        else
        {
            if ($retr_result instanceof OkapiHttpResponse)
                return $retr_result;
            else
                return Okapi::formatted_response($request, $retr_result);
        }
    }

    private static function map_values_to_strings(&$dict)
    {
        foreach (array_keys($dict) as $key)
        {
            $val = $dict[$key];
            if (is_numeric($val) || is_string($val))
                $dict[$key] = (string)$val;
            else
                throw new BadRequest("Invalid value format for key: ".$key);
        }
    }
}
