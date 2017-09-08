<?php

namespace okapi\services\replicate;

use Exception;
use okapi\core\Cache;
use okapi\core\Consumer\OkapiInternalConsumer;
use okapi\core\Db;
use okapi\core\Okapi;
use okapi\core\OkapiServiceRunner;
use okapi\core\Request\OkapiInternalRequest;
use okapi\Settings;

class ReplicateCommon
{
    private static $chunk_size = 50;
    private static $logged_cache_fields = 'code|names|location|type|status|url|owner|founds|notfounds|size|size2|oxsize|difficulty|terrain|rating|rating_votes|recommendations|req_passwd|descriptions|hints|images|trackables_count|trackables|alt_wpts|last_found|last_modified|date_created|date_hidden|attr_acodes|willattends|country|state|preview_image|trip_time|trip_distance|gc_code|hints2|protection_areas';

    # Consider https://github.com/opencaching/okapi/issues/382 before adding
    # any new field to $logged_log_entry_fields! Care must be taken that
    # changed values are reflected in cache_logs.okapi_syncbase.

    private static $logged_log_entry_fields = 'uuid|cache_code|date|user|type|was_recommended|comment';

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
        if (!$old)
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
        $now = Db::select_value("select date_add(now(), interval -1 minute)");  # See issue 157.
        $last_update = Okapi::get_var('last_clog_update');
        if ($last_update === null)
            $last_update = Db::select_value("select date_add(now(), interval -1 day)");

        # Usually this will be fast. But, for example, if admin changes ALL the
        # caches, this will take forever. But we still want it to finish properly
        # without interruption.

        set_time_limit(0);
        ignore_user_abort(true);

        # Get the list of modified cache codes. Split it into groups of N cache codes.
        # Note that we should include ALL cache codes in this particular query, not
        # only "status in (1,2,3)". This way, when the cache changes its status, e.g.
        # from 3 to 6, changelog will include a proper "delete" statement.

