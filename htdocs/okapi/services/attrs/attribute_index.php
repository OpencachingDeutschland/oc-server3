<?php

namespace okapi\services\attrs\attribute_index;

use Exception;
use ErrorException;
use okapi\Okapi;
use okapi\Settings;
use okapi\Cache;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
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

		$include_deprecated = $request->get_parameter('include_deprecated');
		if (!$include_deprecated) $include_deprecated = "true";
		$include_deprecated = ($include_deprecated == "true");

		$only_locally_used = $request->get_parameter('only_locally_used');
		if (!$only_locally_used) $only_locally_used = "false";
		$only_locally_used = ($only_locally_used == "true");

		# Get the list of attributes and filter the A-codes based on the
		# parameters.

		require_once 'attr_helper.inc.php';
		$attrdict = AttrHelper::get_attrdict();
		$acodes = array();
		foreach ($attrdict as $acode => &$attr_ref)
		{
			if ((!$include_deprecated) && ($attr_ref['is_deprecated'])) {
				/* Skip. */
				continue;
			}

			if ($only_locally_used && ($attr_ref['internal_id'] === null)) {
				/* Skip. */
				continue;
			}

			$acodes[] = $acode;
		}

		# Retrieve the attribute objects and return the results.

		$params = array(
			'acodes' => implode("|", $acodes),
			'langpref' => $langpref,
			'fields' => $fields,
		);
		$results = OkapiServiceRunner::call('services/attrs/attributes',
			new OkapiInternalRequest($request->consumer, $request->token, $params));
		return Okapi::formatted_response($request, $results);
	}
}
