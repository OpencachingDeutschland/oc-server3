<?php

namespace okapi\services\caches\formatters\gpx;

use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\OkapiHttpResponse;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\BadRequest;
use okapi\ParamMissing;
use okapi\OkapiAccessToken;
use okapi\InvalidParam;
use okapi\services\caches\search\SearchAssistant;
use okapi\OkapiInternalConsumer;
use okapi\Db;
use okapi\Settings;
use okapi\services\attrs\AttrHelper;

class WebService
{
	public static function options()
	{
		return array(
			'min_auth_level' => 1
		);
	}

	/** Maps OKAPI cache type codes to Geocaching.com GPX cache types. */
	public static $cache_GPX_types = array(
		'Traditional' => 'Traditional Cache',
		'Multi' => 'Multi-Cache',
		'Quiz' => 'Unknown Cache',
		'Event' => 'Event Cache',
		'Virtual' => 'Virtual Cache',
		'Webcam' => 'Webcam Cache',
		'Moving' => 'Unknown Cache',
		'Math/Physics' => 'Unknown Cache',
		'Drive-In' => 'Traditional Cache',
		'Own' => 'Unknown Cache',
		'Other' => 'Unknown Cache'
	);

	/** Maps OKAPI's 'size2' values to geocaching.com size codes. */
	public static $cache_GPX_sizes = array(
		'none' => 'Virtual',
		'nano' => 'Micro',
		'micro' => 'Micro',
		'small' => 'Small',
		'regular' => 'Regular',
		'large' => 'Large',
		'xlarge' => 'Large',
		'other' => 'Other',
	);

