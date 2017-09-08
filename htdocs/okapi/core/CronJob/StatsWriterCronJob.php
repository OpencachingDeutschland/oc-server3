<?php

namespace okapi\core\CronJob;

use okapi\core\Db;
use okapi\core\Okapi;

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
