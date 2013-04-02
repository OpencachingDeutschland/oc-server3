<?php

namespace okapi\services\caches\geocaches;

use Exception;
use okapi\Okapi;
use okapi\Db;
use okapi\Settings;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\BadRequest;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\OkapiAccessToken;
use okapi\services\caches\search\SearchAssistant;

class WebService
{
	public static function options()
	{
		return array(
			'min_auth_level' => 1
		);
	}

	private static $valid_field_names = array('code', 'name', 'names', 'location', 'type',
		'status', 'url', 'owner', 'distance', 'bearing', 'bearing2', 'bearing3', 'is_found',
		'is_not_found', 'founds', 'notfounds', 'size', 'size2', 'oxsize', 'difficulty', 'terrain',
		'rating', 'rating_votes', 'recommendations', 'req_passwd', 'description',
		'descriptions', 'hint', 'hints', 'images', 'attrnames', 'latest_logs',
		'my_notes', 'trackables_count', 'trackables', 'alt_wpts', 'last_found',
		'last_modified', 'date_created', 'date_hidden', 'internal_id', 'is_watched',
		'is_ignored', 'willattends', 'country', 'state', 'preview_image', 'searchtime', 'waylength');

	public static function call(OkapiRequest $request)
	{
		$cache_codes = $request->get_parameter('cache_codes');
		if ($cache_codes === null) throw new ParamMissing('cache_codes');
		if ($cache_codes === "")
		{
			# Issue 106 requires us to allow empty list of cache codes to be passed into this method.
			# All of the queries below have to be ready for $cache_codes to be empty!
			$cache_codes = array();
		}
		else
			$cache_codes = explode("|", $cache_codes);

		if ((count($cache_codes) > 500) && (!$request->skip_limits))
			throw new InvalidParam('cache_codes', "Maximum allowed number of referenced ".
				"caches is 500. You provided ".count($cache_codes)." cache codes.");
		if (count($cache_codes) != count(array_unique($cache_codes)))
			throw new InvalidParam('cache_codes', "Duplicate codes detected (make sure each cache is referenced only once).");

		$langpref = $request->get_parameter('langpref');
		if (!$langpref) $langpref = "en";
		$langpref = explode("|", $langpref);

		$fields = $request->get_parameter('fields');
		if (!$fields) $fields = "code|name|location|type|status";
		$fields = explode("|", $fields);
		foreach ($fields as $field)
			if (!in_array($field, self::$valid_field_names))
				throw new InvalidParam('fields', "'$field' is not a valid field code.");

		# Currently, the "owner" field needs to be included whenever the "description" field is.
		# That's a little ugly. Grep for "issue 178" below for more insight on this.
		if (
			(
				in_array('description', $fields) || in_array('descriptions', $fields)
				|| in_array('hint', $fields) || in_array('hints', $fields)
			)
			&& !in_array('owner', $fields)
		)
			$fields[] = "owner";

		$log_fields = $request->get_parameter('log_fields');
		if (!$log_fields) $log_fields = "uuid|date|user|type|comment";  // validation is done on call

		$user_uuid = $request->get_parameter('user_uuid');
		if ($user_uuid != null)
		{
			$user_id = Db::select_value("select user_id from user where uuid='".mysql_real_escape_string($user_uuid)."'");
			if ($user_id == null)
				throw new InvalidParam('user_uuid', "User not found.");
			if (($request->token != null) && ($this->token->user_id != $user_id))
				throw new InvalidParam('user_uuid', "User does not match the Access Token used.");
		}
		elseif (($user_uuid == null) && ($request->token != null))
			$user_id = $request->token->user_id;
		else
			$user_id = null;

		$lpc = $request->get_parameter('lpc');
		if ($lpc === null) $lpc = 10;
		if ($lpc == 'all')
			$lpc = null;
		else
		{
			if (!is_numeric($lpc))
				throw new InvalidParam('lpc', "Invalid number: '$lpc'");
			$lpc = intval($lpc);
			if ($lpc < 0)
				throw new InvalidParam('lpc', "Must be a positive value.");
		}

		if (in_array('distance', $fields) || in_array('bearing', $fields) || in_array('bearing2', $fields)
			|| in_array('bearing3', $fields))
		{
			$tmp = $request->get_parameter('my_location');
			if (!$tmp)
				throw new BadRequest("When using 'distance' or 'bearing' fields, you have to supply 'my_location' parameter.");
			$parts = explode('|', $tmp);
			if (count($parts) != 2)
				throw new InvalidParam('my_location', "Expecting 2 pipe-separated parts, got ".count($parts).".");
			foreach ($parts as &$part_ref)
			{
				if (!preg_match("/^-?[0-9]+(\.?[0-9]*)$/", $part_ref))
					throw new InvalidParam('my_location', "'$part_ref' is not a valid float number.");
				$part_ref = floatval($part_ref);
			}
			list($center_lat, $center_lon) = $parts;
			if ($center_lat > 90 || $center_lat < -90)
				throw new InvalidParam('current_position', "Latitudes have to be within -90..90 range.");
			if ($center_lon > 180 || $center_lon < -180)
				throw new InvalidParam('current_position', "Longitudes have to be within -180..180 range.");
		}

		if (Settings::get('OC_BRANCH') == 'oc.de')
		{
			# DE branch:
			# - Caches do not have ratings.
			# - Total numbers of founds and notfounds are kept in the "stat_caches" table.
			# - search_time and way_length cannot be null, 0 = not specified

			$rs = Db::query("
				select
					c.cache_id, c.name, c.longitude, c.latitude, c.listing_last_modified as last_modified,
					c.date_created, c.type, c.status, c.date_hidden, c.size, c.difficulty,
					c.terrain, c.wp_oc, c.logpw, c.user_id,
					if(c.search_time=0, null, c.search_time) as searchtime,
					if(c.way_length=0, null, c.way_length) as waylength,

					ifnull(sc.toprating, 0) as topratings,
					ifnull(sc.found, 0) as founds,
					ifnull(sc.notfound, 0) as notfounds,
					sc.last_found,
					0 as votes, 0 as score
					-- SEE ALSO OC.PL BRANCH BELOW
				from
					caches c
					left join stat_caches as sc on c.cache_id = sc.cache_id
				where
					wp_oc in ('".implode("','", array_map('mysql_real_escape_string', $cache_codes))."')
					and status in (1,2,3)
			");
		}
		elseif (Settings::get('OC_BRANCH') == 'oc.pl')
		{
			# PL branch:
			# - Caches have ratings.
			# - Total numbers of found and notfounds are kept in the "caches" table.
			# - search_time and way_length can be null, 0 or null = not specified

			$rs = Db::query("
				select
					c.cache_id, c.name, c.longitude, c.latitude, c.last_modified,
					c.date_created, c.type, c.status, c.date_hidden, c.size, c.difficulty,
					c.terrain, c.wp_oc, c.logpw, c.user_id,
					if(c.search_time=0, null, c.search_time) as searchtime,
					if(c.way_length=0, null, c.way_length) as waylength,

					c.topratings,
					c.founds,
					c.notfounds,
					c.last_found,
					c.votes, c.score
					-- SEE ALSO OC.DE BRANCH ABOVE
				from
					caches c
				where
					wp_oc in ('".implode("','", array_map('mysql_real_escape_string', $cache_codes))."')
					and c.status in (1,2,3)
			");
		}

		$results = array();
		$cacheid2wptcode = array();
		$owner_ids = array();
		while ($row = mysql_fetch_assoc($rs))
		{
			$entry = array();
			$cacheid2wptcode[$row['cache_id']] = $row['wp_oc'];
			foreach ($fields as $field)
			{
				switch ($field)
				{
					case 'code': $entry['code'] = $row['wp_oc']; break;
					case 'name': $entry['name'] = $row['name']; break;
					case 'names': $entry['names'] = array(Settings::get('SITELANG') => $row['name']); break; // for the future
					case 'location': $entry['location'] = round($row['latitude'], 6)."|".round($row['longitude'], 6); break;
					case 'type': $entry['type'] = Okapi::cache_type_id2name($row['type']); break;
					case 'status': $entry['status'] = Okapi::cache_status_id2name($row['status']); break;
					case 'url': $entry['url'] = Settings::get('SITE_URL')."viewcache.php?wp=".$row['wp_oc']; break;
					case 'owner':
						$owner_ids[$row['wp_oc']] = $row['user_id'];
						/* continued later */
						break;
					case 'distance':
						$entry['distance'] = (int)Okapi::get_distance($center_lat, $center_lon, $row['latitude'], $row['longitude']);
						break;
					case 'bearing':
						$tmp = Okapi::get_bearing($center_lat, $center_lon, $row['latitude'], $row['longitude']);
						$entry['bearing'] = ($tmp !== null) ? ((int)(10*$tmp)) / 10.0 : null;
						break;
					case 'bearing2':
						$tmp = Okapi::get_bearing($center_lat, $center_lon, $row['latitude'], $row['longitude']);
						$entry['bearing2'] = Okapi::bearing_as_two_letters($tmp);
						break;
					case 'bearing3':
						$tmp = Okapi::get_bearing($center_lat, $center_lon, $row['latitude'], $row['longitude']);
						$entry['bearing3'] = Okapi::bearing_as_three_letters($tmp);
						break;
					case 'is_found': /* handled separately */ break;
					case 'is_not_found': /* handled separately */ break;
					case 'is_watched': /* handled separately */ break;
					case 'is_ignored': /* handled separately */ break;
					case 'founds': $entry['founds'] = $row['founds'] + 0; break;
					case 'notfounds':
						if ($row['type'] != 6) {  # non-event
							$entry['notfounds'] = $row['notfounds'] + 0;
						} else {  # event
							$entry['notfounds'] = 0;
						}
						break;
					case 'willattends':
						# OCPL stats count "Will attend" log entries as "notfounds"
						# (just another pecularity regarding "event caches").
						# I am not sure about OCDE branch though...
						if ($row['type'] == 6) {  # event
							$entry['willattends'] = $row['notfounds'] + 0;
						} else {  # non-event
							$entry['willattends'] = 0;
						}
						break;
					case 'size':
						# Deprecated. Leave it for backward-compatibility. See issue 155.
						switch (Okapi::cache_sizeid_to_size2($row['size']))
						{
							case 'none': $entry['size'] = null; break;
							case 'nano': $entry['size'] = 1.0; break;  # same as micro
							case 'micro': $entry['size'] = 1.0; break;
							case 'small': $entry['size'] = 2.0; break;
							case 'regular': $entry['size'] = 3.0; break;
							case 'large': $entry['size'] = 4.0; break;
							case 'xlarge': $entry['size'] = 5.0; break;
							case 'other': $entry['size'] = null; break;  # same as none
							default: throw new Exception();
						}
						break;
					case 'size2': $entry['size2'] = Okapi::cache_sizeid_to_size2($row['size']); break;
					case 'oxsize': $entry['oxsize'] = Okapi::cache_size2_to_oxsize(Okapi::cache_sizeid_to_size2($row['size'])); break;
					case 'difficulty': $entry['difficulty'] = round($row['difficulty'] / 2.0, 1); break;
					case 'terrain': $entry['terrain'] = round($row['terrain'] / 2.0, 1); break;
					case 'searchtime':
						# search time is entered in hours:minutes and converted to decimal hours,
						# which can produce lots of unneeded decimal places; 2 of them are sufficient here
						$entry['searchtime'] = $row['searchtime'] === null ? null : round($row['searchtime'],2); break;
						break;
					case 'waylength':
						# way length is entered in km as decimal fraction, but number conversions can
						# create fake digits which should be stripped; meter precision is sufficient here
						$entry['waylength'] = $row['waylength'] === null ? null : round($row['waylength'],3); break;
						break;
					case 'rating':
						if ($row['votes'] < 3) $entry['rating'] = null;
						elseif ($row['score'] >= 2.2) $entry['rating'] = 5.0;
						elseif ($row['score'] >= 1.4) $entry['rating'] = 4.0;
						elseif ($row['score'] >= 0.1) $entry['rating'] = 3.0;
						elseif ($row['score'] >= -1.0) $entry['rating'] = 2.0;
						else $entry['rating'] = 1.0;
						break;
					case 'rating_votes': $entry['rating_votes'] = $row['votes'] + 0; break;
					case 'recommendations': $entry['recommendations'] = $row['topratings'] + 0; break;
					case 'req_passwd': $entry['req_passwd'] = $row['logpw'] ? true : false; break;
					case 'description': /* handled separately */ break;
					case 'descriptions': /* handled separately */ break;
					case 'hint': /* handled separately */ break;
					case 'hints': /* handled separately */ break;
					case 'images': /* handled separately */ break;
					case 'preview_image': /* handled separately */ break;
					case 'attrnames': /* handled separately */ break;
					case 'latest_logs': /* handled separately */ break;
					case 'my_notes': /* handles separately */ break;
					case 'trackables_count': /* handled separately */ break;
					case 'trackables': /* handled separately */ break;
					case 'alt_wpts': /* handled separately */ break;
					case 'country': /* handled separately */ break;
					case 'state': /* handled separately */ break;
					case 'last_found': $entry['last_found'] = ($row['last_found'] > '1980') ? date('c', strtotime($row['last_found'])) : null; break;
					case 'last_modified': $entry['last_modified'] = date('c', strtotime($row['last_modified'])); break;
					case 'date_created': $entry['date_created'] = date('c', strtotime($row['date_created'])); break;
					case 'date_hidden': $entry['date_hidden'] = date('c', strtotime($row['date_hidden'])); break;
					case 'internal_id': $entry['internal_id'] = $row['cache_id']; break;
					default: throw new Exception("Missing field case: ".$field);
				}
			}
			$results[$row['wp_oc']] = $entry;
		}
		mysql_free_result($rs);

		# owner

		if (in_array('owner', $fields) && (count($results) > 0))
		{
			$rs = Db::query("
				select user_id, uuid, username
				from user
				where user_id in ('".implode("','", array_map('mysql_real_escape_string', array_values($owner_ids)))."')
			");
			$tmp = array();
			while ($row = mysql_fetch_assoc($rs))
				$tmp[$row['user_id']] = $row;
			foreach ($results as $cache_code => &$result_ref)
			{
				$row = $tmp[$owner_ids[$cache_code]];
				$result_ref['owner'] = array(
					'uuid' => $row['uuid'],
					'username' => $row['username'],
					'profile_url' => Settings::get('SITE_URL')."viewprofile.php?userid=".$row['user_id']
				);
			}
		}

		# is_found

		if (in_array('is_found', $fields))
		{
			if ($user_id == null)
				throw new BadRequest("Either 'user_uuid' parameter OR Level 3 Authentication is required to access 'is_found' field.");
			$tmp = Db::select_column("
				select c.wp_oc
				from
					caches c,
					cache_logs cl
				where
					c.cache_id = cl.cache_id
					and cl.type in (
						'".mysql_real_escape_string(Okapi::logtypename2id("Found it"))."',
						'".mysql_real_escape_string(Okapi::logtypename2id("Attended"))."'
					)
					and cl.user_id = '".mysql_real_escape_string($user_id)."'
					".((Settings::get('OC_BRANCH') == 'oc.pl') ? "and cl.deleted = 0" : "")."
			");
			$tmp2 = array();
			foreach ($tmp as $cache_code)
				$tmp2[$cache_code] = true;
			foreach ($results as $cache_code => &$result_ref)
				$result_ref['is_found'] = isset($tmp2[$cache_code]);
		}

		# is_not_found

		if (in_array('is_not_found', $fields))
		{
			if ($user_id == null)
				throw new BadRequest("Either 'user_uuid' parameter OR Level 3 Authentication is required to access 'is_not_found' field.");
			$tmp = Db::select_column("
				select c.wp_oc
				from
					caches c,
					cache_logs cl
				where
					c.cache_id = cl.cache_id
					and cl.type = '".mysql_real_escape_string(Okapi::logtypename2id("Didn't find it"))."'
					and cl.user_id = '".mysql_real_escape_string($user_id)."'
					".((Settings::get('OC_BRANCH') == 'oc.pl') ? "and cl.deleted = 0" : "")."
			");
			$tmp2 = array();
			foreach ($tmp as $cache_code)
				$tmp2[$cache_code] = true;
			foreach ($results as $cache_code => &$result_ref)
				$result_ref['is_not_found'] = isset($tmp2[$cache_code]);
		}

		# is_watched

		if (in_array('is_watched', $fields))
		{
			if ($request->token == null)
				throw new BadRequest("Level 3 Authentication is required to access 'is_watched' field.");
			$tmp = Db::select_column("
				select c.wp_oc
				from
					caches c,
					cache_watches cw
				where
					c.cache_id = cw.cache_id
					and cw.user_id = '".mysql_real_escape_string($request->token->user_id)."'
			");
			$tmp2 = array();
			foreach ($tmp as $cache_code)
				$tmp2[$cache_code] = true;
			foreach ($results as $cache_code => &$result_ref)
				$result_ref['is_watched'] = isset($tmp2[$cache_code]);
		}

		# is_ignored

		if (in_array('is_ignored', $fields))
		{
			if ($request->token == null)
				throw new BadRequest("Level 3 Authentication is required to access 'is_ignored' field.");
			$tmp = Db::select_column("
				select c.wp_oc
				from
					caches c,
					cache_ignore ci
				where
					c.cache_id = ci.cache_id
					and ci.user_id = '".mysql_real_escape_string($request->token->user_id)."'
			");
			$tmp2 = array();
			foreach ($tmp as $cache_code)
				$tmp2[$cache_code] = true;
			foreach ($results as $cache_code => &$result_ref)
				$result_ref['is_ignored'] = isset($tmp2[$cache_code]);
		}

		# Descriptions and hints.

		if (in_array('description', $fields) || in_array('descriptions', $fields)
			|| in_array('hint', $fields) || in_array('hints', $fields))
		{
			# At first, we will fill all those 4 fields, even if user requested just one
			# of them. We will chop off the remaining three at the end.

			foreach ($results as &$result_ref)
				$result_ref['descriptions'] = array();
			foreach ($results as &$result_ref)
				$result_ref['hints'] = array();

			# Get cache descriptions and hints.

			$rs = Db::query("
				select cache_id, language, `desc`, hint
				from cache_desc
				where cache_id in ('".implode("','", array_map('mysql_real_escape_string', array_keys($cacheid2wptcode)))."')
			");
			while ($row = mysql_fetch_assoc($rs))
			{
				$cache_code = $cacheid2wptcode[$row['cache_id']];
				// strtolower - ISO 639-1 codes are lowercase
				if ($row['desc'])
				{
					/* Regarding the attribution note - please note, that the "owner" field
					 * is automatically included, whenever the cache description is included.
					 * This is because we may need it for the attribution note - see issue 178. */

					$results[$cache_code]['descriptions'][strtolower($row['language'])] = (
						$row['desc']."\n".
						self::get_cache_attribution_note(
							$row['cache_id'], strtolower($row['language']), $langpref,
							$results[$cache_code]['owner']
						)
					);
				}
				if ($row['hint'])
					$results[$cache_code]['hints'][strtolower($row['language'])] = $row['hint'];
			}
			foreach ($results as &$result_ref)
			{
				$result_ref['description'] = Okapi::pick_best_language($result_ref['descriptions'], $langpref);
				$result_ref['hint'] = Okapi::pick_best_language($result_ref['hints'], $langpref);
			}

			# Remove unwanted fields.

			foreach (array('description', 'descriptions', 'hint', 'hints') as $field)
				if (!in_array($field, $fields))
					foreach ($results as &$result_ref)
						unset($result_ref[$field]);
		}

		# Images.

		if (in_array('images', $fields) || in_array('preview_image', $fields))
		{
			if (in_array('images', $fields))
				foreach ($results as &$result_ref)
					$result_ref['images'] = array();
			if (in_array('preview_image', $fields))
				foreach ($results as &$result_ref)
					$result_ref['preview_image'] = null;

			if (Db::field_exists('pictures', 'mappreview'))
				$preview_field = "mappreview";
			else
				$preview_field = "0";
			$rs = Db::query("
				select object_id, uuid, url, title, spoiler, ".$preview_field." as preview
				from pictures
				where
					object_id in ('".implode("','", array_map('mysql_real_escape_string', array_keys($cacheid2wptcode)))."')
					and display = 1
					and object_type = 2
					and unknown_format = 0
				order by object_id, date_created
			");
			$prev_cache_code = null;
			while ($row = mysql_fetch_assoc($rs))
			{
				$cache_code = $cacheid2wptcode[$row['object_id']];
				if ($cache_code != $prev_cache_code)
				{
					# Group images together. Images must have unique captions within one cache.
					self::reset_unique_captions();
					$prev_cache_code = $cache_code;
				}
				$image = array(
					'uuid' => $row['uuid'],
					'url' => $row['url'],
					'thumb_url' => Settings::get('SITE_URL') . 'thumbs.php?uuid=' . $row['uuid'],
					'caption' => $row['title'],
					'unique_caption' => self::get_unique_caption($row['title']),
					'is_spoiler' => ($row['spoiler'] ? true : false),
				);
				if (in_array('images', $fields))
					$results[$cache_code]['images'][] = $image;
				if ($row['preview'] != 0 && in_array('preview_image', $fields))
					$results[$cache_code]['preview_image'] = $image;
			}
		}

		# Attrnames

		if (in_array('attrnames', $fields))
		{
			foreach ($results as &$result_ref)
				$result_ref['attrnames'] = array();

			# ALL attribute names are loaded into memory here. Assuming there are
			# not so many of them, this will be fast enough. Possible optimalization:
			# Let mysql do the matching.

			$dict = Okapi::get_all_atribute_names();
			$rs = Db::query("
				select cache_id, attrib_id
				from caches_attributes
				where cache_id in ('".implode("','", array_map('mysql_real_escape_string', array_keys($cacheid2wptcode)))."')
			");
			while ($row = mysql_fetch_assoc($rs))
			{
				$cache_code = $cacheid2wptcode[$row['cache_id']];
				if (isset($dict[$row['attrib_id']]))
				{
					# The "isset" condition was added because there were some attrib_ids in caches_attributes
					# which WERE NOT in cache_attrib. http://code.google.com/p/opencaching-api/issues/detail?id=77
					$results[$cache_code]['attrnames'][] = Okapi::pick_best_language($dict[$row['attrib_id']], $langpref);
				}
			}
		}

		# Latest log entries.

		if (in_array('latest_logs', $fields))
		{
			foreach ($results as &$result_ref)
				$result_ref['latest_logs'] = array();

			# Get all log IDs with dates. Sort in groups. Filter out latest ones. This is the fastest
			# technique I could think of...

			$rs = Db::query("
				select cache_id, uuid, date
				from cache_logs
				where
					cache_id in ('".implode("','", array_map('mysql_real_escape_string', array_keys($cacheid2wptcode)))."')
					and ".((Settings::get('OC_BRANCH') == 'oc.pl') ? "deleted = 0" : "true")."
				order by cache_id, date desc
			");
			$loguuids = array();
			$log2cache_map = array();
			if ($lpc !== null)
			{
				# User wants some of the latest logs.
				$tmp = array();
				while ($row = mysql_fetch_assoc($rs))
					$tmp[$row['cache_id']][] = $row;
				foreach ($tmp as $cache_key => &$rowslist_ref)
				{
					usort($rowslist_ref, function($rowa, $rowb) {
						# (reverse order by date)
						return ($rowa['date'] < $rowb['date']) ? 1 : (($rowa['date'] == $rowb['date']) ? 0 : -1);
					});
					for ($i = 0; $i < min(count($rowslist_ref), $lpc); $i++)
					{
						$loguuids[] = $rowslist_ref[$i]['uuid'];
						$log2cache_map[$rowslist_ref[$i]['uuid']] = $cacheid2wptcode[$rowslist_ref[$i]['cache_id']];
					}
				}
			}
			else
			{
				# User wants ALL logs.
				while ($row = mysql_fetch_assoc($rs))
				{
					$loguuids[] = $row['uuid'];
					$log2cache_map[$row['uuid']] = $cacheid2wptcode[$row['cache_id']];
				}
			}

			# We need to retrieve logs/entry for each of the $logids. We do this in groups
			# (there is a limit for log uuids passed to logs/entries method).

			try
			{
				foreach (Okapi::make_groups($loguuids, 500) as $subset)
				{
					$entries = OkapiServiceRunner::call(
						"services/logs/entries",
						new OkapiInternalRequest(
							$request->consumer, $request->token, array(
								'log_uuids' => implode("|", $subset),
								'fields' => $log_fields
							)
						)
					);
					foreach ($subset as $log_uuid)
					{
						if ($entries[$log_uuid])
							$results[$log2cache_map[$log_uuid]]['latest_logs'][] = $entries[$log_uuid];
					}
				}
			}
			catch (Exception $e)
			{
				if (($e instanceof InvalidParam) && ($e->paramName == 'fields'))
				{
					throw new InvalidParam('log_fields', $e->whats_wrong_about_it);
				}
				else
				{
					/* Something is wrong with OUR code. */
					throw new Exception($e);
				}
			}
		}

		# My notes

		if (in_array('my_notes', $fields))
		{
			if ($request->token == null)
				throw new BadRequest("Level 3 Authentication is required to access 'my_notes' field.");
			foreach ($results as &$result_ref)
				$result_ref['my_notes'] = null;
			if (Settings::get('OC_BRANCH') == 'oc.pl')
			{
				# OCPL uses cache_notes table to store notes.

				$rs = Db::query("
					select cache_id, max(date) as date, group_concat(`desc`) as `desc`
					from cache_notes
					where
						cache_id in ('".implode("','", array_map('mysql_real_escape_string', array_keys($cacheid2wptcode)))."')
						and user_id = '".mysql_real_escape_string($request->token->user_id)."'
					group by cache_id
				");
			}
			else
			{
				# OCDE uses coordinates table (with type == 2) to store notes (this is somewhat weird).

				$rs = Db::query("
					select cache_id, null as date, group_concat(description) as `desc`
					from coordinates
					where
						type = 2  -- personal note
						and cache_id in ('".implode("','", array_map('mysql_real_escape_string', array_keys($cacheid2wptcode)))."')
						and user_id = '".mysql_real_escape_string($request->token->user_id)."'
					group by cache_id
				");
			}
			while ($row = mysql_fetch_assoc($rs))
			{
				# This one is plain-text. We may add my_notes_html for those who want it in HTML.
				$results[$cacheid2wptcode[$row['cache_id']]]['my_notes'] = strip_tags($row['desc']);
			}
		}

		if (in_array('trackables', $fields))
		{
			# Currently we support Geokrety only. But this interface should remain
			# compatible. In future, other trackables might be returned the same way.

			$rs = Db::query("
				select
					gkiw.wp as cache_code,
					gki.id as gk_id,
					gki.name
				from
					gk_item_waypoint gkiw,
					gk_item gki
				where
					gkiw.id = gki.id
					and gkiw.wp in ('".implode("','", array_map('mysql_real_escape_string', $cache_codes))."')
			");
			$trs = array();
			while ($row = mysql_fetch_assoc($rs))
				$trs[$row['cache_code']][] = $row;
			foreach ($results as $cache_code => &$result_ref)
			{
				$result_ref['trackables'] = array();
				if (!isset($trs[$cache_code]))
					continue;
				foreach ($trs[$cache_code] as $t)
				{
					$result_ref['trackables'][] = array(
						'code' => 'GK'.str_pad(strtoupper(dechex($t['gk_id'])), 4, "0", STR_PAD_LEFT),
						'name' => $t['name'],
						'url' => 'http://geokrety.org/konkret.php?id='.$t['gk_id']
					);
				}
			}
			unset($trs);
		}
		if (in_array('trackables_count', $fields))
		{
			if (in_array('trackables', $fields))
			{
				# We already got all trackables data, no need to query database again.
				foreach ($results as $cache_code => &$result_ref)
					$result_ref['trackables_count'] = count($result_ref['trackables']);
			}
			else
			{
				$rs = Db::query("
					select wp as cache_code, count(*) as count
					from gk_item_waypoint
					where wp in ('".implode("','", array_map('mysql_real_escape_string', $cache_codes))."')
					group by wp
				");
				$tr_counts = array();
				while ($row = mysql_fetch_assoc($rs))
					$tr_counts[$row['cache_code']] = $row['count'];
				foreach ($results as $cache_code => &$result_ref)
				{
					if (isset($tr_counts[$cache_code]))
						$result_ref['trackables_count'] = $tr_counts[$cache_code] + 0;
					else
						$result_ref['trackables_count'] = 0;
				}
				unset($tr_counts);
			}
		}

		# Alternate/Additional waypoints.

		if (in_array('alt_wpts', $fields))
		{
			foreach ($results as &$result_ref)
				$result_ref['alt_wpts'] = array();
			$cache_codes_escaped_and_imploded = "'".implode("','", array_map('mysql_real_escape_string', array_keys($cacheid2wptcode)))."'";

			if (Settings::get('OC_BRANCH') == 'oc.pl')
			{
				# OCPL uses 'waypoints' table to store additional waypoints. OCPL also have
				# a special 'status' field to denote a hidden waypoint (i.e. final location
				# of a multicache). Such hidden waypoints are not exposed by OKAPI. A stage
				# fields is used for ordering and naming.

				$waypoints = Db::select_all("
					select
						cache_id, stage, latitude, longitude, `desc`,
						case type
							when 3 then 'Flag, Red'
							when 4 then 'Circle with X'
							when 5 then 'Parking Area'
							else 'Flag, Green'
						end as sym
					from waypoints
					where
						cache_id in (".$cache_codes_escaped_and_imploded.")
						and status = 1
					order by cache_id, stage, `desc`
				");
			}
			else
			{
				# OCDE uses 'coordinates' table (with type=1) to store additional waypoints.
				# All waypoints are are public.

				$waypoints = Db::select_all("
					select
						cache_id,
						@stage := @stage + 1 as stage,
						latitude, longitude,
						description as `desc`,
						case subtype
							when 1 then 'Parking Area'
							when 3 then 'Flag, Blue'
							when 4 then 'Circle with X'
							when 5 then 'Diamond, Green'
							else 'Flag, Green'
						end as sym
					from coordinates
					join (select @stage := 0) s
					where
						type = 1
						and cache_id in (".$cache_codes_escaped_and_imploded.")
					order by cache_id, id, `desc`
				");
			}
			$wpt_format = "%s-%0".strlen(count($waypoints))."d";
			foreach ($waypoints as $index => $row)
			{
				$results[$cacheid2wptcode[$row['cache_id']]]['alt_wpts'][] = array(
					'name' => sprintf($wpt_format, $cacheid2wptcode[$row['cache_id']], $index + 1),
					'location' => round($row['latitude'], 6)."|".round($row['longitude'], 6),
					'sym' => $row['sym'],
					'description' => ($row['stage'] ? _("Stage")." ".$row['stage'].": " : "").$row['desc'],
				);
			}
		}

		# Country and/or state.

		if (Settings::get('OC_BRANCH') == 'oc.de')
			$cache_location_state = 'adm2';
		else
			$cache_location_state = 'adm3';
		if (in_array('country', $fields) || in_array('state', $fields))
		{
			$rs = Db::query("
				select
					c.wp_oc as cache_code,
					cl.adm1 as country,
					cl.".$cache_location_state." as state
				from
					caches c,
					cache_location cl
				where
					c.wp_oc in ('".implode("','", array_map('mysql_real_escape_string', $cache_codes))."')
					and c.cache_id = cl.cache_id
			");
			$countries = array();
			$states = array();
			while ($row = mysql_fetch_assoc($rs))
			{
				$countries[$row['cache_code']] = $row['country'];
				$states[$row['cache_code']] = $row['state'];
			}
			if (in_array('country', $fields))
			{
				foreach ($results as $cache_code => &$row_ref)
					$row_ref['country'] = isset($countries[$cache_code]) ? $countries[$cache_code] : null;
			}
			if (in_array('state', $fields))
			{
				foreach ($results as $cache_code => &$row_ref)
					$row_ref['state'] = isset($states[$cache_code]) ? $states[$cache_code] : null;
			}
			unset($countries);
			unset($states);
		}

		# Check which cache codes were not found and mark them with null.
		foreach ($cache_codes as $cache_code)
			if (!isset($results[$cache_code]))
				$results[$cache_code] = null;

		# Order the results in the same order as the input codes were given.
		# This might come in handy for languages which support ordered dictionaries
		# (especially with conjunction with the search_and_retrieve method).
		# See issue#97. PHP dictionaries (assoc arrays) are ordered structures,
		# so we just have to rewrite it (sequentially).

		$ordered_results = array();
		foreach ($cache_codes as $cache_code)
			$ordered_results[$cache_code] = $results[$cache_code];

		return Okapi::formatted_response($request, $ordered_results);
	}

	/**
	 * Create unique caption, safe to be used as a file name for images
	 * uploaded into Garmin's GPS devices. Use reset_unique_captions to reset
	 * unique counter.
	 */
	private static function get_unique_caption($caption)
	{
		# Garmins keep hanging on long file names. We don't have any specification from
		# Garmin and cannot determine WHY. That's why we won't use captions until we
		# know more.

		$caption = self::$caption_no."";
		self::$caption_no++;
		return $caption;

		/* This code is harmful for Garmins!
		$caption = preg_replace('#[^\\pL\d ]+#u', '-', $caption);
		$caption = trim($caption, '-');
		if (function_exists('iconv'))
		{
			$new = iconv("utf-8", "ASCII//TRANSLIT", $caption);
			if (!$new)
				$new = iconv("UTF-8", "ASCII//IGNORE", $caption);
		} else {
			$new = $caption;
		}
		$new = str_replace(array('/', '\\', '?', '%', '*', ':', '|', '"', '<', '>', '.'), '', $new);
		$new = trim($new);
		if ($new == "")
			$new = "(no caption)";
		if (strlen($new) > 240)
			$new = substr($new, 0, 240);
		$new = self::$caption_no." - ".$new;
		self::$caption_no++;
		return $new;*/
	}
	private static $caption_no = 1;
	private static function reset_unique_captions()
	{
		self::$caption_no = 1;
	}

	/**
	 * Return attribution note to be included in the cache description.
	 *
	 * The $lang parameter identifies the language of the cache description
	 * to which the attribution note will be appended to (one cache may
	 * have descriptions in multiple languages!).
	 *
	 * The $langpref parameter is *an array* of language preferences
	 * extracted from the langpref parameter passed to the method by the
	 * OKAPI Consumer.
	 *
	 * Both values ($lang and $langpref) will be taken into account when
	 * generating the attribution note, but $lang will have a higher
	 * priority than $langpref (we don't want to mix the languages in the
	 * descriptions if we don't have to).
	 *
	 * $owner is in object describing the user, it has the same format as
	 * defined in "geocache" method specs (see the "owner" field).
	 */
	public static function get_cache_attribution_note($cache_id, $lang, array $langpref, $owner)
	{
		$site_url = Settings::get('SITE_URL');
		$site_name = Okapi::get_normalized_site_name();
		$cache_url = $site_url."viewcache.php?cacheid=$cache_id";

		Okapi::gettext_domain_init(array_merge(array($lang), $langpref));
		$note = "<p>";
		if (Settings::get('OC_BRANCH') == 'oc.de')
		{
			$note .= sprintf(
				_(
					"<em>&copy; <a href='%s'>%s</a>, <a href='%s'>%s</a>, ".
					"<a href='http://creativecommons.org/licenses/by-nc-nd/3.0/de/deed.en'>CC-BY-NC-ND</a>, ".
					"as of %s; all log entries &copy; their authors</em>"
				),
				$owner['profile_url'], $owner['username'], $cache_url, $site_name, strftime('%x')
			);
		}
		else
		{
			$note .= sprintf(
				_("This <a href='%s'>geocache</a> description comes from the <a href='%s'>%s</a> site."),
				$cache_url, $site_url, $site_name
			);
		}
		$note .= "</p>";
		Okapi::gettext_domain_restore();

		return $note;
	}
}
