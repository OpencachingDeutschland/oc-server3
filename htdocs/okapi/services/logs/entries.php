<?php

namespace okapi\services\logs\entries;

use Exception;
use okapi\Okapi;
use okapi\Db;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\Settings;
use okapi\services\caches\search\SearchAssistant;

class WebService
{
	public static function options()
	{
		return array(
			'min_auth_level' => 1
		);
	}

	private static $valid_field_names = array(
		'uuid', 'cache_code', 'date', 'user', 'type', 'was_recommended', 'comment',
		'internal_id',
	);

	public static function call(OkapiRequest $request)
	{
		$log_uuids = $request->get_parameter('log_uuids');
		if ($log_uuids === null) throw new ParamMissing('log_uuids');
		if ($log_uuids === "")
		{
			$log_uuids = array();
		}
		else
			$log_uuids = explode("|", $log_uuids);

		if ((count($log_uuids) > 500) && (!$request->skip_limits))
			throw new InvalidParam('log_uuids', "Maximum allowed number of referenced ".
				"log entries is 500. You provided ".count($log_uuids)." UUIDs.");
		if (count($log_uuids) != count(array_unique($log_uuids)))
			throw new InvalidParam('log_uuids', "Duplicate UUIDs detected (make sure each UUID is referenced only once).");
		$fields = $request->get_parameter('fields');
		if (!$fields) $fields = "date|user|type|comment";
		$fields = explode("|", $fields);
		foreach ($fields as $field)
			if (!in_array($field, self::$valid_field_names))
				throw new InvalidParam('fields', "'$field' is not a valid field code.");

		$rs = Db::query("
			select
				cl.id, c.wp_oc as cache_code, cl.uuid, cl.type,
				unix_timestamp(cl.date) as date, cl.text,
				u.uuid as user_uuid, u.username, u.user_id,
				if(cr.user_id is null, 0, 1) as was_recommended
			from
				(cache_logs cl,
				user u,
				caches c)
				left join cache_rating cr
					on cr.user_id = u.user_id
					and cr.cache_id = c.cache_id
			where
				cl.uuid in ('".implode("','", array_map('mysql_real_escape_string', $log_uuids))."')
				and ".((Settings::get('OC_BRANCH') == 'oc.pl') ? "cl.deleted = 0" : "true")."
				and cl.user_id = u.user_id
				and c.cache_id = cl.cache_id
				and c.status in (1,2,3)
		");
		$results = array();
		while ($row = mysql_fetch_assoc($rs))
		{
			$results[$row['uuid']] = array(
				'uuid' => $row['uuid'],
				'cache_code' => $row['cache_code'],
				'date' => date('c', $row['date']),
				'user' => array(
					'uuid' => $row['user_uuid'],
					'username' => $row['username'],
					'profile_url' => Settings::get('SITE_URL')."viewprofile.php?userid=".$row['user_id'],
				),
				'type' => Okapi::logtypeid2name($row['type']),
				'was_recommended' => $row['was_recommended'] ? true : false,
				'comment' => $row['text'],
				'internal_id' => $row['id'],
			);
		}
		mysql_free_result($rs);

		# Check which UUIDs were not found and mark them with null.

		foreach ($log_uuids as $log_uuid)
			if (!isset($results[$log_uuid]))
				$results[$log_uuid] = null;

		# Remove unwanted fields.

		foreach (self::$valid_field_names as $field)
			if (!in_array($field, $fields))
				foreach ($results as &$result_ref)
					unset($result_ref[$field]);

		# Order the results in the same order as the input codes were given.

		$ordered_results = array();
		foreach ($log_uuids as $log_uuid)
			$ordered_results[$log_uuid] = $results[$log_uuid];

		return Okapi::formatted_response($request, $ordered_results);
	}
}