        $cache_codes = Db::select_column("
            select wp_oc
            from caches
            where okapi_syncbase > '".Db::escape_string($last_update)."';
        ");
        $cache_code_groups = Okapi::make_groups($cache_codes, 50);
        unset($cache_codes);

        # For each group, update the changelog table accordingly.

        foreach ($cache_code_groups as $cache_codes)
        {
            self::generate_changelog_entries('services/caches/geocaches', 'geocache', 'cache_codes',
                'code', $cache_codes, self::$logged_cache_fields, false, true, null);
        }

        # Same as above, for log entries.

        $offset = 0;
        while (true)
        {
            $log_uuids = Db::select_column("
                select uuid
                from cache_logs
                where okapi_syncbase > '".Db::escape_string($last_update)."'
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
            # split them in groups of 10000 as we did above.

            $DELETED_uuids = Db::select_column("
                select uuid
                from cache_logs_archived
                where okapi_syncbase > '".Db::escape_string($last_update)."'
            ");
            $deleted_uuid_groups = Okapi::make_groups($DELETED_uuids, 100);
            unset($DELETED_uuids);
            foreach ($deleted_uuid_groups as $deleted_uuids)
            {
                self::generate_changelog_entries('services/logs/entries', 'log', 'log_uuids',
                    'uuid', $deleted_uuids, self::$logged_log_entry_fields, false, true, 3600);
            }
        }

        # Update state variables.

        Okapi::set_var("last_clog_update", $now);
        $revision = Db::select_value("select max(id) from okapi_clog");
        Okapi::set_var("clog_revision", $revision);
    }

    /**
     * Scan the database and compare the current values of old entries to
     * the cached values of the same entries. If differences found, update
     * okapi_syncbase accordingly.
     *
     * Currently, only caches are checked; log entries are not. This works
     * well as long as only log entry fields are replicated which update
     * some cache_logs column when changed (by OKAPI or OC code). See
     * https://github.com/opencaching/okapi/issues/382 for further discussion.
     */
    public static function verify_clog_consistency(
        $force_all=false, $geocache_ignored_fields = null
    )
    {
        set_time_limit(0);
        ignore_user_abort(true);

        # We will SKIP the entries which have been modified AFTER one day AGO.
        # Such entries might have not been seen by the update_clog_table() yet
        # (e.g. other long-running cronjob is preventing update_clog_table from
        # running).
        #
        # If $force_all is true, then all caches will be verified. This is
        # quite important when used in conjunction with ignored_fields.

        $cache_codes = Db::select_column("
            select wp_oc
            from caches
            ".($force_all ? "" : "where okapi_syncbase < date_add(now(), interval -1 day)")."
        ");
        $cache_code_groups = Okapi::make_groups($cache_codes, 50);
        unset($cache_codes);

        # For each group, get the changelog entries, but don't store them
        # (the "fulldump" mode). Instead, just update the okapi_syncbase column.

        $sum = 0;
        $two_examples = array();
        foreach ($cache_code_groups as $cache_codes)
        {
            $entries = self::generate_changelog_entries(
                'services/caches/geocaches', 'geocache', 'cache_codes',
                'code', $cache_codes, self::$logged_cache_fields, true, true, null
            );
            foreach ($entries as $entry)
            {
                if ($entry['object_type'] != 'geocache')
                    continue;
                $cache_code = $entry['object_key']['code'];

                if (($entry['change_type'] == 'replace') && ($geocache_ignored_fields != null)) {

                    # We were called with a non-null ignored fields. These
                    # fields should be ignored when comparing objects for
                    # changes, so that no unnecessary clog entries will be
                    # created.

                    foreach ($geocache_ignored_fields as $field) {
                        unset($entry['data'][$field]);
                    }
                    if (count($entry['data']) == 0) {
                        # Skip this geocache. Nothing was changed here, only
                        # new fields have been added.
                        continue;
                    }
                }

                # We will story the first and the last entry in the $two_examples
                # vars which is to be emailed to OKAPI developers.

                if (count($two_examples) == 0)
                    $two_examples[0] = $entry;  /* The first entry */
                $two_examples[1] = $entry;  /* The last entry */

                Db::execute("
                    update caches
                    set okapi_syncbase = now()
                    where wp_oc = '".Db::escape_string($cache_code)."'
                ");
                $sum += 1;
            }
        }
        /*
        if ($sum > 0)
        {
            $message = (
                "Number of invalid entries scheduled to be fixed: $sum\n".
                "Approx revision of the first one: ".Okapi::get_var('clog_revision')."\n\n".
                "Two examples:\n\n".print_r($two_examples, true)
            );
            Okapi::mail_from_okapi(
                "rygielski@mimuw.edu.pl",
                "verify_clog_consistency - ".Okapi::get_normalized_site_name(),
                $message, true
            );
        }
        */
    }


    /**
     * Generate OKAPI changelog entries. This method will call $feeder_method OKAPI
     * service with the following parameters: array($feeder_keys_param => implode('|', $key_values),
     * 'fields' => $fields). Then it will generate the changelog, based on the result.
     * This looks pretty much the same for various object types, that's why it's here.
     *
     * If $use_cache is true, then all the dictionaries from $feeder_method will be also
     * kept in OKAPI cache, for future comparison.
     *
     * In normal mode, update the changelog and don't return anything.
     * In fulldump mode, return the generated changelog entries *instead* of
     * updating it.
     */
    private static function generate_changelog_entries($feeder_method, $object_type, $feeder_keys_param,
        $key_name, $key_values, $fields, $fulldump_mode, $use_cache, $cache_timeout = 86400)
    {
        # Retrieve the previous versions of all objects from OKAPI cache.

        if ($use_cache)
        {
            $cache_keys1 = array();
            $cache_keys2 = array();
            foreach ($key_values as $key)
                $cache_keys1[] = 'clog#'.$object_type.'#'.$key;
            foreach ($key_values as $key)
                $cache_keys2[] = 'clogmd5#'.$object_type.'#'.$key;
            $cached_values1 = Cache::get_many($cache_keys1);
            $cached_values2 = Cache::get_many($cache_keys2);
            if (!$fulldump_mode)
            {
                Cache::delete_many($cache_keys1);
                Cache::delete_many($cache_keys2);
            }
            unset($cache_keys1);
            unset($cache_keys2);
        }

        # Get the current values for objects. Compare them with their previous versions
        # and generate changelog entries.

        $current_values = OkapiServiceRunner::call($feeder_method, new OkapiInternalRequest(
            new OkapiInternalConsumer(), null, array(
                $feeder_keys_param => implode("|", $key_values),
                'fields' => $fields,
                'attribution_append' => 'static'  # currently, this is for the "geocaches" method only
            )));
        $entries = array();
        foreach ($current_values as $key => $object)
        {
            if ($object !== null)
            {
                # Currently, the object exists.
                if ($use_cache)
                {
                    # First, compare the cached hash. The hash has much longer lifetime
                    # than the actual cached object.
                    $cached_md5 = $cached_values2['clogmd5#'.$object_type.'#'.$key];
                    $current_md5 = md5(serialize($object));
                    if ($cached_md5 == $current_md5)
                    {
                        # The object was not changed since it was last replaced.
                        continue;
                    }
                    $diff = self::get_diff($cached_values1['clog#'.$object_type.'#'.$key], $object);
                    if (count($diff) == 0)
                    {
                        # Md5 differs, but diff does not. Weird, but it can happen
                        # (e.g. just after the md5 extension was introduced, or if
                        # md5 somehow expired before the actual object did).
                        continue;
                    }
                }
                $entries[] = array(
                    'object_type' => $object_type,
                    'object_key' => array($key_name => $key),
                    'change_type' => 'replace',
                    'data' => ($use_cache ? $diff : $object),
                );
                if ($use_cache)
                {
                    # Save the last-published state of the object, for future comparison.
                    $cached_values2['clogmd5#'.$object_type.'#'.$key] = $current_md5;
                    $cached_values1['clog#'.$object_type.'#'.$key] = $object;
                }
            }
            else
            {
                # Currently, the object does not exist.
                if ($use_cache && ($cached_values1['clog#'.$object_type.'#'.$key] === false))
                {
                    # No need to delete, we have already published its deletion.
                    continue;
                }
                $entries[] = array(
                    'object_type' => $object_type,
                    'object_key' => array($key_name => $key),
                    'change_type' => 'delete',
                );
                if ($use_cache)
                {
                    # Cache the fact, that the object was deleted.
                    $cached_values2['clogmd5#'.$object_type.'#'.$key] = false;
                    $cached_values1['clog#'.$object_type.'#'.$key] = false;
                }
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
                    values ('".implode("'),('", array_map('\okapi\core\Db::escape_string', $data_values))."');
                ");
            }

            # Update the values kept in OKAPI cache.

            if ($use_cache)
            {
                Cache::set_many($cached_values1, $cache_timeout);
                Cache::set_many($cached_values2, null);  # make it persistent
            }
        }
    }

    /**
     * Check if the 'since' parameter is up-do-date. If it is not, then it means
     * that the user waited too long and he has to download the fulldump again.
     */
    public static function check_since_param($since)
    {
        $first_id = Db::select_value("
            select id from okapi_clog where id > '".Db::escape_string($since)."' limit 1
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
                where id between '".Db::escape_string($from)."' and '".Db::escape_string($to)."'
                order by id
            ");
            $chunk = array();
            while ($row = Db::fetch_assoc($rs))
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
        $dir = Okapi::get_var_dir(). '/okapi-db-dump';
        $i = 1;
        $json_files = array();

        # Cleanup (from a previous, possibly unsuccessful, execution)

        shell_exec("rm -f $dir/*");
        if (is_dir($dir)) {
            shell_exec("rmdir $dir");
        }
        shell_exec("mkdir $dir");
        shell_exec("chmod 777 $dir");

        # Geocaches

        $cache_codes = Db::select_column("select wp_oc from caches");
        $cache_code_groups = Okapi::make_groups($cache_codes, self::$chunk_size);
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
            file_put_contents("$dir/$basename.json", json_encode($filtered));
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
                file_put_contents("$dir/$basename.json", json_encode($filtered));
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
                'okapi_version_number' => Okapi::$version_number,
                'okapi_revision' => Okapi::$version_number, /* Important for backward-compatibility! */
                'okapi_git_revision' => Okapi::$git_revision,
                'generated_at' => $generated_at,
            ),
        );
        file_put_contents("$dir/index.json", json_encode($metadata));

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