	public static function call(OkapiRequest $request)
	{
		$vars = array();

		# Validating arguments. We will also assign some of them to the
		# $vars variable which we will use later in the GPS template.

		$cache_codes = $request->get_parameter('cache_codes');
		if ($cache_codes === null) throw new ParamMissing('cache_codes');

		# Issue 106 requires us to allow empty list of cache codes to be passed into this method.
		# All of the queries below have to be ready for $cache_codes to be empty!

		$langpref = $request->get_parameter('langpref');
		if (!$langpref) $langpref = "en";
		foreach (array('ns_ground', 'ns_gsak', 'ns_ox', 'latest_logs', 'alt_wpts', 'mark_found') as $param)
		{
			$val = $request->get_parameter($param);
			if (!$val) $val = "false";
			elseif (!in_array($val, array("true", "false")))
				throw new InvalidParam($param);
			$vars[$param] = ($val == "true");
		}
		if ($vars['latest_logs'] && (!$vars['ns_ground']))
			throw new BadRequest("In order for 'latest_logs' to work you have to also include 'ns_ground' extensions.");

		$tmp = $request->get_parameter('my_notes');
		$vars['my_notes'] = array();
		if ($tmp && $tmp != 'none') {
			$tmp = explode('|', $tmp);
			foreach ($tmp as $elem) {
				if ($elem == 'none') {
					/* pass */
				} elseif (in_array($elem, array('desc:text', 'gc:personal_note'))) {
					if (in_array('none', $tmp)) {
						throw new InvalidParam(
							'my_notes', "You cannot mix 'none' and '$elem'"
						);
					}
					if ($request->token == null) {
						throw new BadRequest(
							"Level 3 Authentication is required to access my_notes data."
						);
					}
					$vars['my_notes'][] = $elem;
				} else {
					throw new InvalidParam('my_notes', "Invalid list entry: '$elem'");
				}
			}
		}

		$images = $request->get_parameter('images');
		if (!$images) $images = 'descrefs:nonspoilers';
		if (!in_array($images, array('none', 'descrefs:thumblinks', 'descrefs:nonspoilers', 'descrefs:all', 'ox:all')))
			throw new InvalidParam('images', "'$images'");
		$vars['images'] = $images;

		$tmp = $request->get_parameter('attrs');
		if (!$tmp) $tmp = 'desc:text';
		$tmp = explode("|", $tmp);
		$vars['attrs'] = array();
		foreach ($tmp as $elem)
		{
			if ($elem == 'none') {
				/* pass */
			} elseif (in_array($elem, array('desc:text', 'ox:tags', 'gc:attrs', 'gc_ocde:attrs'))) {
				if ($elem == 'gc_ocde:attrs' && Settings::get('OC_BRANCH') != 'oc.de')
					$vars['attrs'][] = 'gc:attrs';
				else
					$vars['attrs'][] = $elem;
			} else {
				throw new InvalidParam('attrs', "Invalid list entry: '$elem'");
			}
		}

		$protection_areas = $request->get_parameter('protection_areas');
		if (!$protection_areas || $protection_areas == 'desc:auto')
		{
			if (Settings::get('OC_BRANCH') == 'oc.de') $protection_areas = 'desc:text';
			else $protection_areas = 'none';
		}
		if (!in_array($protection_areas, array('none', 'desc:text')))
			throw new InvalidParam('protection_areas',"'$protection_areas'");
		$vars['protection_areas'] = $protection_areas;

		$tmp = $request->get_parameter('trackables');
		if (!$tmp) $tmp = 'none';
		if (!in_array($tmp, array('none', 'desc:list', 'desc:count')))
			throw new InvalidParam('trackables', "'$tmp'");
		$vars['trackables'] = $tmp;

		$tmp = $request->get_parameter('recommendations');
		if (!$tmp) $tmp = 'none';
		if (!in_array($tmp, array('none', 'desc:count')))
			throw new InvalidParam('recommendations', "'$tmp'");
		$vars['recommendations'] = $tmp;

		$lpc = $request->get_parameter('lpc');
		if ($lpc === null) $lpc = 10; # will be checked in services/caches/geocaches call

		$user_uuid = $request->get_parameter('user_uuid');

		# Which fields of the services/caches/geocaches method do we need?

		$fields = 'code|name|location|date_created|url|type|status|size|size2|oxsize'.
			'|difficulty|terrain|description|hint2|rating|owner|url|internal_id'.
			'|protection_areas';
		if ($vars['images'] != 'none')
			$fields .= "|images";
		if (count($vars['attrs']) > 0)
			$fields .= "|attrnames|attr_acodes";
		if ($vars['trackables'] == 'desc:list')
			$fields .= "|trackables";
		elseif ($vars['trackables'] == 'desc:count')
			$fields .= "|trackables_count";
		if ($vars['alt_wpts'] == 'true')
			$fields .= "|alt_wpts";
		if ($vars['recommendations'] != 'none')
			$fields .= "|recommendations|founds";
		if (count($vars['my_notes']) > 0)
			$fields .= "|my_notes";
		if ($vars['latest_logs'])
			$fields .= "|latest_logs";
		if ($vars['mark_found'])
			$fields .= "|is_found";

		$vars['caches'] = OkapiServiceRunner::call(
			'services/caches/geocaches', new OkapiInternalRequest(
				$request->consumer, $request->token, array(
					'cache_codes' => $cache_codes,
					'langpref' => $langpref,
					'fields' => $fields,
					'lpc' => $lpc,
					'user_uuid' => $user_uuid,
					'log_fields' => 'uuid|date|user|type|comment|internal_id|was_recommended'
				)
			)
		);

		# Get all the other data need.

		$vars['installation'] = OkapiServiceRunner::call(
			'services/apisrv/installation', new OkapiInternalRequest(
				new OkapiInternalConsumer(), null, array()
			)
		);
		$vars['cache_GPX_types'] = self::$cache_GPX_types;
		$vars['cache_GPX_sizes'] = self::$cache_GPX_sizes;

		if (count($vars['attrs']) > 0)
		{
			/* The user asked for some kind of attribute output. We'll fetch all
			 * the data we MAY need. This is often far too much, but thanks to
			 * caching, it will work fast. */

			$vars['attr_index'] = OkapiServiceRunner::call(
				'services/attrs/attribute_index', new OkapiInternalRequest(
					$request->consumer, $request->token, array(
						'only_locally_used' => 'true',
						'langpref' => $langpref,
						'fields' => 'name|gc_equivs'
					)
				)
			);

			# prepare GS attribute data

			$vars['gc_attrs'] = in_array('gc:attrs', $vars['attrs']);
			$vars['gc_ocde_attrs'] = in_array('gc_ocde:attrs', $vars['attrs']);
			if ($vars['gc_attrs'] || $vars['gc_ocde_attrs'])
			{
				if ($vars['gc_ocde_attrs'])
				{
					# As this is an OCDE compatibility feature, we use the same Pseudo-GS
					# attribute names here as OCDE. Note that this code is specific to OCDE
					# database; OCPL stores attribute names in a different way and may use
					# different names for equivalent attributes.

					$ocde_attrnames = Db::select_group_by('id',"
						select id, name
						from cache_attrib
					");
					$attr_dict = AttrHelper::get_attrdict();
				}

				foreach ($vars['caches'] as &$cache)
				{
					$cache['gc_attrs'] = array();
					foreach ($cache['attr_acodes'] as $acode)
					{
						$has_gc_equivs = false;
						foreach ($vars['attr_index'][$acode]['gc_equivs'] as $gc)
						{
							# The assignment via GC-ID as array key will prohibit duplicate
							# GC attributes, which can result from
							# - assigning the same GC ID to multiple A-Codes,
							# - contradicting attributes in one OC listing, e.g. 24/4 + not 24/7.

							$cache['gc_attrs'][$gc['id']] = $gc;
							$has_gc_equivs = true;
						}
						if (!$has_gc_equivs && $vars['gc_ocde_attrs'])
						{
							# Generate an OCDE pseudo-GS attribute;
							# see http://code.google.com/p/opencaching-api/issues/detail?id=190 and
							# http://code.google.com/p/opencaching-api/issues/detail?id=271.
							#
							# Groundspeak uses ID 1..65 (as of June, 2013), and OCDE makeshift
							# IDs start at 106, so there is space for 40 new GS attributes.

							$internal_id = $attr_dict[$acode]['internal_id'];
							$cache['gc_attrs'][100 + $internal_id] = array(
								'inc' => 1,
								'name' => $ocde_attrnames[$internal_id][0]['name'],
							);
						}
					}
				}
			}
		}

		/* OC sites always used internal user_ids in their generated GPX files.
		 * This might be considered an error in itself (Groundspeak's XML namespace
		 * doesn't allow that), but it very common (Garmin's OpenCaching.COM
		 * also does that). Therefore, for backward-compatibility reasons, OKAPI
		 * will do it the same way. See issue 174.
		 *
		 * Currently, the caches method does not expose "owner.internal_id" and
		 * "latest_logs.user.internal_id" fields, we will read them manually
		 * from the database here. */

		$dict = array();
		foreach ($vars['caches'] as &$cache_ref)
		{
			$dict[$cache_ref['owner']['uuid']] = true;
			if (isset($cache_ref['latest_logs']))
				foreach ($cache_ref['latest_logs'] as &$log_ref)
					$dict[$log_ref['user']['uuid']] = true;
		}
		$rs = Db::query("
			select uuid, user_id
			from user
			where uuid in ('".implode("','", array_map('mysql_real_escape_string', array_keys($dict)))."')
		");
		while ($row = mysql_fetch_assoc($rs))
			$dict[$row['uuid']] = $row['user_id'];
		$vars['user_uuid_to_internal_id'] = &$dict;
		unset($dict);

		$response = new OkapiHttpResponse();
		$response->content_type = "application/gpx; charset=utf-8";
		$response->content_disposition = 'attachment; filename="results.gpx"';
		ob_start();
		Okapi::gettext_domain_init(explode("|", $langpref)); # Consumer gets properly localized GPX file.
		include 'gpxfile.tpl.php';
		Okapi::gettext_domain_restore();
		$response->body = ob_get_clean();
		return $response;
	}
}
