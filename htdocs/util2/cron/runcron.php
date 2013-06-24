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

	// use posix pid-files to lock process 
	if (!CreatePidFile($opt['cron']['pidfile']))
	{
		CleanupAndExit($opt['cron']['pidfile'], "Another instance is running!");
		exit;
	}

	// Run as system user, if possible.
	// This is relevant e.g. for publishing and for auto-archiving caches.
	if ($opt['logic']['systemuser']['user'] != '')
		if (!$login->system_login($opt['logic']['systemuser']['user']))
		  die("ERROR: runcron system user login failed");

	$modules_dir = $opt['rootpath'] . 'util2/cron/modules/';

	$hDir = opendir($modules_dir);
	while (false !== ($file = readdir($hDir)))
		if (substr($file, -10) == '.class.php')
			require($modules_dir . $file);

  CleanupAndExit($opt['cron']['pidfile']); 

function checkJob(&$job)
{
	$last_run = strftime(DB_DATE_FORMAT, time() - $job->interval);
	$count = sqll_value("SELECT COUNT(*) FROM `sys_cron` WHERE `name`='&1' AND `last_run`>'&2' AND `last_run`<=NOW()", 0, $job->name, $last_run);
	if ($count != 1)
	{
		$job->run();
		sqll("INSERT INTO `sys_cron` (`name`, `last_run`) VALUES ('&1', NOW()) ON DUPLICATE KEY UPDATE `last_run`=NOW()", $job->name);
	}
}

// 
// checks if other instance is running, creates pid-file for locking 
// 
function CreatePidFile($PidFile)
{
    if(!CheckDaemon($PidFile))
    {
        return false;
    }

    if(file_exists($PidFile))
    {
        echo "Error: Pidfile (".$PidFile.") already present at ".__FILE__.":".__LINE__."!\n";
        return false;
    }
    else 
    {
        if($pidfile = @fopen($PidFile, "w")) 
        {
            fputs($pidfile, posix_getpid()); 
            fclose($pidfile); 
            return true; 
        }
        else 
        {
            echo "can't create Pidfile $PidFile at ".__FILE__.":".__LINE__."!\n"; 
            return false; 
        }
    }
} 

// 
// checks if other instance of process is running.. 
// 
function CheckDaemon($PidFile) 
{ 
    if($pidfile = @fopen($PidFile, "r")) 
    { 
        $pid_daemon = fgets($pidfile, 20); 
        fclose($pidfile); 

        $pid_daemon = (int)$pid_daemon; 

        // process running? 
        if(posix_kill($pid_daemon, 0)) 
        { 
            // yes, good bye 
            echo "Error: process already running with pid=$pid_daemon!\n"; 
            false; 
        } 
        else 
        { 
            // no, remove pid_file 
            echo "process not running, removing old pid_file (".$PidFile.")\n"; 
            unlink($PidFile); 
            return true; 
        } 
    } 
    else 
    { 
        return true; 
    } 
} 

// 
// deletes pid-file 
// 
function CleanupAndExit($PidFile, $message = false) 
{ 
    if($pidfile = @fopen($PidFile, "r")) 
    { 
        $pid = fgets($pidfile, 20); 
        fclose($pidfile); 
        if($pid == posix_getpid()) 
            unlink($PidFile); 
    } 
    else 
    { 
        echo "Error: can't delete own pidfile (".$PidFile.") at ".__FILE__.":".__LINE__."!\n"; 
    } 

    if($message) 
    { 
      echo $message . "\n"; 
    } 
}
?>