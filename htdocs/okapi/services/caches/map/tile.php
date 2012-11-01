<?php

namespace okapi\services\caches\map\tile;

use Exception;
use okapi\Okapi;
use okapi\Settings;
use okapi\Cache;
use okapi\FileCache;
use okapi\Db;
use okapi\OkapiRequest;
use okapi\OkapiHttpResponse;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\BadRequest;
use okapi\DoesNotExist;
use okapi\OkapiInternalRequest;
use okapi\OkapiInternalConsumer;
use okapi\OkapiServiceRunner;

use okapi\services\caches\map\TileTree;
use okapi\services\caches\map\DefaultTileRenderer;


require_once('tiletree.inc.php');
require_once('tilerenderer.inc.php');

class WebService
{
	/**
	 * Should be always true. You may temporarily set it to false, when you're
	 * testing/debugging the tile renderer.
	 */
	private static $USE_ETAGS_CACHE = true;
	
	/**
	 * Should be always true. You may temporarily set it to false, when you're
	 * testing/debugging the tile renderer.
	 */
	private static $USE_IMAGE_CACHE = true;
	
	/**
	 * Should be always true. You may temporarily set it to false, when you're
	 * testing/debugging. Grep the code to check when this flag is used.
	 */
	private static $USE_OTHER_CACHE = true;
	
	public static function options()
	{
		return array(
			'min_auth_level' => 1
		);
	}
	
	private static function require_uint($request, $name, $min_value = 0)
	{
		$val = $request->get_parameter($name);
		if ($val === null)
			throw new ParamMissing($name);
		$ret = intval($val);
		if ($ret < 0 || ("$ret" !== $val))
			throw new InvalidParam($name, "Expecting non-negative integer.");
		return $ret;
	}
	
