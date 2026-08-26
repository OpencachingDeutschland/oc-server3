-- Patch for sp_update_logstat: wrap caches.needs_maintenance / listing_outdated
-- subqueries with IFNULL(..., 0) so a cache with no matching logs does not
-- get NULL assigned to these NOT NULL columns (caused HTTP 500 on first log).
-- Idempotent: safe to run every ddev post-start.

DROP PROCEDURE IF EXISTS sp_update_logstat;

DELIMITER $$

CREATE PROCEDURE sp_update_logstat(
    IN nCacheId INT(10) UNSIGNED,
    IN nUserId INT(10) UNSIGNED,
    IN nLogType INT,
    IN bLogRemoved BOOLEAN
)
BEGIN
    DECLARE nFound INT DEFAULT 0;
    DECLARE nNotFound INT DEFAULT 0;
    DECLARE nNote INT DEFAULT 0;
    DECLARE nWillAttend INT DEFAULT 0;
    DECLARE nMaintenance INT DEFAULT 0;
    DECLARE nDate DATE DEFAULT NULL;

    IF nLogType = 1 THEN SET nFound=1; END IF;
    IF nLogType = 2 THEN SET nNotFound=1; END IF;
    IF nLogType = 3 THEN SET nNote=1; END IF;
    IF nLogType = 7 THEN SET nFound=1; END IF;
    IF nLogType = 8 THEN SET nWillAttend=1; END IF;
    IF nLogType IN (9,10,11,13,14) THEN SET nMaintenance=1; END IF;

    IF bLogRemoved = TRUE THEN
        SET nFound = -nFound;
        SET nNotFound = -nNotFound;
        SET nNote = -nNote;
        SET nWillAttend = -nWillAttend;
        SET nMaintenance = -nMaintenance;
    END IF;

    IF IFNULL(@deleting_cache,0)=0 THEN
        UPDATE `stat_cache_logs`
        SET
            `found` = IF(`found`+nFound>0, `found`+nFound, 0),
            `notfound` = IF(`notfound`+nNotFound>0, `notfound`+nNotFound, 0),
            `note` = IF(`note`+nNote>0, `note`+nNote, 0),
            `will_attend` = IF(`will_attend`+nWillAttend>0, `will_attend`+nWillAttend, 0),
            `maintenance` = IF(`maintenance`+nMaintenance>0, `maintenance`+nMaintenance, 0)
        WHERE
            `cache_id`=nCacheId
            AND `user_id`=nUserId;

        IF ROW_COUNT() = 0 THEN
            INSERT IGNORE INTO `stat_cache_logs`
                (`cache_id`, `user_id`, `found`, `notfound`, `note`, `will_attend`, `maintenance`)
            VALUES (
                nCacheId, nUserId,
                IF(nFound>0, nFound, 0),
                IF(nNotFound>0, nNotFound, 0),
                IF(nNote>0, nNote, 0),
                IF(nWillAttend>0, nWillAttend, 0),
                IF(nMaintenance>0, nMaintenance, 0)
            );
        END IF;

        UPDATE `stat_caches`
        SET
            `found` = IF(`found`+nFound>0, `found`+nFound, 0),
            `notfound` = IF(`notfound`+nNotFound>0, `notfound`+nNotFound, 0),
            `note` = IF(`note`+nNote>0, `note`+nNote, 0),
            `will_attend` = IF(`will_attend`+nWillAttend>0, `will_attend`+nWillAttend, 0),
            `maintenance` = IF(`maintenance`+nMaintenance>0, `maintenance`+nMaintenance, 0)
        WHERE `cache_id`=nCacheId;

        IF ROW_COUNT() = 0 THEN
            INSERT IGNORE INTO `stat_caches`
                (`cache_id`, `found`, `notfound`, `note`, `will_attend`, `maintenance`)
            VALUES (
                nCacheId,
                IF(nFound>0, nFound, 0),
                IF(nNotFound>0, nNotFound, 0),
                IF(nNote>0, nNote, 0),
                IF(nWillAttend>0, nWillAttend, 0),
                IF(nMaintenance>0, nMaintenance, 0)
            );
        END IF;

        IF nFound!=0 THEN
            SELECT LEFT(`date`,10) INTO nDate
            FROM `cache_logs`
            WHERE `cache_id`=nCacheId AND `type` IN (1, 7)
            ORDER BY `date` DESC
            LIMIT 1;

            UPDATE `stat_caches`
            SET `last_found`=nDate
            WHERE `cache_id`=nCacheId;
        END IF;

        UPDATE `caches`
        SET
            `needs_maintenance` = IFNULL(
                (SELECT GREATEST(0,`needs_maintenance`-1)
                 FROM `cache_logs`
                 WHERE
                    `cache_logs`.`cache_id`=nCacheID
                    AND (`cache_logs`.`needs_maintenance`>0 OR `cache_logs`.`type` In (9,13,14))
                 ORDER BY `order_date` DESC, `date_created` DESC, `id` DESC
                 LIMIT 1
                ), 0),
            `listing_outdated` = IFNULL(
                (SELECT GREATEST(0,`listing_outdated`-1)
                 FROM `cache_logs`
                 WHERE
                    `cache_logs`.`cache_id`=nCacheID
                    AND (`cache_logs`.`listing_outdated`>0 OR `cache_logs`.`type` In (9,13,14))
                 ORDER BY `order_date` DESC, `date_created` DESC, `id` DESC
                 LIMIT 1
                ), 0)
        WHERE `caches`.`cache_id`=nCacheId;
    END IF;

    IF IFNULL(@deleting_user,0)=0 THEN
        UPDATE `stat_user`
        SET
            `found` = IF(`found`+nFound>0, `found`+nFound, 0),
            `notfound` = IF(`notfound`+nNotFound>0, `notfound`+nNotFound, 0),
            `note` = IF(`note`+nNote>0, `note`+nNote, 0),
            `will_attend` = IF(`will_attend`+nWillAttend>0, `will_attend`+nWillAttend, 0),
            `maintenance` = IF(`maintenance`+nMaintenance>0, `maintenance`+nMaintenance, 0)
        WHERE `user_id`=nUserId;

        IF ROW_COUNT() = 0 THEN
            INSERT IGNORE INTO `stat_user`
                (`user_id`, `found`, `notfound`, `note`, `will_attend`, `maintenance`)
            VALUES (
                nUserId,
                IF(nFound>0, nFound, 0),
                IF(nNotFound>0, nNotFound, 0),
                IF(nNote>0, nNote, 0),
                IF(nWillAttend>0, nWillAttend, 0),
                IF(nMaintenance>0, nMaintenance, 0)
            );
        END IF;

        CALL sp_refresh_statpic(nUserId);
    END IF;
END$$

DELIMITER ;
