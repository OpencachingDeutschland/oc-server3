#!/usr/local/bin/php -q
<?php
 /***************************************************************************
		./util/publish_caches/run_publish.php
															-------------------
		begin                : Sat September 2 2006

		For license information see doc/license.txt
 ****************************************************************************/

 /***************************************************************************

		Unicode Reminder メモ

		Ggf. muss die Location des php-Binaries angepasst werden.

		Prueft auf wartende Caches, deren Veröffentlichungszeitpunkt
		gekommen ist und veröffentlicht sie.

	***************************************************************************/

	$rootpath = '../../';

	// chdir to proper directory (needed for cronjobs)
	chdir(substr(realpath($_SERVER['PHP_SELF']), 0, strrpos(realpath($_SERVER['PHP_SELF']), '/')));

	require_once($rootpath . 'lib/clicompatbase.inc.php');
	require_once('settings.inc.php');
	require_once($rootpath . 'lib/eventhandler.inc.php');

/* begin db connect */
	db_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}
/* end db connect */

	$rsPublish = sql("	SELECT `cache_id`, `user_id`
				FROM `caches`
				WHERE `status` = 5
				  AND `date_activate` <= NOW()");

	while($rPublish = sql_fetch_array($rsPublish))
	{
		$userid = $rPublish['user_id'];
		$cacheid = $rPublish['cache_id'];

		// update cache status to active
		sql("UPDATE `caches` SET `status`=1, `date_activate`=NULL WHERE `cache_id`='&1'", $cacheid);

		// send events
		touchCache($cacheid);
		event_new_cache($userid);
		event_notify_new_cache($cacheid);
	}
	mysql_free_result($rsPublish);

?>