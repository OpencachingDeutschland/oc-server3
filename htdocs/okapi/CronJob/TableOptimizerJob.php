<?php

namespace okapi\CronJob;

use okapi\Db;

/** Once per day, optimize certain MySQL tables. */
class TableOptimizerJob extends Cron24Job
{
    public function get_scheduled_time() { return "04:20"; }
    public function execute()
    {
        Db::query("optimize table okapi_tile_caches");
        Db::query("optimize table okapi_tile_status");
    }
}
