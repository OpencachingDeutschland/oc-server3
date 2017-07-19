<?php
/***************************************************************************
 * For license information see LICENSE.md
 *
 * Publish new geocaches that are marked for timed publish
 ***************************************************************************/

use OcLegacy\Cronjobs\PublishCaches;

checkJob(new PublishCaches());
