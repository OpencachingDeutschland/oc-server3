<?php

namespace okapi\services\replicate;

use Exception;
use okapi\Okapi;
use okapi\Db;
use okapi\OkapiRequest;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\BadRequest;
use okapi\DoesNotExist;
use okapi\OkapiInternalRequest;
use okapi\OkapiInternalConsumer;
use okapi\OkapiServiceRunner;
use okapi\Cache;
use okapi\Settings;

class ReplicateCommon
{
	private static $chunk_size = 200;
	private static $logged_cache_fields = 'code|names|location|type|status|url|owner|founds|notfounds|size|difficulty|terrain|rating|rating_votes|recommendations|req_passwd|descriptions|hints|images|trackables_count|trackables|alt_wpts|last_found|last_modified|date_created|date_hidden';
	
	private static $logged_log_entry_fields = 'uuid|cache_code|date|user|type|comment';
	
	/** Return current (greatest) changelog revision number. */
	public static function get_revision()
	{
		return Okapi::get_var('clog_revision', 0) + 0;
	}

	/** Return the number of the oldest changelog revision kept in database. */
	public static function get_min_since()
	{
		static $cache = null;
		if ($cache == null)
		{
			$cache = Db::select_value("select min(id) from okapi_clog");
			if ($cache === null)
				$cache = 1;
			$cache -= 1;
		}
		return $cache;
	}

	
	/**
	 * Compare two dictionaries. Return the $new dictionary with all unchanged
	 * keys removed. Only the changed ones will remain.
	 */
	private static function get_diff($old, $new)
	{
		if ($old === null)
			return $new;
		$changed_keys = array();
		foreach ($new as $key => $value)
		{
			if (!array_key_exists($key, $old))
				$changed_keys[] = $key;
			elseif ($old[$key] != $new[$key])
				$changed_keys[] = $key;
		}
		$changed = array();
		foreach ($changed_keys as $key)
			$changed[$key] = $new[$key];
		return $changed;
	}
	
