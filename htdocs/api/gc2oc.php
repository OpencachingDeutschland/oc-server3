<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  returns GC -> OC waypoint translation table, if available
 *
 *  file format: gcwp,ocwp,checked
 *  checked = 1 if the GC waypoint has been verified,
 *            0 if it is directly taken from an OC cache listing.
 *
 ***************************************************************************/

	$opt['rootpath'] = '../';
	require($opt['rootpath'] . 'lib2/web.inc.php');

	header('Content-type: application/x-gzip');
	header('Content-Disposition: attachment; filename=gc2oc.gz');

	/*
	 * caches.wp_gc_maintained is intended for map and search filtering and
	 * therefore e.g. does not contain WPs of active OC listings that are
	 * archived at GC. So it is not useful for GC->OC translation.
	 * If a better external source is available, we can use data from there.
	 *
	 * This may be refined by combining different sources and/or internal data.
	 * Also, it may be optimized by allowing to request a single GC code or a
	 * list of GC codes.
	 *
	 * Note that it is not possible to create a 100% reliable translation table.
	 * There are many wrong GC wps in listings, and maintained tables always
	 * are incomplete. DO NOT RELY ON THE CORRECTNESS OF SUCH DATA!
	 */

	if ($opt['cron']['gcwp']['fulllist'])
	{
		$gzipped_data = '';
		$cachefile = '../cache2/gc2oc.gz';
		if (!file_exists($cachefile) || time() - filemtime($cachefile) > 3600)
		{
			$gc2oc = file_get_contents($opt['cron']['gcwp']['fulllist']);
			if ($gc2oc)
			{
				$gzipped_data = gzencode($gc2oc);
				file_put_contents($cachefile, $gzipped_data);
			}
		}

		if (!$gzipped_data && file_exists($cachefile))
			$gzipped_data = file_get_contents($cachefile);

		echo $gzipped_data;
	}
?>