	public static function call(OkapiRequest $request)
	{
		$checkpointA_started = microtime(true);
		
		# Make sure the request is internal.
		
		if (!in_array($request->consumer->key, array('internal', 'facade')))
			throw new BadRequest("Your Consumer Key has not been allowed to access this method.");
		
		# zoom, x, y - required tile-specific parameters.
		
		$zoom = self::require_uint($request, 'z');
		if ($zoom > 21)
			throw new InvalidParam('z', "Maximum value for this parameter is 21.");
		$x = self::require_uint($request, 'x');
		$y = self::require_uint($request, 'y');
		if ($x >= 1<<$zoom)
			throw new InvalidParam('x', "Should be in 0..".((1<<$zoom) - 1).".");
		if ($y >= 1<<$zoom)
			throw new InvalidParam('y', "Should be in 0..".((1<<$zoom) - 1).".");
		
		# status
		
		$filter_conds = array();
		$tmp = $request->get_parameter('status');
		if ($tmp == null) $tmp = "Available";
		$allowed_status_codes = array();
		foreach (explode("|", $tmp) as $name)
		{
			try
			{
				$allowed_status_codes[] = Okapi::cache_status_name2id($name);
			}
			catch (Exception $e)
			{
				throw new InvalidParam('status', "'$name' is not a valid cache status.");
			}
		}
		sort($allowed_status_codes);
		if (count($allowed_status_codes) == 0)
			throw new InvalidParam('status');
		if (count($allowed_status_codes) < 3)
			$filter_conds[] = "status in ('".implode("','", array_map('mysql_real_escape_string', $allowed_status_codes))."')";
		
		# type
		
		if ($tmp = $request->get_parameter('type'))
		{
			$operator = "in";
			if ($tmp[0] == '-')
			{
				$tmp = substr($tmp, 1);
				$operator = "not in";
			}
			$types = array();
			foreach (explode("|", $tmp) as $name)
			{
				try
				{
					$id = Okapi::cache_type_name2id($name);
					$types[] = $id;
				}
				catch (Exception $e)
				{
					throw new InvalidParam('type', "'$name' is not a valid cache type.");
				}
			}
			sort($types);
			
			# Check if all cache types were selected. Since we're running
			# on various OC installations, we don't know which caches types
			# are "all" here. We have to check.
			
			$all = self::$USE_OTHER_CACHE ? Cache::get('all_cache_types') : null;
			if ($all === null)
			{
				$all = Db::select_column("
					select distinct type
					from caches
					where status in (1,2,3)
				");
				Cache::set('all_cache_types', $all, 86400);
			}
			$all_included = true;
			foreach ($all as $type)
				if (!in_array($type, $types))
				{
					$all_included = false;
					break;
				}
					
			if ($all_included && ($operator == "in"))
			{
				# All cache types are to be included. This is common.
			}
			else
			{
				$filter_conds[] = "type $operator ('".implode("','", array_map('mysql_real_escape_string', $types))."')";
			}
		}
		
		# User-specific geocaches (cached together).
		
		$cache_key = "tileuser/".$request->token->user_id;
		$user = self::$USE_OTHER_CACHE ? Cache::get($cache_key) : null;
		if ($user === null)
		{
			$user = array();
			
			# Ignored caches.
			
			$rs = Db::query("
				select cache_id
				from cache_ignore
				where user_id = '".mysql_real_escape_string($request->token->user_id)."'
			");
			$user['ignored'] = array();
			while (list($cache_id) = mysql_fetch_row($rs))
				$user['ignored'][$cache_id] = true;
			
			# Found caches.
			
			$rs = Db::query("
				select distinct cache_id
				from cache_logs
				where
					user_id = '".mysql_real_escape_string($request->token->user_id)."'
					and type = 1
					and ".((Settings::get('OC_BRANCH') == 'oc.pl') ? "deleted = 0" : "true")."
			");
			$user['found'] = array();
			while (list($cache_id) = mysql_fetch_row($rs))
				$user['found'][$cache_id] = true;

			# Own caches.
			
			$rs = Db::query("
				select distinct cache_id
				from caches
				where user_id = '".mysql_real_escape_string($request->token->user_id)."'
			");
			$user['own'] = array();
			while (list($cache_id) = mysql_fetch_row($rs))
				$user['own'][$cache_id] = true;
			
			Cache::set($cache_key, $user, 30);
		}

		# exclude_ignored
		
		$tmp = $request->get_parameter('exclude_ignored');
		if ($tmp === null) $tmp = "false";
		if (!in_array($tmp, array('true', 'false'), true))
			throw new InvalidParam('exclude_ignored', "'$tmp'");
		if ($tmp == 'true')
		{
			$excluded_dict = $user['ignored'];
		} else {
			$excluded_dict = array();
		}
		
		# exclude_my_own
		
		if ($tmp = $request->get_parameter('exclude_my_own'))
		{
			if (!in_array($tmp, array('true', 'false'), 1))
				throw new InvalidParam('exclude_my_own', "'$tmp'");
			if (($tmp == 'true') && (count($user['own']) > 0))
			{
				foreach ($user['own'] as $cache_id => $v)
					$excluded_dict[$cache_id] = true;
			}
		}
		
		# found_status
		
		if ($tmp = $request->get_parameter('found_status'))
		{
			if (!in_array($tmp, array('found_only', 'notfound_only', 'either')))
				throw new InvalidParam('found_status', "'$tmp'");
			if ($tmp == 'either') {
				# Do nothing.
			} elseif ($tmp == 'notfound_only') {
				# Easy.
				foreach ($user['found'] as $cache_id => $v)
					$excluded_dict[$cache_id] = true;
			} else {
				# Found only. This will slow down queries somewhat. But it is rare.
				$filter_conds[] = "cache_id in ('".implode("','", array_map('mysql_real_escape_string', array_keys($user['found'])))."')";
			}
		}
		
		# with_trackables_only
		
		if ($tmp = $request->get_parameter('with_trackables_only'))
		{
			if (!in_array($tmp, array('true', 'false'), 1))
				throw new InvalidParam('with_trackables_only', "'$tmp'");
			if ($tmp == 'true')
			{
				$filter_conds[] = "flags & ".TileTree::$FLAG_HAS_TRACKABLES;
			}
		}
		
		# not_yet_found_only
		
		if ($tmp = $request->get_parameter('not_yet_found_only'))  # ftf hunter
		{
			if (!in_array($tmp, array('true', 'false'), 1))
				throw new InvalidParam('not_yet_found_only', "'$tmp'");
			if ($tmp == 'true')
			{
				$filter_conds[] = "flags & ".TileTree::$FLAG_NOT_YET_FOUND;
			}
		}
		
		# rating
		
		if ($tmp = $request->get_parameter('rating'))
		{
			if (!preg_match("/^[1-5]-[1-5](\|X)?$/", $tmp))
				throw new InvalidParam('rating', "'$tmp'");
			list($min, $max) = explode("-", $tmp);
			if (strpos($max, "|X") !== false)
			{
				$max = $max[0];
				$allow_null = true;
			} else {
				$allow_null = false;
			}
			if ($min > $max)
				throw new InvalidParam('rating', "'$tmp'");
			if (($min == 1) && ($max == 5) && $allow_null) {
				/* no extra condition necessary */
			} else {
				$filter_conds[] = "(rating between $min and $max)".
					($allow_null ? " or rating is null" : "");
			}
		}
		
		# Filter out caches in $excluded_dict.
		
		if (count($excluded_dict) > 0)
		{
			$filter_conds[] = "cache_id not in ('".implode("','", array_keys($excluded_dict))."')";
		}
		
		# Get caches within the tile (+ those around the borders). All filtering
		# options need to be applied here.
		
		$rs = TileTree::query_fast($zoom, $x, $y, $filter_conds);
		OkapiServiceRunner::save_stats_extra("caches/map/tile/checkpointA", null,
			microtime(true) - $checkpointA_started);
		$checkpointB_started = microtime(true);

		# Read the rows and add extra flags to them.

		$rows = array();
		if ($rs !== null)
		{
			while ($row = mysql_fetch_row($rs))
			{
				# Add the "found" flag, to indicate that this cache needs
				# to be drawn as found.

				if (isset($user['found'][$row[0]]))
					$row[6] |= TileTree::$FLAG_FOUND;  # $row[6] is "flags"
				if (isset($user['own'][$row[0]]))
					$row[6] |= TileTree::$FLAG_OWN;  # $row[6] is "flags"

				$rows[] = $row;
			}
			unset($row);
		}

		# Compute a fast image fingerprint. This will be used both for ETags
		# and internal cache ($cache_key).
		
		$tile = new DefaultTileRenderer($zoom, $rows);
		$image_fingerprint = $tile->get_unique_hash();
		
		# Start creating response.
		
		$response = new OkapiHttpResponse();
		$response->content_type = $tile->get_content_type();
		$response->cache_control = "Cache-Control: private, max-age=600";
		$response->etag = 'W/"'.$image_fingerprint.'"';
		
		# Check if the request didn't include the same ETag.
		
		OkapiServiceRunner::save_stats_extra("caches/map/tile/checkpointB", null,
			microtime(true) - $checkpointB_started);
		$checkpointC_started = microtime(true);
		if (self::$USE_ETAGS_CACHE && ($request->etag == $response->etag))
		{
			# Hit. Report the content was unmodified.
			
			$response->etag = null;
			$response->status = "304 Not Modified";
			return $response;
		}
		
		# Check if the image was recently rendered and is kept in image cache.
		
		$cache_key = "tile/".$image_fingerprint;
		$response->body = self::$USE_IMAGE_CACHE ? Cache::get($cache_key) : null;
		OkapiServiceRunner::save_stats_extra("caches/map/tile/checkpointC", null,
			microtime(true) - $checkpointC_started);
		$checkpointD_started = microtime(true);
		if ($response->body !== null)
		{
			# Hit. We will use the cached version of the image.
			
			return $response;
		}
		
		# Miss. Render the image. Cache the result.
	
		$response->body = $tile->render();
		Cache::set_scored($cache_key, $response->body);
		OkapiServiceRunner::save_stats_extra("caches/map/tile/checkpointD", null,
			microtime(true) - $checkpointD_started);

		return $response;
	}
}
