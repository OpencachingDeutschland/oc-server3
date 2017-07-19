<?php
/***************************************************************************
 * For license information see LICENSE.md
 *
 * Workaround for OKAPI issue #246
 ***************************************************************************/

use OcLegacy\Cronjobs\OkapiCleanup;

checkJob(new OkapiCleanup(__DIR__ . '/../../../var/okapi'));
