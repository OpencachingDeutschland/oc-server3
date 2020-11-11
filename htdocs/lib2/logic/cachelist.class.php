<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 *
 *   get/set has to be committed with save
 *   add/remove etc. is executed instantly
 ***************************************************************************/

require_once __DIR__ . '/../translate.class.php';

define('ERROR_BAD_LISTNAME', 1);
define('ERROR_DUPLICATE_LISTNAME', 2);


class cachelist
{
    public $nCachelistId = 0;
    public $reCachelist;

    public function __construct($nNewCacheListId = ID_NEW, $nUserId = 0)
    {
        $this->reCachelist = new rowEditor('cache_lists');
        $this->reCachelist->addPKInt('id', null, false, RE_INSERT_AUTOINCREMENT);
        $this->reCachelist->addString('uuid', '', false, RE_INSERT_AUTOUUID);
        $this->reCachelist->addInt('node', 0, false);
        $this->reCachelist->addInt('user_id', $nUserId, false);
        $this->reCachelist->addDate('date_created', time(), true, RE_INSERT_IGNORE);
        $this->reCachelist->addDate('last_modified', time(), true, RE_INSERT_IGNORE);
        $this->reCachelist->addDate('last_added', null, true);
        $this->reCachelist->addString('name', '', false);
        $this->reCachelist->addInt('is_public', 0, false);
        $this->reCachelist->addString('description', '', false);
        $this->reCachelist->addInt('desc_htmledit', 1, false);
        $this->reCachelist->addString('password', '', false);

        $this->nCachelistId = $nNewCacheListId + 0;

        if ($nNewCacheListId == ID_NEW) {
            $this->reCachelist->addNew(null);
        } else {
            $this->reCachelist->load($this->nCachelistId);
        }
    }

    public function exist()
    {
        return $this->reCachelist->exist();
    }

    public function getId()
    {
        return $this->nCachelistId;
    }

    public function getUUID()
    {
        return $this->reCachelist->getValue('uuid');
    }

    public function setNode($value)
    {
        return $this->reCachelist->setValue('node', $value);
    }

    public function getNode()
    {
        return $this->reCachelist->getValue('node');
    }

    public function getUserId()
    {
        return $this->reCachelist->getValue('user_id');
    }

    public function isMyList()
    {
        global $login;

        return $this->getUserId() == $login->userid;
    }

    public function getName()
    {
        return $this->reCachelist->getValue('name');
    }

    // 0 = private, 1 = private & friends (not impl.), 2 = public, 3 = public + listing display
    public function getVisibility()
    {
        return $this->reCachelist->getValue('is_public');
    }

    // !! This method returns an error state instead of a success flag; false means "no error".
    public function setNameAndVisibility($name, $visibility)
    {
        $name = trim($name);
        if ($name == '') {
            return ERROR_BAD_LISTNAME;
        }
        if (sql_value(
                "SELECT `id`
                 FROM `cache_lists`
                 WHERE `user_id`='&1' AND `id`<>'&2' AND `name`='&3'",
                false,
                $this->getUserId(),
                $this->getId(),
                $name
            )) {
            // $this->getId() is 0 when creating a new list -> condition has no effect
            return ERROR_DUPLICATE_LISTNAME;
        } elseif ($visibility >= 2 && strlen($name) < 10) {
            return ERROR_BAD_LISTNAME;
        }

        $error = !$this->reCachelist->setValue('name', trim($name));
        if ($visibility == 0 || $visibility == 2 || $visibility == 3) {
            $error |= !$this->reCachelist->setValue('is_public', $visibility);
        }

        return $error;
    }

    // return description in HTML format
    public function getDescription()
    {
        return $this->reCachelist->getValue('description');
    }

    public function getDescriptionForDisplay()
    {
        return use_current_protocol_in_html(getDescription());
    }

    public function getDescHtmledit()
    {
        return $this->reCachelist->getValue('desc_htmledit');
    }

    // set description in HTML format, must be purified!
    public function setDescription($desc, $htmledit)
    {
        $this->reCachelist->setValue('desc_htmledit', $htmledit ? 1 : 0);

        return $this->reCachelist->setValue('description', $desc);
    }

