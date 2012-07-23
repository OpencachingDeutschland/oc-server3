#!/usr/local/bin/php -q
<?php
 /***************************************************************************
		
		Unicode Reminder メモ

		Ggf. muss die Location des php-Binaries angepasst werden.
		
	***************************************************************************/

	$opt['rootpath'] = '../../../';
  require_once($opt['rootpath'] . 'lib/clicompatbase.inc.php');

  if (!file_exists($opt['rootpath'] . 'util/mysql_root/sql_root.inc.php'))
		die("\n" . 'install util/mysql_root/sql_root.inc.php' . "\n\n");

  require_once($opt['rootpath'] . 'util/mysql_root/sql_root.inc.php');

/* begin db connect */
	db_root_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}
/* end db connect */

	/* get prefered language from string
	 */
	sql_dropFunction('PREFERED_LANG');
	sql("CREATE FUNCTION `PREFERED_LANG` (sExistingTokens VARCHAR(60), sPreferedTokens VARCHAR(60)) RETURNS CHAR(2) DETERMINISTIC SQL SECURITY INVOKER
	     BEGIN
			   DECLARE nPreferedIndex INT DEFAULT 1;
			   DECLARE sPrefered CHAR(2) DEFAULT '';
			   DECLARE sLastPrefered CHAR(2) DEFAULT '';
			   DECLARE nPos INT DEFAULT 0;

	       IF ISNULL(sExistingTokens) THEN
				   RETURN NULL;
	       END IF;

			   SET sExistingTokens = CONCAT(',', sExistingTokens, ',');

	       SET sPrefered = SUBSTRING_INDEX(SUBSTRING_INDEX(sPreferedTokens, ',', nPreferedIndex), ',', -1);
	       pl: LOOP
				   IF sPrefered = sLastPrefered THEN
				     LEAVE pl;
				   END IF;

           SET nPos = INSTR(sExistingTokens, CONCAT(',', sPrefered, ','));
           IF nPos!=0 THEN
					   RETURN sPrefered;
           END IF;

	         SET sLastPrefered = sPrefered;
           SET nPreferedIndex = nPreferedIndex + 1;
           SET sPrefered = SUBSTRING_INDEX(SUBSTRING_INDEX(sPreferedTokens, ',', nPreferedIndex), ',', -1);
	       END LOOP pl;

         SET sPrefered = SUBSTRING_INDEX(SUBSTRING_INDEX(sExistingTokens, ',', 2), ',', -1);
         IF sPrefered = '' THEN
	         RETURN NULL;
         ELSE
	         RETURN sPrefered;
         END IF;
			 END;");

	/* Stored procedures containing database logic
	 */

	// update all last_modified dates of related records
	sql_dropProcedure('sp_touch_cache');
	sql("CREATE PROCEDURE sp_touch_cache (IN nCacheId INT(10) UNSIGNED, IN bUpdateCacheRecord BOOL)
	     BEGIN
				 IF bUpdateCacheRecord = TRUE THEN
					 UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=nCacheId;
				 END IF;

				 UPDATE `cache_desc` SET `last_modified`=NOW() WHERE `cache_id`=nCacheId;
				 UPDATE `cache_logs` SET `last_modified`=NOW() WHERE `cache_id`=nCacheId;
				 UPDATE `pictures` SET `last_modified`=NOW() WHERE `object_type`=2 AND `object_id`=nCacheId;
				 UPDATE `pictures`, `cache_logs` SET `pictures`.`last_modified`=NOW() WHERE `pictures`.`object_type`=1 AND `pictures`.`object_id`=`cache_logs`.`id` AND `cache_logs`.`cache_id`=nCacheId;
				 UPDATE `mp3` SET `last_modified`=NOW() WHERE `object_id`=nCacheId;
	     END;");

	// set caches.desc_languages of given cacheid and fill cache_desc_prefered
	sql_dropProcedure('sp_update_caches_descLanguages');
	sql("CREATE PROCEDURE sp_update_caches_descLanguages (IN nCacheId INT(10) UNSIGNED)
	     BEGIN
	       DECLARE dl VARCHAR(60);

	       SELECT GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') INTO dl FROM `cache_desc` WHERE `cache_id`=nCacheId GROUP BY `cache_id` ;
	       UPDATE `caches` SET `desc_languages`=dl, default_desclang=PREFERED_LANG(dl, '&1') WHERE `cache_id`=nCacheId LIMIT 1;
	     END;", strtoupper($lang . ',EN'));

	// set caches.desc_languages of all caches, fill cache_desc_prefered and return number of modified rows
	sql_dropProcedure('sp_updateall_caches_descLanguages');
	sql("CREATE PROCEDURE sp_updateall_caches_descLanguages (OUT nModified INT)
	     BEGIN
			   SET nModified = 0;
	       UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `dl` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl` SET `caches`.`desc_languages`=`tbl`.`dl`, `caches`.`default_desclang`=PREFERED_LANG(`tbl`.`dl`, '&1') WHERE `caches`.`cache_id`=`tbl`.`cache_id`;
	       SET nModified = nModified + ROW_COUNT() ;
	     END;", strtoupper($lang . ',EN'));

	// update found, last_found, notfound and note of stat_cache_logs, stat_caches and stat_user
	sql_dropProcedure('sp_update_logstat');
	sql("CREATE PROCEDURE sp_update_logstat (IN nCacheId INT(10) UNSIGNED, IN nUserId INT(10) UNSIGNED, IN nLogType INT, IN bLogRemoved BOOLEAN)
	     BEGIN
	       DECLARE nFound INT DEFAULT 0;
	       DECLARE nNotFound INT DEFAULT 0;
	       DECLARE nNote INT DEFAULT 0;
	       DECLARE nWillAttend INT DEFAULT 0;
	       DECLARE nDate DATE DEFAULT NULL;

	       IF nLogType = 1 THEN SET nFound=1; END IF;
	       IF nLogType = 2 THEN SET nNotFound=1; END IF;
	       IF nLogType = 3 THEN SET nNote=1; END IF;
	       IF nLogType = 7 THEN SET nFound=1; END IF;
	       IF nLogType = 8 THEN SET nWillAttend=1; END IF;

	       IF bLogRemoved = TRUE THEN
				   SET nFound = -nFound;
				   SET nNotFound = -nNotFound;
				   SET nNote = -nNote;
				   SET nWillAttend = -nWillAttend;
	       END IF;

	       UPDATE `stat_cache_logs` SET `found`=IF(`found`+nFound>0, `found`+nFound, 0), `notfound`=IF(`notfound`+nNotFound>0, `notfound`+nNotFound, 0), `note`=IF(`note`+nNote>0, `note`+nNote, 0), `will_attend`=IF(`will_attend`+nWillAttend>0, `will_attend`+nWillAttend, 0) WHERE `cache_id`=nCacheId AND `user_id`=nUserId;
	       IF ROW_COUNT() = 0 THEN
				   INSERT IGNORE INTO `stat_cache_logs` (`cache_id`, `user_id`, `found`, `notfound`, `note`, `will_attend`) VALUES (nCacheId, nUserId, IF(nFound>0, nFound, 0), IF(nNotFound>0, nNotFound, 0), IF(nNote>0, nNote, 0), IF(nWillAttend>0, nWillAttend, 0));
	       END IF;

	       UPDATE `stat_caches` SET `found`=IF(`found`+nFound>0, `found`+nFound, 0), `notfound`=IF(`notfound`+nNotFound>0, `notfound`+nNotFound, 0), `note`=IF(`note`+nNote>0, `note`+nNote, 0), `will_attend`=IF(`will_attend`+nWillAttend>0, `will_attend`+nWillAttend, 0) WHERE `cache_id`=nCacheId;
	       IF ROW_COUNT() = 0 THEN
				   INSERT IGNORE INTO `stat_caches` (`cache_id`, `found`, `notfound`, `note`, `will_attend`) VALUES (nCacheId, IF(nFound>0, nFound, 0), IF(nNotFound>0, nNotFound, 0), IF(nNote>0, nNote, 0), IF(nWillAttend>0, nWillAttend, 0));
	       END IF;

	       IF nFound!=0 THEN
           SELECT `date` INTO nDate FROM `cache_logs` WHERE `cache_id`=nCacheId AND `type` IN (1, 7) ORDER BY `date` DESC LIMIT 1;
           UPDATE `stat_caches` SET `last_found`=nDate WHERE `cache_id`=nCacheId;
	       END IF;

	       UPDATE `stat_user` SET `found`=IF(`found`+nFound>0, `found`+nFound, 0), `notfound`=IF(`notfound`+nNotFound>0, `notfound`+nNotFound, 0), `note`=IF(`note`+nNote>0, `note`+nNote, 0), `will_attend`=IF(`will_attend`+nWillAttend>0, `will_attend`+nWillAttend, 0) WHERE `user_id`=nUserId;
	       IF ROW_COUNT() = 0 THEN
				   INSERT IGNORE INTO `stat_user` (`user_id`, `found`, `notfound`, `note`, `will_attend`) VALUES (nUserId, IF(nFound>0, nFound, 0), IF(nNotFound>0, nNotFound, 0), IF(nNote>0, nNote, 0), IF(nWillAttend>0, nWillAttend, 0));
	       END IF;

	       CALL sp_refresh_statpic(nUserId);
	     END;");

	// recalc found, last_found, notfound and note of stat_cache_logs, stat_caches and stat_user for all entries
	sql_dropProcedure('sp_updateall_logstat');
	sql("CREATE PROCEDURE sp_updateall_logstat (OUT nModified INT)
	     BEGIN
	       SET nModified=0;

	       INSERT IGNORE INTO `stat_user` (`user_id`) SELECT `user_id` FROM `cache_logs` GROUP BY `user_id`;
	       INSERT IGNORE INTO `stat_caches` (`cache_id`) SELECT `cache_id` FROM `cache_logs` GROUP BY `cache_id`;
	       INSERT IGNORE INTO `stat_cache_logs` (`cache_id`, `user_id`) SELECT `cache_id`, `user_id` FROM `cache_logs`;

	       /* stat_user.found */
	       UPDATE `stat_user`, (SELECT `user_id`, COUNT(*) AS `count` FROM `cache_logs` WHERE `type` IN (1, 7) GROUP BY `user_id`) AS `tblFound` SET `stat_user`.`found`=`tblFound`.`count` WHERE `stat_user`.`user_id`=`tblFound`.`user_id`;
	       SET nModified=nModified+ROW_COUNT();

	       /* stat_cache_logs.notfound */
	       UPDATE `stat_user`, (SELECT `user_id`, COUNT(*) AS `count` FROM `cache_logs` WHERE `type` IN (2) GROUP BY `user_id`) AS `tblNotFound` SET `stat_user`.`notfound`=`tblNotFound`.`count` WHERE `stat_user`.`user_id`=`tblNotFound`.`user_id`;
	       SET nModified=nModified+ROW_COUNT();

	       /* stat_cache_logs.note */
	       UPDATE `stat_user`, (SELECT `user_id`, COUNT(*) AS `count` FROM `cache_logs` WHERE `type` IN (3) GROUP BY `user_id`) AS `tblNote` SET `stat_user`.`note`=`tblNote`.`count` WHERE `stat_user`.`user_id`=`tblNote`.`user_id`;
	       SET nModified=nModified+ROW_COUNT();

	       /* stat_cache_logs.will_attend */
	       UPDATE `stat_user`, (SELECT `user_id`, COUNT(*) AS `count` FROM `cache_logs` WHERE `type` IN (8) GROUP BY `user_id`) AS `tblWillAttend` SET `stat_user`.`will_attend`=`tblWillAttend`.`count` WHERE `stat_user`.`user_id`=`tblWillAttend`.`user_id`;
	       SET nModified=nModified+ROW_COUNT();

	       /* stat_caches.found and stat_caches.last_found */
	       UPDATE `stat_caches`, (SELECT `cache_id`, COUNT(*) AS `count`, MAX(`date`) AS `last_found` FROM `cache_logs` WHERE `type` IN (1, 7) GROUP BY `cache_id`) AS `tblFound` SET `stat_caches`.`found`=`tblFound`.`count`, `stat_caches`.`last_found`=`tblFound`.`last_found` WHERE `stat_caches`.`cache_id`=`tblFound`.`cache_id`;
	       SET nModified=nModified+ROW_COUNT();

	       /* stat_caches.notfound */
	       UPDATE `stat_caches`, (SELECT `cache_id`, COUNT(*) AS `count` FROM `cache_logs` WHERE `type` IN (2) GROUP BY `cache_id`) AS `tblNotFound` SET `stat_caches`.`notfound`=`tblNotFound`.`count` WHERE `stat_caches`.`cache_id`=`tblNotFound`.`cache_id`;
	       SET nModified=nModified+ROW_COUNT();

	       /* stat_caches.note */
	       UPDATE `stat_caches`, (SELECT `cache_id`, COUNT(*) AS `count` FROM `cache_logs` WHERE `type` IN (3) GROUP BY `cache_id`) AS `tblNote` SET `stat_caches`.`note`=`tblNote`.`count` WHERE `stat_caches`.`cache_id`=`tblNote`.`cache_id`;
	       SET nModified=nModified+ROW_COUNT();

	       /* stat_caches.will_attend */
	       UPDATE `stat_caches`, (SELECT `cache_id`, COUNT(*) AS `count` FROM `cache_logs` WHERE `type` IN (8) GROUP BY `cache_id`) AS `tblWillAttend` SET `stat_caches`.`will_attend`=`tblWillAttend`.`count` WHERE `stat_caches`.`cache_id`=`tblWillAttend`.`cache_id`;
	       SET nModified=nModified+ROW_COUNT();

	       /* stat_cache_logs.found */
	       UPDATE `stat_cache_logs`, (SELECT `cache_id`, `user_id`, COUNT(*) AS `count` FROM `cache_logs` WHERE `type` IN (1, 7) GROUP BY `user_id`, `cache_id`) AS `tblFound` SET `stat_cache_logs`.`found`=`tblFound`.`count` WHERE `stat_cache_logs`.`cache_id`=`tblFound`.`cache_id` AND `stat_cache_logs`.`user_id`=`tblFound`.`user_id`;
	       SET nModified=nModified+ROW_COUNT();

	       /* stat_cache_logs.notfound */
	       UPDATE `stat_cache_logs`, (SELECT `cache_id`, `user_id`, COUNT(*) AS `count` FROM `cache_logs` WHERE `type` IN (2) GROUP BY `user_id`, `cache_id`) AS `tblNotFound` SET `stat_cache_logs`.`notfound`=`tblNotFound`.`count` WHERE `stat_cache_logs`.`cache_id`=`tblNotFound`.`cache_id` AND `stat_cache_logs`.`user_id`=`tblNotFound`.`user_id`;
	       SET nModified=nModified+ROW_COUNT();

	       /* stat_cache_logs.note */
	       UPDATE `stat_cache_logs`, (SELECT `cache_id`, `user_id`, COUNT(*) AS `count` FROM `cache_logs` WHERE `type` IN (3) GROUP BY `user_id`, `cache_id`) AS `tblNote` SET `stat_cache_logs`.`note`=`tblNote`.`count` WHERE `stat_cache_logs`.`cache_id`=`tblNote`.`cache_id` AND `stat_cache_logs`.`user_id`=`tblNote`.`user_id`;
	       SET nModified=nModified+ROW_COUNT();

	       /* stat_cache_logs.will_attend */
	       UPDATE `stat_cache_logs`, (SELECT `cache_id`, `user_id`, COUNT(*) AS `count` FROM `cache_logs` WHERE `type` IN (8) GROUP BY `user_id`, `cache_id`) AS `tblWillAttend` SET `stat_cache_logs`.`will_attend`=`tblWillAttend`.`count` WHERE `stat_cache_logs`.`cache_id`=`tblWillAttend`.`cache_id` AND `stat_cache_logs`.`user_id`=`tblWillAttend`.`user_id`;
	       SET nModified=nModified+ROW_COUNT();

	       CALL sp_refreshall_statpic();
	     END;");

	// increment/decrement stat_user.hidden
	sql_dropProcedure('sp_update_hiddenstat');
	sql("CREATE PROCEDURE sp_update_hiddenstat (IN nUserId INT, IN bRemoved BOOLEAN)
	     BEGIN
			   DECLARE nHidden INT DEFAULT 1;
			   IF bRemoved = TRUE THEN SET nHidden = -1; END IF;
			   UPDATE `stat_user` SET `stat_user`.`hidden`=IF(`stat_user`.`hidden`+nHidden>0, `stat_user`.`hidden`+nHidden, 0) WHERE `stat_user`.`user_id`=nUserId;
			   IF ROW_COUNT() = 0 THEN
			     INSERT IGNORE INTO `stat_user` (`user_id`, `hidden`) VALUES (nUserId, IF(nHidden>0, nHidden, 0));
			   END IF;

	       CALL sp_refresh_statpic(nUserId);
	     END;");

	// recalc hidden of stat_user for all entries
	sql_dropProcedure('sp_updateall_hiddenstat');
	sql("CREATE PROCEDURE sp_updateall_hiddenstat (OUT nModified INT)
	     BEGIN
	       SET nModified=0;

	       INSERT IGNORE INTO `stat_user` (`user_id`) SELECT `user_id` FROM `caches` GROUP BY `user_id`;

	       /* stat_caches.hidden */
	       UPDATE `stat_user`, (SELECT `user_id`, COUNT(*) AS `count` FROM `caches` GROUP BY `user_id`) AS `tblHidden` SET `stat_user`.`hidden`=`tblHidden`.`count` WHERE `stat_user`.`user_id`=`tblHidden`.`user_id`;
	       SET nModified=nModified+ROW_COUNT();

	       CALL sp_refreshall_statpic();
	     END;");

	// increment/decrement stat_caches.watch
	sql_dropProcedure('sp_update_watchstat');
	sql("CREATE PROCEDURE sp_update_watchstat (IN nCacheId INT, IN bRemoved BOOLEAN)
	     BEGIN
			   DECLARE nWatch INT DEFAULT 1;
			   IF bRemoved = TRUE THEN SET nWatch = -1; END IF;
			   UPDATE `stat_caches` SET `stat_caches`.`watch`=IF(`stat_caches`.`watch`+nWatch>0, `stat_caches`.`watch`+nWatch, 0) WHERE `stat_caches`.`cache_id`=nCacheId;
			   IF ROW_COUNT() = 0 THEN
			     INSERT IGNORE INTO `stat_caches` (`cache_id`, `watch`) VALUES (nCacheId, IF(nWatch>0, nWatch, 0));
			   END IF;
	     END;");

	// recalc watch of stat_caches for all entries
	sql_dropProcedure('sp_updateall_watchstat');
	sql("CREATE PROCEDURE sp_updateall_watchstat (OUT nModified INT)
	     BEGIN
	       SET nModified=0;

	       INSERT IGNORE INTO `stat_caches` (`cache_id`) SELECT `cache_id` FROM `cache_watches` GROUP BY `cache_id`;

	       /* stat_caches.watch */
	       UPDATE `stat_caches`, (SELECT `cache_id`, COUNT(*) AS `count` FROM `cache_watches` GROUP BY `cache_id`) AS `tblWatches` SET `stat_caches`.`watch`=`tblWatches`.`count` WHERE `stat_caches`.`cache_id`=`tblWatches`.`cache_id`;
	       SET nModified=nModified+ROW_COUNT();
	     END;");

	// increment/decrement stat_caches.ignore
	sql_dropProcedure('sp_update_ignorestat');
	sql("CREATE PROCEDURE sp_update_ignorestat (IN nCacheId INT, IN bRemoved BOOLEAN)
	     BEGIN
			   DECLARE nIgnore INT DEFAULT 1;
			   IF bRemoved = TRUE THEN SET nIgnore = -1; END IF;
			   UPDATE `stat_caches` SET `stat_caches`.`ignore`=IF(`stat_caches`.`ignore`+nIgnore>0, `stat_caches`.`ignore`+nIgnore, 0) WHERE `stat_caches`.`cache_id`=nCacheId;
			   IF ROW_COUNT() = 0 THEN
			     INSERT IGNORE INTO `stat_caches` (`cache_id`, `ignore`) VALUES (nCacheId, IF(nIgnore>0, nIgnore, 0));
			   END IF;
	     END;");

	// recalc ignore of stat_caches for all entries
	sql_dropProcedure('sp_updateall_ignorestat');
	sql("CREATE PROCEDURE sp_updateall_ignorestat (OUT nModified INT)
	     BEGIN
	       SET nModified=0;

	       INSERT IGNORE INTO `stat_caches` (`cache_id`) SELECT `cache_id` FROM `cache_ignore` GROUP BY `cache_id`;

	       /* stat_caches.ignore */
	       UPDATE `stat_caches`, (SELECT `cache_id`, COUNT(*) AS `count` FROM `cache_ignore` GROUP BY `cache_id`) AS `tblIgnore` SET `stat_caches`.`ignore`=`tblIgnore`.`count` WHERE `stat_caches`.`cache_id`=`tblIgnore`.`cache_id`;
	       SET nModified=nModified+ROW_COUNT();
	     END;");

	// increment/decrement stat_caches.toprating
	sql_dropProcedure('sp_update_topratingstat');
	sql("CREATE PROCEDURE sp_update_topratingstat (IN nCacheId INT, IN bRemoved BOOLEAN)
	     BEGIN
			   DECLARE nTopRating INT DEFAULT 1;
			   IF bRemoved = TRUE THEN SET nTopRating = -1; END IF;
			   UPDATE `stat_caches` SET `stat_caches`.`toprating`=IF(`stat_caches`.`toprating`+nTopRating>0, `stat_caches`.`toprating`+nTopRating, 0) WHERE `stat_caches`.`cache_id`=nCacheId;
			   IF ROW_COUNT() = 0 THEN
			     INSERT IGNORE INTO `stat_caches` (`cache_id`, `toprating`) VALUES (nCacheId, IF(nTopRating>0, nTopRating, 0));
			   END IF;
	     END;");

	// recalc toprating of stat_caches for all entries
	sql_dropProcedure('sp_updateall_topratingstat');
	sql("CREATE PROCEDURE sp_updateall_topratingstat (OUT nModified INT)
	     BEGIN
	       SET nModified=0;

	       INSERT IGNORE INTO `stat_caches` (`cache_id`) SELECT `cache_id` FROM `cache_rating` GROUP BY `cache_id`;
	       UPDATE `stat_caches` LEFT JOIN `cache_rating` ON `stat_caches`.`cache_id`=`cache_rating`.`cache_id` SET `stat_caches`.`toprating`=0 WHERE ISNULL(`cache_rating`.`cache_id`);

	       /* stat_caches.toprating */
	       UPDATE `stat_caches`, (SELECT `cache_id`, COUNT(*) AS `count` FROM `cache_rating` GROUP BY `cache_id`) AS `tblRating` SET `stat_caches`.`toprating`=`tblRating`.`count` WHERE `stat_caches`.`cache_id`=`tblRating`.`cache_id`;
	       SET nModified=nModified+ROW_COUNT();
	     END;");

	// increment/decrement stat_caches.picture
	sql_dropProcedure('sp_update_cache_picturestat');
	sql("CREATE PROCEDURE sp_update_cache_picturestat (IN nCacheId INT, IN bRemoved BOOLEAN)
	     BEGIN
			   DECLARE nPicture INT DEFAULT 1;
			   IF bRemoved = TRUE THEN SET nPicture = -1; END IF;
			   UPDATE `stat_caches` SET `stat_caches`.`picture`=IF(`stat_caches`.`picture`+nPicture>0, `stat_caches`.`picture`+nPicture, 0) WHERE `stat_caches`.`cache_id`=nCacheId;
			   IF ROW_COUNT() = 0 THEN
			     INSERT IGNORE INTO `stat_caches` (`cache_id`, `picture`) VALUES (nCacheId, IF(nPicture>0, nPicture, 0));
			   END IF;
	     END;");

	// recalc picture of stat_caches for all entries
	sql_dropProcedure('sp_updateall_cache_picturestat');
	sql("CREATE PROCEDURE sp_updateall_cache_picturestat (OUT nModified INT)
	     BEGIN
	       SET nModified=0;

	       INSERT IGNORE INTO `stat_caches` (`cache_id`) SELECT DISTINCT `object_id` AS `cache_id` FROM `pictures` WHERE `object_type`=2;

	       /* stat_caches.picture */
	       UPDATE `stat_caches`, (SELECT `object_id` AS `cache_id`, COUNT(*) AS `count` FROM `pictures` WHERE `object_type`=2 GROUP BY `object_type`, `object_id`) AS `tblPictures` SET `stat_caches`.`picture`=`tblPictures`.`count` WHERE `stat_caches`.`cache_id`=`tblPictures`.`cache_id`;
	       SET nModified=nModified+ROW_COUNT();
	     END;");

	// increment/decrement cache_logs.picture
	sql_dropProcedure('sp_update_cachelog_picturestat');
	sql("CREATE PROCEDURE sp_update_cachelog_picturestat (IN nLogId INT, IN bRemoved BOOLEAN)
	     BEGIN
			   DECLARE nPicture INT DEFAULT 1;
			   IF bRemoved = TRUE THEN SET nPicture = -1; END IF;
			   UPDATE `cache_logs` SET `cache_logs`.`picture`=IF(`cache_logs`.`picture`+nPicture>0, `cache_logs`.`picture`+nPicture, 0) WHERE `cache_logs`.`id`=nLogId;
	     END;");

	// recalc picture of cache_logs for all entries
	sql_dropProcedure('sp_updateall_cachelog_picturestat');
	sql("CREATE PROCEDURE sp_updateall_cachelog_picturestat (OUT nModified INT)
	     BEGIN
	       SET nModified=0;

	       /* cache_logs.picture */
	       UPDATE `cache_logs`, (SELECT `object_id` AS `log_id`, COUNT(*) AS `count` FROM `pictures` WHERE `object_type`=1 GROUP BY `object_type`, `object_id`) AS `tblPictures` SET `cache_logs`.`picture`=`tblPictures`.`count` WHERE `cache_logs`.`id`=`tblPictures`.`log_id`;
	       SET nModified=nModified+ROW_COUNT();
	     END;");

	// notify users with matching watch radius about this cache
	sql_dropProcedure('sp_notify_new_cache');
	sql("CREATE PROCEDURE sp_notify_new_cache (IN nCacheId INT(10) UNSIGNED, IN nLongitude DOUBLE, IN nLatitude DOUBLE)
	     BEGIN
	       INSERT IGNORE INTO `notify_waiting` (`id`, `cache_id`, `user_id`, `type`)
	       SELECT NULL, nCacheId, `user`.`user_id`, 1 /* notify_new_cache */
	         FROM `user`
	        WHERE NOT ISNULL(`user`.`latitude`)
	          AND NOT ISNULL(`user`.`longitude`)
	          AND `user`.`notify_radius`>0
	          AND (acos(cos((90-nLatitude) * 3.14159 / 180) * cos((90-`user`.`latitude`) * 3.14159 / 180) + sin((90-nLatitude) * 3.14159 / 180) * sin((90-`user`.`latitude`) * 3.14159 / 180) * cos((nLongitude-`user`.`longitude`) * 3.14159 / 180)) * 6370) <= `user`.`notify_radius`;
	     END;");

	// recreate the user statpic on next request
	sql_dropProcedure('sp_refresh_statpic');
	sql("CREATE PROCEDURE sp_refresh_statpic (IN nUserId INT(10) UNSIGNED)
	     BEGIN
			   DELETE FROM `user_statpic` WHERE `user_id`=nUserId;
	     END;");

	// recreate all user statpic on next request
	sql_dropProcedure('sp_refreshall_statpic');
	sql("CREATE PROCEDURE sp_refreshall_statpic ()
	     BEGIN
			   DELETE FROM `user_statpic`;
	     END;");

	/* Triggers
	 */
	sql_dropTrigger('cachesBeforeInsert');
	sql("CREATE TRIGGER `cachesBeforeInsert` BEFORE INSERT ON `caches` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;
						SET NEW.`need_npa_recalc`=1;
					END;");

	sql_dropTrigger('cachesAfterInsert');
	sql("CREATE TRIGGER `cachesAfterInsert` AFTER INSERT ON `caches` 
				FOR EACH ROW 
					BEGIN 
						INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_created`, `longitude`, `latitude`) 
						                                VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`);
						INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_created`, `country`) 
						                                VALUES (NEW.`cache_id`, NOW(), NEW.`country`);

						CALL sp_update_hiddenstat(NEW.`user_id`, FALSE);

            IF NEW.`status`=1 THEN
              CALL sp_notify_new_cache(NEW.`cache_id`, NEW.`longitude`, NEW.`latitude`);
            END IF;
					END;");

	sql_dropTrigger('cachesBeforeUpdate');
	sql("CREATE TRIGGER `cachesBeforeUpdate` BEFORE UPDATE ON `caches` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							IF OLD.`cache_id`!=NEW.`cache_id` OR 
							   OLD.`uuid`!=NEW.`uuid` OR 
							   OLD.`node`!=NEW.`node` OR 
							   OLD.`date_created`!=NEW.`date_created` OR 
							   OLD.`user_id`!=NEW.`user_id` OR 
							   OLD.`name`!=NEW.`name` OR 
							   OLD.`longitude`!=NEW.`longitude` OR 
							   OLD.`latitude`!=NEW.`latitude` OR 
							   OLD.`type`!=NEW.`type` OR 
							   OLD.`status`!=NEW.`status` OR 
							   OLD.`country`!=NEW.`country` OR 
							   OLD.`date_hidden`!=NEW.`date_hidden` OR 
							   OLD.`size`!=NEW.`size` OR 
							   OLD.`difficulty`!=NEW.`difficulty` OR 
							   OLD.`terrain`!=NEW.`terrain` OR 
							   OLD.`logpw`!=NEW.`logpw` OR 
							   OLD.`search_time`!=NEW.`search_time` OR 
							   OLD.`way_length`!=NEW.`way_length` OR 
							   OLD.`wp_gc`!=NEW.`wp_gc` OR 
							   OLD.`wp_nc`!=NEW.`wp_nc` OR 
							   OLD.`wp_oc`!=NEW.`wp_oc` OR 
							   OLD.`default_desclang`!=NEW.`default_desclang` OR 
							   OLD.`date_activate`!=NEW.`date_activate` THEN

								SET NEW.`last_modified`=NOW();
							END IF;

							IF OLD.`status`!=NEW.`status` THEN
								CALL sp_touch_cache(OLD.`cache_id`, FALSE);
							END IF;
						END IF;

						IF OLD.`longitude`!=NEW.`longitude` OR 
						   OLD.`latitude`!=NEW.`latitude` THEN
							SET NEW.`need_npa_recalc`=1;
						END IF;
					END;");

	sql_dropTrigger('cachesAfterUpdate');
	sql("CREATE TRIGGER `cachesAfterUpdate` AFTER UPDATE ON `caches` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`longitude` != OLD.`longitude` OR NEW.`latitude` != OLD.`latitude` THEN 
							INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_created`, `longitude`, `latitude`) 
								VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`); 
						END IF; 
						IF NEW.`country` != OLD.`country` THEN 
							INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_created`, `country`) 
								VALUES (NEW.`cache_id`, NOW(), NEW.`country`); 
						END IF;
						IF NEW.`user_id`!=OLD.`user_id` THEN
							CALL sp_update_hiddenstat(OLD.`user_id`, TRUE);
							CALL sp_update_hiddenstat(NEW.`user_id`, FALSE);
						END IF;
            IF OLD.`status`=5 AND NEW.`status`=1 THEN
              CALL sp_notify_new_cache(NEW.`cache_id`, NEW.`longitude`, NEW.`latitude`);
            END IF;
					END;");

	sql_dropTrigger('cachesAfterDelete');
	sql("CREATE TRIGGER `cachesAfterDelete` AFTER DELETE ON `caches` 
				FOR EACH ROW 
					BEGIN 
						DELETE FROM `cache_coordinates` WHERE `cache_id`=OLD.`cache_id`;
						DELETE FROM `cache_countries` WHERE `cache_id`=OLD.`cache_id`;
						DELETE FROM `cache_npa_areas` WHERE `cache_id`=OLD.`cache_id`;
						CALL sp_update_hiddenstat(OLD.`user_id`, TRUE);
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`cache_id`, OLD.`uuid`, 2, OLD.`node`);
					END;");

	sql_dropTrigger('cacheDescBeforeInsert');
	sql("CREATE TRIGGER `cacheDescBeforeInsert` BEFORE INSERT ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('cacheDescAfterInsert');
	sql("CREATE TRIGGER `cacheDescAfterInsert` AFTER INSERT ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_caches_descLanguages(NEW.`cache_id`);
					END;");

	sql_dropTrigger('cacheDescBeforeUpdate');
	sql("CREATE TRIGGER `cacheDescBeforeUpdate` BEFORE UPDATE ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite `last_modified` while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('cacheDescAfterUpdate');
	sql("CREATE TRIGGER `cacheDescAfterUpdate` AFTER UPDATE ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						IF OLD.`language`!=NEW.`language` OR OLD.`cache_id`!=NEW.`cache_id` THEN
							IF OLD.`cache_id`!=NEW.`cache_id` THEN
								CALL sp_update_caches_descLanguages(OLD.`cache_id`);
							END IF;
							CALL sp_update_caches_descLanguages(NEW.`cache_id`);
						END IF;
					END;");

	sql_dropTrigger('cacheDescAfterDelete');
	sql("CREATE TRIGGER `cacheDescAfterDelete` AFTER DELETE ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`id`, OLD.`uuid`, 3, OLD.`node`);
						CALL sp_update_caches_descLanguages(OLD.`cache_id`);
					END;");

	sql_dropTrigger('cacheIgnoreAfterInsert');
	sql("CREATE TRIGGER `cacheIgnoreAfterInsert` AFTER INSERT ON `cache_ignore` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_ignorestat(NEW.`cache_id`, FALSE);
					END;");

	sql_dropTrigger('cacheIgnoreAfterUpdate');
	sql("CREATE TRIGGER `cacheIgnoreAfterUpdate` AFTER UPDATE ON `cache_ignore` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`cache_id`!=OLD.`cache_id` THEN
							CALL sp_update_ignorestat(OLD.`cache_id`, TRUE);
							CALL sp_update_ignorestat(NEW.`cache_id`, FALSE);
						END IF;
					END;");

	sql_dropTrigger('cacheIgnoreAfterDelete');
	sql("CREATE TRIGGER `cacheIgnoreAfterDelete` AFTER DELETE ON `cache_ignore` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_ignorestat(OLD.`cache_id`, TRUE);
					END;");

	sql_dropTrigger('cacheLocationBeforeInsert');
	sql("CREATE TRIGGER `cacheLocationBeforeInsert` BEFORE INSERT ON `cache_location` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('cacheLocationBeforeUpdate');
	sql("CREATE TRIGGER `cacheLocationBeforeUpdate` BEFORE UPDATE ON `cache_location` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('cacheLogsBeforeInsert');
	sql("CREATE TRIGGER `cacheLogsBeforeInsert` BEFORE INSERT ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('cacheLogsAfterInsert');
	sql("CREATE TRIGGER `cacheLogsAfterInsert` AFTER INSERT ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
						DECLARE done INT DEFAULT 0;
						DECLARE notify_user_id INT;
						DECLARE cur1 CURSOR FOR SELECT `cache_watches`.`user_id` FROM `cache_watches` INNER JOIN `caches` ON `cache_watches`.`cache_id`=`caches`.`cache_id` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE `cache_watches`.`cache_id`=NEW.cache_id AND `cache_status`.`allow_user_view`=1;
						DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

	          CALL sp_update_logstat(NEW.`cache_id`, NEW.`user_id`, NEW.`type`, FALSE);

						OPEN cur1;
						REPEAT
							FETCH cur1 INTO notify_user_id;
							IF NOT done THEN
								INSERT INTO `watches_logqueue` (`log_id`, `user_id`) VALUES (NEW.id, notify_user_id);
							END IF;
						UNTIL done END REPEAT;
						CLOSE cur1;
					END;");

	sql_dropTrigger('cacheLogsBeforeUpdate');
	sql("CREATE TRIGGER `cacheLogsBeforeUpdate` BEFORE UPDATE ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite `last_modified` while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							IF NEW.`id`!=OLD.`id` OR
							   NEW.`uuid`!=OLD.`uuid` OR
							   NEW.`node`!=OLD.`node` OR
							   NEW.`date_created`!=OLD.`date_created` OR
							   NEW.`cache_id`!=OLD.`cache_id` OR
							   NEW.`user_id`!=OLD.`user_id` OR
							   NEW.`type`!=OLD.`type` OR
							   NEW.`date`!=OLD.`date` OR
							   NEW.`text`!=OLD.`text` OR
							   NEW.`text_html`!=OLD.`text_html` THEN
						
								SET NEW.`last_modified`=NOW();
							END IF;
						END IF;
					END;");

	sql_dropTrigger('cacheLogsAfterUpdate');
	sql("CREATE TRIGGER `cacheLogsAfterUpdate` AFTER UPDATE ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
						IF OLD.`cache_id`!=NEW.`cache_id` OR OLD.`user_id`!=NEW.`user_id` OR OLD.`type`!=NEW.`type` THEN
							CALL sp_update_logstat(OLD.`cache_id`, OLD.`user_id`, OLD.`type`, TRUE);
							CALL sp_update_logstat(NEW.`cache_id`, NEW.`user_id`, NEW.`type`, FALSE);
						END IF;
					END;");

	sql_dropTrigger('cacheLogsAfterDelete');
	sql("CREATE TRIGGER `cacheLogsAfterDelete` AFTER DELETE ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_logstat(OLD.`cache_id`, OLD.`user_id`, OLD.`type`, TRUE);
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`id`, OLD.`uuid`, 1, OLD.`node`);
					END;");

	sql_dropTrigger('cacheRatingAfterInsert');
	sql("CREATE TRIGGER `cacheRatingAfterInsert` AFTER INSERT ON `cache_rating` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_topratingstat(NEW.`cache_id`, FALSE);
					END;");

	sql_dropTrigger('cacheRatingAfterUpdate');
	sql("CREATE TRIGGER `cacheRatingAfterUpdate` AFTER UPDATE ON `cache_rating` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`cache_id`!=OLD.`cache_id` THEN
							CALL sp_update_topratingstat(OLD.`cache_id`, TRUE);
							CALL sp_update_topratingstat(NEW.`cache_id`, FALSE);
						END IF;
					END;");

	sql_dropTrigger('cacheRatingAfterDelete');
	sql("CREATE TRIGGER `cacheRatingAfterDelete` AFTER DELETE ON `cache_rating` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_topratingstat(OLD.`cache_id`, TRUE);
					END;");

	sql_dropTrigger('cacheVisitsBeforeInsert');
	sql("CREATE TRIGGER `cacheVisitsBeforeInsert` BEFORE INSERT ON `cache_visits` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('cacheVisitsBeforeUpdate');
	sql("CREATE TRIGGER `cacheVisitsBeforeUpdate` BEFORE UPDATE ON `cache_visits` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('cacheWatchesAfterInsert');
	sql("CREATE TRIGGER `cacheWatchesAfterInsert` AFTER INSERT ON `cache_watches` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_watchstat(NEW.`cache_id`, FALSE);
					END;");

	sql_dropTrigger('cacheWatchesAfterUpdate');
	sql("CREATE TRIGGER `cacheWatchesAfterUpdate` AFTER UPDATE ON `cache_watches` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`cache_id`!=OLD.`cache_id` THEN
							CALL sp_update_watchstat(OLD.`cache_id`, TRUE);
							CALL sp_update_watchstat(NEW.`cache_id`, FALSE);
						END IF;
					END;");

	sql_dropTrigger('cacheWatchesAfterDelete');
	sql("CREATE TRIGGER `cacheWatchesAfterDelete` AFTER DELETE ON `cache_watches` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_watchstat(OLD.`cache_id`, TRUE);
					END;");

	sql_dropTrigger('emailUserBeforeInsert');
	sql("CREATE TRIGGER `emailUserBeforeInsert` BEFORE INSERT ON `email_user` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('logentriesBeforeInsert');
	sql("CREATE TRIGGER `logentriesBeforeInsert` BEFORE INSERT ON `logentries` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('newsBeforeInsert');
	sql("CREATE TRIGGER `newsBeforeInsert` BEFORE INSERT ON `news` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('picturesBeforeInsert');
	sql("CREATE TRIGGER `picturesBeforeInsert` BEFORE INSERT ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('picturesAfterInsert');
	sql("CREATE TRIGGER `picturesAfterInsert` AFTER INSERT ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`object_type`=1 THEN
							CALL sp_update_cachelog_picturestat(NEW.`object_id`, FALSE);
						ELSEIF NEW.`object_type`=2 THEN
							CALL sp_update_cache_picturestat(NEW.`object_id`, FALSE);
						END IF;
					END;");

	sql_dropTrigger('picturesBeforeUpdate');
	sql("CREATE TRIGGER `picturesBeforeUpdate` BEFORE UPDATE ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('picturesAfterUpdate');
	sql("CREATE TRIGGER `picturesAfterUpdate` AFTER UPDATE ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						IF OLD.`object_type`!=NEW.`object_type` OR OLD.`object_id`!=NEW.`object_id` THEN
							IF OLD.`object_type`=1 THEN
								CALL sp_update_cachelog_picturestat(OLD.`object_id`, TRUE);
							ELSEIF OLD.`object_type`=2 THEN
								CALL sp_update_cache_picturestat(OLD.`object_id`, TRUE);
							END IF;
							IF NEW.`object_type`=1 THEN
								CALL sp_update_cachelog_picturestat(NEW.`object_id`, FALSE);
							ELSEIF NEW.`object_type`=2 THEN
								CALL sp_update_cache_picturestat(NEW.`object_id`, FALSE);
							END IF;
						END IF;
					END;");

	sql_dropTrigger('picturesAfterDelete');
	sql("CREATE TRIGGER `picturesAfterDelete` AFTER DELETE ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`id`, OLD.`uuid`, 6, OLD.`node`);
						IF OLD.`object_type`=1 THEN
							CALL sp_update_cachelog_picturestat(OLD.`object_id`, TRUE);
						ELSEIF OLD.`object_type`=2 THEN
							CALL sp_update_cache_picturestat(OLD.`object_id`, TRUE);
						END IF;
					END;");

	sql_dropTrigger('mp3BeforeInsert');
	sql("CREATE TRIGGER `mp3BeforeInsert` BEFORE INSERT ON `mp3` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('mp3BeforeUpdate');
	sql("CREATE TRIGGER `mp3BeforeUpdate` BEFORE UPDATE ON `mp3` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('mp3AfterDelete');
	sql("CREATE TRIGGER `mp3AfterDelete` AFTER DELETE ON `mp3` 
				FOR EACH ROW 
					BEGIN 
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`id`, OLD.`uuid`, 8, OLD.`node`);
					END;");

	sql_dropTrigger('removedObjectsBeforeInsert');
	sql("CREATE TRIGGER `removedObjectsBeforeInsert` BEFORE INSERT ON `removed_objects` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`removed_date`=NOW();
						END IF;
					END;");

	sql_dropTrigger('sysLoginsBeforeInsert');
	sql("CREATE TRIGGER `sysLoginsBeforeInsert` BEFORE INSERT ON `sys_logins` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('sysTransBeforeInsert');
	sql("CREATE TRIGGER `sysTransBeforeInsert` BEFORE INSERT ON `sys_trans` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`last_modified` < '2000' THEN  
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('sysTransBeforeUpdate');
	sql("CREATE TRIGGER `sysTransBeforeUpdate` BEFORE UPDATE ON `sys_trans` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('sysTransTextBeforeInsert');
	sql("CREATE TRIGGER `sysTransTextBeforeInsert` BEFORE INSERT ON `sys_trans_text` 
				FOR EACH ROW 
					BEGIN
						IF NEW.`last_modified` < '2000' THEN  
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('sysTransTextBeforeUpdate');
	sql("CREATE TRIGGER `sysTransTextBeforeUpdate` BEFORE UPDATE ON `sys_trans_text` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
					END;");

	sql_dropTrigger('userBeforeInsert');
	sql("CREATE TRIGGER `userBeforeInsert` BEFORE INSERT ON `user` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('userBeforeUpdate');
	sql("CREATE TRIGGER `userBeforeUpdate` BEFORE UPDATE ON `user` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							IF NEW.`user_id`!=OLD.`user_id` OR 
							   NEW.`uuid`!=OLD.`uuid` OR 
							   NEW.`node`!=OLD.`node` OR 
							   NEW.`date_created`!=OLD.`date_created` OR 
							   NEW.`username`!=OLD.`username` OR 
							   NEW.`pmr_flag`!=OLD.`pmr_flag` THEN
							   
								SET NEW.`last_modified`=NOW();
							END IF;
						END IF;
					END;");

	sql_dropTrigger('userAfterDelete');
	sql("CREATE TRIGGER `userAfterDelete` AFTER DELETE ON `user` 
				FOR EACH ROW 
					BEGIN 
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`user_id`, OLD.`uuid`, 4, OLD.`node`);
					END;");

	sql_dropTrigger('userDelegatesBeforeInsert');
	sql("CREATE TRIGGER `userDelegatesBeforeInsert` BEFORE INSERT ON `user_delegates` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('userDelegatesBeforeUpdate');
	sql("CREATE TRIGGER `userDelegatesBeforeUpdate` BEFORE UPDATE ON `user_delegates` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('watchesNotifiedBeforeInsert');
	sql("CREATE TRIGGER `watchesNotifiedBeforeInsert` BEFORE INSERT ON `watches_notified` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('watchesWaitingBeforeInsert');
	sql("CREATE TRIGGER `watchesWaitingBeforeInsert` BEFORE INSERT ON `watches_waiting` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('xmlsessionBeforeInsert');
	sql("CREATE TRIGGER `xmlsessionBeforeInsert` BEFORE INSERT ON `xmlsession` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('cacheAdoptionBeforeInsert');
	sql("CREATE TRIGGER `cacheAdoptionBeforeInsert` BEFORE INSERT ON `cache_adoption` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('cacheAdoptionBeforeUpdate');
	sql("CREATE TRIGGER `cacheAdoptionBeforeUpdate` BEFORE UPDATE ON `cache_adoption` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('userStatpicBeforeInsert');
	sql("CREATE TRIGGER `userStatpicBeforeInsert` BEFORE INSERT ON `user_statpic` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`date_created`=NOW();
					END;");

	sql_dropTrigger('sysSessionsAfterInsert');
	sql("CREATE TRIGGER `sysSessionsAfterInsert` AFTER INSERT ON `sys_sessions` 
				FOR EACH ROW 
					BEGIN 
						UPDATE `user` SET `user`.`last_login`=NEW.`last_login` WHERE `user`.`user_id`=NEW.`user_id`;
					END;");

	sql_dropTrigger('cacheAttributesAfterInsert');
	sql("CREATE TRIGGER `cacheAttributesAfterInsert` AFTER INSERT ON `caches_attributes` 
				FOR EACH ROW 
					BEGIN 
						UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=NEW.`cache_id`;
					END;");

	sql_dropTrigger('cacheAttributesAfterUpdate');
	sql("CREATE TRIGGER `cacheAttributesAfterUpdate` AFTER UPDATE ON `caches_attributes` 
				FOR EACH ROW 
					BEGIN 
						UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=NEW.`cache_id`;
						IF OLD.`cache_id`!=NEW.`cache_id` THEN
							UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=OLD.`cache_id`;
						END IF;
					END;");

	sql_dropTrigger('cacheAttributesAfterDelete');
	sql("CREATE TRIGGER `cacheAttributesAfterDelete` AFTER DELETE ON `caches_attributes` 
				FOR EACH ROW 
					BEGIN 
						UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=OLD.`cache_id`;
					END;");

	sql_dropTrigger('map2resultAfterDelete');
	sql("CREATE TRIGGER `map2resultAfterDelete` AFTER DELETE ON `map2_result` 
				FOR EACH ROW 
					BEGIN 
						DELETE FROM `map2_data` WHERE `result_id`=OLD.`result_id`;
					END;");
?>