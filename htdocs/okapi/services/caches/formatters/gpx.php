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
		'Own' => 'Unknown Cache',
		'Other' => 'Unknown Cache'
	);
	
	/** Maps OpenCaching cache sizes Geocaching.com size codes. */
	public static $cache_GPX_sizes = array(
		1 => 'Micro',
		2 => 'Small',
		3 => 'Regular',
		4 => 'Large',
		5 => 'Large',
		null => 'Virtual'
	);
	/** Maps OpenCaching cache sizes opencaching.com (OX) size codes. */
	public static $cache_OX_sizes = array(
		1 => 2,
		2 => 3,
		3 => 4,
		4 => 5,
		5 => 5,
		null => null
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
		if (!$tmp) $tmp = "none";
		if (!in_array($tmp, array("none", "desc:text")))
			throw new InvalidParam("my_notes");
		if (($tmp != 'none') && ($request->token == null))
			throw new BadRequest("Level 3 Authentication is required to access my_notes data.");
		$vars['my_notes'] = $tmp;
		
		$images = $request->get_parameter('images');
		if (!$images) $images = 'descrefs:nonspoilers';
		if (!in_array($images, array('none', 'descrefs:nonspoilers', 'descrefs:all', 'ox:all')))
			throw new InvalidParam('images', "'$images'");
		$vars['images'] = $images;
		
		$tmp = $request->get_parameter('attrs');
		if (!$tmp) $tmp = 'desc:text';
		if (!in_array($tmp, array('none', 'desc:text', 'ox:tags')))
			throw new InvalidParam('attrs', "'$tmp'");
		$vars['attrs'] = $tmp;
		
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
		
		# We can get all the data we need from the services/caches/geocaches method.
		# We don't need to do any additional queries here.
		
		$fields = 'code|name|location|date_created|url|type|status|size'.
			'|difficulty|terrain|description|hint|rating|owner|url|internal_id';
		if ($vars['images'] != 'none')
			$fields .= "|images";
		if ($vars['attrs'] != 'none')
			$fields .= "|attrnames";
		if ($vars['trackables'] == 'desc:list')
			$fields .= "|trackables";
		elseif ($vars['trackables'] == 'desc:count')
			$fields .= "|trackables_count";
		if ($vars['alt_wpts'] == 'true')
			$fields .= "|alt_wpts";
		if ($vars['recommendations'] != 'none')
			$fields .= "|recommendations|founds";
		if ($vars['my_notes'] != 'none')
			$fields .= "|my_notes";
		if ($vars['latest_logs'])
			$fields .= "|latest_logs";
		if ($vars['mark_found'])
			$fields .= "|is_found";
		
		$vars['caches'] = OkapiServiceRunner::call('services/caches/geocaches', new OkapiInternalRequest(
			$request->consumer, $request->token, array('cache_codes' => $cache_codes,
			'langpref' => $langpref, 'fields' => $fields, 'lpc' => $lpc, 'user_uuid' => $user_uuid)));
		$vars['installation'] = OkapiServiceRunner::call('services/apisrv/installation', new OkapiInternalRequest(
			new OkapiInternalConsumer(), null, array()));
		$vars['cache_GPX_types'] = self::$cache_GPX_types;
		$vars['cache_GPX_sizes'] = self::$cache_GPX_sizes;
		$vars['cache_OX_sizes'] = self::$cache_OX_sizes;
		
		$response = new OkapiHttpResponse();
		$response->content_type = "text/xml; charset=utf-8";
		$response->content_disposition = 'attachment; filename="results.gpx"';
		ob_start();
		Okapi::gettext_domain_init(explode("|", $langpref)); # Consumer gets properly localized GPX file.
		include 'gpxfile.tpl.php';
		Okapi::gettext_domain_restore();
		$response->body = ob_get_clean();
		return $response;
	}
}
