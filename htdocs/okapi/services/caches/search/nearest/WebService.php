<?php

namespace okapi\services\caches\search\nearest;

use okapi\Db;
use okapi\InvalidParam;
use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\ParamMissing;
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
        # You may wonder, why there are no parameters like "bbox" or "center" in the
        # "search/all" method. This is *intentional* and should be kept this way.
        # Such parameters would fall in conflict with each other and - in result -
        # make the documentation very fuzzy. That's why they were intentionally
        # left out of the "search/all" method, and put in separate (individual) ones.
        # It's much easier to grasp their meaning this way.

        $tmp = $request->get_parameter('center');
        if (!$tmp)
            throw new ParamMissing('center');
        $parts = explode('|', $tmp);
        if (count($parts) != 2)
            throw new InvalidParam('center', "Expecting 2 pipe-separated parts, got ".count($parts).".");
        foreach ($parts as &$part_ref)
        {
            if (!preg_match("/^-?[0-9]+(\.?[0-9]*)$/", $part_ref))
                throw new InvalidParam('center', "'$part_ref' is not a valid float number.");
            $part_ref = floatval($part_ref);
        }
        list($center_lat, $center_lon) = $parts;
        if ($center_lat > 90 || $center_lat < -90)
            throw new InvalidParam('center', "Latitudes have to be within -90..90 range.");
        if ($center_lon > 180 || $center_lon < -180)
            throw new InvalidParam('center', "Longitudes have to be within -180..180 range.");

        #
        # In the method description, we promised to return caches ordered by the *rough*
        # distance from the center point. We'll use ORDER BY with a simplified distance
        # formula and combine it with the LIMIT clause to get the best results.
        #

        $search_assistant = new SearchAssistant($request);
        $search_assistant->prepare_common_search_params();
        $search_assistant->prepare_location_search_params();
        $distance_formula = Okapi::get_distance_sql(
            $center_lat, $center_lon,
            $search_assistant->get_latitude_expr(), $search_assistant->get_longitude_expr()
        );

        # 'radius' parameter is optional. If not given, we'll have to calculate the
        # distance for every cache in the database.

        $where_conds = array();
        $radius = null;
        if ($tmp = $request->get_parameter('radius'))
        {
            if (!preg_match("/^-?[0-9]+(\.?[0-9]*)$/", $tmp))
                throw new InvalidParam('radius', "'$tmp' is not a valid float number.");
            $radius = floatval($tmp);  # is given in kilometers
            if ($radius <= 0)
                throw new InvalidParam('radius', "Has to be a positive number.");

            # Apply a latitude-range prefilter if it looks promising.
            # See https://github.com/opencaching/okapi/issues/363 for more info.

            $optimization_radius = 100;  # in kilometers, optimized for Opencaching.de
            $km2degrees_upper_estimate_factor = 0.01;

            if ($radius <= $optimization_radius)
            {
                $radius_degrees = $radius * $km2degrees_upper_estimate_factor;
                $where_conds[] = "
                    caches.latitude >= '".Db::escape_string($center_lat - $radius_degrees)."'
                    and caches.latitude <= '".Db::escape_string($center_lat + $radius_degrees)."'
                    ";
            }

            $radius *= 1000;  # convert from kilometers to meters
            $where_conds[] = "$distance_formula <= '".Db::escape_string($radius)."'";
        }

        $search_params = $search_assistant->get_search_params();
        $search_params['where_conds'] = array_merge($where_conds, $search_params['where_conds']);
        $search_params['caches_indexhint'] = "use index (latitude)";
        $search_params['order_by'][] = $distance_formula; # not replaced; added to the end!
        $search_assistant->set_search_params($search_params);

        $result = $search_assistant->get_common_search_result();
        if ($radius == null)
        {
            # 'more' is meaningless in this case, we'll remove it.
            unset($result['more']);
        }

        return Okapi::formatted_response($request, $result);
    }
}
