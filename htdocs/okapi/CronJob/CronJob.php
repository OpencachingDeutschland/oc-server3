<?php

namespace okapi\CronJob;

abstract class CronJob
{
    /** Run the job. */
    public abstract function execute();

    /** Get unique name for this cronjob. */
    public function get_name() { return get_class($this); }

    /**
     * Get the type of this cronjob. Currently there are two: 'pre-request'
     * and 'cron-5'. The first can be executed before every request, the second
     * is executed from system's crontab, as a separate process. 'cron-5' can be
     * executed every 5 minutes, or every 10, 15 etc. minutes. 'pre-request'
     * can be executed before each HTTP request, AND additionally every 5 minutes
     * (before 'cron-5' runs).
     */
    public abstract function get_type();

    /**
     * Get the next scheduled run (unix timestamp). This method will be called
     * ONLY ONCE per cronjob execution, directly AFTER the job was run.
     * (Scheduling the *first* run of `execute` method is out of this method's
     * control, but you can prevent the actual job from being executed by using
     * custom conditions in your `execute` method.)
     */
    public abstract function get_next_scheduled_run($previously_scheduled_run);
}