    public function setPassword($pw): void
    {
        $this->reCachelist->setValue('password', $pw);
    }

    public function getPassword()
    {
        return $this->reCachelist->getValue('password');
    }

    public function getCachesCount()
    {
        return sql_value(
            "
            SELECT `entries` FROM `stat_cache_lists`
            WHERE `stat_cache_lists`.`cache_list_id`='" . sql_escape($this->getId()) . "'",
            0
        );
    }

    public function getWatchersCount()
    {
        return sql_value(
            "
            SELECT `watchers` FROM `stat_cache_lists`
            WHERE `stat_cache_lists`.`cache_list_id`='" . sql_escape($this->getId()) . "'",
            0
        );
    }

    public function save()
    {
        if ($this->getVisibility() > 0) {
            $this->setPassword('');
        }
        sql_slave_exclude();
        if ($this->reCachelist->save()) {
            if ($this->getId() == ID_NEW) {
                $this->nCachelistId = $this->reCachelist->getValue('id');
            }

            return true;
        }

        return false;
    }

    // get and set list contents

    // locked/hidden caches may be added to a list by the owner or an administrator,
    // but getCaches() will return visible==0 if the list is queried by someone else.
    // The 'visible' flag MUST be evaluated and the cache name must not be shown
    // if it is 0. This also ensures that cache names are hidden if a cache is locked/hidden
    // after being added to a list.

    public function getCaches()
    {
        global $login;
        $login->verify();

        $rs = sql(
            "
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
            $this->nCachelistId,
            $login->userid,
            ($login->admin & ADMIN_USER) ? 1 : 0
        );

        return sql_fetch_assoc_table($rs);
    }

    public function addCacheByWP($wp)
    {
        $cache = cache::fromWP($wp);
        if (!is_object($cache)) {
            return false;
        }

        return $this->addCache($cache);
    }

    // returns true if all wayPoints were valid, or an array of invalid wayPoints
    public function addCachesByWPs($wps)
    {
        $wpa = explode(' ', trim($wps));
        $non_added_wps = [];
        foreach ($wpa as $wp) {
            $wp = trim($wp);
            if ($wp) {
                $result = $this->addCacheByWP($wp);
                if ($result !== true) {
                    $non_added_wps[] = $wp;
                }
            }
        }
        if (count($non_added_wps)) {
            return $non_added_wps;
        }

        return true;
    }

    public function addCachesByIDs($cache_ids)
    {
        $number_wps = 0;
        foreach ($cache_ids as $cache_id) {
            $result = $this->addCacheByID($cache_id);
            if ($result == true) {
                $number_wps++;
            }
        }
        return $number_wps;
    }

    public function addCacheByID($cache_id)
    {
        return $this->addCache(new cache($cache_id));
    }

    public function addCache($cache)
    {
        if (!$cache->exist() || !$cache->allowView()) {
            return false;
        }
        sql(
            "
            INSERT IGNORE INTO `cache_list_items` (`cache_list_id`, `cache_id`)
            VALUES ('&1', '&2')",
            $this->nCachelistId,
            $cache->getCacheId()
        );

        return true;
    }

    public function removeCacheById($cache_id): void
    {
        sql(
            "DELETE FROM `cache_list_items` WHERE `cache_list_id`='&1' AND `cache_id`='&2'",
            $this->nCachelistId,
            $cache_id
        );
    }

    // watching, bookmarking and access tests
    public function watch($watch): void
    {
        global $login;
        $login->verify();

        if ($login->userid != 0) {
            if ($watch) {
                if ($this->allowView()) {
                    sql(
                        "
                        INSERT IGNORE INTO `cache_list_watches` (`cache_list_id`, `user_id`)
                        VALUES ('&1','&2')",
                        $this->getId(),
                        $login->userid
                    );
                }
            } else {
                sql(
                    "
                    DELETE FROM `cache_list_watches`
                    WHERE `cache_list_id`='&1' AND `user_id`='&2'",
                    $this->getId(),
                    $login->userid
                );
            }
        }
    }

