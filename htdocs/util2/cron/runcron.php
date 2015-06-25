#!/usr/bin/php -q
<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Create a cronjob to execute this file every minute
 *
 *  DO NOT RUN THIS JOB AS ROOT!
 *
 ***************************************************************************/

	$opt['rootpath'] = dirname(__FILE__) . '/../../';
	require($opt['rootpath'] . 'lib2/cli.inc.php');

	// test for user who runs the cronjob
	$processUser = posix_getpwuid(posix_geteuid());
	if ($processUser['name'] != $opt['cron']['username'])
		die("ERROR: runcron must be run by '" . $opt['cron']['username'] . "' but was called by '" . $processUser['name'] . "'\n".
		    "Try something like 'sudo -u ".$opt['cron']['username']." php runcron.php'.\n");

	// ensure that we do not run concurrently
	$process_sync = new ProcessSync('runcron');
	if ($process_sync->Enter())
	{
		// Run as system user, if possible.
		// This is relevant e.g. for publishing and for auto-archiving caches.
		if ($opt['logic']['systemuser']['user'] != '')
			if (!$login->system_login($opt['logic']['systemuser']['user']))
				die("ERROR: runcron system user login failed");

		$modules_dir = $opt['rootpath'] . 'util2/cron/modules/';

		if (count($argv) == 2 && !strstr("/", $argv[1]))
		{
			// run one job manually for debugging purpose
			$ignore_interval = true;
			require($modules_dir . $argv[1] . ".class.php");
		}
		else
		{
			$ignore_interval = false;
			$hDir = opendir($modules_dir);
			while (false !== ($file = readdir($hDir)))
				if (substr($file, -10) == '.class.php')
					require($modules_dir . $file);
		}

		$process_sync->Leave();
	}


function checkJob(&$job)
{
	global $ignore_interval;

	$max_last_run = strftime(DB_DATE_FORMAT, time() - ($ignore_interval ? 0 : $job->interval));
	$count = sqll_value("SELECT COUNT(*) FROM `sys_cron` WHERE `name`='&1' AND `last_run`>'&2' AND `last_run`<=NOW()", 0, $job->name, $max_last_run);
	if ($count != 1)
	{
		$job->run();
		sqll("INSERT INTO `sys_cron` (`name`, `last_run`) VALUES ('&1', NOW()) ON DUPLICATE KEY UPDATE `last_run`=NOW()", $job->name);
	}
}

?>