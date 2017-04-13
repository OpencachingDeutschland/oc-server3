<?php

namespace okapi\cronjobs;

# If you want to debug ONE specific cronjob - see views/cron5.php file!

# If you want to debug the entire "system", then you should know that OKAPI
# uses two cache layers in order to decide if the cronjob has to be run,
# or even if the cronjobs.php file should be included. If you want to force OKAPI
# to run ALL cronjobs, then you should run these two queries on your database:
#
# - delete from okapi_cache where `key`='cron_schedule';
# - delete from okapi_vars where var='cron_nearest_event';
#
# Then, visit http://yoursite/okapi/cron5.

use Exception;
use okapi\BadRequest;
use okapi\Cache;
use okapi\Db;
use okapi\Locales;
use okapi\Okapi;
use okapi\OkapiExceptionHandler;
use okapi\OkapiInternalConsumer;
use okapi\OkapiInternalRequest;
use okapi\OkapiLock;
use okapi\OkapiServiceRunner;
use okapi\services\replicate\ReplicateCommon;
use okapi\Settings;


/**
 * Thrown in CronJobController::run_jobs when other thread is already
 * handling the jobs.
 */
class JobsAlreadyInProgress extends Exception {}

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
                    throw new Exception("Cronjob '".$cronjob->get_name()."' has an invalid (unsupported) type.");
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
        require_once($GLOBALS['rootpath'].'okapi/service_runner.php');

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
                    catch (Exception $e)
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
        require_once($GLOBALS['rootpath'].'okapi/service_runner.php');

        foreach (self::get_enabled_cronjobs() as $cronjob)
        {
            if (($cronjob->get_name() == $job_name) || ($cronjob->get_name() == "okapi\\cronjobs\\".$job_name))
            {
                $cronjob->execute();
                return;
            }
        }
        throw new Exception("CronJob $job_name not found.");
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
            throw new Exception("Could not reset schedule for job $job_name. $jon_name not found.");

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

/**
 * Deletes old Request Tokens and Nonces every 5 minutes. This is required for
 * OAuth to run safely.
 */
class OAuthCleanupCronJob extends PrerequestCronJob
{
    public function get_period() { return 300; } # 5 minutes
    public function execute()
    {
        if (Okapi::$data_store)
            Okapi::$data_store->cleanup();
    }
}

