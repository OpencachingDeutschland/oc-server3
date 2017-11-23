<?php
/***************************************************************************
 * for license information see LICENSE.md
 *   get/set has to be committed with save
 *   add/remove etc. is executed instantly
 ***************************************************************************/

require_once __DIR__ . '/logtypes.inc.php';

class cache
{
    public $nCacheId = 0;

    public $reCache;

    /**
     * @param $wp
     * @return int|null
     */
    public static function cacheIdFromWP($wp)
    {
        if (mb_strtoupper(mb_substr($wp, 0, 2)) === 'GC') {
            $rs = sql("SELECT `cache_id` FROM `caches` WHERE `wp_gc_maintained`='&1'", $wp);
            if (sql_num_rows($rs) !== 1) {
                sql_free_result($rs);

                return null;
            }
            $r = sql_fetch_assoc($rs);
            sql_free_result($rs);

            $cacheId = $r['cache_id'];
        } else {
            $cacheId = sql_value("SELECT `cache_id` FROM `caches` WHERE `wp_oc`='&1'", 0, $wp);
        }

        return $cacheId;
    }

    /**
     * @param $wp
     * @return cache|null
     */
    public static function fromWP($wp)
    {
        $cacheId = self::cacheIdFromWP($wp);
        if ($cacheId === 0) {
            return null;
        }

        return new self($cacheId);
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public static function cacheIdFromUUID($uuid)
    {
        $cacheId = sql_value("SELECT `cache_id` FROM `caches` WHERE `uuid`='&1'", 0, $uuid);

        return $cacheId;
    }

    /**
     * @param $uuid
     * @return cache|null
     */
    public static function fromUUID($uuid)
    {
        $cacheId = self::cacheIdFromUUID($uuid);
        if ($cacheId === 0) {
            return null;
        }

        return new self($cacheId);
    }

    public function __construct($nNewCacheId = ID_NEW)
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
        $this->reCache->addString('wp_gc_maintained', '', false);
        $this->reCache->addString('wp_nc', '', false);
        $this->reCache->addString('desc_languages', '', false, RE_INSERT_IGNORE);
        $this->reCache->addString('default_desclang', '', false);
        $this->reCache->addDate('date_activate', null, true);
        $this->reCache->addInt('need_npa_recalc', 1, false, RE_INSERT_IGNORE);
        $this->reCache->addInt('show_cachelists', 1, false);
        $this->reCache->addInt('protect_old_coords', 0, false);
        $this->reCache->addInt('needs_maintenance', 0, false);
        $this->reCache->addInt('listing_outdated', 0, false);
        $this->reCache->addDate('flags_last_modified', '0000-00-00 00:00:00', false);

        $this->nCacheId = $nNewCacheId + 0;

        if ($nNewCacheId == ID_NEW) {
            $this->reCache->addNew(null);
        } else {
            $this->reCache->load($this->nCacheId);
        }
    }

    /**
     * @return bool
     */
    public function exist()
    {
        return $this->reCache->exist();
    }

    /**
     * @return int
     */
    public function getCacheId()
    {
        return $this->nCacheId;
    }

    public function getStatus()
    {
        return $this->reCache->getValue('status');
    }

    public function getType()
    {
        return $this->reCache->getValue('type');
    }

    public function getName()
    {
        return $this->reCache->getValue('name');
    }

    public function getLongitude()
    {
        return $this->reCache->getValue('longitude');
    }

    public function getLatitude()
    {
        return $this->reCache->getValue('latitude');
    }

    public function getUserId()
    {
        return $this->reCache->getValue('user_id');
    }

    public function getUsername()
    {
        return sql_value("SELECT `username` FROM `user` WHERE `user_id`='&1'", '', $this->getUserId());
    }

    public function getWPOC()
    {
        return $this->reCache->getValue('wp_oc');
    }

    public function getWPGC()
    {
        return $this->reCache->getValue('wp_gc');
    }

    public function getWPGC_maintained()
    {
        return $this->reCache->getValue('wp_gc_maintained');
    }

    public function getUUID()
    {
        return $this->reCache->getValue('uuid');
    }

    public function getDateCreated()
    {
        return $this->reCache->getValue('date_created');
    }

    public function getLastModified()
    {
        return $this->reCache->getValue('last_modified');
    }

    public function getListingLastModified()
    {
        return $this->reCache->getValue('listing_last_modified');
    }

    public function getNode()
    {
        return $this->reCache->getValue('node');
    }

