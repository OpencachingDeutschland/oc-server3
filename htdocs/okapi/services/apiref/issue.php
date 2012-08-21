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
		
		$cache_key = "apiref/issue#".$issue_id;
		$result = Cache::get($cache_key);
		if ($result == null)
		{
			# Download list of comments from Google Code Issue Tracker.
			
			try
			{
				$opts = array(
					'http' => array(
						'method' => "GET",
						'timeout' => 2.0
					)
				);
				$context = stream_context_create($opts);
				$xml = file_get_contents("http://code.google.com/feeds/issues/p/opencaching-api/issues/$issue_id/comments/full",
					false, $context);
			}
			catch (ErrorException $e)
			{
				throw new BadRequest("Sorry, we could not retrieve issue stats from the Google Code site. ".
					"This is probably due to a temporary connection problem. Try again later or contact ".
					"us if this seems permanent.");
			}
			
			$doc = simplexml_load_string($xml);
			$result = array(
				'id' => $issue_id + 0,
				'last_updated' => (string)$doc->updated,
				'title' => (string)$doc->title,
				'url' => (string)$doc->link[0]['href'],
				'comment_count' => $doc->entry->count()
			);
			
			# On one hand, we want newly added comments to show up quickly.
			# On the other, we don't want OKAPI to contantly query Google Code.
			# It's difficult to choose a correct timeout for this...
			
			Cache::set($cache_key, $result, 3600);
		}
		return Okapi::formatted_response($request, $result);
	}
}
