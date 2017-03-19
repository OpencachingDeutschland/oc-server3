<?php
/***************************************************************************
 * For license information see doc/license.txt
 *
 * Workaround for OKAPI issue #246
 ***************************************************************************/

use OcLegacy\Cronjobs\OkapiCleanup;

checkJob(new OkapiCleanup(__DIR__ . '/../../../var/okapi'));
