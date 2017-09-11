<?php

namespace okapi\core\CronJob;

/**
 * A bit similar to Cron5Job with get_period=24h, but this one is trying to be
 * executed exactly at the given hour. Useful for long-running jobs which
 * should always be executed at night.
 *
 * Note, that the first execution will occur just after if has been registered.
 * You can control the time of all further executions, but not the first one.
 */
abstract class Cron24Job extends CronJob
{
    /**
     * Always returns 'cron-5'.
     */
    public final function get_type() { return 'cron-5'; }

    /**
     * Return "hh:mm" (must be exactly 5 chars!) - the (local) time of day at
     * which the cronjob should be executed.
     */
    public abstract function get_scheduled_time();

    public function get_next_scheduled_run($previously_scheduled_run)
    {
        $datestr = date('c'); // e.g. "2004-02-12T15:19:21+02:00"
        $datestr = (
            substr($datestr, 0, 11) . $this->get_scheduled_time() . ":00" .
            substr($datestr, 19)
        );
        $t = strtotime($datestr);
        if ($t <= time()) {
            $t += 86400;
        }
        return $t;
    }
}
