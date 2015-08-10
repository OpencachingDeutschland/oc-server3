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
require_once($opt['rootpath'] . 'lib2/logic/cache.class.php');
require_once($opt['rootpath'] . 'lib2/logic/logtypes.inc.php');

class cachelog
{
	const LOGTYPE_FOUND      = 1;
	const LOGTYPE_NOTFOUND   = 2;
	const LOGTYPE_NOTE       = 3;
	const LOGTYPE_ATTENDED   = 7;
	const LOGTYPE_WILLATTEND = 8;
	const LOGTYPE_ARCHIVED   = 9;
	const LOGTYPE_ACTIVE     = 10;
	const LOGTYPE_DISABLED   = 11;
	const LOGTYPE_LOCKED     = 13;
	const LOGTYPE_LOCKED_INVISIBLE = 14;

	var $nLogId = 0;

	var $reCacheLog;

	static function logIdFromUUID($uuid)
	{
		$cacheid = sql_value("SELECT `id` FROM `cache_logs` WHERE `uuid`='&1'", 0, $uuid);
		return $cacheid;
	}

	static function fromUUID($uuid)
	{
		$logid = cachelog::logIdFromUUID($uuid);
		if ($logid == 0)
			return null;

		return new cachelog($logid);
	}

	static function createNew($nCacheId, $nUserId)
	{
		global $opt;

		// check if user is allowed to log this cache!
		$cache = new cache($nCacheId);
		if ($cache->exist() == false)
			return false;
		if ($cache->allowLog() == false)
			return false;

		$oCacheLog = new cachelog(ID_NEW);
		$oCacheLog->setUserId($nUserId);
		$oCacheLog->setCacheId($nCacheId);
		$oCacheLog->setNode($opt['logic']['node']['id']);
		return $oCacheLog;
	}

	static function createNewFromCache($oCache, $nUserId)
	{
		global $opt;
		
		// check if user is allowed to log this cache!
		if ($oCache->exist() == false)
			return false;
		if ($oCache->allowLog() == false)
			return false;

		$oCacheLog = new cachelog(ID_NEW);
		$oCacheLog->setUserId($nUserId);
		$oCacheLog->setCacheId($oCache->getCacheId());
		$oCacheLog->setNode($opt['logic']['node']['id']);
		return $oCacheLog;
	}

