<?php
/***************************************************************************
 * For license information see doc/license.txt
 *
 * Publish new geocaches that are marked for timed publish
 ***************************************************************************/

use OcLegacy\Cronjobs\PublishCaches;

checkJob(new PublishCaches());
