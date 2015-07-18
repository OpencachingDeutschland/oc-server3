<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *   get/set has to be commited with save
 *   add/remove etc. is executed instantly
 ***************************************************************************/

require_once($opt['rootpath'] . 'lib2/logic/rowEditor.class.php');
require_once($opt['rootpath'] . 'lib2/logic/logtypes.inc.php');

class cache
{
	var $nCacheId = 0;

	var $reCache;

	static function cacheIdFromWP($wp)
	{
		$cacheid = 0;
		if (mb_strtoupper(mb_substr($wp, 0, 2)) == 'GC')
		{
			$rs = sql("SELECT `cache_id` FROM `caches` WHERE `wp_gc_maintained`='&1'", $wp);
			if (sql_num_rows($rs) != 1)
			{
				sql_free_result($rs);
				return null;
			}
			$r = sql_fetch_assoc($rs);
			sql_free_result($rs);

			$cacheid = $r['cache_id'];
		}
		else if (mb_strtoupper(mb_substr($wp, 0, 1)) == 'N')
		{
			$rs = sql("SELECT `cache_id` FROM `caches` WHERE `wp_nc`='&1'", $wp);
			if (sql_num_rows($rs) != 1)
			{
				sql_free_result($rs);
				return null;
			}
			$r = sql_fetch_assoc($rs);
			sql_free_result($rs);

			$cacheid = $r['cache_id'];
		}
		else
		{
			$cacheid = sql_value("SELECT `cache_id` FROM `caches` WHERE `wp_oc`='&1'", 0, $wp);
		}
		
		return $cacheid;
	}

	static function fromWP($wp)
	{
		$cacheid = cache::cacheIdFromWP($wp);
		if ($cacheid == 0)
			return null;

		return new cache($cacheid);
	}

	static function cacheIdFromUUID($uuid)
	{
		$cacheid = sql_value("SELECT `cache_id` FROM `caches` WHERE `uuid`='&1'", 0, $uuid);
		return $cacheid;
	}

	static function fromUUID($uuid)
	{
		$cacheid = cache::cacheIdFromUUID($uuid);
		if ($cacheid == 0)
			return null;

		return new cache($cacheid);
	}

