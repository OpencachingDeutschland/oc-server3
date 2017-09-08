<?php

namespace okapi\core\CronJob;

use okapi\core\Consumer\OkapiInternalConsumer;
use okapi\core\Db;
use okapi\core\Okapi;
use okapi\core\OkapiServiceRunner;
use okapi\core\Request\OkapiInternalRequest;

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
