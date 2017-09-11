<?php

namespace okapi\core\CronJob;

use okapi\services\replicate\ReplicateCommon;

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
        $ignored_fields = array('url');
        ReplicateCommon::verify_clog_consistency(false, $ignored_fields);
    }
}
