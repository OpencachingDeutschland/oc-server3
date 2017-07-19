<?php
/***************************************************************************
 * For license information see LICENSE.md
 *
 *  Generate sitemap.xml as specified by http://www.sitemaps.org
 *  And send ping to search engines
 ***************************************************************************/

use OcLegacy\Cronjobs\SiteMaps;

checkJob(new SiteMaps());
