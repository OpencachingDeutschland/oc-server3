#!/usr/local/bin/php -q
<?php
 /***************************************************************************
													./util/watchlist/runwatch.php
															-------------------
		begin                : Sat September 3 2005
		copyright            : (C) 2005 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

 /***************************************************************************
		
		Unicode Reminder ..

		Ggf. muss die Location des php-Binaries angepasst werden.
		
		Dieses Script sucht nach neuen Logs und Caches, die von Usern beobachtet
		werden und verschickt dann die Emails.
		
	***************************************************************************/

  $rootpath = '/mnt/opencaching/www.opencaching.de/code/htdocs/';
	require_once('/mnt/opencaching/www.opencaching.de/code/htdocs/lib/clicompatbase.inc.php');
  require_once('settings.inc.php');
	require_once('/mnt/opencaching/www.opencaching.de/code/htdocs/lib/consts.inc.php');
	require_once($rootpath . 'lib2/html2text.class.php');

	// use posix pid-files to lock process 
	if (!CreatePidFile($watchpid))
	{
		CleanupAndExit($watchpid, "Another instance is running!"); 
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
  
/* begin owner notifies */
  $rsNewLogs = sql("SELECT cache_logs.id log_id, caches.user_id user_id FROM cache_logs, caches WHERE cache_logs.cache_id=caches.cache_id AND cache_logs.owner_notified=0");
  for ($i = 0; $i < mysql_num_rows($rsNewLogs); $i++)
  {
		$rNewLog = sql_fetch_array($rsNewLogs);
		
		$rsNotified = sql("SELECT `id` FROM watches_notified WHERE user_id='&1' AND object_id='&2' AND object_type=1", $rNewLog['user_id'], $rNewLog['log_id']);
		if (mysql_num_rows($rsNotified) == 0)
		{
			// Benachrichtigung speichern
			sql("INSERT IGNORE INTO `watches_notified` (`user_id`, `object_id`, `object_type`, `date_created`) VALUES ('&1', '&2', 1, NOW())", $rNewLog['user_id'], $rNewLog['log_id']);
		
			process_owner_log($rNewLog['user_id'], $rNewLog['log_id']);
		}
		mysql_free_result($rsNotified);
		
		sql("UPDATE cache_logs SET owner_notified=1 WHERE id='&1'", $rNewLog['log_id']);
  }
  mysql_free_result($rsNewLogs);
/* end owner notifies */

/* begin cache_watches */
  $rscw = sql("SELECT `watches_logqueue`.`log_id`, `watches_logqueue`.`user_id`, `cache_logs`.`cache_id` 
                 FROM `watches_logqueue` 
           INNER JOIN `cache_logs` ON `watches_logqueue`.`log_id`=`cache_logs`.`id`");
  while($rcw = mysql_fetch_assoc($rscw))
  {
    // Benachrichtigung speichern
    sql("INSERT IGNORE INTO `watches_notified` (`user_id`, `object_id`, `object_type`, `date_created`) VALUES ('&1', '&2', 1, NOW())", $rcw['user_id'], $rcw['log_id']);
    process_log_watch($rcw['user_id'], $rcw['log_id']);

    sql("DELETE FROM `watches_logqueue` WHERE `log_id`='&1' AND `user_id`='&2'", $rcw['log_id'], $rcw['user_id']);
  }
  mysql_free_result($rscw);
/* end cache_watches */

/* begin send out everything that has to be sent */
	
	$email_headers = 'From: "' . $mailfrom . '" <' . $mailfrom . '>';
	
	$rsUsers = sql('SELECT `user`.`user_id`, `user`.`username`, `user`.`email`, `user`.`watchmail_mode`, `user`.`watchmail_hour`, `user`.`watchmail_day`, `user`.`watchmail_nextmail` FROM `user` INNER JOIN `watches_waiting` ON `user`.`user_id`=`watches_waiting`.`user_id` WHERE `user`.`watchmail_nextmail`<NOW()');
	for ($i = 0; $i < mysql_num_rows($rsUsers); $i++)
	{
		$rUser = sql_fetch_array($rsUsers);

		if ($rUser['watchmail_nextmail'] != '0000-00-00 00:00:00')
		{
			$rsWatches = sql("SELECT COUNT(*) count FROM watches_waiting WHERE user_id='&1'", $rUser['user_id']);
			if (mysql_num_rows($rsWatches) > 0)
			{
				$r = sql_fetch_array($rsWatches);
				if ($r['count'] > 0)
				{
					// ok, eine mail ist fäig
					$mailbody = read_file('/mnt/opencaching/www.opencaching.de/code/htdocs/util/watchlist/watchlist.email');
					$mailbody = mb_ereg_replace('{username}', $rUser['username'], $mailbody);

					$rsWatchesOwner = sql("SELECT id, watchtext FROM watches_waiting WHERE user_id='&1' AND watchtype=1 ORDER BY id DESC", $rUser['user_id']);
					if (mysql_num_rows($rsWatchesOwner) > 0)
					{
						$logtexts = '';
						for ($j = 0; $j < mysql_num_rows($rsWatchesOwner); $j++)
						{
							$rWatch = sql_fetch_array($rsWatchesOwner);
							$logtexts .= $rWatch['watchtext'];
						}
						
						while ((mb_substr($logtexts, -1) == "\n") || (mb_substr($logtexts, -1) == "\r"))
							$logtexts = mb_substr($logtexts, 0, mb_strlen($logtexts) - 1);
						
						$mailbody = mb_ereg_replace('{ownerlogs}', $logtexts, $mailbody);
					}
					else
					{
						$mailbody = mb_ereg_replace('{ownerlogs}', $nologs, $mailbody);
					}
					mysql_free_result($rsWatchesOwner);
					
					$rsWatchesLog = sql("SELECT id, watchtext FROM watches_waiting WHERE user_id='&1' AND watchtype=2 ORDER BY id DESC", $rUser['user_id']);
					if (mysql_num_rows($rsWatchesLog) > 0)
					{
						$logtexts = '';
						for ($j = 0; $j < mysql_num_rows($rsWatchesLog); $j++)
						{
							$rWatch = sql_fetch_array($rsWatchesLog);
							$logtexts .= $rWatch['watchtext'];
						}
						
						while ((mb_substr($logtexts, -1) == "\n") || (mb_substr($logtexts, -1) == "\r"))
							$logtexts = mb_substr($logtexts, 0, mb_strlen($logtexts) - 1);
						
						$mailbody = mb_ereg_replace('{watchlogs}', $logtexts, $mailbody);
					}
					else
					{
						$mailbody = mb_ereg_replace('{watchlogs}', $nologs, $mailbody);
					}
					mysql_free_result($rsWatchesLog);
					
					// mail versenden
					if ($debug == true)
						$mailadr = $debug_mailto;
					else
						$mailadr = $rUser['email'];

					if ($mailadr != '')
					{
						if (is_existent_maildomain(getToMailDomain($mailadr)))
						{
							mb_send_mail($mailadr, $mailsubject, $mailbody, $email_headers);
					
							// logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)																
							logentry('watchlist', 2, $rUser['user_id'], 0, 0, 'Sending mail to ' . $mailadr, array());
						}
					}

					// entries entfernen
					sql("DELETE FROM watches_waiting WHERE user_id='&1' AND watchtype IN (1, 2)", $rUser['user_id']);
				}
			}
		}
			
		// Zeitpunkt der nästen Mail berechnen
		if ($rUser['watchmail_mode'] == 0)
			$nextmail = date($sDateformat);
		elseif ($rUser['watchmail_mode'] == 1)
			$nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') + 1, date('Y')));
		elseif ($rUser['watchmail_mode'] == 2)
		{
			$weekday = date('w');
			if ($weekday == 0) $weekday = 7;

			if ($weekday == $rUser['watchmail_day'])
				$nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') + 7, date('Y')));
			elseif ($weekday > $rUser['watchmail_day'])
				$nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') - $weekday + $rUser['watchmail_day'] + 7, date('Y')));
			else
				$nextmail = date($sDateformat, mktime($rUser['watchmail_hour'], 0, 0, date('n'), date('j') + 6 - $rUser['watchmail_day'], date('Y')));
		}

		sql("UPDATE user SET watchmail_nextmail='&1' WHERE user_id='&2'", $nextmail, $rUser['user_id']);
	}
	mysql_free_result($rsUsers);

/* end send out everything that has to be sent */

	CleanupAndExit($watchpid);
 
function process_owner_log($user_id, $log_id)
{
	global $dblink, $logowner_text;

//	echo "process_owner_log($user_id, $log_id)\n";
	
	$rsLog = sql("SELECT cache_logs.cache_id cache_id, cache_logs.text text, cache_logs.text_html text_html, cache_logs.date logdate, user.username username, caches.name cachename FROM `cache_logs`, `user`, `caches` WHERE (cache_logs.user_id = user.user_id) AND (cache_logs.cache_id = caches.cache_id) AND (cache_logs.id ='&1')", $log_id);
	$rLog = sql_fetch_array($rsLog);
	mysql_free_result($rsLog);
	
	$watchtext = $logowner_text;
	$logtext = $rLog['text'];
	if ($rLog['text_html'] != 0)
	{
		$logtext = html_entity_decode($logtext, ENT_COMPAT, 'UTF-8');
		$h2t =& new html2text($logtext);
		$logtext = $h2t->get_text();
/*
		$logtext = html_entity_decode($logtext, ENT_COMPAT, 'UTF-8');
		$logtext = mb_ereg_replace("\r", '', $logtext);
		$logtext = mb_ereg_replace("\n", '', $logtext);
		$logtext = mb_ereg_replace('</p>', "</p>\n", $logtext);
		$logtext = mb_ereg_replace('<br/>', "<br/>\n", $logtext);
		$logtext = mb_ereg_replace('<br />', "<br />\n", $logtext);
		$logtext = strip_tags($logtext);
*/
	}
	
	$watchtext = mb_ereg_replace('{date}', date('d.m.Y', strtotime($rLog['logdate'])), $watchtext);
	$watchtext = mb_ereg_replace('{cacheid}', $rLog['cache_id'], $watchtext);
	$watchtext = mb_ereg_replace('{text}', $logtext, $watchtext);
	$watchtext = mb_ereg_replace('{user}', $rLog['username'], $watchtext);
	$watchtext = mb_ereg_replace('{cachename}', $rLog['cachename'], $watchtext);
	
	sql("INSERT IGNORE INTO watches_waiting (`user_id`, `object_id`, `object_type`, `date_created`, `watchtext`, `watchtype`) VALUES (
																		'&1', '&2', 1, NOW(), '&3', 1)", $user_id, $log_id, $watchtext);
	
	// logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)																
	logentry('watchlist', 1, $user_id, $log_id, 0, $watchtext, array());
}

function process_log_watch($user_id, $log_id)
{
	global $dblink, $logwatch_text;

//	echo "process_log_watch($user_id, $log_id)\n";
	
	$rsLog = sql("SELECT cache_logs.cache_id cache_id, cache_logs.text text, cache_logs.text_html text_html, cache_logs.date logdate, user.username username, caches.name cachename FROM `cache_logs`, `user`, `caches` WHERE (cache_logs.user_id = user.user_id) AND (cache_logs.cache_id = caches.cache_id) AND (cache_logs.id = '&1')", $log_id);
	$rLog = sql_fetch_array($rsLog);
	mysql_free_result($rsLog);
	
	$watchtext = $logwatch_text;
	$logtext = $rLog['text'];
	if ($rLog['text_html'] != 0)
	{
		$logtext = html_entity_decode($logtext, ENT_COMPAT, 'UTF-8');
		$h2t =& new html2text($logtext);
		$logtext = $h2t->get_text();
/*
		$logtext = html_entity_decode($logtext, ENT_COMPAT, 'UTF-8');
		$logtext = mb_ereg_replace("\r", '', $logtext);
		$logtext = mb_ereg_replace("\n", '', $logtext);
		$logtext = mb_ereg_replace('</p>', "</p>\n", $logtext);
		$logtext = mb_ereg_replace('<br/>', "<br/>\n", $logtext);
		$logtext = mb_ereg_replace('<br />', "<br />\n", $logtext);
		$logtext = strip_tags($logtext);
*/
	}
	
	$watchtext = mb_ereg_replace('{date}', date('d.m.Y', strtotime($rLog['logdate'])), $watchtext);
	$watchtext = mb_ereg_replace('{cacheid}', $rLog['cache_id'], $watchtext);
	$watchtext = mb_ereg_replace('{text}', $logtext, $watchtext);
	$watchtext = mb_ereg_replace('{user}', $rLog['username'], $watchtext);
	$watchtext = mb_ereg_replace('{cachename}', $rLog['cachename'], $watchtext);
	
	sql("INSERT IGNORE INTO watches_waiting (`user_id`, `object_id`, `object_type`, `date_created`, `watchtext`, `watchtype`) VALUES (
																		'&1', '&2', 1, NOW(), '&3', 2)", $user_id, $log_id, $watchtext);
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
