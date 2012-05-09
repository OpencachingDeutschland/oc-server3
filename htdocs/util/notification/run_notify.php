#!/usr/local/bin/php -q
<?php
 /***************************************************************************
		./util/notification/run_notify.php
															-------------------
		begin                : August 25 2006
		copyright            : (C) 2006 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

 /***************************************************************************

		Unicode Reminder メモ

		Ggf. muss die Location des php-Binaries angepasst werden.

		Arbeitet die Tabelle `notify_waiting` ab und verschickt
		Benachrichtigungsmails ueber neue Caches.

	***************************************************************************/

	$rootpath = '../../';

	// chdir to proper directory (needed for cronjobs)
	chdir(substr(realpath($_SERVER['PHP_SELF']), 0, strrpos(realpath($_SERVER['PHP_SELF']), '/')));

	require_once($rootpath . 'lib/clicompatbase.inc.php');
	require_once('settings.inc.php');
	require_once($rootpath . 'lib/consts.inc.php');

	// use posix pid-files to lock process 
	if (!CreatePidFile($notifypid))
	{
	      CleanupAndExit($notifypid, "Another instance is running!"); 
	      exit;
	}

/* begin with some constants */

	$sDateformat = 'Y-m-d H:i:s';

/* end with some constants */

/* begin db connect */
	db_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}
/* end db connect */

  $rsNotify = sql("	SELECT  `notify_waiting`.`id`, `notify_waiting`.`cache_id`, `notify_waiting`.`type`,
				`user`.`username`,
				`user2`.`email`, `user2`.`username` as `recpname`, `user2`.`latitude` as `lat1`, `user2`.`longitude` as `lon1`, `user2`.`user_id` as `recid`,
				`caches`.`name` as `cachename`, `caches`.`date_hidden`, `caches`.`latitude` as `lat2`, `caches`.`longitude` as `lon2`, `caches`.`wp_oc`,
				`cache_type`.`de` as `cachetype`,
				`cache_size`.`de` as `cachesize`
			FROM `notify_waiting`, `caches`, `user`, `user` `user2`, `cache_type`, `cache_size`, `cache_status`
			WHERE `notify_waiting`.`cache_id`=`caches`.`cache_id`
			  AND `notify_waiting`.`user_id`=`user2`.`user_id`
			  AND `caches`.`user_id`=`user`.`user_id`
			  AND `caches`.`type`=`cache_type`.`id`
			  AND `caches`.`status`=`cache_status`.`id`
			  AND `caches`.`size`=`cache_size`.`id`
			  AND `cache_status`.`allow_user_view`=1");

  while($rNotify = sql_fetch_array($rsNotify))
  {
	sql("DELETE FROM `notify_waiting` WHERE `id` ='&1'", $rNotify['id']);
	process_new_cache($rNotify);
  }
  mysql_free_result($rsNotify);

  CleanupAndExit($notifypid); 

/* end send out everything that has to be sent */

function process_new_cache($notify)
{
	global $notify_text, $mailfrom, $mailsubject, $debug, $debug_mailto, $rootpath;

	//echo "process_new_cache(".$notify['id'].")\n";
	$fehler = false;

	// mail-template lesen
	switch($notify['type'])
	{
		case notify_new_cache: // Type: new cache
			$mailbody = read_file($rootpath . 'util/notification/notify_newcache.email');
			break;
		default:
			$fehler = true;
			break;
	}

	if(!$fehler)
	{
		$mailbody = mb_ereg_replace('{username}', $notify['recpname'], $mailbody);
		$mailbody = mb_ereg_replace('{date}', date('d.m.Y', strtotime($notify['date_hidden'])), $mailbody);
		$mailbody = mb_ereg_replace('{cacheid}', $notify['cache_id'], $mailbody);
		$mailbody = mb_ereg_replace('{wp_oc}', $notify['wp_oc'], $mailbody);
		$mailbody = mb_ereg_replace('{user}', $notify['username'], $mailbody);
		$mailbody = mb_ereg_replace('{cachename}', $notify['cachename'], $mailbody);
		$mailbody = mb_ereg_replace('{distance}', round(calcDistance($notify['lat1'], $notify['lon1'], $notify['lat2'], $notify['lon2'], 1), 1), $mailbody);
		$mailbody = mb_ereg_replace('{unit}', 'km', $mailbody);
		$mailbody = mb_ereg_replace('{bearing}', Bearing2Text(calcBearing($notify['lat1'], $notify['lon1'], $notify['lat2'], $notify['lon2'])), $mailbody);
		$mailbody = mb_ereg_replace('{cachetype}', $notify['cachetype'], $mailbody);
		$mailbody = mb_ereg_replace('{cachesize}', $notify['cachesize'], $mailbody);

		$subject = mb_ereg_replace('{cachename}', $notify['cachename'], $mailsubject);

		/* begin send out everything that has to be sent */
		$email_headers = 'From: "' . $mailfrom . '" <' . $mailfrom . '>';

		// mail versenden
		if ($debug == true)
		    $mailadr = $debug_mailto;
		else
		    $mailadr = $notify['email'];

		if (is_existent_maildomain(getToMailDomain($mailadr)))
			mb_send_mail($mailadr, $subject, $mailbody, $email_headers);
	}
	else
	{
		echo "Unbekannter Notification-Typ: " . $notify['type'] . "<br />";
	}

	// logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)
	logentry('notify_newcache', 5, $notify['recid'], $notify['cache_id'], 0, 'Sending mail to ' . $mailadr, array());

	return 0;
}

function is_existent_maildomain($domain)
{
	$smtp_serverlist = array();
	$smtp_serverweight = array();

	if (getmxrr($domain, $smtp_serverlist, $smtp_serverweight) != false)
		if (count($smtp_serverlist)>0)
			return true;

	// check if A exists
	$a = dns_get_record($domain, DNS_A);
	if (count($a) > 0)
		return true;

	return false;
}

function getToMailDomain($mail)
{
	if ($mail == '')
		return '';

	if (strrpos($mail, '@') === false)
		$domain = 'localhost';
	else
		$domain = substr($mail, strrpos($mail, '@') + 1);

	return $domain;
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
