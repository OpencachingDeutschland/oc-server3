<?php

namespace okapi\services\apiref\issue;

use Exception;
use ErrorException;
use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\BadRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use okapi\Cache;

class WebService
{
	public static function options()
	{
		return array(
			'min_auth_level' => 0
		);
	}

	public static function call(OkapiRequest $request)
	{
		$issue_id = $request->get_parameter('issue_id');
		if (!$issue_id)
			throw new ParamMissing('issue_id');
		if ((!preg_match("/^[0-9]+$/", $issue_id)) || (strlen($issue_id) > 6))
			throw new InvalidParam('issue_id');

		# In October 2013, Google Code feed at:
		# http://code.google.com/feeds/issues/p/opencaching-api/issues/$issue_id/comments/full
		# stopped working. We are forced to respond with a simple placeholder.
		
		$result = array(
			'id' => $issue_id + 0,
			'last_updated' => null,
			'title' => null,
			'url' => "https://code.google.com/p/opencaching-api/issues/detail?id=".$issue_id,
			'comment_count' => null
		);
		return Okapi::formatted_response($request, $result);
	}
}
