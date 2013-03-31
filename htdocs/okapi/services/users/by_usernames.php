<?php

namespace okapi\services\users\by_usernames;

use okapi\Okapi;
use okapi\Db;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\services\caches\search\SearchAssistant;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;

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
		$usernames = $request->get_parameter('usernames');
		if (!$usernames) throw new ParamMissing('usernames');
		$usernames = explode("|", $usernames);
		if (count($usernames) > 500)
			throw new InvalidParam('usernames', "Maximum allowed number of referenced users ".
				"is 500. You provided ".count($usernames)." usernames.");
		$fields = $request->get_parameter('fields');
		if (!$fields)
			throw new ParamMissing('fields');

		# There's no need to validate the fields parameter as the 'users'
		# method does this (it will raise a proper exception on invalid values).

		$rs = Db::query("
			select username, uuid
			from user
			where username in ('".implode("','", array_map('mysql_real_escape_string', $usernames))."')
		");
		$username2useruuid = array();
		while ($row = mysql_fetch_assoc($rs))
		{
			$username2useruuid[$row['username']] = $row['uuid'];
		}
		mysql_free_result($rs);

		# Retrieve data on given user_uuids.
		$id_results = OkapiServiceRunner::call('services/users/users', new OkapiInternalRequest(
			$request->consumer, $request->token, array('user_uuids' => implode("|", array_values($username2useruuid)),
			'fields' => $fields)));

		# Map user_uuids to usernames. Also check which usernames were not found
		# and mark them with null.
		$results = array();
		foreach ($usernames as $username)
		{
			if (!isset($username2useruuid[$username]))
				$results[$username] = null;
			else
				$results[$username] = $id_results[$username2useruuid[$username]];
		}

		return Okapi::formatted_response($request, $results);
	}
}
