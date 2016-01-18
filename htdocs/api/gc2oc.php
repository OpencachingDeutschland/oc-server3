<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  returns GC -> OC waypoint translation table, if available
 ***************************************************************************/

	$opt['rootpath'] = '../';
	require($opt['rootpath'] . 'lib2/web.inc.php');

	header('Content-type: text/html; charset=utf-8');

	/*
	 * caches.wp_gc_maintained is intended for map and search filtering and
	 * therefore e.g. does not contain WPs of active OC listings that are
	 * archived at GC. So it is not useful for GC->OC translation.
	 * If a better external source is available, we can use data from there.
	 *
	 * This may be refined by combining different sources and/or internal data.
	 * Also, it should be optimized by caching the list contents and allowing
	 * to request a single GC code or a list of GC codes.
	 *
	 * Note that it is not possible to create a 100% reliable translation table.
	 * There are many wrong GC wps in listings, and maintained tables always
	 * are incomplete. DO NOT RELY ON THE CORRECTNESS OF SUCH DATA!
	 */

	if ($opt['cron']['gcwp']['fulllist'])
		echo @file_get_contents($opt['cron']['gcwp']['fulllist']);

?>