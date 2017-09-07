<?php

namespace okapi\CronJob;

use okapi\Cache;
use okapi\Db;
use okapi\services\replicate\ReplicateCommon;

/** Once per day, removes all revisions older than 10 days from okapi_clog table. */
class ChangeLogCleanerJob extends Cron24Job
{
    public function get_scheduled_time() { return "04:20"; }
    public function execute()
    {
        $max_revision = ReplicateCommon::get_revision();
        $cache_key = 'clog_revisions_daily';
        $data = Cache::get($cache_key);
        if ($data == null)
            $data = array();
        $data[time()] = $max_revision;
        $new_min_revision = 1;
        $new_data = array();
        foreach ($data as $time => $r)
        {
            if ($time < time() - 10*86400)
                $new_min_revision = max($new_min_revision, $r);
            else
                $new_data[$time] = $r;
        }
        Db::execute("
            delete from okapi_clog
            where id < '".Db::escape_string($new_min_revision)."'
        ");
        Cache::set($cache_key, $new_data, 10*86400);
        Db::query("optimize table okapi_clog");
    }
}
