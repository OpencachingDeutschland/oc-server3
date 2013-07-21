#!/usr/local/bin/php -q
<?php
 /***************************************************************************
		For license information see doc/license.txt

		Unicode Reminder メモ

		Processes the `notify_waiting` table and sends notification mails
		on new caches and new OConly attributes.
	***************************************************************************/

	// needs absolute rootpath because called as cronjob
	$rootpath = dirname(__FILE__) . '/../../';

	require_once($rootpath . 'lib/clicompatbase.inc.php');
	require_once('settings.inc.php');
	require_once($rootpath . 'lib/consts.inc.php');
	require_once($rootpath . 'lib2/ProcessSync.class.php');

	// db connect
	db_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}

	$process_sync = new ProcessSync('run_notify');
	if ($process_sync->Enter())
	{
		// send out everything that has to be sent
	  $rsNotify = sql("
				SELECT
					`notify_waiting`.`id`, `notify_waiting`.`cache_id`, `notify_waiting`.`type`,
					`user`.`username`,
					`user2`.`email`, `user2`.`username` as `recpname`, `user2`.`latitude` as `lat1`, `user2`.`longitude` as `lon1`, `user2`.`user_id` as `recid`,
					`caches`.`name` as `cachename`, `caches`.`date_hidden`, `caches`.`latitude` as `lat2`, `caches`.`longitude` as `lon2`, `caches`.`wp_oc`,
					`cache_type`.`de` as `cachetype`,
					`cache_size`.`de` as `cachesize`,
					`cache_status`.`allow_user_view`,
					`ca`.`attrib_id` IS NOT NULL AS `oconly`
				FROM `notify_waiting`
				INNER JOIN `caches` ON `notify_waiting`.`cache_id`=`caches`.`cache_id`
				INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
				INNER JOIN `user` `user2` ON `notify_waiting`.`user_id`=`user2`.`user_id`
				INNER JOIN `cache_type` ON `caches`.`type`=`cache_type`.`id`
				INNER JOIN `cache_size` ON `caches`.`size`=`cache_size`.`id`
				INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
				LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6");

	  while ($rNotify = sql_fetch_array($rsNotify))
	  {
			if ($rNotify['allow_user_view'])
				process_new_cache($rNotify);
			sql("DELETE FROM `notify_waiting` WHERE `id` ='&1'", $rNotify['id']);
	  }
	  mysql_free_result($rsNotify);

		$process_sync->Leave();
	}



function process_new_cache($notify)
{
	global $debug, $debug_mailto, $rootpath;
	global $mailfrom, $new_cache_subject, $new_oconly_subject;

	//echo "process_new_cache(".$notify['id'].")\n";
	$error = false;

	// mail-template lesen
	switch ($notify['type'])
	{
		case notify_new_cache: // Type: new cache
			$mailbody = read_file($rootpath . 'util/notification/notify_newcache.email');
			$mailsubject = $new_cache_subject;
			break;

		case notify_new_oconly: // Type: new OConly flag
			$mailbody = read_file($rootpath . 'util/notification/notify_newoconly.email');
			$mailsubject = $new_oconly_subject;
			break;

		default:
			$error = true;
			break;
	}

	if (!$error)
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
		$mailbody = mb_ereg_replace('{oconly}', $notify['oconly'] ? 'OConly-' : '', $mailbody);

		$subject = mb_ereg_replace('{cachename}', $notify['cachename'], $mailsubject);
		$subject = mb_ereg_replace('{oconly}', $notify['oconly'] ? 'OConly-' : '', $subject);

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
	logentry('notify_newcache', 8, $notify['recid'], $notify['cache_id'], 0, 'Sending mail to ' . $mailadr, array());

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

?>
