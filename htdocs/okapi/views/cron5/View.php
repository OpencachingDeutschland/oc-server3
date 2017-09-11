<?php

namespace okapi\views\cron5;

use okapi\core\Okapi;

/**
 * This is an entry point for system's crontab. System's crontab will be
 * running this view every 5 minutes.
 */
class View
{
    public static function call()
    {
        ignore_user_abort(true);
        set_time_limit(0);
        header("Content-Type: text/plain; charset=utf-8");

        # Uncomment the following if you want to debug a specific cronjob. It will be run
        # every 5 minutes (run 'crontab -e' to change or disable it) AND additionally
        # every time you visit http(s)://yoursite/okapi/cron5

        # require_once "okapi/cronjobs.php"; CronJobController::force_run("JOB_NAME"); die();

        Okapi::execute_cron5_cronjobs();
    }
}
