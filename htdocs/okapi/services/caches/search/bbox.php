<?php

namespace okapi\services\caches\search\bbox;

require_once('searching.inc.php');

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
		# You may wonder, why there are no parameters like "bbox" or "center" in the
		# "search/all" method. This is *intentional* and should be kept this way.
		# Such parameters would fall in conflict with each other and - in result -
		# make the documentation very fuzzy. That's why they were intentionally
		# left out of the "search/all" method, and put in separate (individual) ones.
		# It's much easier to grasp their meaning this way.
		
		$tmp = $request->get_parameter('bbox');
		if (!$tmp)
			throw new ParamMissing('bbox');
		$parts = explode('|', $tmp);
		if (count($parts) != 4)
			throw new InvalidParam('bbox', "Expecting 4 pipe-separated parts, got ".count($parts).".");
		foreach ($parts as &$part_ref)
		{
			if (!preg_match("/^-?[0-9]+(\.?[0-9]*)$/", $part_ref))
				throw new InvalidParam('bbox', "'$part_ref' is not a valid float number.");
			$part_ref = floatval($part_ref);
		}
		list($bbsouth, $bbwest, $bbnorth, $bbeast) = $parts;
		if ($bbnorth <= $bbsouth)
			throw new InvalidParam('bbox', "Northern edge must be situated to the north of the southern edge.");
		if ($bbeast == $bbwest)
			throw new InvalidParam('bbox', "Eastern edge longitude is the same as the western one.");
		if ($bbnorth > 90 || $bbnorth < -90 || $bbsouth > 90 || $bbsouth < -90)
			throw new InvalidParam('bbox', "Latitudes have to be within -90..90 range.");
		if ($bbeast > 180 || $bbeast < -180 || $bbwest > 180 || $bbwest < -180)
			throw new InvalidParam('bbox', "Longitudes have to be within -180..180 range.");
		
		# Construct SQL conditions for the specified bounding box.
		
		$where_conds = array();
		$where_conds[] = "caches.latitude between '".mysql_real_escape_string($bbsouth)."' and '".mysql_real_escape_string($bbnorth)."'";
		if ($bbeast > $bbwest)
		{
			# Easy one.
			$where_conds[] = "caches.longitude between '".mysql_real_escape_string($bbwest)."' and '".mysql_real_escape_string($bbeast)."'";
		}
		else
		{
			# We'll have to assume that this box goes through the 180-degree meridian.
			# For example, $bbwest = 179 and $bbeast = -179.
			$where_conds[] = "(caches.longitude > '".mysql_real_escape_string($bbwest)."' or caches.longitude < '".mysql_real_escape_string($bbeast)."')";
		}
		
		#
		# In the method description, we promised to return caches ordered by the *rough*
		# distance from the center of the bounding box. We'll use ORDER BY with a simplified
		# distance formula and combine it with the LIMIT clause to get the best results.
		#
		
		$center_lat = ($bbsouth + $bbnorth) / 2.0;
		$center_lon = ($bbwest + $bbeast) / 2.0; 
		
		$search_params = SearchAssistant::get_common_search_params($request);
		$search_params['where_conds'] = array_merge($where_conds, $search_params['where_conds']);
		$search_params['order_by'][] = Okapi::get_distance_sql($center_lat, $center_lon,
			"caches.latitude", "caches.longitude"); # not replaced; added to the end!
		
		$result = SearchAssistant::get_common_search_result($search_params);
		
		return Okapi::formatted_response($request, $result);
	}
}
