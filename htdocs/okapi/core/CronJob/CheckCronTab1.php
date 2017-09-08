<?php

namespace okapi\core\CronJob;

use okapi\core\Cache;

/**
 * Once per hour, puts a test entry in the database. This is to make sure
 * that crontab is set up properly.
 */
class CheckCronTab1 extends Cron5Job
{
    public function get_period() { return 3600; }
    public function execute()
    {
        Cache::set('crontab_last_ping', time(), 86400);
    }
}
