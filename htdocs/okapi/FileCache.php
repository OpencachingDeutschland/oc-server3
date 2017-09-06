<?php

namespace okapi;

/**
 * Sometimes it is desirable to get the cached contents in a file,
 * instead in a string (i.e. for imagecreatefromgd2). In such cases, you
 * may use this class instead of the Cache class.
 */
class FileCache
{
    public static function get_file_path($key)
    {
        $filename = Okapi::get_var_dir()."/okapi_filecache_".md5($key);
        if (!file_exists($filename))
            return null;
        return $filename;
    }

    /**
     * Note, there is no $timeout (time to live) parameter. Currently,
     * OKAPI will delete every old file after certain amount of time.
     * See CacheCleanupCronJob for details.
     */
    public static function set($key, $value)
    {
        $filename = Okapi::get_var_dir()."/okapi_filecache_".md5($key);
        file_put_contents($filename, $value);
        return $filename;
    }
}