/** Clean up the saved search tables, every 10 minutes. */
class SearchSetsCleanerJob extends Cron5Job
{
    public function get_period() { return 600; }
    public function execute()
    {
        Db::execute("
            delete oss, osr
            from
                okapi_search_sets oss
                left join okapi_search_results osr
                    on oss.id = osr.set_id
            where
                date_add(oss.expires, interval 60 second) < now()
        ");
    }
}

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

/** Reads temporary (fast) stats-tables and reformats them into more permanent structures. */
class StatsWriterCronJob extends PrerequestCronJob
{
    public function get_period() { return 60; } # 1 minute
    public function execute()
    {
        if (Okapi::get_var('db_version', 0) + 0 < 32)
            return;
        Db::execute("lock tables okapi_stats_hourly write, okapi_stats_temp write;");
        try {
            $rs = Db::query("
                select
                    consumer_key,
                    user_id,
                    concat(substr(`datetime`, 1, 13), ':00:00') as period_start,
                    service_name,
                    calltype,
                    count(*) as calls,
                    sum(runtime) as runtime
                from okapi_stats_temp
                group by substr(`datetime`, 1, 13), consumer_key, user_id, service_name, calltype
            ");
            while ($row = Db::fetch_assoc($rs))
            {
                Db::execute("
                    insert into okapi_stats_hourly (consumer_key, user_id, period_start, service_name,
                        total_calls, http_calls, total_runtime, http_runtime)
                    values (
                        '".Db::escape_string($row['consumer_key'])."',
                        '".Db::escape_string($row['user_id'])."',
                        '".Db::escape_string($row['period_start'])."',
                        '".Db::escape_string($row['service_name'])."',
                        '".Db::escape_string($row['calls'])."',
                        '".Db::escape_string(($row['calltype'] == 'http') ? $row['calls'] : 0)."',
                        '".Db::escape_string($row['runtime'])."',
                        '".Db::escape_string(($row['calltype'] == 'http') ? $row['runtime'] : 0)."'
                    )
                    on duplicate key update
                        ".(($row['calltype'] == 'http') ? "
                            http_calls = http_calls + '".Db::escape_string($row['calls'])."',
                            http_runtime = http_runtime + '".Db::escape_string($row['runtime'])."',
                        " : "")."
                        total_calls = total_calls + '".Db::escape_string($row['calls'])."',
                        total_runtime = total_runtime + '".Db::escape_string($row['runtime'])."'
                ");
            }
            Db::execute("delete from okapi_stats_temp;");
        } finally {
            Db::execute("unlock tables;");
        }
    }
}

/**
 * Reads old hourly stats and reformats them into monthly stats.
 * See issue #361.
 */
class StatsCompressorCronJob extends Cron24Job
{
    public function get_scheduled_time() { return "04:20"; }
    public function execute()
    {
        if (Okapi::get_var('db_version', 0) + 0 < 94)
            return;

        # We will process a single month, every time we are being run.

        $month = Db::select_value("
            select substr(min(period_start), 1, 7)
            from okapi_stats_hourly ignore index (primary)
            where period_start < date_add(now(), interval -12 month)
        ");

        Db::execute("
            lock tables
                okapi_stats_monthly write,
                okapi_stats_hourly write,
                okapi_stats_monthly m read,
                okapi_stats_hourly h read;
        ");
        try {
            if ($month !== null) {

                # Update the monthly stats.

                Db::execute("
                    replace into okapi_stats_monthly (
                        consumer_key, user_id, period_start, service_name, total_calls, http_calls,
                        total_runtime, http_runtime
                    )
                    select
                        h.consumer_key,
                        h.user_id,
                        concat(substr(h.period_start, 1, 7), '-01 00:00:00') as period_start,
                        h.service_name,
                        if(m.total_calls is null, 0, m.total_calls) + sum(h.total_calls) as total_calls,
                        if(m.http_calls is null, 0, m.http_calls) + sum(h.http_calls) as http_calls,
                        if(m.total_runtime is null, 0, m.total_runtime) + sum(h.total_runtime) as total_runtime,
                        if(m.http_runtime is null, 0, m.http_runtime) + sum(h.http_runtime) as http_runtime
                    from
                        okapi_stats_hourly h
                        left join okapi_stats_monthly m
                            on m.consumer_key = h.consumer_key
                            and m.user_id = h.user_id
                            and substr(m.period_start, 1, 7) = substr(h.period_start, 1, 7)
                            and m.service_name = h.service_name
                    where substr(h.period_start, 1, 7) = '".Db::escape_string($month)."'
                    group by substr(h.period_start, 1, 7), h.consumer_key, h.user_id, h.service_name;
                ");

                # Remove the processed data.

                Db::execute("
                    delete from okapi_stats_hourly
                    where substr(period_start, 1, 7) = '".Db::escape_string($month)."'
                ");
            }
        } finally {
            Db::execute("unlock tables;");
        }
    }
}

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

/**
 * Twice an hour, upon request, checks if the test entry (previously put by
 * CheckCronTab1 job) is up-to-date (the one which was saved by CheckCronTab1 job).
 */
class CheckCronTab2 extends PrerequestCronJob
{
    public function get_period() { return 30 * 60; }
    public function execute()
    {
        $last_ping = Cache::get('crontab_last_ping');
        if ($last_ping === null)
            $last_ping = time() - 86400; # if not set, assume 1 day ago.
        if ($last_ping > time() - 3600)
        {
            # There was a ping during the last hour. Everything is okay.
            # Reset the counter and return.

            Cache::set('crontab_check_counter', 5, 86400);
            return;
        }

        # There was no ping. Decrement the counter. When reached zero, alert.

        $counter = Cache::get('crontab_check_counter');
        if ($counter === null)
            $counter = 5;
        $counter--;
        if ($counter > 0)
        {
            Cache::set('crontab_check_counter', $counter, 86400);
        }
        elseif ($counter == 0)
        {
            Okapi::mail_admins(
                "Crontab not working.",
                "Hello. OKAPI detected, that it's crontab is not working properly.\n".
                "Please check your configuration or contact OKAPI developers.\n\n".
                "This line should be present among your crontab entries:\n\n".
                "*/5 * * * * wget -O - -q -t 1 ".Settings::get('SITE_URL')."okapi/cron5\n\n".
                "If you're receiving this in Virtual Machine development environment, then\n".
                "ignore it. Probably you just paused (or switched off) your VM for some time\n".
                "(which would be considered an error in production environment)."
            );

            # Schedule the next admin-nagging. Each subsequent notification will be sent
            # with a greater delay.

            $since_last = time() - $last_ping;
            Cache::set('crontab_check_counter', (int)($since_last / $this->get_period()), 86400);
        }
    }
}

/**
 * Once per 5 minutes, searches for changes in the database and updates the changelog.
 */
class ChangeLogWriterJob extends Cron5Job
{
    public function get_period() { return 300; }
    public function execute()
    {
        require_once($GLOBALS['rootpath']."okapi/services/replicate/replicate_common.inc.php");
        ReplicateCommon::update_clog_table();
    }
}

/**
 * Once per day, compares all geocaches to the cached versions
 * kept by the 'replicate' module. If it finds any inconsistencies, it
 * emails the developers (such inconsistencies shouldn't happen) and it changes
 * the okapi_syncbase column accordingly. See issue 157.
 */
class ChangeLogCheckerJob extends Cron24Job
{
    public function get_scheduled_time() { return "04:20"; }
    public function execute()
    {
        require_once($GLOBALS['rootpath']."okapi/services/replicate/replicate_common.inc.php");
        $ignored_fields = array('url');
        ReplicateCommon::verify_clog_consistency(false, $ignored_fields);
    }
}

/**
 * Once per week, generates the fulldump archive.
 */
class FulldumpGeneratorJob extends Cron5Job
{
    public function get_period() { return 7*86400; }
    public function execute()
    {
        require_once($GLOBALS['rootpath']."okapi/services/replicate/replicate_common.inc.php");
        ReplicateCommon::generate_fulldump();
    }
}

/**
 * Listen for changelog updates. Update okapi_tile_caches accordingly.
 */
class TileTreeUpdater extends Cron5Job
{
    public function get_period() { return 5*60; }
    public function execute()
    {
        $current_clog_revision = Okapi::get_var('clog_revision', 0);
        $tiletree_revision = Okapi::get_var('clog_followup_revision', 0);
        if ($tiletree_revision === $current_clog_revision) {
            # No update necessary.
        } elseif ($tiletree_revision < $current_clog_revision) {
            require_once($GLOBALS['rootpath']."okapi/services/caches/map/replicate_listener.inc.php");
            if ($current_clog_revision - $tiletree_revision < 30000)  # In the middle of 2012, OCPL generated 30000 entries per week
            {
                for ($timeout = time() + 240; time() < $timeout; )  # Try to stop after 4 minutes.
                {
                    try {
                        $response = OkapiServiceRunner::call('services/replicate/changelog', new OkapiInternalRequest(
                            new OkapiInternalConsumer(), null, array('since' => $tiletree_revision)));
                        \okapi\services\caches\map\ReplicateListener::receive($response['changelog']);
                        $tiletree_revision = $response['revision'];
                        Okapi::set_var('clog_followup_revision', $tiletree_revision);
                        if (!$response['more'])
                            break;
                    } catch (BadRequest $e) {
                        # Invalid 'since' parameter? May happen when crontab was
                        # not working for more than 10 days. Or, just after OKAPI
                        # is installed (and this is the first time this cronjob
                        # if being run).

                        \okapi\services\caches\map\ReplicateListener::reset();
                        Okapi::set_var('clog_followup_revision', $current_clog_revision);
                        break;
                    }
                }
            } else {
                # Some kind of bigger update. Resetting TileTree might be a better option.
                \okapi\services\caches\map\ReplicateListener::reset();
                Okapi::set_var('clog_followup_revision', $current_clog_revision);
            }
        }
    }
}

/** Once per day, removes all revisions older than 10 days from okapi_clog table. */
class ChangeLogCleanerJob extends Cron24Job
{
    public function get_scheduled_time() { return "04:20"; }
    public function execute()
    {
        require_once($GLOBALS['rootpath']."okapi/services/replicate/replicate_common.inc.php");
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

/**
 * Once per week, sends simple OKAPI usage stats to the admins.
 */
class AdminStatsSender extends Cron5Job
{
    public function get_period() { return 7*86400; }
    public function execute()
    {
        ob_start();
        $apisrv_stats = OkapiServiceRunner::call('services/apisrv/stats', new OkapiInternalRequest(
            new OkapiInternalConsumer(), null, array()));
        $active_apps_count = Db::select_value("
            select count(distinct s.consumer_key)
            from
                okapi_stats_hourly s,
                okapi_consumers c
            where
                s.consumer_key = c.`key`
                and s.period_start > date_add(now(), interval -7 day)
        ");
        $weekly_stats = Db::select_row("
            select
                sum(s.http_calls) as total_http_calls,
                sum(s.http_runtime) as total_http_runtime
            from okapi_stats_hourly s
            where
                s.consumer_key != 'internal' -- we don't want to exclude 'anonymous' nor 'facade'
                and s.period_start > date_add(now(), interval -7 day)
        ");
        print "Hello! This is your weekly summary of OKAPI usage.\n\n";
        print "Apps active this week: ".$active_apps_count." out of ".$apisrv_stats['apps_count'].".\n";
        print "Total of ".$weekly_stats['total_http_calls']." requests were made (".sprintf("%01.1f", $weekly_stats['total_http_runtime'])." seconds).\n\n";
        $consumers = Db::select_all("
            select
                s.consumer_key,
                c.name,
                sum(s.http_calls) as http_calls,
                sum(s.http_runtime) as http_runtime
            from
                okapi_stats_hourly s
                left join okapi_consumers c
                    on s.consumer_key = c.`key`
            where s.period_start > date_add(now(), interval -7 day)
            group by s.consumer_key
            having sum(s.http_calls) > 0
            order by sum(s.http_calls) desc
        ");
        print "== Consumers ==\n\n";
        print "Consumer name                         Calls     Runtime\n";
        print "----------------------------------- ------- -----------\n";
        foreach ($consumers as $row)
        {
            $name = $row['name'];
            if ($row['consumer_key'] == 'anonymous')
                $name = "Anonymous (Level 0 Authentication)";
            elseif ($row['consumer_key'] == 'facade')
                $name = "Internal usage via Facade";
            if (mb_strlen($name) > 35)
                $name = mb_substr($name, 0, 32)."...";
            print self::mb_str_pad($name, 35, " ", STR_PAD_RIGHT);
            print str_pad($row['http_calls'], 8, " ", STR_PAD_LEFT);
            print str_pad(sprintf("%01.2f", $row['http_runtime']), 11, " ", STR_PAD_LEFT)."s\n";
        }
        print "\n";
        $methods = Db::select_all("
            select
                s.service_name,
                sum(s.http_calls) as http_calls,
                sum(s.http_runtime) as http_runtime
            from okapi_stats_hourly s
            where s.period_start > date_add(now(), interval -7 day)
            group by s.service_name
            having sum(s.http_calls) > 0
            order by sum(s.http_calls) desc
        ");
        print "== Methods ==\n\n";
        print "Service name                          Calls     Runtime      Avg\n";
        print "----------------------------------- ------- ----------- --------\n";
        foreach ($methods as $row)
        {
            $name = $row['service_name'];
            if (mb_strlen($name) > 35)
                $name = mb_substr($name, 0, 32)."...";
            print self::mb_str_pad($name, 35, " ", STR_PAD_RIGHT);
            print str_pad($row['http_calls'], 8, " ", STR_PAD_LEFT);
            print str_pad(sprintf("%01.2f", $row['http_runtime']), 11, " ", STR_PAD_LEFT)."s";
            print str_pad(sprintf("%01.4f", (
                ($row['http_calls'] > 0) ? ($row['http_runtime'] / $row['http_calls']) : 0
                )), 8, " ", STR_PAD_LEFT)."s\n";
        }
        print "\n";
        $oauth_users = Db::select_all("
            select
                c.name,
                count(*) as users
            from
                okapi_authorizations a,
                okapi_consumers c
            where a.consumer_key = c.`key`
            group by a.consumer_key
            having count(*) >= 5
            order by count(*) desc;
        ");
        print "== Current OAuth usage by Consumers with at least 5 users ==\n\n";
        print "Consumer name                         Users\n";
        print "----------------------------------- -------\n";
        foreach ($oauth_users as $row)
        {
            $name = $row['name'];
            if (mb_strlen($name) > 35)
                $name = mb_substr($name, 0, 32)."...";
            print self::mb_str_pad($name, 35, " ", STR_PAD_RIGHT);
            print str_pad($row['users'], 8, " ", STR_PAD_LEFT)."\n";
        }
        print "\n";

        print "This report includes requests from external consumers and those made via\n";
        print "Facade class (used by OC code). It does not include methods used by OKAPI\n";
        print "internally (i.e. while running cronjobs). Runtimes do not include HTTP\n";
        print "request handling overhead.\n";

        $message = ob_get_clean();
        Okapi::mail_admins("Weekly OKAPI usage report", $message);
    }

    private static function mb_str_pad($input, $pad_length, $pad_string, $pad_style)
    {
        return str_pad($input, strlen($input) - mb_strlen($input) + $pad_length,
            $pad_string, $pad_style);
    }
}

/**
 * Once per week, check if all required locales are installed. If not,
 * keep nagging the admins to do so.
 */
class LocaleChecker extends Cron5Job
{
    public function get_period() { return 7*86400; }
    public function execute()
    {
        require_once($GLOBALS['rootpath']."okapi/locale/locales.php");
        $required = Locales::get_required_locales();
        $installed = Locales::get_installed_locales();
        $missing = array();
        foreach ($required as $locale)
            if (!in_array($locale, $installed))
                $missing[] = $locale;
        if (count($missing) == 0)
            return; # okay!
        ob_start();
        print "Hi!\n\n";
        print "Your system is missing some locales required by OKAPI for proper\n";
        print "internationalization support. OKAPI comes with support for different\n";
        print "languages. This number (hopefully) will be growing.\n\n";
        print "Please take a moment to install the following missing locales:\n\n";
        $prefixes = array();
        foreach ($missing as $locale)
        {
            print " - ".$locale."\n";
            $prefixes[substr($locale, 0, 2)] = true;
        }
        $prefixes = array_keys($prefixes);
        print "\n";
        if ((count($missing) == 1) && ($missing[0] == 'POSIX'))
        {
            # I don't remember how to install POSIX, probably everyone has it anyway.
        }
        else
        {
            print "On Debian, try the following:\n\n";
            foreach ($prefixes as $lang)
            {
                if ($lang != 'PO') # Two first letters cut from POSIX.
                    print "sudo apt-get install language-pack-".$lang."-base\n";
            }
            print "sudo service apache2 restart\n";
            print "\n";
        }
        print "Thanks!\n\n";
        print "-- \n";
        print "OKAPI Team";
        Okapi::mail_admins("Additional setup needed: Missing locales.", ob_get_clean());
    }
}

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

class TokenRevokerJob extends Cron5Job
{
    public function get_period() { return 7200; }
    public function execute()
    {
        # Remove tokens of banned users (there's no need to remove authorizations).
        # See https://github.com/opencaching/okapi/issues/432

        Db::execute("
            delete t from
                okapi_tokens t,
                user u
            where
                t.user_id = u.user_id
                and u.is_active_flag != 1
        ");
    }
}
