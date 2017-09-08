<?php

namespace okapi\core\CronJob;

/**
 * CronJob which is run before requests. All implenatations specify a *minimum* time period
 * that should pass between running a job. If job was run at time X, then it will
 * be run again just before the first request made after X+period. The job also
 * will be run after server gets updated.
 */
abstract class PrerequestCronJob extends CronJob
{
    /**
     * Always returns 'pre-request'.
     */
    public final function get_type() { return 'pre-request'; }

    /**
     * Return number of seconds - a *minimum* time period that should pass between
     * running the job.
     */
    public abstract function get_period();

    public function get_next_scheduled_run($previously_scheduled_run)
    {
        return time() + $this->get_period();
    }
}
