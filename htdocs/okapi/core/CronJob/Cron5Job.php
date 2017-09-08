<?php

namespace okapi\core\CronJob;

/**
 * CronJob which is run from crontab. It may be invoked every 5 minutes, or
 * every 10, 15 etc. Hence the name - cron-5.
 */
abstract class Cron5Job extends CronJob
{
    /**
     * Always returns 'cron-5'.
     */
    public final function get_type() { return 'cron-5'; }

    /**
     * Return number of seconds - period of time after which cronjob execution
     * should be repeated. This should be dividable be 300 (5 minutes).
     */
    public abstract function get_period();

    public function get_next_scheduled_run($previously_scheduled_run)
    {
        $t = time() + $this->get_period();
        return ($t - ($t % 300));
    }
}
