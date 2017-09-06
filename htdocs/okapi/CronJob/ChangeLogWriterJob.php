<?php

namespace okapi\CronJob;

use okapi\services\replicate\ReplicateCommon;

/**
 * Once per 5 minutes, searches for changes in the database and updates the changelog.
 */
class ChangeLogWriterJob extends Cron5Job
{
    public function get_period() { return 300; }
    public function execute()
    {
        ReplicateCommon::update_clog_table();
    }
}
