<?php

namespace okapi\CronJob;

use okapi\Db;
use okapi\Okapi;

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
