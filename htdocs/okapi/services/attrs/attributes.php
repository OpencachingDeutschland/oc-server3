<?php

namespace okapi\services\attrs\attributes;

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

	private static $valid_field_names = array(
		'acode', 'name', 'names', 'description', 'descriptions', 'gc_equivs',
		'is_locally_used', 'is_deprecated'
	);

	public static function call(OkapiRequest $request)
	{
		# Read the parameters.

		$acodes = $request->get_parameter('acodes');
		if ($acodes === null) throw new ParamMissing('acodes');
		$acodes = explode("|", $acodes);

		$langpref = $request->get_parameter('langpref');
		if (!$langpref) $langpref = "en";
		$langpref = explode("|", $langpref);

		$fields = $request->get_parameter('fields');
		if (!$fields) $fields = "name";
		$fields = explode("|", $fields);
		foreach ($fields as $field)
		{
			if (!in_array($field, self::$valid_field_names))
				throw new InvalidParam('fields', "'$field' is not a valid field code.");
		}

		$forward_compatible = $request->get_parameter('forward_compatible');
		if (!$forward_compatible) $forward_compatible = "true";
		if (!in_array($forward_compatible, array("true", "false")))
			throw new InvalidParam('forward_compatible');
		$forward_compatible = ($forward_compatible == "true");

		# Load the attributes (all of them).

		require_once 'attr_helper.inc.php';
		$attrdict = AttrHelper::get_attrdict();

		# For each A-code, check if it exists, filter its fields and add it
		# to the results.

		$results = array();
		foreach ($acodes as $acode)
		{
			/* Please note, that the $attr variable from the $attrdict dictionary
			 * below is NOT fully compatible with the interface of the "attribute"
			 * method. Some of $attr's fields are private and should not be exposed,
			 * other fields don't exist and have to be added dynamically! */

			if (isset($attrdict[$acode])) {
				$attr = $attrdict[$acode];
			} elseif ($forward_compatible) {
				$attr = AttrHelper::get_unknown_placeholder($acode);
			} else {
				$results[$acode] = null;
				continue;
			}

			# Fill langpref-specific fields.

			$attr['name'] = Okapi::pick_best_language($attr['names'], $langpref);
			$attr['description'] = Okapi::pick_best_language($attr['descriptions'], $langpref);

			# Fill all the other fields not kept in the (private) attrdict.

			$attr['is_locally_used'] = ($attr['internal_id'] !== null);

			# Filter the fields.

			$clean_attr = array();
			foreach ($fields as $field)
				$clean_attr[$field] = $attr[$field];

			# Add to results.

			$results[$acode] = $clean_attr;
		}

		return Okapi::formatted_response($request, $results);
	}
}
