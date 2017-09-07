<?php

namespace okapi\CronJob;

use okapi\Okapi;

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
