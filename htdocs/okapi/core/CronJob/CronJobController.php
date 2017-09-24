<?php

namespace okapi\core\CronJob;

use okapi\core\Cache;
use okapi\core\Exception\JobsAlreadyInProgress;
use okapi\core\Exception\OkapiExceptionHandler;
use okapi\core\Okapi;
use okapi\core\OkapiLock;

class CronJobController
{
    /** Return the list of all currently enabled cronjobs. */
    public static function get_enabled_cronjobs()
    {
        static $cache = null;
        if ($cache == null)
        {
            $cache = array(
                new OAuthCleanupCronJob(),
                new CacheCleanupCronJob(),
                new StatsWriterCronJob(),
                new StatsCompressorCronJob(),
                new CheckCronTab1(),
                new CheckCronTab2(),
                new ChangeLogWriterJob(),
                new ChangeLogCleanerJob(),
                new ChangeLogCheckerJob(),
                new AdminStatsSender(),
                new LocaleChecker(),
                new FulldumpGeneratorJob(),
                new TileTreeUpdater(),
                new SearchSetsCleanerJob(),
                new TableOptimizerJob(),
                new TokenRevokerJob(),
            );
            foreach ($cache as $cronjob)
                if (!in_array($cronjob->get_type(), array('pre-request', 'cron-5')))
                    throw new \Exception("Cronjob '".$cronjob->get_name()."' has an invalid (unsupported) type.");
        }
        return $cache;
    }

    /**
     * Execute all scheduled cronjobs of given type, reschedule, and return
     * UNIX timestamp of the nearest scheduled event.
     *
     * If $wait is false, then it may throw JobsAlreadyInProgress if another
     * thread is currently executing these type of jobs.
     */
    public static function run_jobs($type, $wait=false)
    {
        # We don't want other cronjobs of the same time to run simultanously.
        $lock = OkapiLock::get('cronjobs-'.$type);
        if (!$lock->try_acquire()) {
            if ($wait) {
                $lock->acquire();
            } else {
                throw new JobsAlreadyInProgress();
            }
        }

        $schedule = Cache::get("cron_schedule");
        if ($schedule == null)
            $schedule = array();
        foreach (self::get_enabled_cronjobs() as $cronjob)
        {
            $name = $cronjob->get_name();
            if ((!isset($schedule[$name])) || ($schedule[$name] <= time()))
            {
                if ($cronjob->get_type() != $type)
                {
                    $next_run = isset($schedule[$name]) ? $schedule[$name] : (time() - 1);
                }
                else
                {
                    try
                    {
                        $cronjob->execute();
                    }
                    catch (\Exception $e)
                    {
                        Okapi::mail_admins("Cronjob error: ".$cronjob->get_name(),
                            OkapiExceptionHandler::get_exception_info($e));
                    }
                    $next_run = $cronjob->get_next_scheduled_run(isset($schedule[$name]) ? $schedule[$name] : time());
                }
                $schedule[$name] = $next_run;
                Cache::set("cron_schedule", $schedule, 30*86400);
            }
        }

        # Remove "stale" schedule keys (those which are no longer declared).

        $fixed_schedule = array();
        foreach (self::get_enabled_cronjobs() as $cronjob)
        {
            $name = $cronjob->get_name();
            $fixed_schedule[$name] = $schedule[$name];
        }
        unset($schedule);

        # Return the nearest scheduled event time.

        $nearest = time() + 3600;
        foreach ($fixed_schedule as $name => $time)
            if ($time < $nearest)
                $nearest = $time;
        Cache::set("cron_schedule", $fixed_schedule, 30*86400);
        $lock->release();
        return $nearest;
    }

    /**
     * Force a specified cronjob to run. Throw an exception if cronjob not found.
     * $job_name mast equal one of the names returned by ->get_name() method.
     */
    public static function force_run($job_name)
    {
        foreach (self::get_enabled_cronjobs() as $cronjob)
        {
            if (($cronjob->get_name() == $job_name) || ($cronjob->get_name() == "okapi\\CronJob\\".$job_name))
            {
                $cronjob->execute();
                return;
            }
        }
        throw new \Exception("CronJob $job_name not found.");
    }

    /**
     * Reset the schedule of a specified cronjob. This will force the job to
     * run on nearest occasion (but not NOW).
     */
    public static function reset_job_schedule($job_name)
    {
        $thejob = null;
        foreach (self::get_enabled_cronjobs() as $tmp)
            if (($tmp->get_name() == $job_name) || ($tmp->get_name() == "okapi\\cronjobs\\".$job_name))
                $thejob = $tmp;
        if ($thejob == null)
            throw new \Exception("Could not reset schedule for job $job_name. $job_name not found.");

        # We have to acquire lock on the schedule. This might take some time if cron-5 jobs are
        # currently being run.

        $type = $thejob->get_type();
        $lock = OkapiLock::get('cronjobs-'.$type);
        $lock->acquire();

        $schedule = Cache::get("cron_schedule");
        if ($schedule != null)
        {
            if (isset($schedule[$thejob->get_name()]))
                unset($schedule[$thejob->get_name()]);
            Cache::set("cron_schedule", $schedule, 30*86400);
        }

        $lock->release();
    }
}
