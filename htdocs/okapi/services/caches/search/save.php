<?php

namespace okapi\services\caches\search\save;

require_once('searching.inc.php');

use okapi\Okapi;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\Db;
use okapi\OkapiLock;
use okapi\services\caches\search\SearchAssistant;

class WebService
{
	public static function options()
	{
		return array(
			'min_auth_level' => 1
		);
	}
	
	/**
	 * Get [set_id, date_created, expires] for the given params_hash
	 * (or [null, null, null] if not found).
	 */
	private static function find_param_set($params_hash, $ref_max_age)
	{
		$tmp = Db::select_row("
			select
				id as id,
				unix_timestamp(date_created) as date_created,
				unix_timestamp(expires) as expires
			from okapi_search_sets
			where
				params_hash = '".mysql_real_escape_string($params_hash)."'
				and date_add(date_created, interval '".mysql_real_escape_string($ref_max_age)."' second) > now()
			order by id desc
			limit 1
		");
		if ($tmp === null)
			return array(null, null, null);
		return array($tmp['id'], $tmp['date_created'], $tmp['expires']);
	}
	
	public static function call(OkapiRequest $request)
	{
		# "Cache control" parameters.
		
		$tmp = $request->get_parameter('min_store');
		if ($tmp === null) $tmp = "300";
		$min_store = intval($tmp);
		if (("$min_store" !== $tmp) ||($min_store < 0) || ($min_store > 64800))
			throw new InvalidParam('min_store', "Has to be in the 0..64800 range.");

		$tmp = $request->get_parameter('ref_max_age');
		if ($tmp === null) $tmp = "300";
		if ($tmp == "nolimit") $tmp = "9999999";
		$ref_max_age = intval($tmp);
		if (("$ref_max_age" !== $tmp) || ($ref_max_age < 300))
			throw new InvalidParam('ref_max_age', "Has to be >=300.");
		
		# Search params.
		
		$search_params = SearchAssistant::get_common_search_params($request);
		$tables = array_merge(
			array('caches'),
			$search_params['extra_tables']
		);
		$where_conds = array_merge(
			array('caches.wp_oc is not null'),
			$search_params['where_conds']
		);
		unset($search_params);
		
		# Generate, or retrieve an existing set, and return the result.
		
		$result = self::get_set($tables, $where_conds, $min_store, $ref_max_age);
		return Okapi::formatted_response($request, $result);
	}
	
	public static function get_set($tables, $where_conds, $min_store, $ref_max_age)
	{
		# Compute the "params hash".
		
		$params_hash = md5(serialize(array($tables, $where_conds)));
		
		# Check if there exists an entry for this hash, which also meets the
		# given freshness criteria.
		
		list($set_id, $date_created, $expires) = self::find_param_set($params_hash, $ref_max_age);
		if ($set_id === null)
		{
			# To avoid generating the same results by multiple threads at once
			# (the "tile" method uses the "save" method, so the problem is
			# quite real!), we will acquire a write-lock here.
			
			$lock = OkapiLock::get("search-results-writer");
			$lock->acquire();
			
			try
			{
				# Make sure we were the first to acquire the lock.
				
				list($set_id, $date_created, $expires) = self::find_param_set($params_hash, $ref_max_age);
				if ($set_id === null)
				{
					# We are in the first thread which have acquired the lock.
					# We will proceed with result-set creation. Other threads
					# will be waiting until we finish.
					
					Db::execute("
						insert into okapi_search_sets (params_hash, date_created, expires)
						values (
							'processing in progress',
							now(),
							date_add(now(), interval '".mysql_real_escape_string($min_store + 60)."' second)
						)
					");
					$set_id = Db::last_insert_id();
					$date_created = time();
					$expires = $date_created + $min_store + 60;
					Db::execute("
						insert into okapi_search_results (set_id, cache_id)
						select distinct
							'".mysql_real_escape_string($set_id)."',
							caches.cache_id
						from ".implode(", ", $tables)."
						where (".implode(") and (", $where_conds).")
					");
					
					# Lock barrier, to make sure the data is visible by other
					# sessions. See http://bugs.mysql.com/bug.php?id=36618
					
					Db::execute("lock table okapi_search_results write");
					Db::execute("unlock tables");
					
					Db::execute("
						update okapi_search_sets
						set params_hash = '".mysql_real_escape_string($params_hash)."'
						where id = '".mysql_real_escape_string($set_id)."'
					");
				} else {
					# Some other thread acquired the lock before us and it has
					# generated the result set. We don't need to do anything.
				}
				$lock->release();
			}
			catch (Exception $e)
			{
				# SQL error? Make sure the lock is released and rethrow.
				
				$lock->release();
				throw $e;
			}
		}
		
		# If we got an old set, we may need to expand its lifetime in order to
		# meet user's "min_store" criterium.
		
		if (time() + $min_store > $expires)
		{
			Db::execute("
				update okapi_search_sets
				set expires = date_add(now(), interval '".mysql_real_escape_string($min_store + 60)."' second)
				where id = '".mysql_real_escape_string($set_id)."'
			");
		}
		
		return array(
			'set_id' => "$set_id",
			'generated_at' => date('c', $date_created),
			'expires' => date('c', $expires),
		);
	}
}