	function __construct($nNewLogId=ID_NEW)
	{
		$this->reCacheLog = new rowEditor('cache_logs');
		$this->reCacheLog->addPKInt('id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->reCacheLog->addString('uuid', '', false, RE_INSERT_AUTOUUID);
		$this->reCacheLog->addInt('node', 0, false);
		$this->reCacheLog->addDate('date_created', time(), true, RE_INSERT_IGNORE);
		$this->reCacheLog->addDate('last_modified', time(), true, RE_INSERT_IGNORE);
		$this->reCacheLog->addInt('cache_id', 0, false);
		$this->reCacheLog->addInt('user_id', 0, false);
		$this->reCacheLog->addInt('type', 0, false);
		$this->reCacheLog->addInt('oc_team_comment', 0, false);
		$this->reCacheLog->addDate('date', time(), false);
		$this->reCacheLog->addString('text', '', false);
		$this->reCacheLog->addInt('text_htmledit', 1, false);
		$this->reCacheLog->addInt('owner_notified', 0, false);
		$this->reCacheLog->addInt('picture', 0, false);

		$this->nLogId = $nNewLogId+0;

		if ($nNewLogId == ID_NEW)
		{
			$this->reCacheLog->addNew(null);
		}
		else
		{
			$this->reCacheLog->load($this->nLogId);
		}
	}

	function exist()
	{
		return $this->reCacheLog->exist();
	}

	function getLogId()
	{
		return $this->nLogId;
	}
	function getUserId()
	{
		return $this->reCacheLog->getValue('user_id');
	}
	function setUserId($value)
	{
		return $this->reCacheLog->setValue('user_id', $value);
	}
	function getCacheId()
	{
		return $this->reCacheLog->getValue('cache_id');
	}
	function setCacheId($value)
	{
		return $this->reCacheLog->setValue('cache_id', $value);
	}
	function getType()
	{
		return $this->reCacheLog->getValue('type');
	}
	function setType($value,$force=false)
	{
		if (!$force)
		{
			$nValidLogTypes = $this->getValidLogTypes();
			if (array_search($value, $nValidLogTypes) === false)
				return false;
		}
		return $this->reCacheLog->setValue('type', $value);
	}
	function getOcTeamComment()
	{
		return $this->reCacheLog->getValue('oc_team_comment');
	}
	function setOcTeamComment($value)
	{
		return $this->reCacheLog->setValue('oc_team_comment', $value);
	}
	function getDate()
	{
		return $this->reCacheLog->getValue('date');
	}
	function setDate($value)
	{
		return $this->reCacheLog->setValue('date', $value);
	}
	function getText()
	{
		return $this->reCacheLog->getValue('text');
	}
	function setText($value)
	{
		return $this->reCacheLog->setValue('text', $value);
	}
	function getTextHtmlEdit()
	{
		return $this->reCacheLog->getValue('text_html');
	}
	function setTextHtmlEdit($value)
	{
		return $this->reCacheLog->setValue('text_htmledit', $value ? 1 : 0);
	}
	function getUUID()
	{
		return $this->reCacheLog->getValue('uuid');
	}
	function getLastModified()
	{
		return $this->reCacheLog->getValue('last_modified');
	}
	function getDateCreated()
	{
		return $this->reCacheLog->getValue('date_created');
	}
	function getNode()
	{
		return $this->reCacheLog->getValue('node');
	}
	function setNode($value)
	{
		return $this->reCacheLog->setValue('node', $value);
	}

	function getOwnerNotified()
	{
		return $this->reCacheLog->getValue('owner_notified') != 0;
	}
	function setOwnerNotified($value)
	{
		return $this->reCacheLog->setValue('owner_notified', $value ? 1 : 0);
	}

	function getAnyChanged()
	{
		return $this->reCacheLog->getAnyChanged();
	}

	// return if successfull (with insert)
	function save()
	{
		sql_slave_exclude();
		$saved = $this->reCacheLog->save();
		if ($saved && $this->nLogId == ID_NEW)
			$this->nLogId = $this->reCacheLog->getValue('id');
		return $saved;
	}

	function updatePictureStat()
	{
		sql("UPDATE `cache_logs` SET `picture` =
		       (SELECT COUNT(*) FROM `pictures` WHERE `object_type`=1 AND `object_id`='&1')
		     WHERE `id`= '&1'",
         $this->getLogId());
	}

	function allowView()
	{
		global $login;

		$login->verify();
		if (sql_value("SELECT `cache_status`.`allow_user_view` FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE `caches`.`cache_id`='&1'", 0, $this->getCacheId()) == 1)
			return true;
		else if ($login->userid == sql_value("SELECT `user_id` FROM `caches` WHERE `cache_id`='&1'", 0, $this->getCacheId()))
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

	function getValidLogTypes()
	{
		$cache = new cache($this->getCacheId());
		if ($cache->exist() == false)
			return array();
		// if ($cache->allowLog() == false)
		//	 return array();
		// Logic Error - log types are still valid when no NEW logs are allowed for the cache.
		// (Would e.g. block admin logs and log-type restoring for locked caches.)
		return get_cache_log_types($this->getCacheId(),$this->getType());  // depends on userid 
	}
	
	static function isDuplicate($cacheId, $userId, $logType, $logDate, $logText)
	{
		// get info if exact the same values are already in database
		return (sql_value("
							SELECT COUNT(`id`)
							FROM `cache_logs`
							WHERE `cache_id`='&1'
								AND `user_id`='&2'
								AND `type`='&3'
								AND `date`='&4'
								AND `text`='&5'",
						0,
						$cacheId, $userId, $logType, $logDate, $logText) != 0);
	}
	
	static function isMasslogging($userId)
	{
		global $opt;
		
		// check for probably wrong-dated mass logs

		$rs = sql("
					SELECT `date`, `text`
					FROM `cache_logs`
					WHERE `id`= (
						SELECT `id`
						FROM `cache_logs`
						WHERE `user_id`='&1'
						ORDER BY `date_created` DESC,
								 `id` DESC
						LIMIT 1)",
				$userId); 
				
		$rLastLog = sql_fetch_array($rs); 
		sql_free_result($rs); 
		
		if ($rLastLog) 
		{ 
			$rs = sql("
						SELECT COUNT(*) as `masslogs`
						FROM `cache_logs`
						WHERE `user_id`='&1'
							AND `date`='&2'
							AND `text`='&3'",
			$userId,
			$rLastLog['date'],
			$rLastLog['text']);
			 
			$r = sql_fetch_array($rs); 
			$masslogs = $r['masslogs']; 
			sql_free_result($rs); 
		} 
		else 
			$masslogs = 0;
		 
		return ($masslogs > $opt['logic']['masslog']['count']);
	}
}
?>
