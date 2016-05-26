<?php

namespace okapi;

# OKAPI Framework -- Wojciech Rygielski <rygielski@mimuw.edu.pl>

# Use this class when you want to use OKAPI's services within OC code.
# (Your service calls will appear with the name "Facade" in the weekly
# OKAPI usage report).

# IMPORTANT COMPATIBILITY NOTES:

# Note, that this is the *ONLY* internal OKAPI file that is guaranteed
# to stay backward-compatible (note that we mean FILES here, all OKAPI
# methods will stay compatible forever). If you want to use any class or
# method that has not been exposed through the Facade class, contact
# OKAPI developers, we will add it here.

# Including this file will initialize OKAPI Framework with its default
# exception and error handlers. OKAPI is strict about PHP warnings and
# notices, so you might need to temporarily disable the error handler in
# order to get it to work with your code. Just call this after you
# include the Facade file: Facade::disable_error_handling().

# EXAMPLE OF USAGE:

# require_once($rootpath.'okapi/facade.php');
# \okapi\Facade::schedule_user_entries_check(...);
# \okapi\Facade::disable_error_handling();


use Exception;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use okapi\OkapiFacadeConsumer;
use okapi\OkapiFacadeAccessToken;
use okapi\Cache;

require_once($GLOBALS['rootpath']."okapi/core.php");
OkapiErrorHandler::$treat_notices_as_errors = true;
require_once($GLOBALS['rootpath']."okapi/service_runner.php");
Okapi::init_internals();

/**
 * Use this class to access OKAPI's services from external code (i.e. OC code).
 */
class Facade
{
    /**
     * Perform OKAPI service call, signed by internal 'facade' consumer key, and return the result
     * (this will be PHP object or OkapiHttpResponse, depending on the method). Use this method
     * whenever you need to access OKAPI services from within OC code. If you want to simulate
     * Level 3 Authentication, you should supply user's internal ID (the second parameter).
     */
    public static function service_call($service_name, $user_id_or_null, $parameters)
    {
        $request = new OkapiInternalRequest(
            new OkapiFacadeConsumer(),
            ($user_id_or_null !== null) ? new OkapiFacadeAccessToken($user_id_or_null) : null,
            $parameters
        );
        $request->perceive_as_http_request = true;
        return OkapiServiceRunner::call($service_name, $request);
    }

    /**
     * This works like service_call with two exceptions: 1. It passes all your
     * current HTTP request headers to OKAPI (which can make use of them in
     * terms of caching), 2. It outputs the service response directly, instead
     * of returning it.
     */
    public static function service_display($service_name, $user_id_or_null, $parameters)
    {
        $request = new OkapiInternalRequest(
            new OkapiFacadeConsumer(),
            ($user_id_or_null !== null) ? new OkapiFacadeAccessToken($user_id_or_null) : null,
            $parameters
        );
        $request->i_want_OkapiResponse = true;
        $request->perceive_as_http_request = true;
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']))
            $request->etag = $_SERVER['HTTP_IF_NONE_MATCH'];
        $response = OkapiServiceRunner::call($service_name, $request);
        $response->display();
    }

    /**
     * Create a search set from a temporary table. This is very similar to
     * the "services/caches/search/save" method, but allows OC server to
     * include its own result instead of using OKAPI's search options. The
     * $temp_table should be a valid name of a temporary table with the
     * following (or similar) structure:
     *
     *   create temporary table temp_12345 (
     *     cache_id integer primary key
     *   ) engine=memory;
     */
    public static function import_search_set($temp_table, $min_store, $max_ref_age)
    {
        require_once($GLOBALS['rootpath'].'okapi/services/caches/search/save.php');
        $tables = array('caches', $temp_table);
        $where_conds = array(
            $temp_table.".cache_id = caches.cache_id",
            'caches.status in (1,2,3)',
        );
        return \okapi\services\caches\search\save\WebService::get_set(
            $tables, array() /* joins */, $where_conds, $min_store, $max_ref_age
        );
    }

