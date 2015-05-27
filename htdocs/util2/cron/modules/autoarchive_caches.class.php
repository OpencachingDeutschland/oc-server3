<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Automatic archiving of disabled caches
 ***************************************************************************/

require_once($opt['rootpath'] . "lib2/logic/cache.class.php");
require_once($opt['rootpath'] . "lib2/logic/cachelog.class.php");

checkJob(new autoarchive());


class autoarchive
{
	var $name = 'autoarchive';
	var $interval = 3600;  // once per hour


	function run()
	{
		global $opt, $login;

		if ($opt['cron']['autoarchive']['run'])
		{
			if (!$login->logged_in())
				echo $this->name . ": not logged in / no system user configured\n";
			elseif ($login->hasAdminPriv(ADMIN_USER))
			{
				$this->archive_disabled_caches();
				$this->archive_events();
			}
			else
				echo $this->name . ": user '".$opt['logic']['systemuser']['user']."' cannot maintain caches\n";
		}
	}

	function archive_disabled_caches()
	{
		// Logging of status changes in cache_status_modified has started on June 1, 2013.
		// For archiving caches that were disabled earlier, we also check the listing
		// modification date.
		$rs = sql("SELECT `caches`.`cache_id`
		             FROM `caches`
		            WHERE `caches`.`status`=2
								  AND IFNULL((SELECT MAX(`date_modified`) FROM `cache_status_modified` `csm` WHERE `csm`.`cache_id`=`caches`.`cache_id`),`caches`.`listing_last_modified`) < NOW() - INTERVAL 366 DAY
						 GROUP BY `caches`.`cache_id`
						 ORDER BY `caches`.`listing_last_modified`
						    LIMIT 3");   // limit to avoid mass emails and spam-filter triggers
		while ($rCache = sql_fetch_assoc($rs))
		{
			$this->archive_cache(
					$rCache['cache_id'],
				  'This cache has been "temporarily unavailable" for more than one year now; ' .
					'therefore it is being archived automatically. The owner may decide to ' .
					'maintain the cache and re-enable the listing.');
		}
		sql_free_result($rs);
	}

	function archive_events()
	{
		// To prevent archiving events that were accidentally published with a wrong
		// event date - before the owner notices it - we also apply a limit of one month
		// to the publication date.
		$rs = sql("SELECT `cache_id`
		             FROM `caches`
		            WHERE `caches`.`type`=6 AND `caches`.`status`=1
								      AND GREATEST(`date_hidden`,`date_created`) < NOW() - INTERVAL 35 DAY
						 ORDER BY `date_hidden`
						    LIMIT 1");
		while ($rCache = sql_fetch_assoc($rs))
		{
			$this->archive_cache(
					$rCache['cache_id'],
			    'This event took place more than five weeks ago; therefore it is ' .
					'being archived automatically. The owner may re-enable the listing ' .
					'if it should stay active for some exceptional reason.');
		}
		sql_free_result($rs);
	}

	function archive_cache($cache_id, $comment)
	{
		global $opt, $login, $translate;

		$log = cachelog::createNew($cache_id,$login->userid);
		if ($log === false)
			echo $this->name . ": cannot create log for cache $cache_id\n";
		else
		{
			$cache = new cache($cache_id);
			if (!$cache->setStatus(3) || !$cache->save())
				echo $this->name . ": cannot change status of cache $cache_id\n";
			else
			{
				// create log
				$log->setType(cachelog::LOGTYPE_ARCHIVED, true);
				$log->setOcTeamComment(true);
				$log->setDate(date('Y-m-d'));
					// Log without time, so that owner reactions will always appear AFTER
					// the system log, no matter if logged with or without date.

				// create log text in appropriate language
				$translated_comment = $translate->t($comment, '','',0,'',1, $cache->getDefaultDescLanguage());
				$log->setText('<p>'.$translated_comment.'</p>');
				$log->setTextHtml(1);

				if (!$log->save())
					echo $this->name . ": could not save archive log for cache $cache_id\n";
			}
		}
	}

}

?>