	function __construct($nNewCacheId=ID_NEW)
	{
		$this->reCache = new rowEditor('caches');
		$this->reCache->addPKInt('cache_id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->reCache->addString('uuid', '', false, RE_INSERT_AUTOUUID);
		$this->reCache->addInt('node', 0, false);
		$this->reCache->addDate('date_created', time(), true, RE_INSERT_IGNORE);
		$this->reCache->addDate('last_modified', time(), true, RE_INSERT_IGNORE);
		$this->reCache->addDate('listing_last_modified', time(), true, RE_INSERT_IGNORE);
		$this->reCache->addInt('user_id', 0, false);
		$this->reCache->addString('name', '', false);
		$this->reCache->addDouble('longitude', 0, false);
		$this->reCache->addDouble('latitude', 0, false);
		$this->reCache->addInt('type', 1, false);
		$this->reCache->addInt('status', 5, false);
		$this->reCache->addString('country', '', false);
		$this->reCache->addDate('date_hidden', time(), false);
		$this->reCache->addInt('size', 1, false);
		$this->reCache->addFloat('difficulty', 1, false);
		$this->reCache->addFloat('terrain', 1, false);
		$this->reCache->addString('logpw', '', false);
		$this->reCache->addFloat('search_time', 0, false);
		$this->reCache->addFloat('way_length', 0, false);
		$this->reCache->addString('wp_oc', null, true);
		$this->reCache->addString('wp_gc', '', false);
		$this->reCache->addString('wp_nc', '', false);
		$this->reCache->addString('desc_languages', '', false, RE_INSERT_IGNORE);
		$this->reCache->addString('default_desclang', '', false);
		$this->reCache->addDate('date_activate', null, true);
		$this->reCache->addInt('need_npa_recalc', 1, false, RE_INSERT_IGNORE);
		$this->reCache->addInt('show_cachelists', 1, false);

		$this->nCacheId = $nNewCacheId+0;

		if ($nNewCacheId == ID_NEW)
		{
			$this->reCache->addNew(null);
		}
		else
		{
			$this->reCache->load($this->nCacheId);
		}
	}

	function exist()
	{
		return $this->reCache->exist();
	}

	function getCacheId()
	{
		return $this->nCacheId;
	}
	function getStatus()
	{
		return $this->reCache->getValue('status');
	}
	function getType()
	{
		return $this->reCache->getValue('type');
	}
	function getName()
	{
		return $this->reCache->getValue('name');
	}
	function getLongitude()
	{
		return $this->reCache->getValue('longitude');
	}
	function getLatitude()
	{
		return $this->reCache->getValue('latitude');
	}
	function getUserId()
	{
		return $this->reCache->getValue('user_id');
	}
	function getUsername()
	{
		return sql_value("SELECT `username` FROM `user` WHERE `user_id`='&1'", '', $this->getUserId());
	}
	function getWPOC()
	{
		return $this->reCache->getValue('wp_oc');
	}
	function getWPGC()
	{
		return $this->reCache->getValue('wp_gc');
	}
	function getWPNC()
	{
		return $this->reCache->getValue('wp_nc');
	}

	function getUUID()
	{
		return $this->reCache->getValue('uuid');
	}
	function getDateCreated()
	{
		return $this->reCache->getValue('date_created');
	}
	function getLastModified()
	{
		return $this->reCache->getValue('last_modified');
	}
	function getListingLastModified()
	{
		return $this->reCache->getValue('listing_last_modified');
	}
	function getNode()
	{
		return $this->reCache->getValue('node');
	}
	function setNode($value)
	{
		return $this->reCache->setValue('node', $value);
	}
	function setStatus($value)
	{
		global $login;
		if (sql_value("SELECT COUNT(*) FROM `cache_status` WHERE `id`='&1'", 0, $value) == 1)
		{
			sql("SET @STATUS_CHANGE_USER_ID='&1'", $login->userid);
			return $this->reCache->setValue('status', $value);
		}
		else
		{
			return false;
		}
	}

	function getDescLanguages()
	{
		return explode($this->reCache->getValue('desc_languages'),',');
	}

	function getDefaultDescLanguage()
	{
		return $this->reCache->getValue('default_desclang');
	}

	function getAnyChanged()
	{
		return $this->reCache->getAnyChanged();
	}

	// return if successfull (with insert)
	function save()
	{
		if ($this->reCache->save())
		{
			sql_slave_exclude();
			return true;
		}
		else
			return false;
	}

	function requireLogPW()
	{
		return $this->reCache->getValue('logpw') != '';
	}

	// TODO: use prepared one way hash
	function validateLogPW($nLogType, $sLogPW)
	{
		if (!$this->requireLogPW())
			return true;

		if (sql_value("SELECT `require_password` FROM `log_types` WHERE `id`='&1'", 0, $nLogType) == 0)
			return true;

		return ($sLogPW == $this->reCache->getValue('logpw'));
	}

	static function visitCounter($nVisitUserId, $sRemoteAddr, $nCacheId)
	{
		global $opt, $_SERVER;

		// delete cache_visits older 1 day 60*60*24 = 86400
		sql("DELETE FROM `cache_visits` WHERE `cache_id`='&1' AND `user_id_ip`!='0' AND NOW()-`last_modified`>86400", $nCacheId);

		if ($nVisitUserId==0)
		{
			$se = explode(';',$opt['logic']['search_engines']);
			$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
			foreach ($se as $s)
				if (strpos($ua,$s) !== FALSE)
					return;   // do not count search engine views

			$sIdentifier = $sRemoteAddr;
		}
		else
			$sIdentifier = $nVisitUserId;

		// note the visit of this user
		sql("INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`) VALUES (&1, '&2', 1)
				ON DUPLICATE KEY UPDATE `count`=`count`+1", $nCacheId, $sIdentifier);

		// if the previous statement does an INSERT, it was the first visit for this user today
		if (sql_affected_rows() == 1)
		{
			if ($nVisitUserId != sql_value("SELECT `user_id` FROM `caches` WHERE `cache_id`='&1'", 0, $nCacheId))
			{
				// increment the counter for this cache
				sql("INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`) VALUES (&1, '0', 1)
						ON DUPLICATE KEY UPDATE `count`=`count`+1", $nCacheId);
			}
		}
	}

	static function getLogsCount($cacheid)
	{
		//prepare the logs
		$rsLogs = sql("SELECT COUNT(*) FROM `cache_logs` WHERE `cache_id`='&1'", $cacheid);
		$rLog = sql_fetch_assoc($rsLogs);		
		sql_free_result($rsLogs);

		return $rLog;
	}


	static function getLogsArray($cacheid, $start, $count, $deleted=false)
	{
		global $login;

		//prepare the logs
		if ($deleted && ($login->admin && ADMIN_USER)>0)
		{
			// admins may view owner-deleted logs
			$table = 'cache_logs_archived';
			$delfields = 'IFNULL(`u2`.`username`,"") AS `deleted_by_name`, `deletion_date`, "1" AS `deleted`';
			$addjoin = 'LEFT JOIN `user` `u2` ON `u2`.`user_id`=`cache_logs`.`deleted_by`';
		}
		else
		{
			$table = 'cache_logs';
			$delfields = '"" AS `deleted_by_name`, NULL AS `deletion_date`, "0" AS `deleted`';
			$addjoin = '';
		}

		$rsLogs = sql("
			SELECT `cache_logs`.`user_id` AS `userid`,
				`cache_logs`.`id` AS `id`,
				`cache_logs`.`uuid` AS `uuid`,
				`cache_logs`.`date` AS `date`,
				substr(`cache_logs`.`date`,12) AS `time`,  /* 00:00:01 = 00:00 logged, 00:00:00 = no time */
				`cache_logs`.`type` AS `type`,
				`cache_logs`.`oc_team_comment` AS `oc_team_comment`,
				`cache_logs`.`text` AS `text`,
				`cache_logs`.`text_html` AS `texthtml`,
				`cache_logs`.`picture`,
				".$delfields.",
				`user`.`username` AS `username`,
				IF(ISNULL(`cache_rating`.`cache_id`), 0, `cache_logs`.`type` IN (1,7)) AS `recommended`
			FROM $table AS `cache_logs`
			INNER JOIN `user` ON `user`.`user_id` = `cache_logs`.`user_id`
			LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id` AND `cache_logs`.`date`=`cache_rating`.`rating_date`
			".$addjoin."
			WHERE `cache_logs`.`cache_id`='&1'
			ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`date_created` DESC
			LIMIT &2, &3", $cacheid, $start+0, $count+0);

		$logs = array();
		while ($rLog = sql_fetch_assoc($rsLogs))
		{
			$pictures = array();
			$rsPictures = sql("SELECT `url`, `title`, `uuid`, `id`, `spoiler` FROM `pictures` WHERE `object_id`='&1' AND `object_type`=1", $rLog['id']);
			while ($rPicture = sql_fetch_assoc($rsPictures))
				$pictures[] = $rPicture;
			sql_free_result($rsPictures);
			$rLog['pictures'] = $pictures;

			$logs[] = $rLog;
		}
		sql_free_result($rsLogs);

		return $logs;
	}

	function report($userid, $reportreason, $reportnote)
	{
		sql("INSERT INTO cache_reports (`cacheid`, `userid`, `reason`, `note`)
		     VALUES(&1, &2, &3, '&4')",
		     $this->nCacheId, $userid, $reportreason, $reportnote);

		return true;
	}

	function addAdoption($userid)
	{
		if ($this->allowEdit() == false)
			return "noaccess";

		if (sql_value("SELECT COUNT(*) FROM `user` WHERE `user_id`='&1'", 0, $userid) == 0)
			return "userunknown";

		if (sql_value("SELECT COUNT(*) FROM `user` WHERE `user_id`='&1' AND `is_active_flag`=1", 0, $userid) == 0)
			return "userdisabled";

		// same user?
		if ($this->getUserId() == $userid)
			return "self";

		sql("INSERT IGNORE INTO `cache_adoption` (`cache_id`, `user_id`) VALUES ('&1', '&2')", $this->nCacheId, $userid);

		return true;
	}

	function cancelAdoption($userid)
	{
		global $login;

		if ($this->allowEdit() == false && $login->userid != $userid)
			return false;

		sql("DELETE FROM `cache_adoption` WHERE `user_id`='&1' AND `cache_id`='&2'", $userid, $this->nCacheId);

		return true;
	}

	function commitAdoption($userid)
	{
		global $login;

		// cache_adoption exists?
		if (sql_value("SELECT COUNT(*) FROM `cache_adoption` WHERE `cache_id`='&1' AND `user_id`='&2'", 0, $this->nCacheId, $userid) == 0)
			return false;

		// new user active?
		if (sql_value("SELECT `is_active_flag` FROM `user` WHERE `user_id`='&1'", 0, $userid) != 1)
			return false;

		sql("INSERT INTO `logentries` (`module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`)
		                       VALUES ('cache', 5, '&1', '&2', '&3', '&4')",
		                       $login->userid, $this->nCacheId, 0, 
		                       'Cache ' . sql_escape($this->nCacheId) . ' has changed the owner from userid ' . sql_escape($this->getUserId()) . ' to ' . sql_escape($userid) . ' by ' . sql_escape($login->userid));
			// Adoptions now are recorded by trigger in cache_adoptions table.
			// Recording adoptions in 'logentries' may be discarded after ensuring that the
			// log entries are not used anywhere.
		sql("UPDATE `caches` SET `user_id`='&1' WHERE `cache_id`='&2'", $userid, $this->nCacheId);
		sql("DELETE FROM `cache_adoption` WHERE `cache_id`='&1'", $this->nCacheId);

		$this->reCache->setValue('user_id', $userid);

		return true;
	}
	
	// checks if $userId has adopted this cache
	function hasAdopted($userId)
	{
		// cache_adoption exists?
		return (sql_value("
						SELECT COUNT(*)
						FROM `cache_adoption`
						WHERE `cache_id`='&1'
							AND `user_id`='&2'",
					0,
					$this->nCacheId,
					$userId) != 0);
	}

	// true if anyone can view the cache
	function isPublic()
	{
		return (sql_value("SELECT `allow_user_view` FROM `cache_status` WHERE `id`='&1'", 0, $this->getStatus()) == 1);
	}
	function allowView()
	{
		global $login;

		if ($this->isPublic())
			return true;

		$login->verify();

		if (($login->admin & ADMIN_USER) == ADMIN_USER)
			return true;
		else if ($this->getUserId() == $login->userid)
			return true;

		return false;
	}
	function allowEdit()
	{
		global $login;

		$login->verify();
		if ($this->getUserId() == $login->userid)
			return true;

		return false;
	}
	function allowLog()
	{
		global $login;

		$login->verify();
		if ($this->getUserId() == $login->userid || $login->hasAdminPriv(ADMIN_USER))
			return true;

		return (sql_value("SELECT `allow_user_log` FROM `cache_status` WHERE `id`='&1'", 0, $this->getStatus()) == 1);
	}

	function isRecommendedByUser($nUserId)
	{
		return (sql_value("SELECT COUNT(*) FROM `cache_rating` WHERE `cache_id`='&1' AND `user_id`='&2'", 0, $this->nCacheId, $nUserId) > 0);
	}
	function addRecommendation($nUserId, $logdate)
	{
		// rating_date will be set to NOW() by Insert-trigger
		sql("INSERT IGNORE INTO `cache_rating` (`cache_id`, `user_id`, `rating_date`) VALUES ('&1', '&2', '&3')", $this->nCacheId, $nUserId, $logdate);
	}
	function removeRecommendation($nUserId)
	{
		sql("DELETE FROM `cache_rating` WHERE `cache_id`='&1' AND `user_id`='&2'", $this->nCacheId, $nUserId);
	}


	// retrieves admin cache history data and stores it to template variables
	// for display by adminhistory.tpl and adminreports.tpl
	function setTplHistoryData($exclude_report_id)
	{
		global $opt, $tpl;

		// (other) reports for this cache
		$rs = sql("SELECT `cr`.`id`, `cr`.`date_created`, `cr`.`lastmodified`,
		                  `cr`.`userid`, `cr`.`adminid`,
				              `users`.`username` AS `usernick`,
				              `admins`.`username` AS `adminnick`,
											IFNULL(`tt`.`text`, `crs`.`name`) AS `status`,
				              IFNULL(`tt2`.`text`, `crr`.`name`) AS `reason`
				         FROM `cache_reports` AS `cr`
				    LEFT JOIN `cache_report_reasons` AS `crr` ON `cr`.`reason`=`crr`.`id`
			      LEFT JOIN `user` AS `users` ON `users`.`user_id`=`cr`.`userid`
			      LEFT JOIN `user` AS `admins` ON `admins`.`user_id`=`cr`.`adminid`
			      LEFT JOIN `cache_report_status` AS `crs` ON `cr`.`status`=`crs`.`id`
			      LEFT JOIN `sys_trans_text` AS `tt` ON `crs`.`trans_id`=`tt`.`trans_id` AND `tt`.`lang`='&2'
			      LEFT JOIN `sys_trans_text` AS `tt2` ON `crr`.`trans_id`=`tt2`.`trans_id` AND `tt2`.`lang`='&2'
			          WHERE `cr`.`cacheid`='&1' AND `cr`.`id`<>'&3'
			       ORDER BY `cr`.`status`,`cr`.`id` DESC", $this->getCacheId(), $opt['template']['locale'], $exclude_report_id);
		$tpl->assign_rs('reports',$rs);
		sql_free_result($rs);

		// user; deleted logs
		$rs = sql("SELECT * FROM `caches` WHERE `cache_id`='&1'", $this->getCacheId());
		$rCache = sql_fetch_array($rs);
		$tpl->assign('cache', $rCache);
		sql_free_result($rs);
		$tpl->assign('ownername', sql_value("SELECT `username` FROM `user` WHERE `user_id`='&1'", "", $rCache['user_id']));

		$tpl->assign('deleted_logs', $this->getLogsArray($this->getCacheId(), 0, 1000, true));

		// status changes
		$rs = sql("SELECT `csm`.`date_modified`,
		                  `csm`.`old_state` AS `old_status_id`,
		                  `csm`.`new_state` AS `new_status_id`,
		                  `user`.`username`,
				              `user`.`user_id`,
		                  IFNULL(`stt_old`.`text`,`cs_old`.`name`) AS `old_status`,
		                  IFNULL(`stt_new`.`text`,`cs_new`.`name`) AS `new_status`
		             FROM `cache_status_modified` `csm`
	           LEFT JOIN `cache_status` `cs_old` ON `cs_old`.`id`=`csm`.`old_state`
						LEFT JOIN `sys_trans_text` `stt_old` ON `stt_old`.`trans_id`=`cs_old`.`trans_id` AND `stt_old`.`lang`='&2'
	           LEFT JOIN `cache_status` `cs_new` ON `cs_new`.`id`=`csm`.`new_state`
						LEFT JOIN `sys_trans_text` `stt_new` ON `stt_new`.`trans_id`=`cs_new`.`trans_id` AND `stt_new`.`lang`='&2'
						LEFT JOIN `user` ON `user`.`user_id`=`csm`.`user_id`
		            WHERE `cache_id`='&1'
		         ORDER BY `date_modified` DESC", $this->getCacheId(), $opt['template']['locale']);
		$tpl->assign_rs('status_changes',$rs);
		sql_free_result($rs);

		// coordinate changes
		$rs = sql("SELECT `cc`.`date_created`, `cc`.`longitude`, `cc`.`latitude`,
		                  IFNULL(`admin`.`user_id`, `owner`.`user_id`) AS `user_id`,
		                  IFNULL(`admin`.`username`, `owner`.`username`) AS `username`
		             FROM `cache_coordinates` `cc`
		        LEFT JOIN `caches` ON `caches`.`cache_id`=`cc`.`cache_id`
		        LEFT JOIN `user` `owner` ON `owner`.`user_id`=`caches`.`user_id`
		        LEFT JOIN `user` `admin` ON `admin`.`user_id`=`cc`.`restored_by`
		            WHERE `cc`.`cache_id`='&1'
		         ORDER BY `cc`.`date_created` DESC", $this->getCacheId());
		$coords = array();
		while ($rCoord = sql_fetch_assoc($rs))
		{
			$coord = new coordinate($rCoord['latitude'], $rCoord['longitude']);
			$coords[] = array('date' => $rCoord['date_created'], 
			                  'coord' => $coord->getDecimalMinutes(),
			                  'user_id' => $rCoord['user_id'],
			                  'username' => $rCoord['username']);
		}
		sql_free_result($rs);
		$tpl->assign('coordinates', $coords);

		// Adoptions
		$rs = sql("SELECT `cache_adoptions`.`date`,
		                  `cache_adoptions`.`from_user_id`,
		                  `cache_adoptions`.`to_user_id`,
		                  `from_user`.`username` AS `from_username`,
		                  `to_user`.`username` AS `to_username`
		             FROM `cache_adoptions`
		        LEFT JOIN `user` `from_user` ON `from_user`.`user_id`=`from_user_id`
		        LEFT JOIN `user` `to_user` ON `to_user`.`user_id`=`to_user_id`
		            WHERE `cache_id`='&1'
		         ORDER BY `cache_adoptions`.`date`, `cache_adoptions`.`id`",
						          $this->getCacheId());
		$tpl->assign_rs('adoptions',$rs);
		sql_free_result($rs);
	}

	
	function logTypeAllowed($logType, $oldLogType = 0)
	{
		// check if given logType is valid for this cache type
		return logtype_ok($this->getCacheId(), $logType, $oldLogType);
	}
	
	
	function updateCacheStatus($logType)
	{
		// get cache status
		$cacheStatus = sql_value("
									SELECT `cache_status`
									FROM `log_types`
									WHERE `id`='&1'",
							0,
							$logType);
		// set status, if not 0
		if ($cacheStatus != 0)
			$this->setStatus($cacheStatus);
	}
	
	
	// $userLogType:
	//   Logtype selected by the user, or null if not applicable

	function getUserLogTypes($userLogType, $oldLogType = 0)
	{
		global $translate, $login;
		
		$logTypes = array();
		
		$logtypeNames = get_logtype_names();
		$allowedLogtypes = get_cache_log_types($this->getCacheId(), $oldLogType);
		$defaultLogType = $userLogType; 
		if (!logtype_ok($this->getCacheId(), $defaultLogType, $oldLogType))
			$defaultLogType = $allowedLogtypes[0];
		
		// prepare array
		$i = 0;
		foreach ($allowedLogtypes as $logtype)
		{
			$logTypes[$i]['selected'] = ($logtype == $defaultLogType) ? true : false;
			$logTypes[$i]['name'] = $logtypeNames[$logtype];
			$logTypes[$i]['id'] = $logtype;
			$i++;
		}
		
		// return
		return $logTypes;
	}
	
	function teamcommentAllowed($logType, $oldTeamComment = false)
	{
		// checks if teamcomment is allowed
		return teamcomment_allowed($this->getCacheId(), $logType, $oldTeamComment);
	}
	
	function statusUserLogAllowed()
	{
		return (sql_value("
							SELECT `cache_status`.`allow_user_log`
							FROM `cache_status`,`caches`
							WHERE `caches`.`status`=`cache_status`.`id`
								AND `caches`.`cache_id`='&1'",
						0,
						$this->getCacheId()) == 1);
	}
}
?>
