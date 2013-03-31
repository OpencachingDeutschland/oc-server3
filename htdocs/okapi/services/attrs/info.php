<?php

namespace okapi\services\attrs\info;

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
		# The list of attributes is periodically refreshed by contacting OKAPI
		# repository (the refreshing is done via a cronjob). This method
		# displays the cached version of the list.

		require_once 'attr_helper.inc.php';
		AttrHelper::refresh_if_stale();
		$results = array(
			'attributes' => AttrHelper::get_attrdict()
		);
		return Okapi::formatted_response($request, $results);
	}
}
