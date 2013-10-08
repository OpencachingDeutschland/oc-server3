<?php

namespace okapi\services\caches\search;

use okapi\Okapi;
use okapi\Db;
use okapi\OkapiInternalRequest;
use okapi\OkapiServiceRunner;
use okapi\OkapiRequest;
use okapi\InvalidParam;
use okapi\BadRequest;
use okapi\Settings;
use Exception;

class SearchAssistant
{
	/**
	 * Load, parse and check common geocache search parameters from the
	 * given OKAPI request. Most cache search methods share a common set
	 * of filtering parameters recognized by this method. It returns
	 * a dictionary of the following structure:
	 *
	 *  - "where_conds" - list of additional WHERE conditions to be ANDed
	 *    to the rest of your SQL query,
	 *  - "offset" - value of the offset parameter to be used in the LIMIT clause,
	 *  - "limit" - value of the limit parameter to be used in the LIMIT clause,
	 *  - "order_by" - list of order by clauses to be included in the "order by"
	 *    SQL clause,
	 *  - "extra_tables" - extra tables to be included in the FROM clause.
	 *  - "extra_joins" - extra join statements to be included
	 *
	 * Important: YOU HAVE TO make sure that all data returned by this function
	 * are properly sanitized for SQL queries! I.e. they cannot contain unescaped
	 * user-supplied data. All user-suppied data which is returned by this function
	 * MUST be escaped!
	 */
	public static function get_common_search_params(OkapiRequest $request)
	{
		$where_conds = array('true');
		$extra_tables = array();
		$extra_joins = array();

		# At the beginning we have to set up some "magic e$Xpressions".
		# We will use them to make our query run on both OCPL and OCDE databases.

		if (Settings::get('OC_BRANCH') == 'oc.pl')
		{
			# OCPL's 'caches' table contains some fields which OCDE's does not
			# (topratings, founds, notfounds, last_found, votes, score). If
			# we're being run on OCPL installation, we will simply use them.

			$X_TOPRATINGS = 'caches.topratings';
			$X_FOUNDS = 'caches.founds';
			$X_NOTFOUNDS = 'caches.notfounds';
			$X_LAST_FOUND = 'caches.last_found';
			$X_VOTES = 'caches.votes';
			$X_SCORE = 'caches.score';
		}
		else
		{
			# OCDE holds this data in a separate table. Additionally, OCDE
			# does not provide a rating system (votes and score fields).
			# If we're being run on OCDE database, we will include this
			# additional table in our query and we will map the field
			# expressions to approriate places.

			# stat_caches entries are optional, therefore we must do a left join:
			$extra_joins[] = 'left join stat_caches on stat_caches.cache_id = caches.cache_id';

			$X_TOPRATINGS = 'ifnull(stat_caches.toprating,0)';
			$X_FOUNDS = 'ifnull(stat_caches.found,0)';
			$X_NOTFOUNDS = 'ifnull(stat_caches.notfound,0)';
			$X_LAST_FOUND = 'ifnull(stat_caches.last_found,0)';
			$X_VOTES = '0'; // no support for ratings
			$X_SCORE = '0'; // no support for ratings
		}

		#
		# type
		#

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
			if (count($types) > 0)
				$where_conds[] = "caches.type $operator ('".implode("','", array_map('mysql_real_escape_string', $types))."')";
			else if ($operator == "in")
				$where_conds[] = "false";
		}

		#
		# size2
		#

