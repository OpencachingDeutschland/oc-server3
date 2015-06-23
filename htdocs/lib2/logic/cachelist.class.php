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
require_once($opt['rootpath'] . 'lib2/translate.class.php');
require_once($opt['rootpath'] . 'lib2/OcHTMLPurifier.class.php');


class cachelist
{
	var $nCachelistId = 0;
	var $reCachelist;

	function __construct($nNewCachelistId=ID_NEW, $nUserId=0)
	{
		global $opt;

		$this->reCachelist = new rowEditor('cache_lists');
		$this->reCachelist->addPKInt('id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->reCachelist->addString('uuid', '', false, RE_INSERT_AUTOUUID);
		$this->reCachelist->addInt('user_id', $nUserId, false);
		$this->reCachelist->addDate('date_created', time(), true, RE_INSERT_IGNORE);
		$this->reCachelist->addDate('last_modified', time(), true, RE_INSERT_IGNORE);
		$this->reCachelist->addDate('last_added', null, true);
		$this->reCachelist->addString('name', '', false);
		$this->reCachelist->addInt('is_public', 0, false);
		$this->reCachelist->addString('description', '', false);
		$this->reCachelist->addInt('desc_htmledit', 0, false);

		$this->nCachelistId = $nNewCachelistId + 0;

		if ($nNewCachelistId == ID_NEW)
			$this->reCachelist->addNew(null);
		else
			$this->reCachelist->load($this->nCachelistId);
	}

	function exist()
	{
		return $this->reCachelist->exist();
	}

	function getId()
	{
		return $this->nCachelistId;
	}

	function getUUID()
	{
		return $this->reCachelist->getValue('uuid');
	}

	function getUserId()
	{
		return $this->reCachelist->getValue('user_id');
	}

	function getName()
	{
		return $this->reCachelist->getValue('name');
	}

	function setName($value)
	{
		if (trim($value))      // name must not be empty
			return $this->reCachelist->setValue('name', trim($value));
		else
			return false;
	}

	function isPublic()
	{
		return $this->reCachelist->getValue('is_public');
	}

	function setPublic($value)
	{
		return $this->reCachelist->setValue('is_public', $value ? 1 : 0); 
	}

	// return description in HTML format
	function getDescription()
	{
		return $this->reCachelist->getValue('description');
	}

	function getDescHtmledit()
	{
		return $this->reCachelist->getValue('desc_htmledit');
	}

	// set description in HTML format
	function setDescription($desc, $htmledit)
	{
		global $opt;
		$this->reCachelist->setValue('desc_htmledit', $htmledit ? 1 : 0);
		$purifier = new OcHTMLPurifier($opt);
		return $this->reCachelist->setValue('description', $purifier->purify($desc));
	}

	function getCachesCount()
	{
		return sql_value("
			SELECT `entries` FROM `stat_cache_lists` 
			WHERE `stat_cache_lists`.`cache_list_id`=" . sql_escape($this->getId()),
			0);
	}

	function getWatchersCount()
	{
		return sql_value("
			SELECT `watchers` FROM `stat_cache_lists` 
			WHERE `stat_cache_lists`.`cache_list_id`=" . sql_escape($this->getId()),
			0);
	}

	function save()
	{
		sql_slave_exclude();
		if ($this->reCachelist->save())
		{
			if ($this->getId() == ID_NEW)
				$this->nCachelistId = $this->reCachelist->getValue('id');
			return true;
		}
		else
			return false;
	}


	// get and set list contents

	// locked/hidden caches may be added to a list by the owner or an administrator,
	// but getCaches() will return visible==0 if the list is queried by someone else.
	// The 'visible' flag MUST be evaluated and the cache name must not be shown 
	// if it is 0. This also ensures that cache names are hidden if a cache is locked/hidden
	// after being added to a list.

	function getCaches()
	{
		global $login;
		$login->verify();

		$rs = sql("
			SELECT `cache_list_items`.`cache_id`, `caches`.`wp_oc`, `caches`.`name`,
			       `caches`.`type`, `caches`.`status`,
			       (`cache_status`.`allow_user_view` OR `caches`.`user_id`='&2' OR '&3') AS `visible`,
			       `ca`.`attrib_id` IS NOT NULL AS `oconly`
			FROM `cache_list_items`
			LEFT JOIN `caches` ON `caches`.`cache_id`=`cache_list_items`.`cache_id`
			LEFT JOIN `cache_status` ON `cache_status`.`id`=`caches`.`status`
			LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6
			WHERE `cache_list_items`.`cache_list_id` = '&1'
			ORDER BY `caches`.`name`",
			$this->nCachelistId, $login->userid, ($login->admin & ADMIN_USER) ? 1 : 0);
		return sql_fetch_assoc_table($rs); 
	}

	function addCacheByWP($wp)
	{
		global $translate;

		$cache = cache::fromWP($wp);
		if (!is_object($cache))
			return false;
		else
			return $this->addCache($cache);
	}

	// returns true if all waypoints were valid, or an array of invalid waypoints  
	function addCachesByWPs($wps)
	{
		$wpa = explode(' ', trim($wps));
		$non_added_wps = array();
		foreach ($wpa as $wp)
		{
			$wp = trim($wp);
			if ($wp)
			{
				$result = $this->addCacheByWP($wp);
				if ($result !== true)
					$non_added_wps[] = $wp;
			}
		}
		if (count($non_added_wps))
			return $non_added_wps;
		else
			return true;
	}

	function addCacheByID($cache_id)
	{
		return $this->addCache(new cache($cache_id));
	}

	function addCache($cache)
	{
		global $translate;

		if (!$cache->exist() || !$cache->allowView()) 
			return false;
		else
		{
			sql("
				INSERT IGNORE INTO `cache_list_items` (`cache_list_id`, `cache_id`)
				VALUES ('&1', '&2')",
				$this->nCachelistId, $cache->getCacheId());
			return true;
		}
	}

	function removeCacheById($cache_id)
	{
		sql("DELETE FROM `cache_list_items` WHERE `cache_list_id`='&1' AND `cache_id`='&2'",
		    $this->nCachelistId, $cache_id);
	}

	function watch($watch)
	{
		global $login;
		$login->verify();

		if ($login->userid != 0)
		{
			if ($watch)
			{
				if ($this->isPublic() || $this->getUserId() == $login->userid)
					sql("
						INSERT IGNORE INTO `cache_list_watches` (`cache_list_id`, `user_id`) 
						VALUES ('&1','&2')",
				    $this->getId(), $login->userid);
			}
			else
			{
				sql("
					DELETE FROM `cache_list_watches`
					WHERE `cache_list_id`='&1' AND `user_id`='&2'",
					$this->getId(), $login->userid);
			}
		}
	}

	function isWatchedByMe()
	{
		global $login;
		return sql_value("
			SELECT 1 FROM `cache_list_watches`
			WHERE `cache_list_id`='&1' AND `user_id`='&2'",
			0, $this->getId(), $login->userid) != 0;
	}


	// get list of lists -- static functions

	static function getMyLists()
	{
		global $login;
		return cachelist::getLists("`cache_lists`.`user_id`=" . sql_escape($login->userid));
	}

	static function getListsWatchedByMe()
	{
		global $login;
		return cachelist::getLists("`id` IN (SELECT `cache_list_id` FROM `cache_list_watches` WHERE `user_id`=" . sql_escape($login->userid) . ")");
	}

	static function getPublicListCount($namelike='', $userlike='')
	{
		return sql_value("
			SELECT COUNT(*) 
			FROM `cache_lists`
			LEFT JOIN `stat_cache_lists` ON  `stat_cache_lists`.`cache_list_id`=`cache_lists`.`id`
			LEFT JOIN `user` ON `user`.`user_id`=`cache_lists`.`user_id`
			WHERE `is_public` AND `entries`>0"
			. ($namelike ? " AND `name` LIKE '%" . sql_escape($namelike) ."%'" : '')
			. ($userlike ? " AND `username` LIKE '%" . sql_escape($userlike) . "%'" : ''),
			0);
	}

	static function getPublicLists($startat=0, $maxitems=PHP_INT_MAX, $namelike='', $userlike='')
	{
		return cachelist::getLists(
			"`is_public` AND `entries`>0"
			. ($namelike ? " AND `name` LIKE '%" . sql_escape($namelike) ."%'" : '')
			. ($userlike ? " AND `username` LIKE '%" . sql_escape($userlike) . "%'" : ''),
			$startat,
			$maxitems);
	}

	static function getPublicListsOf($userid)
	{
		return cachelist::getLists("`is_public` AND `entries`>0 AND `cache_lists`.`user_id`=" . sql_escape($userid));
	}

	// If $all is false, only own lists and public lists of the cache owner will be returned.
	static function getListsByCacheId($cacheid, $all)
	{
		global $login;

		if ($all)
			$cache_owner_id = 0;   // dummy
		else
			$cache_owner_id = sql_value("
				SELECT `user_id`
				FROM `caches`
				WHERE `cache_id`=" . sql_escape($cacheid),
				0);

		return cachelist::getLists("
			`cache_lists`.`id` IN
				(SELECT `cache_list_id`
				 FROM `cache_list_items`
				 WHERE `cache_id`=" . sql_escape($cacheid) . ")
			AND
			(" .
				($all ? "is_public OR " : "") . "
			  `cache_lists`.`user_id`=" . sql_escape($login->userid) . " OR
			  (is_public AND `cache_lists`.`user_id`=" . sql_escape($cache_owner_id) . ")
			)");
	}

	static function getListById($listid)
	{
		$lists = cachelist::getLists("`id`=" . sql_escape($listid));
		if (count($lists))
			return $lists[0];
		else
			return false;
	}

	private function getLists($condition, $startat=0, $maxitems=PHP_INT_MAX)
	{
		global $login;
		$login->verify();

		$rs = sql("
			SELECT `cache_lists`.`id`, `cache_lists`.`user_id`, `user`.`username`, 
			       `cache_lists`.`name`, `cache_lists`.`is_public`,
			       `cache_lists`.`description`, `cache_lists`.`desc_htmledit`,
			       `cache_lists`.`user_id`='&1' `own_list`,
			       `stat_cache_lists`.`entries`, `stat_cache_lists`.`watchers`,
			       `w`.`user_id` IS NOT NULL `watched_by_me`
			FROM `cache_lists`
			LEFT JOIN `stat_cache_lists` ON `stat_cache_lists`.`cache_list_id`=`cache_lists`.`id`
			LEFT JOIN `user` ON `user`.`user_id`=`cache_lists`.`user_id`
			LEFT JOIN `cache_list_watches` `w` ON `w`.`cache_list_id`=`cache_lists`.`id` AND `w`.`user_id`='&1'
			WHERE $condition
			ORDER BY `name`
			LIMIT &2,&3", 
			$login->userid, $startat, $maxitems);
		return sql_fetch_assoc_table($rs);
	}

	static function getMyLastAddedToListId()
	{
		global $login;
		$login->verify();

		$maxdate = sql_value("SELECT MAX(`last_added`) FROM `cache_lists` WHERE `user_id`='&1'", null, $login->userid);
		if (!$maxdate)
			return 0;
		else
			return sql_value("
				SELECT `id` FROM `cache_lists` 
			  WHERE `user_id`='&1' AND `last_added`='&2'
				LIMIT 1", 0,
			  $login->userid, $maxdate);
	}

}

?>
