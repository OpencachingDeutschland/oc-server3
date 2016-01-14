#!/usr/local/bin/php -q
<?php
/****************************************************************************
	 For license information see doc/license.txt

	 Unicode Reminder メモ

	 refresh the search-index of all modified descriptions

	 This is run as separate cronjob because it takes much time, may not
	 fit into the runcron frequency and should run at some time in the night.

 ****************************************************************************/

	// needs absolute rootpath because called as cronjob
	$opt['rootpath'] = dirname(__FILE__) . '/../../';

	require $opt['rootpath'] . 'lib2/cli.inc.php';
	require $opt['rootpath'] . 'lib2/search/ftsearch.inc.php';

	if (!cronjobs_enabled())
		exit;

	$process_sync = new ProcessSync('fill_searchindex');
	if ($process_sync->Enter())
	{
		ftsearch_refresh();
		$process_sync->Leave();
	}

?>