		if ($tmp = $request->get_parameter('size2'))
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
					$id = Okapi::cache_size2_to_sizeid($name);
					$types[] = $id;
				}
				catch (Exception $e)
				{
					throw new InvalidParam('size2', "'$name' is not a valid cache size.");
				}
			}
			$where_conds[] = "caches.size $operator ('".implode("','", array_map('mysql_real_escape_string', $types))."')";
		}

		#
		# status - filter by status codes
		#

		$tmp = $request->get_parameter('status');
		if ($tmp == null) $tmp = "Available";
		$codes = array();
		foreach (explode("|", $tmp) as $name)
		{
			try
			{
				$codes[] = Okapi::cache_status_name2id($name);
			}
			catch (Exception $e)
			{
				throw new InvalidParam('status', "'$name' is not a valid cache status.");
			}
		}
		$where_conds[] = "caches.status in ('".implode("','", array_map('mysql_real_escape_string', $codes))."')";

		#
		# owner_uuid
		#

		if ($tmp = $request->get_parameter('owner_uuid'))
		{
			$operator = "in";
			if ($tmp[0] == '-')
			{
				$tmp = substr($tmp, 1);
				$operator = "not in";
			}
			try
			{
				$users = OkapiServiceRunner::call("services/users/users", new OkapiInternalRequest(
					$request->consumer, null, array('user_uuids' => $tmp, 'fields' => 'internal_id')));
			}
			catch (InvalidParam $e) # invalid uuid
			{
				throw new InvalidParam('owner_uuid', $e->whats_wrong_about_it);
			}
			$user_ids = array();
			foreach ($users as $user)
				$user_ids[] = $user['internal_id'];
			$where_conds[] = "caches.user_id $operator ('".implode("','", array_map('mysql_real_escape_string', $user_ids))."')";
		}

		#
		# terrain, difficulty, size, rating - these are similar, we'll do them in a loop
		#

		foreach (array('terrain', 'difficulty', 'size', 'rating') as $param_name)
		{
			if ($tmp = $request->get_parameter($param_name))
			{
				if (!preg_match("/^[1-5]-[1-5](\|X)?$/", $tmp))
					throw new InvalidParam($param_name, "'$tmp'");
				list($min, $max) = explode("-", $tmp);
				if (strpos($max, "|X") !== false)
				{
					$max = $max[0];
					$allow_null = true;
				} else {
					$allow_null = false;
				}
				if ($min > $max)
					throw new InvalidParam($param_name, "'$tmp'");
				switch ($param_name)
				{
					case 'terrain':
						if ($allow_null)
							throw new InvalidParam($param_name, "The '|X' suffix is not allowed here.");
						if (($min == 1) && ($max == 5)) {
							/* no extra condition necessary */
						} else {
							$where_conds[] = "caches.terrain between 2*$min and 2*$max";
						}
						break;
					case 'difficulty':
						if ($allow_null)
							throw new InvalidParam($param_name, "The '|X' suffix is not allowed here.");
						if (($min == 1) && ($max == 5)) {
							/* no extra condition necessary */
						} else {
							$where_conds[] = "caches.difficulty between 2*$min and 2*$max";
						}
						break;
					case 'size':
						# Deprecated. Leave it for backward-compatibility. See issue 155.
						if (($min == 1) && ($max == 5) && $allow_null) {
							# No extra condition necessary ('other' caches will be
							# included).
						} else {
							# 'other' size caches will NOT be included (user must use the
							# 'size2' parameter to search these). 'nano' caches will be
							# included whenever 'micro' caches are included ($min=1).
							$where_conds[] = "(caches.size between $min+1 and $max+1)".
								($allow_null ? " or caches.size=7" : "").
								(($min == 1) ? " or caches.size=8" : "");
						}
						break;
					case 'rating':
						if (Settings::get('OC_BRANCH') == 'oc.pl')
						{
							if (($min == 1) && ($max == 5) && $allow_null) {
								/* no extra condition necessary */
							} else {
								$divisors = array(-999, -1.0, 0.1, 1.4, 2.2, 999);
								$min = $divisors[$min - 1];
								$max = $divisors[$max];
								$where_conds[] = "($X_SCORE >= $min and $X_SCORE < $max and $X_VOTES >= 3)".
									($allow_null ? " or ($X_VOTES < 3)" : "");
							}
						}
						else
						{
							# OCDE does not support rating. We will ignore this parameter.
						}
						break;
				}
			}
		}

		#
		# min_rcmds
		#

		if ($tmp = $request->get_parameter('min_rcmds'))
		{
			if ($tmp[strlen($tmp) - 1] == '%')
			{
				$tmp = substr($tmp, 0, strlen($tmp) - 1);
				if (!is_numeric($tmp))
					throw new InvalidParam('min_rcmds', "'$tmp'");
				$tmp = intval($tmp);
				if ($tmp > 100 || $tmp < 0)
					throw new InvalidParam('min_rcmds', "'$tmp'");
				$tmp = floatval($tmp) / 100.0;
				$where_conds[] = "$X_TOPRATINGS >= $X_FOUNDS * '".mysql_real_escape_string($tmp)."'";
				$where_conds[] = "$X_FOUNDS > 0";
			}
			if (!is_numeric($tmp))
				throw new InvalidParam('min_rcmds', "'$tmp'");
			$where_conds[] = "$X_TOPRATINGS >= '".mysql_real_escape_string($tmp)."'";
		}

		#
		# min_founds
		#

		if ($tmp = $request->get_parameter('min_founds'))
		{
			if (!is_numeric($tmp))
				throw new InvalidParam('min_founds', "'$tmp'");
			$where_conds[] = "$X_FOUNDS >= '".mysql_real_escape_string($tmp)."'";
		}

		#
		# max_founds
		# may be '0' for FTF hunts
		#

		if (!is_null($tmp = $request->get_parameter('max_founds')))
		{
			if (!is_numeric($tmp))
				throw new InvalidParam('max_founds', "'$tmp'");
			$where_conds[] = "$X_FOUNDS <= '".mysql_real_escape_string($tmp)."'";
		}

		#
		# modified_since
		#

		if ($tmp = $request->get_parameter('modified_since'))
		{
			$timestamp = strtotime($tmp);
			if ($timestamp)
				$where_conds[] = "unix_timestamp(caches.last_modified) > '".mysql_real_escape_string($timestamp)."'";
			else
				throw new InvalidParam('modified_since', "'$tmp' is not in a valid format or is not a valid date.");
		}

		#
		# found_status
		#

		if ($tmp = $request->get_parameter('found_status'))
		{
			if ($request->token == null)
				throw new InvalidParam('found_status', "Might be used only for requests signed with an Access Token.");
			if (!in_array($tmp, array('found_only', 'notfound_only', 'either')))
				throw new InvalidParam('found_status', "'$tmp'");
			if ($tmp != 'either')
			{
				$found_cache_ids = self::get_found_cache_ids($request->token->user_id);
				$operator = ($tmp == 'found_only') ? "in" : "not in";
				$where_conds[] = "caches.cache_id $operator ('".implode("','", array_map('mysql_real_escape_string', $found_cache_ids))."')";
			}
		}

		#
		# found_by
		#

		if ($tmp = $request->get_parameter('found_by'))
		{
			try {
				$user = OkapiServiceRunner::call("services/users/user", new OkapiInternalRequest(
					$request->consumer, null, array('user_uuid' => $tmp, 'fields' => 'internal_id')));
			} catch (InvalidParam $e) { # invalid uuid
				throw new InvalidParam('found_by', $e->whats_wrong_about_it);
			}
			$found_cache_ids = self::get_found_cache_ids($user['internal_id']);
			$where_conds[] = "caches.cache_id in ('".implode("','", array_map('mysql_real_escape_string', $found_cache_ids))."')";
		}

		#
		# not_found_by
		#

		if ($tmp = $request->get_parameter('not_found_by'))
		{
			try {
				$user = OkapiServiceRunner::call("services/users/user", new OkapiInternalRequest(
					$request->consumer, null, array('user_uuid' => $tmp, 'fields' => 'internal_id')));
			} catch (InvalidParam $e) { # invalid uuid
				throw new InvalidParam('not_found_by', $e->whats_wrong_about_it);
			}
			$found_cache_ids = self::get_found_cache_ids($user['internal_id']);
			$where_conds[] = "caches.cache_id not in ('".implode("','", array_map('mysql_real_escape_string', $found_cache_ids))."')";
		}

		#
		# watched_only
		#

		if ($tmp = $request->get_parameter('watched_only'))
		{
			if ($request->token == null)
				throw new InvalidParam('watched_only', "Might be used only for requests signed with an Access Token.");
			if (!in_array($tmp, array('true', 'false')))
				throw new InvalidParam('watched_only', "'$tmp'");
			if ($tmp == 'true')
			{
				$watched_cache_ids = Db::select_column("
					select cache_id
					from cache_watches
					where user_id = '".mysql_real_escape_string($request->token->user_id)."'
				");
				$where_conds[] = "caches.cache_id in ('".implode("','", array_map('mysql_real_escape_string', $watched_cache_ids))."')";
			}
		}

		#
		# exclude_ignored
		#

		if ($tmp = $request->get_parameter('exclude_ignored'))
		{
			if ($request->token == null)
				throw new InvalidParam('exclude_ignored', "Might be used only for requests signed with an Access Token.");
			if (!in_array($tmp, array('true', 'false')))
				throw new InvalidParam('exclude_ignored', "'$tmp'");
			if ($tmp == 'true') {
				$ignored_cache_ids = Db::select_column("
					select cache_id
					from cache_ignore
					where user_id = '".mysql_real_escape_string($request->token->user_id)."'
				");
				$where_conds[] = "caches.cache_id not in ('".implode("','", array_map('mysql_real_escape_string', $ignored_cache_ids))."')";
			}
		}

		#
		# exclude_my_own
		#

		if ($tmp = $request->get_parameter('exclude_my_own'))
		{
			if ($request->token == null)
				throw new InvalidParam('exclude_my_own', "Might be used only for requests signed with an Access Token.");
			if (!in_array($tmp, array('true', 'false')))
				throw new InvalidParam('exclude_my_own', "'$tmp'");
			if ($tmp == 'true')
				$where_conds[] = "caches.user_id != '".mysql_real_escape_string($request->token->user_id)."'";
		}

		#
		# name
		#

		if ($tmp = $request->get_parameter('name'))
		{
			# WRTODO: Make this more user-friendly. See:
			# http://code.google.com/p/opencaching-api/issues/detail?id=121

			if (strlen($tmp) > 100)
				throw new InvalidParam('name', "Maximum length of 'name' parameter is 100 characters");
			$tmp = str_replace("*", "%", str_replace("%", "%%", $tmp));
			$where_conds[] = "caches.name LIKE '".mysql_real_escape_string($tmp)."'";
		}

		#
		# with_trackables_only
		#

		if ($tmp = $request->get_parameter('with_trackables_only'))
		{
			if (!in_array($tmp, array('true', 'false'), 1))
				throw new InvalidParam('with_trackables_only', "'$tmp'");
			if ($tmp == 'true')
			{
				$where_conds[] = "
					caches.wp_oc in (
						select distinct wp
						from gk_item_waypoint
					)
				";
			}
		}

		#
		# ftf_hunter
		#

		if ($tmp = $request->get_parameter('ftf_hunter'))
		{
			if (!in_array($tmp, array('true', 'false'), 1))
				throw new InvalidParam('not_yet_found_only', "'$tmp'");
			if ($tmp == 'true')
			{
				$where_conds[] = "$X_FOUNDS = 0";
			}
		}

		#
		# set_and
		#

		if ($tmp = $request->get_parameter('set_and'))
		{
			# Check if the set exists.

			$exists = Db::select_value("
				select 1
				from okapi_search_sets
				where id = '".mysql_real_escape_string($tmp)."'
			");
			if (!$exists)
				throw new InvalidParam('set_and', "Couldn't find a set by given ID.");
			$extra_tables[] = "okapi_search_results osr_and";
			$where_conds[] = "osr_and.cache_id = caches.cache_id";
			$where_conds[] = "osr_and.set_id = '".mysql_real_escape_string($tmp)."'";
		}

		#
		# limit
		#

		$limit = $request->get_parameter('limit');
		if ($limit == null) $limit = "100";
		if (!is_numeric($limit))
			throw new InvalidParam('limit', "'$limit'");
		if ($limit < 1 || (($limit > 500) && (!$request->skip_limits)))
			throw new InvalidParam('limit', "Has to be between 1 and 500.");

		#
		# offset
		#

		$offset = $request->get_parameter('offset');
		if ($offset == null) $offset = "0";
		if (!is_numeric($offset))
			throw new InvalidParam('offset', "'$offset'");
		if (($offset + $limit > 500) && (!$request->skip_limits))
			throw new BadRequest("The sum of offset and limit may not exceed 500.");
		if ($offset < 0 || $offset > 499)
			throw new InvalidParam('offset', "Has to be between 0 and 499.");

		#
		# order_by
		#

		$order_clauses = array();
		$order_by = $request->get_parameter('order_by');
		if ($order_by != null)
		{
			$order_by = explode('|', $order_by);
			foreach ($order_by as $field)
			{
				$dir = 'asc';
				if ($field[0] == '-')
				{
					$dir = 'desc';
					$field = substr($field, 1);
				}
				elseif ($field[0] == '+')
					$field = substr($field, 1); # ignore leading "+"
				switch ($field)
				{
					case 'code': $cl = "caches.wp_oc"; break;
					case 'name': $cl = "caches.name"; break;
					case 'founds': $cl = "$X_FOUNDS"; break;
					case 'rcmds': $cl = "$X_TOPRATINGS"; break;
					case 'rcmds%':
						$cl = "$X_TOPRATINGS / if($X_FOUNDS = 0, 1, $X_FOUNDS)";
						break;
					default:
						throw new InvalidParam('order_by', "Unsupported field '$field'");
				}
				$order_clauses[] = "($cl) $dir";
			}
		}

		# To avoid join errors, put each of the $where_conds in extra paranthesis.

		$tmp = array();
		foreach($where_conds as $cond)
			$tmp[] = "(".$cond.")";
		$where_conds = $tmp;
		unset($tmp);

		$ret_array = array(
			'where_conds' => $where_conds,
			'offset' => (int)$offset,
			'limit' => (int)$limit,
			'order_by' => $order_clauses,
			'extra_tables' => $extra_tables,
			'extra_joins' => $extra_joins,
		);

		return $ret_array;
	}

	/**
	 * Search for caches using given conditions and options. Return
	 * an array in a "standard" format of array('results' => list of
	 * cache codes, 'more' => boolean). This method takes care of the
	 * 'more' variable in an appropriate way.
	 *
	 * The $options parameter include:
	 *  - where_conds - list of additional WHERE conditions to be ANDed
	 *    to the rest of your SQL query,
	 *  - extra_tables - list of additional tables to be joined within
	 *    the query,
	 *  - order_by - list or SQL clauses to be used with ORDER BY,
	 *  - limit - maximum number of cache codes to be returned.
	 *
	 * Important: YOU HAVE TO make sure that all options are properly sanitized
	 * for SQL queries! I.e. they cannot contain unescaped user-supplied data.
	 */
	public static function get_common_search_result($options)
	{
		$tables = array_merge(
			array('caches'),
			$options['extra_tables']
		);
		$where_conds = array_merge(
			array('caches.wp_oc is not null'),
			$options['where_conds']
		);

		# We need to pull limit+1 items, in order to properly determine the
		# value of "more" variable.

		$cache_codes = Db::select_column("
			select caches.wp_oc
			from ".implode(", ", $tables)." ".
			implode(" ", $options['extra_joins'])."
			where ".implode(" and ", $where_conds)."
			".((count($options['order_by']) > 0) ? "order by ".implode(", ", $options['order_by']) : "")."
			limit ".($options['offset']).", ".($options['limit'] + 1).";
		");

		if (count($cache_codes) > $options['limit'])
		{
			$more = true;
			array_pop($cache_codes); # get rid of the one above the limit
		} else {
			$more = false;
		}

		$result = array(
			'results' => $cache_codes,
			'more' => $more,
		);
		return $result;
	}

	/**
	 * Get the list of cache IDs which were found by given user.
	 * Parameter needs to be *internal* user id, not uuid.
	 */
	private static function get_found_cache_ids($internal_user_id)
	{
		return Db::select_column("
			select cache_id
			from cache_logs
			where
				user_id = '".mysql_real_escape_string($internal_user_id)."'
				and type in (
					'".mysql_real_escape_string(Okapi::logtypename2id("Found it"))."',
					'".mysql_real_escape_string(Okapi::logtypename2id("Attended"))."'
				)
				and ".((Settings::get('OC_BRANCH') == 'oc.pl') ? "deleted = 0" : "true")."
		");
	}
}
