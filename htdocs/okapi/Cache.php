<?php

namespace okapi;

/** A data caching layer. For slow SQL queries etc. */
class Cache
{
    /**
     * Save object $value under the key $key. Store this object for
     * $timeout seconds. $key must be a string of max 64 characters in length.
     * $value might be any serializable PHP object.
     *
     * If $timeout is null, then the object will be treated as persistent
     * (the Cache will do its best to NEVER remove it).
     */
    public static function set($key, $value, $timeout)
    {
        if ($timeout == null)
        {
            # The current cache implementation is ALWAYS persistent, so we will
            # just replace it with a big value.
            $timeout = 100*365*86400;
        }
        Db::execute("
            replace into okapi_cache (`key`, value, expires)
            values (
                '".Db::escape_string($key)."',
                '".Db::escape_string(gzdeflate(serialize($value)))."',
                date_add(now(), interval '".Db::escape_string($timeout)."' second)
            );
        ");
    }

    /**
     * Scored version of set. Elements set up this way will expire when they're
     * not used.
     */
    public static function set_scored($key, $value)
    {
        Db::execute("
            replace into okapi_cache (`key`, value, expires, score)
            values (
                '".Db::escape_string($key)."',
                '".Db::escape_string(gzdeflate(serialize($value)))."',
                date_add(now(), interval 120 day),
                1.0
            );
        ");
    }

    /** Do 'set' on many keys at once. */
    public static function set_many($dict, $timeout)
    {
        if (count($dict) == 0)
            return;
        if ($timeout == null)
        {
            # The current cache implementation is ALWAYS persistent, so we will
            # just replace it with a big value.
            $timeout = 100*365*86400;
        }
        $entries_escaped = array();
        foreach ($dict as $key => $value)
        {
            $entries_escaped[] = "(
                '".Db::escape_string($key)."',
                '".Db::escape_string(gzdeflate(serialize($value)))."',
                date_add(now(), interval '".Db::escape_string($timeout)."' second)
            )";
        }
        Db::execute("
            replace into okapi_cache (`key`, value, expires)
            values ".implode(", ", $entries_escaped)."
        ");
    }

    /**
     * Retrieve object stored under the key $key. If object does not
     * exist or timeout expired, return null.
     */
    public static function get($key)
    {
        $rs = Db::query("
            select value, score
            from okapi_cache
            where
                `key` = '".Db::escape_string($key)."'
                and expires > now()
        ");
        list($blob, $score) = Db::fetch_row($rs);
        if (!$blob)
            return null;
        if ($score != null)  # Only non-null entries are scored.
        {
            Db::execute("
                insert into okapi_cache_reads (`cache_key`)
                values ('".Db::escape_string($key)."')
            ");
        }
        return unserialize(gzinflate($blob));
    }

    /** Do 'get' on many keys at once. */
    public static function get_many($keys)
    {
        $dict = array();
        $rs = Db::query("
            select `key`, value
            from okapi_cache
            where
                `key` in ('".implode("','", array_map('\okapi\Db::escape_string', $keys))."')
                and expires > now()
        ");
        while ($row = Db::fetch_assoc($rs))
        {
            try
            {
                $dict[$row['key']] = unserialize(gzinflate($row['value']));
            }
            catch (ErrorException $e)
            {
                unset($dict[$row['key']]);
                Okapi::mail_admins("Debug: Unserialize error",
                    "Could not unserialize key '".$row['key']."' from Cache.\n".
                    "Probably something REALLY big was put there and data has been truncated.\n".
                    "Consider upgrading cache table to LONGBLOB.\n\n".
                    "Length of data, compressed: ".strlen($row['value']));
            }
        }
        if (count($dict) < count($keys))
            foreach ($keys as $key)
                if (!isset($dict[$key]))
                    $dict[$key] = null;
        return $dict;
    }

    /**
     * Delete key $key from the cache.
     */
    public static function delete($key)
    {
        self::delete_many(array($key));
    }

    /** Do 'delete' on many keys at once. */
    public static function delete_many($keys)
    {
        if (count($keys) == 0)
            return;
        Db::execute("
            delete from okapi_cache
            where `key` in ('".implode("','", array_map('\okapi\Db::escape_string', $keys))."')
        ");
    }
}