    public function isWatchedByMe()
    {
        global $login;

        return sql_value(
            "SELECT 1 FROM `cache_list_watches`
             WHERE `cache_list_id`='&1' AND `user_id`='&2'",
            0,
            $this->getId(),
            $login->userid
        ) != 0;
    }

    public function bookmark($pw): void
    {
        global $login;

        if ($login->userid != 0 &&
            !$this->isMyList() &&
            ($this->getVisibility() >= 2 || ($this->getPassword() != '' && $pw == $this->getPassword()))
        ) {
            sql(
                "INSERT IGNORE INTO `cache_list_bookmarks` (`cache_list_id`, `user_id`, `password`)
                 VALUES('&1','&2','&3')
                 ON DUPLICATE KEY UPDATE `password`='&3'",
                $this->getId(),
                $login->userid,
                $pw
            );
        }
    }

    public function unbookmark(): void
    {
        global $login;

        sql(
            "DELETE FROM `cache_list_bookmarks`
             WHERE `cache_list_id`='&1' AND `user_id`='&2'",
            $this->getId(),
            $login->userid
        );
    }

    public function allowView($pw = '')
    {
        global $login;

        if (!$this->exist()) {
            return false;
        }

        return $this->isMyList() ||
        $this->getVisibility() >= 2 ||
        ($this->getPassword() != '' && $pw == $this->getPassword()) ||
        sql_value(
            "
                         SELECT COUNT(*)
                         FROM `cache_lists` `cl`
                         LEFT JOIN `cache_list_bookmarks` `clb` ON `clb`.`cache_list_id`=`cl`.`id`
                         WHERE `cl`.`id`='&1' AND `cl`.`password`<>''
                           AND `clb`.`user_id`='&2' AND `clb`.`password`=`cl`.`password`",
            0,
            $this->getId(),
            $login->userid
        );
    }

    // get list of lists -- public static functions
    public static function getMyLists()
    {
        global $login;

        return self::getLists("`cache_lists`.`user_id`='" . sql_escape($login->userid) . "'");
    }

    public static function getListsWatchedByMe()
    {
        global $login;

        return self::getLists(
            "`id` IN (SELECT `cache_list_id` FROM `cache_list_watches` WHERE `user_id`='" . sql_escape(
                $login->userid
            ) . "')"
        );
    }

    public static function getBookmarkedLists()
    {
        global $login;

        return self::getLists(
            "`id` IN (SELECT `cache_list_id` FROM `cache_list_bookmarks` WHERE `user_id`='" . sql_escape(
                $login->userid
            ) . "')"
        );
    }

    public static function getPublicListCount($namelike = '', $userlike = '')
    {
        return sql_value(
            '
            SELECT COUNT(*)
            FROM `cache_lists`
            LEFT JOIN `stat_cache_lists` ON  `stat_cache_lists`.`cache_list_id`=`cache_lists`.`id`
            LEFT JOIN `user` ON `user`.`user_id`=`cache_lists`.`user_id`
            WHERE `is_public`>=2 AND `entries`>0'
            . ($namelike ? " AND `name` LIKE '%" . sql_escape($namelike) . "%'" : '')
            . ($userlike ? " AND `username` LIKE '%" . sql_escape($userlike) . "%'" : ''),
            0
        );
    }

    public static function getPublicLists($startat = 0, $maxitems = PHP_INT_MAX, $namelike = '', $userlike = '')
    {
        return self::getLists(
            '`is_public`>=2 AND `entries`>0'
            . ($namelike ? " AND `name` LIKE '%" . sql_escape($namelike) . "%'" : '')
            . ($userlike ? " AND `username` LIKE '%" . sql_escape($userlike) . "%'" : ''),
            0,
            (int) $startat,
            (int) $maxitems,
            true
        );
    }

    public static function getPublicListsOf($userid)
    {
        return self::getLists(
            "`is_public`>=2 AND `entries`>0 AND `cache_lists`.`user_id`='" . sql_escape($userid) . "'"
        );
    }