    public function setNode($value)
    {
        return $this->reCache->setValue('node', $value);
    }

    public function setStatus($value)
    {
        global $login;
        if (sql_value("SELECT COUNT(*) FROM `cache_status` WHERE `id`='&1'", 0, $value) == 1) {
            sql("SET @STATUS_CHANGE_USER_ID='&1'", $login->userid);

            return $this->reCache->setValue('status', $value);
        }

        return false;
    }

    public function getDescLanguages()
    {
        return explode($this->reCache->getValue('desc_languages'), ',');
    }

    public function getDefaultDescLanguage()
    {
        return $this->reCache->getValue('default_desclang');
    }

    public function getProtectOldCoords()
    {
        return $this->reCache->getValue('protect_old_coords');
    }

    public function setProtectOldCoords($value)
    {
        return $this->reCache->setValue('protect_old_coords', $value);
    }

    // cache condition flags
    public function getNeedsMaintenance()
    {
        return $this->reCache->getValue('needs_maintenance');
    }

    public function setNeedsMaintenance($value)
    {
        return $this->reCache->setValue('needs_maintenance', $value);
    }

    public function getListingOutdated()
    {
        return $this->reCache->getValue('listing_outdated');
    }

    public function getListingOutdatedLogUrl()
    {
        $url = '';
        $rs = sql(
            "SELECT `id`, `listing_outdated`
             FROM `cache_logs`
             WHERE `cache_id`='&1'
             AND `listing_outdated`>0
             ORDER BY `order_date` DESC, `date_created` DESC, `id` DESC",
            // same sorting order as in DB function sp_update_logstat()
            $this->getCacheId()
        );
        if ($r = sql_fetch_assoc($rs)) {
            if ($r['listing_outdated'] == 2) {
                $url = 'viewlogs.php?cacheid=' . $this->getCacheId() . '#log' . $r['id'];
            }
        }
        sql_free_result($rs);

        return $url;
    }

    // test if af the time of insertion of an existing log the listing
    // was outdated and there is no newer listing-[not]-outdated-log

    public function getListingOutdatedRelativeToLog($logId)
    {
        $logDate = sql_value("SELECT `order_date` FROM `cache_logs` WHERE `id`='&1'", '', $logId);

        return
            sql_value(  // Is there a newer log with LO flag?
                "SELECT 1
                 FROM `cache_logs`
                 WHERE `cache_id`='&1'
                 AND `order_date`>'&2'
                 AND `listing_outdated`>0
                 LIMIT 1",
                0,
                $this->getCacheId(),
                $logDate
            ) == 0 && sql_value(  // Was the listing outdated before the log was posted?
                "SELECT `listing_outdated`
                 FROM `cache_logs`
                 WHERE `cache_id`='&1'
                 AND `listing_outdated`>0
                 AND `id`!='&2'
                 ORDER BY `order_date` DESC, `date_created` DESC, `id` DESC
                 LIMIT 1",
                0,
                $this->getCacheId(),
                $logId
            ) == 2;
    }

    public function setListingOutdated($value)
    {
        return $this->reCache->setValue('listing_outdated', $value);
    }

    public function getConditionHistory()
    {
        $cache_id = $this->nCacheId;
        $rs = sql(
            "SELECT `date`, `needs_maintenance`, `listing_outdated`
         FROM `cache_logs`
         WHERE `cache_id`='&1' AND (`needs_maintenance` IS NOT NULL OR `listing_outdated` IS NOT NULL)
         ORDER BY `date`, `id`",
            $cache_id
        );
        $nm = $lo = 0;
        $cond = [
            [
                'needs_maintenance' => $nm,
                'listing_outdated' => $lo,
                'date' => '0000-00-00 00:00:00',
            ],
        ];
        while ($r = sql_fetch_assoc($rs)) {
            if ($r['needs_maintenance'] != $nm || $r['listing_outdated'] != $lo) {
                $nm = $r['needs_maintenance'];
                $lo = $r['listing_outdated'];
                $cond[] = [
                    'needs_maintenance' => $nm,
                    'listing_outdated' => $lo,
                    'date' => $r['date'],
                ];
            }
        }
        sql_free_result($rs);
        $cond[] = [
            'needs_maintenance' => $nm,
            'listing_outdated' => $lo,
            'date' => '9999-12-31 23:59:59',
        ];

        return $cond;
    }

