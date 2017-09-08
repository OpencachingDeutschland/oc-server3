<?php

namespace okapi\core\CronJob;

use okapi\services\replicate\ReplicateCommon;

/**
 * Once per week, generates the fulldump archive.
 */
class FulldumpGeneratorJob extends Cron5Job
{
    public function get_period() { return 7*86400; }
    public function execute()
    {
        ReplicateCommon::generate_fulldump();
    }
}
