<?php

namespace okapi\core\CronJob;

use okapi\core\Db;

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