    /**
     * @param int $attr_id
     * @return bool
     */
    public function hasAttribute($attr_id)
    {
        return sql_value(
            "SELECT 1
             FROM `caches_attributes` `ca`
             WHERE `ca`.`cache_id`='&1'
             AND `attrib_id`='&2'",
            0,
            $this->nCacheId,
            $attr_id
        ) == 1;
    }

    // other
    public function getAnyChanged()
    {
        return $this->reCache->getAnyChanged();
    }

    // return if successful (with insert)
    public function save()
    {
        if ($this->reCache->save()) {
            sql_slave_exclude();

            return true;
        }

        return false;
    }

    public function requireLogPW()
    {
        return $this->reCache->getValue('logpw') != '';
    }

    // TODO: use prepared one way hash
    public function validateLogPW($nLogType, $sLogPW)
    {
        if (!$this->requireLogPW()) {
            return true;
        }

        if (sql_value("SELECT `require_password` FROM `log_types` WHERE `id`='&1'", 0, $nLogType) == 0) {
            return true;
        }

        return ($sLogPW == $this->reCache->getValue('logpw'));
    }

    /**
     * @param $nVisitUserId
     * @param $sRemoteAddr
     * @param $nCacheId
     */
    public static function visitCounter($nVisitUserId, $sRemoteAddr, $nCacheId)
    {
        global $opt, $_SERVER;

        // delete cache_visits older 1 day 60*60*24 = 86400
        sql(
            "DELETE FROM `cache_visits`
             WHERE `cache_id`='&1'
             AND `user_id_ip`!='0'
             AND NOW()-`last_modified`>86400",
            $nCacheId
        );

        if ($nVisitUserId == 0) {
            $se = explode(';', $opt['logic']['search_engines']);
            $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            foreach ($se as $s) {
                if (strpos($ua, $s) !== false) {
                    return;
                }
            }   // do not count search engine views

            $sIdentifier = $sRemoteAddr;
        } else {
            $sIdentifier = $nVisitUserId;
        }

        // note the visit of this user
        sql(
            "INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`)
             VALUES (&1, '&2', 1)
             ON DUPLICATE KEY UPDATE `count`=`count`+1",
            $nCacheId,
            $sIdentifier
        );

        // if the previous statement does an INSERT, it was the first visit for this user today
        if (sql_affected_rows() == 1) {
            if ($nVisitUserId != sql_value("SELECT `user_id` FROM `caches` WHERE `cache_id`='&1'", 0, $nCacheId)) {
                // increment the counter for this cache
                sql(
                    "INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`)
                     VALUES (&1, '0', 1)
                     ON DUPLICATE KEY UPDATE `count`=`count`+1",
                    $nCacheId
                );
            }
        }
    }

    /**
     * @param $cacheId
     * @return array
     */
    public static function getLogsCount($cacheId)
    {
        //prepare the logs
        $rsLogs = sql("SELECT COUNT(*) FROM `cache_logs` WHERE `cache_id`='&1'", $cacheId);
        $rLog = sql_fetch_assoc($rsLogs);
        sql_free_result($rsLogs);

        return $rLog;
    }