	/** Check for modifications in the database and update the changelog table accordingly. */
	public static function update_clog_table()
	{
		$now = Db::select_value("select now()");
		$last_update = Okapi::get_var('last_clog_update');
		if ($last_update === null)
			$last_update = Db::select_value("select date_add(now(), interval -1 day)");
		
		# Usually this will be fast. But, for example, if admin changes ALL the
		# caches, this will take forever. But we still want it to finish properly
		# without interruption.
		
		set_time_limit(0);
		ignore_user_abort(true); 
		
		# Get the list of modified cache codes. Split it into groups of N cache codes.
		
		$cache_codes = Db::select_column("
			select wp_oc
			from caches
			where okapi_syncbase > '".mysql_real_escape_string($last_update)."';
		");
		$cache_code_groups = Okapi::make_groups($cache_codes, 50);
		unset($cache_codes);
		
		# For each group, update the changelog table accordingly.
		
		foreach ($cache_code_groups as $cache_codes)
		{
			self::generate_changelog_entries('services/caches/geocaches', 'geocache', 'cache_codes',
				'code', $cache_codes, self::$logged_cache_fields, false, true, 30*86400);
		}
		
		# Same as above, for log entries.
		
		$offset = 0;
		while (true)
		{
			$log_uuids = Db::select_column("
				select uuid
				from cache_logs
				where last_modified > '".mysql_real_escape_string($last_update)."'
				limit $offset, 10000;
			");
			if (count($log_uuids) == 0)
				break;
			$offset += 10000;
			$log_uuid_groups = Okapi::make_groups($log_uuids, 100);
			unset($log_uuids);
			foreach ($log_uuid_groups as $log_uuids)
			{
				self::generate_changelog_entries('services/logs/entries', 'log', 'log_uuids',
					'uuid', $log_uuids, self::$logged_log_entry_fields, false, true, 3600);
			}
		}
		if (Settings::get('OC_BRANCH') == 'oc.de')
		{
			# On OCDE branch, deleted log entries are MOVED to another table.
			# So the above queries won't detect them. We need to run one more.
			# We will assume there are not so many of them and we don't have to
			# split them in groups as we did above.
			
			$DELETED_uuids = Db::select_column("
				select uuid
				from cache_logs_archived
				where last_modified > '".mysql_real_escape_string($last_update)."'
			");
			self::generate_changelog_entries('services/logs/entries', 'log', 'log_uuids',
				'uuid', $DELETED_uuids, self::$logged_log_entry_fields, false, true, 3600);
		}
		
		# Update state variables and release DB lock.
		
		Okapi::set_var("last_clog_update", $now);
		$revision = Db::select_value("select max(id) from okapi_clog");
		Okapi::set_var("clog_revision", $revision);
	}

	/**
	 * Generate OKAPI changelog entries. This method will call $feeder_method OKAPI
	 * service with the following parameters: array($feeder_keys_param => implode('|', $key_values),
	 * 'fields' => $fields). Then it will generate the changelog, based on the result.
	 * This looks pretty much the same for various object types, that's why it's here.
	 * If $use_cache is true, then all the dictionaries from $feeder_method will be also
	 * kept in OKAPI cache, for future comparison. 
	 */
	private static function generate_changelog_entries($feeder_method, $object_type, $feeder_keys_param,
		$key_name, $key_values, $fields, $fulldump_mode, $use_cache, $cache_timeout = 86400)
	{
		# Retrieve the previous versions of all objects from OKAPI cache.
		
		if ($use_cache)
		{
			$cache_keys = array();
			foreach ($key_values as $key)
				$cache_keys[] = 'clog#'.$object_type.'#'.$key;
			$cached_values = Cache::get_many($cache_keys);
			Cache::delete_many($cache_keys);
			unset($cache_keys);
		}
		
		# Get the current values for objects. Compare them with their previous versions
		# and generate changelog entries.
		
		require_once $GLOBALS['rootpath'].'okapi/service_runner.php';
		$current_values = OkapiServiceRunner::call($feeder_method, new OkapiInternalRequest(
			new OkapiInternalConsumer(), null, array($feeder_keys_param => implode("|", $key_values),
			'fields' => $fields)));
		$entries = array();
		foreach ($current_values as $key => $object)
		{
			if ($object !== null)
			{
				if ($use_cache)
				{
					$diff = self::get_diff($cached_values['clog#'.$object_type.'#'.$key], $object);
					if (count($diff) == 0)
						continue;
				}
				$entries[] = array(
					'object_type' => $object_type,
					'object_key' => array($key_name => $key),
					'change_type' => 'replace',
					'data' => ($use_cache ? $diff : $object),
				);
				if ($use_cache)
					$cached_values['clog#'.$object_type.'#'.$key] = $object;
			}
			else
			{
				$entries[] = array(
					'object_type' => $object_type,
					'object_key' => array($key_name => $key),
					'change_type' => 'delete',
				);
				if ($use_cache)
					$cached_values['clog#'.$object_type.'#'.$key] = null;
			}
		}
		
		if ($fulldump_mode)
		{
			return $entries;
		}
		else
		{
			# Save the entries to the clog table.
		
			if (count($entries) > 0)
			{
				$data_values = array();
				foreach ($entries as $entry)
					$data_values[] = gzdeflate(serialize($entry));
				Db::execute("
					insert into okapi_clog (data)
					values ('".implode("'),('", array_map('mysql_real_escape_string', $data_values))."');
				");
			}
			
			# Update the values kept in OKAPI cache.
			
			if ($use_cache)
				Cache::set_many($cached_values, $cache_timeout);
		}
	}
	
	/**
	 * Check if the 'since' parameter is up-do-date. If it is not, then it means
	 * that the user waited too long and he has to download the fulldump again.
	 */
	public static function check_since_param($since)
	{
		$first_id = Db::select_value("
			select id from okapi_clog where id > '".mysql_real_escape_string($since)."' limit 1
		");
		if ($first_id === null)
			return true; # okay, since points to the newest revision 
		if ($first_id == $since + 1)
			return true; # okay, revision $since + 1 is present
		
		# If we're here, then this means that $first_id > $since + 1.
		# Revision $since + 1 is already deleted, $since must be too old!
		
		return false;
	}
	
	/**
	 * Select best chunk for a given $since parameter. This function will try to select
	 * one chunk for different values of $since parameter, this is done in order to
	 * allow more optimal caching. Returns: list($from, $to). NOTICE: If $since is
	 * at the newest revision, then this will return list($since + 1, $since) - an
	 * empty chunk.
	 */
	public static function select_best_chunk($since)
	{
		$current_revision = self::get_revision();
		$last_chunk_cut = $current_revision - ($current_revision % self::$chunk_size);
		if ($since >= $last_chunk_cut)
		{
			# If, for example, we have a choice to give user 50 items he wants, or 80 items
			# which we probably already have in cache (and this includes the 50 which the
			# user wants), then we'll give him 80. If user wants less than half of what we
			# have (ex. 30), then we'll give him only his 30.
			
			if ($current_revision - $since > $since - $last_chunk_cut)
				return array($last_chunk_cut + 1, $current_revision);
			else
				return array($since + 1, $current_revision);
		}
		$prev_chunk_cut = $since - ($since % self::$chunk_size);
		return array($prev_chunk_cut + 1, $prev_chunk_cut + self::$chunk_size);
	}
	
	/**
	 * Return changelog chunk, starting at $from, ending as $to.
	 */
	public static function get_chunk($from, $to)
	{
		if ($to < $from)
			return array();
		if ($to - $from > self::$chunk_size)
			throw new Exception("You should not get chunksize bigger than ".self::$chunk_size." entries at one time.");
		
		# Check if we already have this chunk in cache.
		
		$cache_key = 'clog_chunk#'.$from.'-'.$to;
		$chunk = Cache::get($cache_key);
		if ($chunk === null)
		{
			$rs = Db::query("
				select id, data
				from okapi_clog
				where id between '".mysql_real_escape_string($from)."' and '".mysql_real_escape_string($to)."'
				order by id
			");
			$chunk = array();
			while ($row = mysql_fetch_assoc($rs))
			{
				$chunk[] = unserialize(gzinflate($row['data']));
			}
			
			# Cache timeout depends on the chunk starting and ending point. Chunks
			# which start and end on the boundries of chunk_size should be cached
			# longer (they can be accessed even after 10 days). Other chunks won't
			# be ever accessed after the next revision appears, so there is not point
			# in storing them that long.
			
			if (($from % self::$chunk_size === 0) && ($to % self::$chunk_size === 0))
				$timeout = 10 * 86400;
			else
				$timeout = 86400;
			Cache::set($cache_key, $chunk, $timeout);
		}
		
		return $chunk;
	}
	
	/**
	 * Generate a new fulldump file and put it into the OKAPI cache table.
	 * Return the cache key.
	 */
	public static function generate_fulldump()
	{
		# First we will create temporary files, then compress them in the end.
		
		$revision = self::get_revision();
		$generated_at = date('c', time());
		$dir = Okapi::get_var_dir()."/okapi-db-dump";
		$i = 1;
		$json_files = array();
		
		# Cleanup (from a previous, possibly unsuccessful, execution)
		
		shell_exec("rm -f $dir/*");
		shell_exec("rmdir $dir");
		shell_exec("mkdir $dir");
		shell_exec("chmod 777 $dir");
		
		# Geocaches
		
		$cache_codes = Db::select_column("select wp_oc from caches");
		$cache_code_groups = Okapi::make_groups($cache_codes, 200);
		unset($cache_codes);
		foreach ($cache_code_groups as $cache_codes)
		{
			$basename = "part".str_pad($i, 5, "0", STR_PAD_LEFT);
			$json_files[] = $basename.".json";
			$entries = self::generate_changelog_entries('services/caches/geocaches', 'geocache', 'cache_codes',
				'code', $cache_codes, self::$logged_cache_fields, true, false);
			$filtered = array();
			foreach ($entries as $entry)
				if ($entry['change_type'] == 'replace')
					$filtered[] = $entry;
			unset($entries);
			$fp = fopen("$dir/$basename.json", "wb");
			fwrite($fp, json_encode($filtered));
			fclose($fp);
			unset($filtered);
			$i++;
		}
		unset($cache_code_groups);
		
		# Log entries. We cannot load all the uuids at one time, this would take
		# too much memory. Hence the offset/limit loop.
		
		$offset = 0;
		while (true)
		{
			$log_uuids = Db::select_column("
				select uuid
				from cache_logs
				where ".((Settings::get('OC_BRANCH') == 'oc.pl') ? "deleted = 0" : "true")."
				order by uuid
				limit $offset, 10000
			");
			if (count($log_uuids) == 0)
				break;
			$offset += 10000;
			$log_uuid_groups = Okapi::make_groups($log_uuids, 500);
			unset($log_uuids);
			foreach ($log_uuid_groups as $log_uuids)
			{
				$basename = "part".str_pad($i, 5, "0", STR_PAD_LEFT);
				$json_files[] = $basename.".json";
				$entries = self::generate_changelog_entries('services/logs/entries', 'log', 'log_uuids',
					'uuid', $log_uuids, self::$logged_log_entry_fields, true, false);
				$filtered = array();
				foreach ($entries as $entry)
					if ($entry['change_type'] == 'replace')
						$filtered[] = $entry;
				unset($entries);
				$fp = fopen("$dir/$basename.json", "wb");
				fwrite($fp, json_encode($filtered));
				fclose($fp);
				unset($filtered);
				$i++;
			}
		}
		
		# Package data.
		
		$metadata = array(
			'revision' => $revision,
			'data_files' => $json_files,
			'meta' => array(
				'site_name' => Okapi::get_normalized_site_name(),
				'okapi_revision' => Okapi::$revision,
				'generated_at' => $generated_at,
			),
		);
		$fp = fopen("$dir/index.json", "wb");
		fwrite($fp, json_encode($metadata));
		fclose($fp);
		
		# Compute uncompressed size.
		
		$size = filesize("$dir/index.json");
		foreach ($json_files as $filename)
			$size += filesize("$dir/$filename");
		
		# Create JSON archive. We use tar options: -j for bzip2, -z for gzip
		# (bzip2 is MUCH slower).
		
		$use_bzip2 = true;
		$dumpfilename = "okapi-dump.tar.".($use_bzip2 ? "bz2" : "gz");
		shell_exec("tar --directory $dir -c".($use_bzip2 ? "j" : "z")."f $dir/$dumpfilename index.json ".implode(" ", $json_files). " 2>&1");
		
		# Delete temporary files.
		
		shell_exec("rm -f $dir/*.json");
		
		# Move the archive one directory upwards, replacing the previous one.
		# Remove the temporary directory.
		
		shell_exec("mv -f $dir/$dumpfilename ".Okapi::get_var_dir());
		shell_exec("rmdir $dir");
		
		# Update the database info.
		
		$metadata['meta']['filepath'] = Okapi::get_var_dir().'/'.$dumpfilename;
		$metadata['meta']['content_type'] = ($use_bzip2 ? "application/octet-stream" : "application/x-gzip");
		$metadata['meta']['public_filename'] = 'okapi-dump-r'.$metadata['revision'].'.tar.'.($use_bzip2 ? "bz2" : "gz");
		$metadata['meta']['uncompressed_size'] = $size;
		$metadata['meta']['compressed_size'] = filesize($metadata['meta']['filepath']);
		Cache::set("last_fulldump", $metadata, 10 * 86400);
	}
}
