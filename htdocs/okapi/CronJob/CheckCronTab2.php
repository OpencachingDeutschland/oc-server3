<?php

namespace okapi\CronJob;

use okapi\Cache;
use okapi\Okapi;

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