    /**
     * @param int $start
     * @param int $count
     * @param bool $deleted
     * @param bool $protect_old_coords
     * @return array
     */
    public function getLogsArray($start, $count, $deleted = false, $protect_old_coords = false)
    {
        global $login, $translate;

        // negative or abornally high numbers like 1.0E+15 can crash the LIMIT statement
        if ($count <= 0 || $count > 10000) {
            return [];
        }

        $coords = [];
        if ($this->getType() != 7 && $this->getType() != 8 &&  // quiz cache
            $this->hasAttribute(61) != true &&                 // safari cache
            $this->getStatus() != 5                            // unpublished cache
        ) {
            $rsCoords = sql(
                "SELECT
                    `date_created` `date`,
                    `date_created` - INTERVAL 4 SECOND AS `adjusted_date_created`,
                     /*
                        The first cache_coordinates entry is created immediately after
                        inserting the cache into caches table. This usually takes
                        a few milliseconds. We apply a 4 seconds 'safety margin' for
                        detecting the original coords. In the extremly unlikely case that
                        it took more than 4 seconds AND the owner did a coordinate change
                        before publish AND someone posts a log entry with date inbetween
                        the coord change and the publish, the coords before the change
                        will be exposed (which would be unpleasant but not critical).

                        Increasing the 4 seconds lag would increase the chance that
                        original coords which were set immediately before publish are
                        hidden (which would be unpleasant but not critical).

                        4 seconds was chosen as estimated shortest interval someone will
                        need to change coordinates of an unpublished cache AND publish
                        it, minus 1 second.
                     */
                    `latitude`,
                    `longitude`
                 FROM `cache_coordinates`
                 WHERE `cache_id`='&1'
                 ORDER BY `date_created` DESC",
                $this->nCacheId
            );
            $publish_date = $this->getDateCreated();
            while ($rCoord = sql_fetch_assoc($rsCoords)) {
                $coords[] = $rCoord;
                if (strtotime($rCoord['adjusted_date_created']) <= $publish_date) {
                    // This are the cache coords at publish time, which we will
                    // show as "original coords". Older coordinate changes
                    // (before publish) are discarded.
                    break;
                }
            }
            sql_free_result($rsCoords);
        }

        if ($deleted && ($login->admin && ADMIN_USER) > 0) {
            // admins may view owner-deleted logs
            $table = 'cache_logs_archived';
            $delfields = 'IFNULL(`u2`.`username`,"") AS `deleted_by_name`, `deletion_date`, "1" AS `deleted`';
            $addjoin = 'LEFT JOIN `user` `u2` ON `u2`.`user_id`=`cache_logs`.`deleted_by`';
        } else {
            $table = 'cache_logs';
            $delfields = '"" AS `deleted_by_name`, NULL AS `deletion_date`, "0" AS `deleted`';
            $addjoin = '';
        }

        $rsLogs = sql(
            "SELECT `cache_logs`.`user_id` AS `userid`,
                    `cache_logs`.`id` AS `id`,
                    `cache_logs`.`uuid` AS `uuid`,
                    `cache_logs`.`date` AS `date`,
                    `cache_logs`.`order_date` AS `order_date`,
                    `cache_logs`.`entry_last_modified`,
                    (`cache_logs`.`node` != 1 OR `cache_logs`.`entry_last_modified` != '2017-02-14 22:49:20')  /* see redmine #1109 */
                    AND DATEDIFF(`cache_logs`.`entry_last_modified`, `cache_logs`.`date_created`) >= 1 AS `late_modified`,
                    substr(`cache_logs`.`date`,12) AS `time`,  /* 00:00:01 = 00:00 logged, 00:00:00 = no time */
                    `cache_logs`.`type` AS `type`,
                    `cache_logs`.`oc_team_comment` AS `oc_team_comment`,
                    `cache_logs`.`needs_maintenance` AS `needs_maintenance`,
                    `cache_logs`.`listing_outdated` AS `listing_outdated`,
                    `cache_logs`.`text` AS `text`,
                    `cache_logs`.`text_html` AS `texthtml`,
                    `cache_logs`.`picture`,
                    " . $delfields . ",
                    `user`.`username` AS `username`,
                    IF(ISNULL(`cache_rating`.`cache_id`), 0, `cache_logs`.`type` IN (1,7)) AS `recommended`,
                    FALSE AS `cache_moved`
             FROM $table AS `cache_logs`
             INNER JOIN `user`
                 ON `user`.`user_id` = `cache_logs`.`user_id`
             LEFT JOIN `cache_rating`
                 ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id`
                 AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
                 AND `cache_logs`.`date`=`cache_rating`.`rating_date`
             " . $addjoin . "
             WHERE `cache_logs`.`cache_id`='&1'
             ORDER BY `cache_logs`.`order_date` DESC, `cache_logs`.`date_created` DESC, `id` DESC
             LIMIT &2, &3",
            $this->nCacheId,
            $start + 0,
            $count + 0
        );

        $logs = [];
        $coord_changes = (count($coords) > 1);
        if ($coord_changes) {
            $coordpos = 0;
            $current_coord = new coordinate($coords[0]['latitude'], $coords[0]['longitude']);
        }
        $displayed_coord_changes = 0;

        while ($rLog = sql_fetch_assoc($rsLogs)) {
            $pictures = [];
            $rsPictures = sql(
                "SELECT `url`, `title`, `uuid`, `id`, `spoiler`
                 FROM `pictures`
                 WHERE `object_id`='&1'
                 AND `object_type`=1
                 ORDER BY `seq`",
                $rLog['id']
            );
            while ($rPicture = sql_fetch_assoc($rsPictures)) {
                if (trim($rPicture['title']) == '') {
                    $rPicture['title'] = $translate->t('Picture', '', '', 0) . ' ' . (count($pictures) + 1);
                }
                $rPicture['url'] = use_current_protocol($rPicture['url']);
                $pictures[] = $rPicture;
            }
            sql_free_result($rsPictures);
            $rLog['pictures'] = $pictures;
            $rLog['text'] = use_current_protocol_in_html($rLog['text']);

            if ($coord_changes) {
                $newcoord = false;
                while ($coordpos < count($coords) && $coords[$coordpos]['date'] > $rLog['order_date']) {
                    if (!$newcoord) {
                        $newcoord = new coordinate($coords[$coordpos]['latitude'], $coords[$coordpos]['longitude']);
                    }
                    ++$coordpos;
                }
                if ($newcoord) {
                    if ($coordpos < count($coords)) {
                        $distance = geomath::calcDistance(
                            $newcoord->nLat,
                            $newcoord->nLon,
                            $coords[$coordpos]['latitude'],
                            $coords[$coordpos]['longitude']
                        );
                        if (abs($distance) > 0.005) {
                            $rLog['newcoord'] = $newcoord->getDecimalMinutes($protect_old_coords && $new != $current_coord);
                            if ($protect_old_coords) {
                                $rLog['movedbykm'] = false;
                            } elseif ($distance <= 1) {
                                $rLog['movedbym'] = floor($distance * 1000);
                            } elseif ($distance < 10) {
                                $rLog['movedbykm'] = sprintf('%1.1f', $distance);
                            } else {
                                $rLog['movedbykm'] = round($distance);
                            }
                            $rLog['cache_moved'] = true;
                            ++$displayed_coord_changes;
                        }
                    } else {
                        // This is the original coord of the cache.
                        $rLog['newcoord'] = $newcoord->getDecimalMinutes($protect_old_coords);
                        $rLog['cache_moved'] = false;
                    }
                }
            }

            $logs[] = $rLog;
        }
        sql_free_result($rsLogs);

        // Append a dummy log entry for the original coordinate, if it was
        // not added to a a real log entry because there are logs older than the
        // OC cache listing (cmp. https://redmine.opencaching.de/issues/1102):

        if ($displayed_coord_changes > 0 && $coordpos < count($coords) && count($logs) > 0) {
            $coord = new coordinate($coords[$coordpos]['latitude'], $coords[$coordpos]['longitude']);
            $logs[] = [
                'newcoord' => $coord->getDecimalMinutes($protect_old_coords),
                'cache_moved' => false,
                'type' => false,
            ];
        }

        return $logs;
    }

    /**
     * @param $userId
     * @param $reportReason
     * @param $reportNote
     * @return bool
     */
    public function report($userId, $reportReason, $reportNote)
    {
        sql(
            "INSERT INTO cache_reports (`cacheid`, `userid`, `reason`, `note`)
             VALUES(&1, &2, &3, '&4')",
            $this->nCacheId,
            $userId,
            $reportReason,
            $reportNote
        );

        return true;
    }

    /**
     * @param $userId
     * @return bool|string
     */
    public function addAdoption($userId)
    {
        if ($this->allowEdit() == false) {
            return 'noaccess';
        }

        if (sql_value("SELECT COUNT(*) FROM `user` WHERE `user_id`='&1'", 0, $userId) == 0) {
            return 'userunknown';
        }

        if (sql_value("SELECT COUNT(*) FROM `user` WHERE `user_id`='&1' AND `is_active_flag`=1", 0, $userId) == 0) {
            return 'userdisabled';
        }

        // same user?
        if ($this->getUserId() == $userId) {
            return 'self';
        }

        sql(
            "INSERT IGNORE INTO `cache_adoption` (`cache_id`, `user_id`)
             VALUES ('&1', '&2')",
            $this->nCacheId,
            $userId
        );

        return true;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function cancelAdoption($userId)
    {
        global $login;

        if ($this->allowEdit() == false && $login->userid != $userId) {
            return false;
        }

        sql(
            "DELETE FROM `cache_adoption`
             WHERE `user_id`='&1'
             AND `cache_id`='&2'",
            $userId,
            $this->nCacheId
        );

        return true;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function commitAdoption($userId)
    {
        global $login;

        // cache_adoption exists?
        if (sql_value(
                "SELECT COUNT(*) FROM `cache_adoption` WHERE `cache_id`='&1' AND `user_id`='&2'",
                0,
                $this->nCacheId,
                $userId
            ) == 0
        ) {
            return false;
        }

        // new user active?
        if (sql_value("SELECT `is_active_flag` FROM `user` WHERE `user_id`='&1'", 0, $userId) != 1) {
            return false;
        }

        sql(
            "INSERT INTO `logentries` (`module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`)
             VALUES ('cache', 5, '&1', '&2', '&3', '&4')",
            $login->userid,
            $this->nCacheId,
            0,
            'Cache ' . sql_escape($this->nCacheId) . ' has changed the owner from userid ' . sql_escape(
                $this->getUserId()
            ) . ' to ' . sql_escape($userId) . ' by ' . sql_escape($login->userid)
        );
        // Adoptions now are recorded by trigger in cache_adoptions table.
        // Recording adoptions in 'logentries' may be discarded after ensuring that the
        // log entries are not used anywhere.
        sql("UPDATE `caches` SET `user_id`='&1' WHERE `cache_id`='&2'", $userId, $this->nCacheId);
        sql("DELETE FROM `cache_adoption` WHERE `cache_id`='&1'", $this->nCacheId);

        $this->reCache->setValue('user_id', $userId);

        return true;
    }

    // checks if $userId has adopted this cache
    public function hasAdopted($userId)
    {
        // cache_adoption exists?
        return sql_value(
            "SELECT COUNT(*)
             FROM `cache_adoption`
             WHERE `cache_id`='&1'
             AND `user_id`='&2'",
            0,
            $this->nCacheId,
            $userId
        ) != 0;
    }

    // true if anyone can view the cache
    public function isPublic()
    {
        return (sql_value("SELECT `allow_user_view` FROM `cache_status` WHERE `id`='&1'", 0, $this->getStatus()) == 1);
    }

    public function allowView()
    {
        global $login;

        if ($this->isPublic()) {
            return true;
        }

        $login->verify();

        if (($login->admin & ADMIN_USER) == ADMIN_USER) {
            return true;
        } elseif ($this->getUserId() == $login->userid) {
            return true;
        }

        return false;
    }

    public function allowEdit()
    {
        global $login;

        $login->verify();
        if ($this->getUserId() == $login->userid) {
            return true;
        }

        return false;
    }

    public function allowLog()
    {
        global $login;

        $login->verify();
        if ($this->getUserId() == $login->userid || $login->hasAdminPriv(ADMIN_USER)) {
            return true;
        }

        return (sql_value("SELECT `allow_user_log` FROM `cache_status` WHERE `id`='&1'", 0, $this->getStatus()) == 1);
    }

    public function isRecommendedByUser($nUserId)
    {
        return sql_value(
            "SELECT COUNT(*) FROM `cache_rating` WHERE `cache_id`='&1' AND `user_id`='&2'",
            0,
            $this->nCacheId,
            $nUserId
        ) > 0;
    }

    public function addRecommendation($nUserId, $logdate)
    {
        // rating_date will be set to NOW() by Insert-trigger
        sql(
            "INSERT IGNORE INTO `cache_rating` (`cache_id`, `user_id`, `rating_date`) VALUES ('&1', '&2', '&3')",
            $this->nCacheId,
            $nUserId,
            $logdate
        );
    }

    public function removeRecommendation($nUserId)
    {
        sql("DELETE FROM `cache_rating` WHERE `cache_id`='&1' AND `user_id`='&2'", $this->nCacheId, $nUserId);
    }

    // retrieves admin cache history data and stores it to template variables
    // for display by adminhistory.tpl and adminreports.tpl
    public function setTplHistoryData($exclude_report_id)
    {
        global $opt, $tpl;

        // (other) reports for this cache
        $rs = sql(
            "SELECT `cr`.`id`,
                    `cr`.`date_created`,
                    `cr`.`lastmodified`,
                    `cr`.`userid`,
                    `cr`.`adminid`,
                    `users`.`username` AS `usernick`,
                    `admins`.`username` AS `adminnick`,
                    IFNULL(`tt`.`text`, `crs`.`name`) AS `status`,
                    IFNULL(`tt2`.`text`, `crr`.`name`) AS `reason`
             FROM `cache_reports` AS `cr`
             LEFT JOIN `cache_report_reasons` AS `crr`
                 ON `cr`.`reason`=`crr`.`id`
             LEFT JOIN `user` AS `users`
                 ON `users`.`user_id`=`cr`.`userid`
             LEFT JOIN `user` AS `admins`
                 ON `admins`.`user_id`=`cr`.`adminid`
             LEFT JOIN `cache_report_status` AS `crs`
                 ON `cr`.`status`=`crs`.`id`
             LEFT JOIN `sys_trans_text` AS `tt`
                 ON `crs`.`trans_id`=`tt`.`trans_id`
                 AND `tt`.`lang`='&2'
             LEFT JOIN `sys_trans_text` AS `tt2`
                 ON `crr`.`trans_id`=`tt2`.`trans_id`
                 AND `tt2`.`lang`='&2'
             WHERE `cr`.`cacheid`='&1'
                 AND `cr`.`id`<>'&3'
             ORDER BY `cr`.`status`,`cr`.`id` DESC",
            $this->getCacheId(),
            $opt['template']['locale'],
            $exclude_report_id
        );
        $tpl->assign_rs('reports', $rs);
        sql_free_result($rs);

        // user; deleted logs
        $rs = sql("SELECT * FROM `caches` WHERE `cache_id`='&1'", $this->getCacheId());
        $rCache = sql_fetch_array($rs);
        $tpl->assign('cache', $rCache);
        sql_free_result($rs);
        $tpl->assign(
            'ownername',
            sql_value("SELECT `username` FROM `user` WHERE `user_id`='&1'", '', $rCache['user_id'])
        );

        $tpl->assign('deleted_logs', $this->getLogsArray(0, 1000, true));

        // status changes
        $rs = sql(
            "SELECT `csm`.`date_modified`,
                    `csm`.`old_state` AS `old_status_id`,
                    `csm`.`new_state` AS `new_status_id`,
                    `user`.`username`,
                    `user`.`user_id`,
                    IFNULL(`stt_old`.`text`,`cs_old`.`name`) AS `old_status`,
                    IFNULL(`stt_new`.`text`,`cs_new`.`name`) AS `new_status`
            FROM `cache_status_modified` `csm`
            LEFT JOIN `cache_status` `cs_old`
                ON `cs_old`.`id`=`csm`.`old_state`
            LEFT JOIN `sys_trans_text` `stt_old`
                ON `stt_old`.`trans_id`=`cs_old`.`trans_id`
                AND `stt_old`.`lang`='&2'
            LEFT JOIN `cache_status` `cs_new`
                ON `cs_new`.`id`=`csm`.`new_state`
            LEFT JOIN `sys_trans_text` `stt_new`
                ON `stt_new`.`trans_id`=`cs_new`.`trans_id`
                AND `stt_new`.`lang`='&2'
            LEFT JOIN `user`
                ON `user`.`user_id`=`csm`.`user_id`
            WHERE `cache_id`='&1'
            ORDER BY `date_modified` DESC",
            $this->getCacheId(),
            $opt['template']['locale']
        );
        $tpl->assign_rs('status_changes', $rs);
        sql_free_result($rs);

        // coordinate changes
        $rs = sql(
            "SELECT `cc`.`date_created`, `cc`.`longitude`, `cc`.`latitude`,
                          IFNULL(`admin`.`user_id`, `owner`.`user_id`) AS `user_id`,
                          IFNULL(`admin`.`username`, `owner`.`username`) AS `username`
                     FROM `cache_coordinates` `cc`
                LEFT JOIN `caches` ON `caches`.`cache_id`=`cc`.`cache_id`
                LEFT JOIN `user` `owner` ON `owner`.`user_id`=`caches`.`user_id`
                LEFT JOIN `user` `admin` ON `admin`.`user_id`=`cc`.`restored_by`
                    WHERE `cc`.`cache_id`='&1'
                 ORDER BY `cc`.`date_created` DESC",
            $this->getCacheId()
        );
        $coords = [];
        while ($rCoord = sql_fetch_assoc($rs)) {
            $coord = new coordinate($rCoord['latitude'], $rCoord['longitude']);
            $coords[] = [
                'date' => $rCoord['date_created'],
                'coord' => $coord->getDecimalMinutes(),
                'user_id' => $rCoord['user_id'],
                'username' => $rCoord['username'],
            ];
        }
        sql_free_result($rs);
        $tpl->assign('coordinates', $coords);

        // Adoptions
        $rs = sql(
            "SELECT `cache_adoptions`.`date`,
                          `cache_adoptions`.`from_user_id`,
                          `cache_adoptions`.`to_user_id`,
                          `from_user`.`username` AS `from_username`,
                          `to_user`.`username` AS `to_username`
                     FROM `cache_adoptions`
                LEFT JOIN `user` `from_user` ON `from_user`.`user_id`=`from_user_id`
                LEFT JOIN `user` `to_user` ON `to_user`.`user_id`=`to_user_id`
                    WHERE `cache_id`='&1'
                 ORDER BY `cache_adoptions`.`date`, `cache_adoptions`.`id`",
            $this->getCacheId()
        );
        $tpl->assign_rs('adoptions', $rs);
        sql_free_result($rs);
    }

    public function logTypeAllowed($logType, $oldLogType = 0)
    {
        // check if given logType is valid for this cache type
        return logtype_ok($this->getCacheId(), $logType, $oldLogType);
    }

    /**
     * @param int $logId
     * @param int $oldLogType
     * @param int $newLogType
     */
    public function updateCacheStatusFromLatestLog($logId, $oldLogType, $newLogType)
    {
        if ($newLogType != $oldLogType) {
            // get new cache-status property of the new log type
            $cacheStatus = sql_value(
                "SELECT `cache_status` FROM `log_types` WHERE `id`='&1'",
                0,
                $newLogType
            );

            if ($cacheStatus != 0) {
                // If the new log type is a status-changing type, it's easy -
                // we can directly change the cache status:

                $this->setStatus($cacheStatus);
            } else {
                // If the new log type is not status-changing, but the old type was, the
                // cache status may need to be recalculated. To keep things simple, we will
                // always recalculate the cache status in this case.

                $cacheStatus = sql_value(
                    "SELECT `cache_status` FROM `log_types` WHERE `id`='&1'",
                    0,
                    $oldLogType
                );
                if ($cacheStatus != 0) {
                    // The only safe way to restore an old cache status is from table
                    // cache_status_modified. This is available since April 2013. As we
                    // do not allow status-changes by editing logs that are older than
                    // half a year, this is ok.

                    // The cache status is updated in a separate operation immediatly
                    // (usually within a millisecond or so) after saving a log, so we must
                    // apply some time lag to be on the safe side. It should be safe to
                    // assume that users will not edit the log type twice within <= 2 seconds:

                    $logCreated = sql_value(
                        "SELECT `date_created` FROM `cache_logs` WHERE `id`='&1'",
                        null,
                        $logId
                    );
                    $oldCacheStatus = sql_value(
                        "SELECT `old_state`
                         FROM `cache_status_modified`
                         WHERE `cache_id`='&1'
                         AND '&2' <= `date_modified`
                         AND TIMESTAMPDIFF(SECOND, '&2', `date_modified`) <= 2
                         ORDER BY `date_modified` DESC
                         LIMIT 1",
                        0,
                        $this->getCacheId(),
                        $logCreated
                    );

                    if ($oldCacheStatus > 0) {
                        $this->setStatus($oldCacheStatus);
                    }
                }
            }
        }
    }

    // $userLogType:
    //   Logtype selected by the user, or null if not applicable

    public function getUserLogTypes($userLogType, $oldLogType = 0, $statusLogs = true)
    {
        $logTypes = [];

        $logtypeNames = get_logtype_names();
        $allowedLogtypes = get_cache_log_types($this->getCacheId(), $oldLogType, $statusLogs);
        $defaultLogType = $userLogType;
        if (!logtype_ok($this->getCacheId(), $defaultLogType, $oldLogType, $statusLogs)) {
            $defaultLogType = $allowedLogtypes[0];
        }

        // prepare array
        $i = 0;
        foreach ($allowedLogtypes as $logtype) {
            $logTypes[$i]['selected'] = ($logtype == $defaultLogType) ? true : false;
            $logTypes[$i]['name'] = $logtypeNames[$logtype];
            $logTypes[$i]['id'] = $logtype;
            $i++;
        }

        // return
        return $logTypes;
    }

    public function teamcommentAllowed($logType, $oldTeamComment = false)
    {
        // checks if teamcomment is allowed
        return teamcomment_allowed($this->getCacheId(), $logType, $oldTeamComment);
    }

    public function statusUserLogAllowed()
    {
        return sql_value(
            "SELECT `cache_status`.`allow_user_log`
             FROM `cache_status`,`caches`
             WHERE `caches`.`status`=`cache_status`.`id`
             AND `caches`.`cache_id`='&1'",
            0,
            $this->getCacheId()
        ) == 1;
    }

    /**
     * @param $logId
     * @return bool
     */
    public function statusChangeAllowedForLog($logId)
    {
        $latestLogId = sql_value(
            "SELECT `id` FROM `cache_logs`
             WHERE `cache_id`='&1' AND DATEDIFF(NOW(), `date_created`) < 180
             ORDER BY `order_date` DESC, `date_created` DESC, `id` DESC
             LIMIT 1",
            0,
            $this->nCacheId
        );

        return ($logId == $latestLogId);
    }
}
