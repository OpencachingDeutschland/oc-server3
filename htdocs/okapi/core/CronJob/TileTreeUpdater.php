<?php

namespace okapi\core\CronJob;

use okapi\core\Consumer\OkapiInternalConsumer;
use okapi\core\Exception\BadRequest;
use okapi\core\Okapi;
use okapi\core\OkapiServiceRunner;
use okapi\core\Request\OkapiInternalRequest;
use okapi\services\caches\map\ReplicateListener;

/**
 * Listen for changelog updates. Update okapi_tile_caches accordingly.
 */
class TileTreeUpdater extends Cron5Job
{
    public function get_period() { return 5*60; }
    public function execute()
    {
        $current_clog_revision = Okapi::get_var('clog_revision', 0);
        $tiletree_revision = Okapi::get_var('clog_followup_revision', 0);
        if ($tiletree_revision === $current_clog_revision) {
            # No update necessary.
        } elseif ($tiletree_revision < $current_clog_revision) {
            if ($current_clog_revision - $tiletree_revision < 30000)  # In the middle of 2012, OCPL generated 30000 entries per week
            {
                for ($timeout = time() + 240; time() < $timeout; )  # Try to stop after 4 minutes.
                {
                    try {
                        $response = OkapiServiceRunner::call('services/replicate/changelog', new OkapiInternalRequest(
                            new OkapiInternalConsumer(), null, array('since' => $tiletree_revision)));
                        ReplicateListener::receive($response['changelog']);
                        $tiletree_revision = $response['revision'];
                        Okapi::set_var('clog_followup_revision', $tiletree_revision);
                        if (!$response['more'])
                            break;
                    } catch (BadRequest $e) {
                        # Invalid 'since' parameter? May happen when crontab was
                        # not working for more than 10 days. Or, just after OKAPI
                        # is installed (and this is the first time this cronjob
                        # if being run).

                        ReplicateListener::reset();
                        Okapi::set_var('clog_followup_revision', $current_clog_revision);
                        break;
                    }
                }
            } else {
                # Some kind of bigger update. Resetting TileTree might be a better option.
                ReplicateListener::reset();
                Okapi::set_var('clog_followup_revision', $current_clog_revision);
            }
        }
    }
}