    /**
     * Mark the specified caches as *possibly* modified. The replicate module
     * will scan for changes within these caches on the next changelog update.
     * This is useful in some cases, when OKAPI cannot detect the modification
     * for itself (grep OCPL code for examples). See issue #179.
     *
     * $cache_codes - a single cache code OR an array of cache codes.
     */
    public static function schedule_geocache_check($cache_codes)
    {
        if (!is_array($cache_codes))
            $cache_codes = array($cache_codes);
        Db::execute("
            update caches
            set okapi_syncbase = now()
            where wp_oc in ('".implode("','", array_map('\okapi\Db::escape_string', $cache_codes))."')
        ");
    }

    /**
     * Find all log entries of the specified user for the specified cache and
     * mark them as *possibly* modified. See issue #265.
     *
     * $cache_id - internal ID of the geocache,
     * $user_id - internal ID of the user.
     */
    public static function schedule_user_entries_check($cache_id, $user_id)
    {
        Db::execute("
            update cache_logs
            set okapi_syncbase = now()
            where
                cache_id = '".Db::escape_string($cache_id)."'
                and user_id = '".Db::escape_string($user_id)."'
        ");
    }

    /**
     * Remove all OAuth Access Tokens bound to a certain user. This method
     * should be called (once) e.g. after a user is banned. It will disable his
     * ability to submit cache logs, etc.
     *
     * Note, that OKAPI will *automatically* remove Access Tokens of banned
     * users, but it will perform this action with a slight delay. This method
     * can be used to do this immediatelly. See #432 for details.
     */
    public static function remove_user_tokens($user_id)
    {
        Db::execute("
            delete from okapi_tokens
            where user_id = '".Db::escape_string($user_id)."'
        ");
    }

    /**
     * Run OKAPI database update.
     * Will output messages to stdout.
     */
    public static function database_update()
    {
        require_once($GLOBALS['rootpath']."okapi/views/update.php");
        $update = new views\update\View;
        $update->call();
    }

    /**
     * You will probably want to call that with FALSE when using Facade
     * in buggy, legacy OC code. This will disable OKAPI's default behavior
     * of treating NOTICEs as errors.
     */
    public static function disable_error_handling()
    {
        OkapiErrorHandler::disable();
    }

    /**
     * If you disabled OKAPI's error handling with disable_error_handling,
     * you may reenable it with this method.
     */
    public static function reenable_error_handling()
    {
        OkapiErrorHandler::reenable();
    }

    /**
     * Store the object $value in OKAPI's cache, under the name of $key.
     *
     * Parameters:
     *
     * $key -- must be a string of max 57 characters in length (you can use
     *     md5(...) to shorten your keys). Use the same $key to retrieve your
     *     value later.
     *
     * $value -- can be any serializable PHP object. Currently there's no
     *     strict size limit, but try to keep it below 1 MB (for future
     *     compatibility with memcached).
     *
     * $timeout -- *maximum* time allowed to store the value, given in seconds
     *     (however, the value *can* be removed sooner than that, see the note
     *     below). Timeout can be also set to null, but you should avoid this,
     *     because such objects may clutter the cache unnecessarilly. (You must
     *     remember to remove them yourself!)
     *
     * Please note, that this cache is not guaranteed to be persistent.
     * Usually it is, but it can be emptied in case of emergency (low disk
     * space), or if we decide to switch the underlying cache engine in the
     * future (e.g. to memcached). Most of your values should be safe though.
     */
    public static function cache_set($key, $value, $timeout)
    {
        Cache::set("facade#".$key, $value, $timeout);
    }

    /** Same as `cache_set`, but works on many key->value pair at once. */
    public static function cache_set_many($dict, $timeout)
    {
        $prefixed_dict = array();
        foreach ($dict as $key => &$value_ref) {
            $prefixed_dict["facade#".$key] = &$value_ref;
        }
        Cache::set_many($prefixed_dict, $timeout);
    }

    /**
     * Retrieve object stored in cache under the name of $key. If object does
     * not exist or its timeout has expired, return null.
     */
    public static function cache_get($key)
    {
        return Cache::get("facade#".$key);
    }

    /** Same as `cache_get`, but it works on multiple keys at once. */
    public static function get_many($keys)
    {
        $prefixed_keys = array();
        foreach ($keys as $key) {
            $prefixed_keys[] = "facade#".$key;
        }
        $prefixed_result = Cache::get_many($prefixed_keys);
        $result = array();
        foreach ($prefixed_result as $prefixed_key => &$value_ref) {
            $result[substr($prefixed_key, 7)] = &$value_ref;
        }
        return $result;
    }

    /**
     * Delete the entry named $key from the cache.
     */
    public static function cache_delete($key)
    {
        Cache::delete("facade#".$key);
    }

    /** Same as `cache_delete`, but works on many keys at once. */
    public static function cache_delete_many($keys)
    {
        $prefixed_keys = array();
        foreach ($keys as $key) {
            $prefixed_keys[] = "facade#".$key;
        }
        Cache::delete_many($prefixed_keys);
    }
}

# (This comment is added here simply to debug OKAPI deployment.......)
