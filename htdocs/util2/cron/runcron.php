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

	$opt['rootpath'] = '../../';

	// chdir to proper directory (needed for cronjobs)
	chdir(substr(realpath($_SERVER['PHP_SELF']), 0, strrpos(realpath($_SERVER['PHP_SELF']), '/')));

	require($opt['rootpath'] . 'lib2/cli.inc.php');

	// use posix pid-files to lock process 
	if (!CreatePidFile($opt['cron']['pidfile']))
	{
      CleanupAndExit($opt['cron']['pidfile'], "Another instance is running!"); 
      exit;
	}

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