    // If $all is false, only own lists and public lists of the cache owner will be returned.
    public static function getListsByCacheId($cacheid, $all)
    {
        global $login;

        $cache_owner_id = sql_value(
            "
            SELECT `user_id`
            FROM `caches`
            WHERE `cache_id`='" . sql_escape($cacheid) . "'",
            0
        );
        $my_watches = sql_fetch_column(
            sql("SELECT `cache_list_id` FROM `cache_list_watches` WHERE `user_id`='&1'", $login->userid)
        );

        return self::getLists(
            "
            `id` IN
                (SELECT `cache_list_id`
                 FROM `cache_list_items`
                 WHERE `cache_id`='" . sql_escape($cacheid) . "')
            AND
            (
                `cache_lists`.`user_id`='" . sql_escape($login->userid) . "' " .
            ($all ? 'OR `is_public`= 3 ' : '') .
            "OR (`is_public`> 0 AND
                   `cache_lists`.`id` IN ('" . implode("','", array_map('sql_escape', $my_watches)) . "'))
            )",
            "`cache_lists`.`user_id`<>'" . sql_escape($cache_owner_id) . "'",
            0,
            20,
            true
        );
    }

    public static function getListById($listid)
    {
        $lists = self::getLists("`id`='" . sql_escape($listid) . "'");
        if (count($lists)) {
            $lists[0]['description_for_display'] = use_current_protocol_in_html($lists[0]['description']);

            return $lists[0];
        }

        return false;
    }

    private static function getLists(
        $condition,
        $prio = 0,
        $startat = 0,
        $maxitems = PHP_INT_MAX,
        $strip_nagchars = false
    ) {
        global $login;
        $login->verify();

        $nameField = ($strip_nagchars ? 'STRIP_LEADING_NONALNUM(`cache_lists`.`name`)' : '`cache_lists`.`name`');
        $rs = sql(
            "SELECT `cache_lists`.`id`, `cache_lists`.`user_id`, `user`.`username`,
                    $nameField `name`,
                    `cache_lists`.`is_public` `visibility`, `cache_lists`.`password`,
                    `cache_lists`.`description`, `cache_lists`.`desc_htmledit`,
                    `cache_lists`.`user_id`='&1' `own_list`,
                    `stat_cache_lists`.`entries`, `stat_cache_lists`.`watchers`,
                    `w`.`user_id` IS NOT NULL `watched_by_me`,
                    `b`.`user_id` IS NOT NULL `bookmarked`,
                    $prio `prio`
             FROM `cache_lists`
             LEFT JOIN `stat_cache_lists` ON `stat_cache_lists`.`cache_list_id`=`cache_lists`.`id`
             LEFT JOIN `user` ON `user`.`user_id`=`cache_lists`.`user_id`
             LEFT JOIN `cache_list_watches` `w` ON `w`.`cache_list_id`=`cache_lists`.`id` AND `w`.`user_id`='&1'
             LEFT JOIN `cache_list_bookmarks` `b` ON `b`.`cache_list_id`=`cache_lists`.`id` AND `b`.`user_id`='&1'
             WHERE $condition
             ORDER BY `prio`, $nameField
             LIMIT &2,&3",
            $login->userid,
            (int) $startat,
            (int) $maxitems
        );

        $lists = sql_fetch_assoc_table($rs);
        foreach ($lists as &$list) {
            $list['description_for_display'] = use_current_protocol_in_html($list['description']);
        }

        return $lists;
    }

    // other
    public static function getMyLastAddedToListId()
    {
        global $login;
        $login->verify();

        $maxDate = sql_value("SELECT MAX(`last_added`) FROM `cache_lists` WHERE `user_id`='&1'", null, $login->userid);
        if (!$maxDate) {
            return 0;
        }

        return sql_value(
            "SELECT `id` FROM `cache_lists`
             WHERE `user_id`='&1' AND `last_added`='&2'
             LIMIT 1",
            0,
            $login->userid,
            $maxDate
        );
    }

    public static function watchingCacheByListsCount($userId, $cacheId)
    {
        if (!$userId) {
            return 0;
        }

        return sql_value(
            "SELECT COUNT(*)
             FROM `cache_list_watches` `clw`, `cache_list_items` `cli`
             WHERE `clw`.`user_id`='&1' AND `cli`.`cache_id`='&2' AND `clw`.`cache_list_id`=`cli`.`cache_list_id`",
            0,
            $userId,
            $cacheId
        );
    }
}
