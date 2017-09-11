<?php

namespace okapi\core\CronJob;

use okapi\core\Db;
use okapi\core\Okapi;

/** Clean up the cache, once per day. */
class CacheCleanupCronJob extends Cron24Job
{
    public function get_scheduled_time() { return "04:20"; }
    public function execute()
    {
        # Delete all expired elements.

        Db::execute("
            delete from okapi_cache
            where expires < now()
        ");

        # Update the "score" stats.

        $multiplier = 0.08;  # Every day, all scores are multiplied by this.
        $limit = 0.01;  # When a score reaches this limit, the entry is deleted.

        # Every time the entry is read, its score is increased by 1. If an entry
        # is saved, but never read, it will be deleted after log(L,M) days
        # (log(0.01, 0.08) = ~2days). If an entry is read 1000000 times and then
        # never read anymore, it will be deleted after log(1000000/L, 1/M)
        # hours (log(1000000/0.01, 1/0.08) = ~7 days).

        Db::execute("
            update okapi_cache
            set score = score * '".Db::escape_string($multiplier)."'
            where score is not null
        ");
        Db::execute("
            update
                okapi_cache c,
                (
                    select cache_key, count(*) as count
                    from okapi_cache_reads
                    group by cache_key
                ) cr
            set c.score = c.score + cr.count
            where
                c.`key` = cr.cache_key
                and c.score is not null
        ");
        Db::execute("truncate okapi_cache_reads");

        # Delete elements with the lowest score. Entries which have been set
        # but never read will be removed after 2 days (0.08^2 < 0.01 < 0.08^1).

        Db::execute("
            delete from okapi_cache
            where
                score is not null
                and score < '".Db::escape_string($limit)."'
        ");
        Db::query("optimize table okapi_cache");

        # FileCache does not have an expiry date. We will delete all files older
        # than 24 hours.

        $dir = Okapi::get_var_dir();
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (strpos($file, "okapi_filecache_") === 0) {
                    if (filemtime("$dir/$file") < time() - 86400) {
                        unlink("$dir/$file");
                    }
                }
            }
            closedir($dh);
        }
    }
}
