<?php
 /***************************************************************************

		Unicode Reminder メモ

		Current trigger and stored procedure definitions

	***************************************************************************/

 /**
  * global variables which are used here:
  * 
  * @LAST_UUID
	*   The last generated UUID.
	* 
  * @dont_update_listingdate and @dont_update_logdate
	*   Flags which prevent recursive write access to 'caches' resp. 'cache_logs'
	*   from within SELECTs on the same tables.
	* 
	* @XMLSYNC
	*   Set by the XML client (local/ocxml11client) to prevent updates of
	*   modification dates. The dates are replicated and set by the XML client
	*   itself.
	* 
	* @restoredby
	*   ID of the admin who is restoring a vandalized listing, see
	*   htdocs/restorecaches.php.
	* 
	* @archive_picop
	*   determines if a 'pictures' table change is to be recorded to make it
	*   vandalism-restorable.
	* @original_picid
	*   original ID of a restored picture
	*/ 


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

	// get decimal value of waypoint
	sql_dropFunction('WPTODEC');
	sql("CREATE FUNCTION `WPTODEC` (wp VARCHAR(7), prefix VARCHAR(2)) RETURNS INT DETERMINISTIC SQL SECURITY INVOKER
		BEGIN
		  -- all used chars in waypoint, in their ascending order
		  DECLARE WP_ORDER CHAR(36) DEFAULT '&1';
		  -- list of base 36 chars in their ascending order
		  DECLARE B36_ORDER CHAR(36) DEFAULT '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		  -- will contain the waypoint value, without prefix
		  DECLARE WP_VALUE CHAR(5) DEFAULT '00000';
		  -- will contain WP_VALUE where all chars replaced by their equivalents in B36_ORDER
		  DECLARE B36_VALUE CHAR(5) DEFAULT '';
		  -- loop counter
		  DECLARE WP_POS INT DEFAULT 1;
		  -- index of a char in WP_ORDER/B36_ORDER
		  DECLARE WP_ORDER_INDEX INT;

		  -- validate input
		  IF ISNULL(wp) OR ISNULL(prefix) THEN
			RETURN 0;
		  END IF;
		  IF LENGTH(prefix) != 2 OR LENGTH(wp)<3 OR LENGTH(wp)>7 THEN
			RETURN 0;
		  END IF;
		  IF LEFT(wp, 2) != prefix THEN
			RETURN 0;
		  END IF;

		  -- get waypoint value with exactly 5 digits
		  SET WP_VALUE = RIGHT(CONCAT('00000', SUBSTRING(wp, 3)), 5);

		  -- replace each char in WP_VALUE with the equivalent base 36 char
		  REPEAT
			SET WP_ORDER_INDEX = LOCATE(SUBSTRING(WP_VALUE, WP_POS, 1), WP_ORDER);
			IF WP_ORDER_INDEX = 0 THEN
			  RETURN 0;
			END IF;
			SET B36_VALUE = CONCAT(B36_VALUE, SUBSTRING(B36_ORDER, WP_ORDER_INDEX, 1));
			SET WP_POS = WP_POS + 1;
		  UNTIL WP_POS>5 END REPEAT;

		  -- now use CONV() to convert from base 36 system to decimal
		  RETURN CONV(B36_VALUE, LENGTH(WP_ORDER), 10);

		END;",
		$opt['logic']['waypoint_pool']['valid_chars']);

	// inverse function of WPTODEC
	sql_dropFunction('DECTOWP');
	sql("CREATE FUNCTION `DECTOWP` (wp INT, prefix VARCHAR(2)) RETURNS VARCHAR(7) DETERMINISTIC SQL SECURITY INVOKER
		BEGIN
		  -- all used chars in waypoint, in their ascending order
		  DECLARE WP_ORDER CHAR(36) DEFAULT '&1';
		  -- list of base 36 chars in their ascending order
		  DECLARE B36_ORDER CHAR(36) DEFAULT '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		  -- base 36 value of the decimal waypoint value
		  DECLARE B36_VALUE VARCHAR(5);
		  -- will contain the waypoint value, without prefix
		  DECLARE WP_VALUE CHAR(5) DEFAULT '';
		  -- loop counter
		  DECLARE B36_POS INT DEFAULT 1;
		  -- index of a char in WP_ORDER/B36_ORDER
		  DECLARE B36_ORDER_INDEX INT;

		  -- validate input
		  IF ISNULL(wp) OR ISNULL(prefix) THEN
			RETURN '';
		  END IF;
		  IF LENGTH(prefix) != 2 OR wp=0 THEN
			RETURN '';
		  END IF;

		  -- convert the decimal waypoint value to base 36
		  SET B36_VALUE = CONV(wp, 10, LENGTH(WP_ORDER));

		  -- replace each char in B36_VALUE with the equivalent wp-char
		  REPEAT
			SET B36_ORDER_INDEX = LOCATE(SUBSTRING(B36_VALUE, B36_POS, 1), B36_ORDER);
			IF B36_ORDER_INDEX = 0 THEN
			  RETURN '';
			END IF;
			SET WP_VALUE = CONCAT(WP_VALUE, SUBSTRING(WP_ORDER, B36_ORDER_INDEX, 1));
			SET B36_POS = B36_POS + 1;
		  UNTIL B36_POS>LENGTH(B36_VALUE) END REPEAT;

		  IF LENGTH(WP_VALUE)<4 THEN
			RETURN CONCAT(prefix, RIGHT(CONCAT('0000', WP_VALUE), 4));
		  ELSE
			RETURN CONCAT(prefix, WP_VALUE);
		  END IF;
		END;",
		$opt['logic']['waypoint_pool']['valid_chars']);

	sql_dropFunction('CREATE_UUID');
	sql("CREATE FUNCTION `CREATE_UUID` () RETURNS VARCHAR(36) DETERMINISTIC SQL SECURITY INVOKER
		BEGIN
			SET @LAST_UUID = UUID();
			RETURN @LAST_UUID;
		END;");

	sql_dropFunction('GET_LAST_UUID');
	sql("CREATE FUNCTION `GET_LAST_UUID` () RETURNS VARCHAR(36) DETERMINISTIC SQL SECURITY INVOKER
		BEGIN
			RETURN @LAST_UUID;
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

				 /* This is a hack for the XML interface which delivers cache-related records
				  * like descriptions and pictures only depending on their last_modified date.
				  * Data may not have been deliverd or stored somewhere depending on the cache 
				  * status, so when status changes, all has to be sent (again) via XML.
				  */

				 UPDATE `cache_desc` SET `last_modified`=NOW() WHERE `cache_id`=nCacheId;
				 UPDATE `cache_logs` SET `last_modified`=NOW() WHERE `cache_id`=nCacheId;
				 UPDATE `coordinates` SET `last_modified`=NOW() WHERE `cache_id`=nCacheId AND `type`=1;
				 UPDATE `pictures` SET `last_modified`=NOW() WHERE `object_type`=2 AND `object_id`=nCacheId;
				 SET @dont_update_logdate=TRUE;  /* avoid access collision to cache_logs table */
				 UPDATE `pictures`, `cache_logs` SET `pictures`.`last_modified`=NOW() WHERE `pictures`.`object_type`=1 AND `pictures`.`object_id`=`cache_logs`.`id` AND `cache_logs`.`cache_id`=nCacheId;
				 SET @dont_update_logdate=FALSE;
				 UPDATE `mp3` SET `last_modified`=NOW() WHERE `object_id`=nCacheId;
	     END;");

	// update listing modification date
	sql_dropProcedure('sp_update_cache_listingdate');
	sql("CREATE PROCEDURE sp_update_cache_listingdate (IN nCacheId INT(10) UNSIGNED)
	     BEGIN
	       IF (ISNULL(@XMLSYNC) OR @XMLSYNC!=1) AND IFNULL(@dont_update_listingdate,0)=0 THEN
	         /* @dont_update_listingdate avoids illegal update recursions in caches table, e.g.
					      update caches.status -> sp_touch_cache -> update coordinates 
								  -> sp_update_cache_listingdate -> update caches  */
	         UPDATE `caches` SET `listing_last_modified`=NOW() WHERE `cache_id`=nCacheId LIMIT 1;
	       END IF;
	     END;");

	sql_dropProcedure('sp_updateall_cache_listingdates');
	sql("CREATE PROCEDURE sp_updateall_cache_listingdates (OUT nModified INT)
	     BEGIN
			   UPDATE `caches` SET `listing_last_modified` =
			      /* listing_last_modified can be greater then all the other dates, if a description,
			   	     a coordinate or a picture was deleted. Therefore it should generally not be
			   	     set back to an earlier datetime! */
			   		GREATEST(`listing_last_modified`,
						GREATEST(`last_modified`,
						GREATEST(IFNULL((SELECT MAX(`last_modified`) FROM `cache_desc` WHERE `cache_desc`.`cache_id`=`caches`.`cache_id`),'0'),
						GREATEST(IFNULL((SELECT MAX(`last_modified`) FROM `coordinates` WHERE `coordinates`.`type`=1 AND `coordinates`.`cache_id`=`caches`.`cache_id`),'0'),
						         IFNULL((SELECT MAX(`last_modified`) FROM `pictures` WHERE `pictures`.`object_type`=2 AND `pictures`.`object_id` = `caches`.`cache_id`),'0')
						        ))));
	       SET nModified = ROW_COUNT();
	     END;");

	sql_dropProcedure('sp_updateall_cachelog_logdates');
	sql("CREATE PROCEDURE sp_updateall_cachelog_logdates (OUT nModified INT)
	     BEGIN
			   UPDATE `cache_logs` SET `log_last_modified` =
			      /* log_last_modified can be greater then all the other dates, if a picture was deleted.
						Therefore it should generally not be set back to an earlier datetime! */
						GREATEST(`log_last_modified`,
						GREATEST(`last_modified`,
						         IFNULL((SELECT MAX(`last_modified`) FROM `pictures` WHERE `pictures`.`object_type`=1 AND `pictures`.`object_id` = `cache_logs`.`id`),'0')
						        ));
	       SET nModified = ROW_COUNT();
	       UPDATE `cache_logs_archived` SET `log_last_modified` =
	           GREATEST(`last_modified`,`log_last_modified`);
	       SET nModified = nModified + ROW_COUNT();
	     END;");

	sql_dropProcedure('sp_updateall_cachelist_counts');
	sql("CREATE PROCEDURE sp_updateall_cachelist_counts (OUT nModified INT)
	     BEGIN
				UPDATE `stat_cache_lists` SET `entries`=
					(SELECT COUNT(*) from `cache_list_items` WHERE `cache_list_items`.`cache_list_id`=`stat_cache_lists`.`cache_list_id`); 
				SET nModified = ROW_COUNT();
				UPDATE `stat_cache_lists` SET `watchers`=
					(SELECT COUNT(*) from `cache_list_watches` WHERE `cache_list_watches`.`cache_list_id`=`stat_cache_lists`.`cache_list_id`); 
				SET nModified = nModified + ROW_COUNT();
	     END;");

	/* update log modification date when rating changed, so that it is resent via
	   XML interface; see issue #244 */
	sql_dropProcedure('sp_update_cachelog_rating');
	sql("CREATE PROCEDURE sp_update_cachelog_rating (IN nCacheId INT, IN nUserID INT, IN dRatingDate DATETIME)
	     BEGIN
	       IF (ISNULL(@XMLSYNC) OR @XMLSYNC!=1) THEN
	         UPDATE `cache_logs` SET `last_modified`=NOW()
	          WHERE `cache_logs`.`cache_id`=nCacheId AND `cache_logs`.`user_id`=nUserID AND `cache_logs`.`date`=dRatingDate;
	       END IF;
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
	       UPDATE `caches`, (SELECT `cache_id`, GROUP_CONCAT(DISTINCT `language` ORDER BY `language` SEPARATOR ',') AS `dl` FROM `cache_desc` GROUP BY `cache_id`) AS `tbl` SET `caches`.`desc_languages`=`tbl`.`dl`, `caches`.`default_desclang`=PREFERED_LANG(`tbl`.`dl`, '&1') WHERE `caches`.`cache_id`=`tbl`.`cache_id`;
	       SET nModified = ROW_COUNT() ;
	     END;", strtoupper($lang . ',EN'));

	// update found, last_found, notfound and note of stat_cache_logs, stat_caches and stat_user
	sql_dropProcedure('sp_update_logstat');
	sql("CREATE PROCEDURE sp_update_logstat (IN nCacheId INT(10) UNSIGNED, IN nUserId INT(10) UNSIGNED, IN nLogType INT, IN bLogRemoved BOOLEAN)
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

	       UPDATE `stat_cache_logs` SET `found`=IF(`found`+nFound>0, `found`+nFound, 0), `notfound`=IF(`notfound`+nNotFound>0, `notfound`+nNotFound, 0), `note`=IF(`note`+nNote>0, `note`+nNote, 0), `will_attend`=IF(`will_attend`+nWillAttend>0, `will_attend`+nWillAttend, 0), `maintenance`=IF(`maintenance`+nMaintenance>0, `maintenance`+nMaintenance, 0) WHERE `cache_id`=nCacheId AND `user_id`=nUserId;
	       IF ROW_COUNT() = 0 THEN
				   INSERT IGNORE INTO `stat_cache_logs` (`cache_id`, `user_id`, `found`, `notfound`, `note`, `will_attend`, `maintenance`) VALUES (nCacheId, nUserId, IF(nFound>0, nFound, 0), IF(nNotFound>0, nNotFound, 0), IF(nNote>0, nNote, 0), IF(nWillAttend>0, nWillAttend, 0), IF(nMaintenance>0, nMaintenance, 0));
	       END IF;

	       UPDATE `stat_caches` SET `found`=IF(`found`+nFound>0, `found`+nFound, 0), `notfound`=IF(`notfound`+nNotFound>0, `notfound`+nNotFound, 0), `note`=IF(`note`+nNote>0, `note`+nNote, 0), `will_attend`=IF(`will_attend`+nWillAttend>0, `will_attend`+nWillAttend, 0), `maintenance`=IF(`maintenance`+nMaintenance>0, `maintenance`+nMaintenance, 0) WHERE `cache_id`=nCacheId;
	       IF ROW_COUNT() = 0 THEN
				   INSERT IGNORE INTO `stat_caches` (`cache_id`, `found`, `notfound`, `note`, `will_attend`, `maintenance`) VALUES (nCacheId, IF(nFound>0, nFound, 0), IF(nNotFound>0, nNotFound, 0), IF(nNote>0, nNote, 0), IF(nWillAttend>0, nWillAttend, 0), IF(nMaintenance>0, nMaintenance, 0));
	       END IF;

	       IF nFound!=0 THEN
           SELECT LEFT(`date`,10) INTO nDate FROM `cache_logs` WHERE `cache_id`=nCacheId AND `type` IN (1, 7) ORDER BY `date` DESC LIMIT 1;
           UPDATE `stat_caches` SET `last_found`=nDate WHERE `cache_id`=nCacheId;
	       END IF;

	       UPDATE `stat_user` SET `found`=IF(`found`+nFound>0, `found`+nFound, 0), `notfound`=IF(`notfound`+nNotFound>0, `notfound`+nNotFound, 0), `note`=IF(`note`+nNote>0, `note`+nNote, 0), `will_attend`=IF(`will_attend`+nWillAttend>0, `will_attend`+nWillAttend, 0), `maintenance`=IF(`maintenance`+nMaintenance>0, `maintenance`+nMaintenance, 0) WHERE `user_id`=nUserId;
	       IF ROW_COUNT() = 0 THEN
				   INSERT IGNORE INTO `stat_user` (`user_id`, `found`, `notfound`, `note`, `will_attend`, `maintenance`) VALUES (nUserId, IF(nFound>0, nFound, 0), IF(nNotFound>0, nNotFound, 0), IF(nNote>0, nNote, 0), IF(nWillAttend>0, nWillAttend, 0), IF(nMaintenance>0, nMaintenance, 0));
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

         /* This implementation is less performant than the previouse code (up to ~ commit 4ed7ee0),
            but the old did not update any values to zero - these entries were ignored!
						-- following 2013-05-12 */

	       /* stat_caches */
	       UPDATE `stat_caches` SET 
				   `found` = (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (1, 7) AND `cache_logs`.`cache_id` = `stat_caches`.`cache_id`),
					 `last_found` = (SELECT MAX(`date`) FROM `cache_logs` WHERE `type` IN (1, 7) AND `cache_logs`.`cache_id` = `stat_caches`.`cache_id`),
					 `notfound`= (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (2) AND `cache_logs`.`cache_id` = `stat_caches`.`cache_id`),
					 `note`= (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (3) AND `cache_logs`.`cache_id` = `stat_caches`.`cache_id`),
					 `will_attend`= (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (8) AND `cache_logs`.`cache_id` = `stat_caches`.`cache_id`),
					 `maintenance`= (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (9,10,11,13,14) AND `cache_logs`.`cache_id` = `stat_caches`.`cache_id`);

	       /* stat_cache_logs */
	       UPDATE `stat_cache_logs` SET
				   `found` = (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (1, 7) AND `cache_logs`.`cache_id` = `stat_cache_logs`.`cache_id` AND `cache_logs`.`user_id` = `stat_cache_logs`.`user_id`),
					 `notfound`= (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (2) AND `cache_logs`.`cache_id` = `stat_cache_logs`.`cache_id` AND `cache_logs`.`user_id` = `stat_cache_logs`.`user_id`),
					 `note`= (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (3) AND `cache_logs`.`cache_id` = `stat_cache_logs`.`cache_id` AND `cache_logs`.`user_id` = `stat_cache_logs`.`user_id`),
					 `will_attend`= (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (8) AND `cache_logs`.`cache_id` = `stat_cache_logs`.`cache_id` AND `cache_logs`.`user_id` = `stat_cache_logs`.`user_id`),
					 `maintenance`= (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (9,10,11,13,14) AND `cache_logs`.`cache_id` = `stat_cache_logs`.`cache_id` AND `cache_logs`.`user_id` = `stat_cache_logs`.`user_id`);
	       SET nModified=nModified+ROW_COUNT();

	       /* stat_user */
	       UPDATE `stat_user` SET
				   `found` = (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (1, 7) AND `cache_logs`.`user_id` = `stat_user`.`user_id`),
					 `notfound`= (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (2) AND `cache_logs`.`user_id` = `stat_user`.`user_id`),
					 `note`= (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (3) AND `cache_logs`.`user_id` = `stat_user`.`user_id`),
					 `will_attend`= (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (8) AND `cache_logs`.`user_id` = `stat_user`.`user_id`),
					 `maintenance`= (SELECT COUNT(*) FROM `cache_logs` WHERE `type` IN (9,10,11,13,14) AND `cache_logs`.`user_id` = `stat_user`.`user_id`);
	       SET nModified=nModified+ROW_COUNT();

	       CALL sp_refreshall_statpic();
	     END;");

	// increment/decrement stat_user.hidden
	sql_dropProcedure('sp_update_hiddenstat');
	sql("CREATE PROCEDURE sp_update_hiddenstat (IN nUserId INT, IN iStatus INT, IN bRemoved BOOLEAN)
	     BEGIN
			   DECLARE nHidden INT DEFAULT 1;
				 IF (SELECT `allow_user_view` FROM `cache_status` WHERE `id`=iStatus) THEN
				   IF bRemoved = TRUE THEN SET nHidden = -1; END IF;
				   UPDATE `stat_user` SET `stat_user`.`hidden`=IF(`stat_user`.`hidden`+nHidden>0, `stat_user`.`hidden`+nHidden, 0) WHERE `stat_user`.`user_id`=nUserId;
				   IF ROW_COUNT() = 0 THEN
				     INSERT IGNORE INTO `stat_user` (`user_id`, `hidden`) VALUES (nUserId, IF(nHidden>0, nHidden, 0));
				   END IF;

		       CALL sp_refresh_statpic(nUserId);
				 END IF;
	     END;");

	// recalc hidden of stat_user for all entries
	sql_dropProcedure('sp_updateall_hiddenstat');
	sql("CREATE PROCEDURE sp_updateall_hiddenstat (OUT nModified INT)
	     BEGIN
	       SET nModified=0;

	       INSERT IGNORE INTO `stat_user` (`user_id`) SELECT `user_id` FROM `caches` GROUP BY `user_id`;

	       /* stat_caches.hidden */
	       UPDATE `stat_user`, (SELECT `user_id`, COUNT(*) AS `count` FROM `caches` INNER JOIN `cache_status` ON `cache_status`.`id`=`caches`.`status` AND `allow_user_view`=1 GROUP BY `user_id`) AS `tblHidden` SET `stat_user`.`hidden`=`tblHidden`.`count` WHERE `stat_user`.`user_id`=`tblHidden`.`user_id`;
	       SET nModified=nModified+ROW_COUNT();

	       CALL sp_refreshall_statpic();
	     END;");

	// re-calculate stat_caches.watch for one cache
	sql_dropProcedure('sp_update_watchstat');
	sql("CREATE PROCEDURE sp_update_watchstat (IN nCacheId INT)
	     BEGIN
			   DECLARE nWatches INT DEFAULT 0;
				 SET nWatches =
					(SELECT COUNT(*) FROM
						(SELECT `cache_list_watches`.`user_id` 
						 FROM `cache_list_watches`, `cache_lists`, `cache_list_items`
						 WHERE `cache_list_items`.`cache_id`=nCacheId AND `cache_lists`.`id`=`cache_list_items`.`cache_list_id` AND `cache_list_watches`.`cache_list_id`=`cache_lists`.`id`
						 UNION   /* UNION discards duplicates */
						 SELECT `user_id` FROM `cache_watches` WHERE `cache_id`=nCacheId) AS `wu`); 
			   UPDATE `stat_caches` SET `stat_caches`.`watch` = nWatches WHERE `cache_id`=nCacheId;
			   IF ROW_COUNT() = 0 THEN
			     INSERT IGNORE INTO `stat_caches` (`cache_id`, `watch`) VALUES (nCacheId, nWatches);
			   END IF;
	     END;");

	// re-calculate stat_caches.watch for all entries of a cache list
	sql_dropProcedure('sp_update_list_watchstat');
	sql("CREATE PROCEDURE sp_update_list_watchstat (IN nCachelistId INT)
	     BEGIN
					DECLARE done INT DEFAULT 0;
					DECLARE cacheid INT DEFAULT 0;
					DECLARE cur1 CURSOR FOR SELECT `cache_id` FROM `cache_list_items` WHERE `cache_list_id` = nCachelistId;
					DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
					OPEN cur1;
					REPEAT
						FETCH cur1 INTO cacheid;
						IF NOT done THEN
							CALL sp_update_watchstat(cacheid); 
						END IF;
					UNTIL done END REPEAT;
					CLOSE cur1;
	     END;");

	// re-calculate stat_caches.watch for all entries
	sql_dropProcedure('sp_updateall_watchstat');
	sql("CREATE PROCEDURE sp_updateall_watchstat (OUT nModified INT)
	     BEGIN
	       SET nModified=0;

	       INSERT IGNORE INTO `stat_caches` (`cache_id`) 
				 	 SELECT DISTINCT `cache_id` FROM `cache_watches` 
					 UNION 
					 SELECT DISTINCT `cache_id` FROM `cache_list_items` 
					 WHERE `cache_list_items`.`cache_list_id` IN
					   (SELECT `cache_list_id` FROM `cache_list_watches`); 

				 /* initialize temp watch stats with 0 */
				 DROP TEMPORARY TABLE IF EXISTS `tmp_watchstat`;
				 CREATE TEMPORARY TABLE `tmp_watchstat` ENGINE=MEMORY (SELECT `cache_id`, 0 AS `watch` FROM `stat_caches`);
				 ALTER TABLE `tmp_watchstat` ADD PRIMARY KEY (`cache_id`); 

	       /* calculate temp stats for all watches caches (no effect for unwatched) */
				 UPDATE `tmp_watchstat`, 
								(SELECT `cache_id`, COUNT(*) AS `count` FROM 
									(SELECT `cache_id`, `user_id` FROM `cache_watches` 
									 UNION
									 SELECT `cache_id`, `user_id` FROM `cache_list_items`, `cache_list_watches`
									 WHERE `cache_list_items`.`cache_list_id` = `cache_list_watches`.`cache_list_id`
									) `ws` 
								 GROUP BY `cache_id`) `users_watching_caches`
				 SET `tmp_watchstat`.`watch` = `users_watching_caches`.`count` 
				 WHERE `tmp_watchstat`.`cache_id` = `users_watching_caches`.`cache_id`;

				 /* transfer temp data to stat_caches */
				 UPDATE `stat_caches`, (SELECT * FROM `tmp_watchstat`) AS `ws`
				 SET `stat_caches`.`watch` = `ws`.`watch`
				 WHERE `stat_caches`.`cache_id` = `ws`.`cache_id`;
	       SET nModified=nModified+ROW_COUNT();

				 DROP TEMPORARY TABLE `tmp_watchstat`;
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

	// Update out-of-sync rating dates. These probably were caused by rating-related
	// bugs when deleting one of multiple found logs and when changing the log type
	// (9 mismatches within ~9 months up to June 2013).
	sql_dropProcedure('sp_updateall_rating_dates');
	sql("CREATE PROCEDURE sp_updateall_rating_dates (OUT nModified INT)
	     BEGIN
	       UPDATE `cache_rating` SET `rating_date` =
	        (SELECT `date` FROM `cache_logs` WHERE `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id` AND `cache_logs`.`type` IN (1,7) ORDER BY `date` LIMIT 1)
	       WHERE (SELECT COUNT(*) FROM `cache_logs` WHERE `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id` AND `cache_logs`.`date`=`cache_rating`.`rating_date` AND `type` IN (1,7))=0;
	       /* will set rating_date to 0000-00...:00 for orphan records */
	       SET nModified=ROW_COUNT();
	     END;");

	// notify users with matching watch radius about this cache
	sql_dropProcedure('sp_notify_new_cache');
	sql("CREATE PROCEDURE sp_notify_new_cache (IN nCacheId INT(10) UNSIGNED, IN nLongitude DOUBLE, IN nLatitude DOUBLE, IN nType INT(1))
	     BEGIN
	       /* type 1 = new cache, 2 = new OConly attribute */
	       IF (nType=1 OR
	           (nType=2 AND (SELECT `notify_oconly` FROM `user`,`caches` WHERE `user`.`user_id`=`caches`.`user_id` AND `caches`.`cache_id`=nCacheId))) THEN
		       INSERT IGNORE INTO `notify_waiting` (`cache_id`, `user_id`, `type`)
		       SELECT nCacheId, `user`.`user_id`, nType /* notify_new_cache */
		         FROM `user`
	          /* Throttle email sending after undeliverable mails. See also runwatch.php. */
		        WHERE (`email_problems` = 0 OR DATEDIFF(NOW(),`last_email_problem`) > 1+DATEDIFF(`last_email_problem`,`first_email_problem`))
		          AND `user`.`latitude`+`user`.`longitude` <> 0
		          AND `user`.`notify_radius`>0
		          AND (acos(cos((90-nLatitude) * 3.14159 / 180) * cos((90-`user`.`latitude`) * 3.14159 / 180) + sin((90-nLatitude) * 3.14159 / 180) * sin((90-`user`.`latitude`) * 3.14159 / 180) * cos((nLongitude-`user`.`longitude`) * 3.14159 / 180)) * 6370) <= `user`.`notify_radius`;
         END IF;
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
						SET @dont_update_listingdate=1;

						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
							SET NEW.`listing_last_modified`=NOW();
						END IF;
						IF NEW.`status` <> 5 THEN
							SET NEW.`is_publishdate`=1;
						END IF;
						IF SUBSTR(TRIM(NEW.`wp_gc`),1,2)='GC' THEN
							SET NEW.`wp_gc_maintained`=UCASE(TRIM(NEW.`wp_gc`));
						END IF;
						SET NEW.`need_npa_recalc`=1;

						IF ISNULL(NEW.`uuid`) OR NEW.`uuid`='' THEN
							SET NEW.`uuid`=CREATE_UUID();
						END IF;

						/* reserve and set cache waypoint 
						 *
						 * Table cache_waypoint_pool is used to prevent race conditions
						 * when 2 caches will be inserted simultaneously
						 */
						IF ISNULL(NEW.`wp_oc`) OR NEW.`wp_oc`='' THEN
							
							/* cleanup previous assignments failures /*
							DELETE FROM `cache_waypoint_pool` WHERE `uuid`=NEW.`uuid`;

							/* reserve a waypoint */
							UPDATE `cache_waypoint_pool` SET `uuid`=NEW.`uuid` WHERE `uuid` IS NULL ORDER BY WPTODEC(`wp_oc`, '&1') ASC LIMIT 1;
							
							IF (SELECT COUNT(*) FROM `cache_waypoint_pool` WHERE `uuid`=NEW.`uuid`) = 0 THEN

								/* waypoint reservation was not successfull. Maybe we are on a development machine, where cronjob for waypoint pool
								 * generation did not run or the pool is empty. To get a valid waypoint, we simply increment the highest used waypoint by one.
								 * NOTE: This ignores the setting of opt[logic][waypoint_pool][fill_gaps]
								 * CAUTION: This statement is realy slow and you should always keep your waypoint pool filled with some waypoint on a production server
								 */
								INSERT INTO `cache_waypoint_pool` (`wp_oc`, `uuid`) 
									SELECT DECTOWP(MAX(`dec_wp`)+1, '&1'), NEW.`uuid` AS `uuid` 
										FROM (
												  SELECT MAX(WPTODEC(`wp_oc`, '&1')) AS dec_wp FROM `caches` WHERE `wp_oc` REGEXP '&2' 
											UNION SELECT MAX(WPTODEC(`wp_oc`, '&1')) AS dec_wp FROM `cache_waypoint_pool`
										) AS `tbl`;

							END IF;

							/* query and assign the reserved waypoint */
							SET NEW.`wp_oc` = (SELECT `wp_oc` FROM `cache_waypoint_pool` WHERE `uuid`=`NEW`.`uuid`);

						END IF;

						SET @dont_update_listingdate=0;
					END;",
					$opt['logic']['waypoint_pool']['prefix'],
					'^' . $opt['logic']['waypoint_pool']['prefix'] . '[' . $opt['logic']['waypoint_pool']['valid_chars'] . ']{1,}$');

	sql_dropTrigger('cachesAfterInsert');
	sql("CREATE TRIGGER `cachesAfterInsert` AFTER INSERT ON `caches` 
				FOR EACH ROW 
					BEGIN 
						SET @dont_update_listingdate=1;

						INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_created`, `longitude`, `latitude`) 
						                                VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`);
						INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_created`, `country`) 
						                                VALUES (NEW.`cache_id`, NOW(), NEW.`country`);

						CALL sp_update_hiddenstat(NEW.`user_id`, NEW.`status`, FALSE);

						IF NEW.`status`=1 THEN
						  CALL sp_notify_new_cache(NEW.`cache_id`, NEW.`longitude`, NEW.`latitude`, 1);
						END IF;

						/* cleanup/delete reserved waypoint */
						DELETE FROM `cache_waypoint_pool` WHERE `uuid`=NEW.`uuid`;

						SET @dont_update_listingdate=0;
					END;");

	sql_dropTrigger('cachesBeforeUpdate');
	sql("CREATE TRIGGER `cachesBeforeUpdate` BEFORE UPDATE ON `caches` 
				FOR EACH ROW 
					BEGIN 
						SET @dont_update_listingdate=1;

						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							IF OLD.`cache_id`!=NEW.`cache_id` OR 
							   OLD.`uuid`!=BINARY NEW.`uuid` OR 
							   OLD.`node`!=NEW.`node` OR 
							   OLD.`date_created`!=NEW.`date_created` OR 
							   OLD.`is_publishdate`!=NEW.`is_publishdate` OR 
							   OLD.`user_id`!=NEW.`user_id` OR 
							   OLD.`name`!=BINARY NEW.`name` OR 
							   OLD.`longitude`!=NEW.`longitude` OR 
							   OLD.`latitude`!=NEW.`latitude` OR 
							   OLD.`type`!=NEW.`type` OR 
							   OLD.`status`!=NEW.`status` OR 
							   OLD.`country`!=BINARY NEW.`country` OR 
							   OLD.`date_hidden`!=NEW.`date_hidden` OR 
							   OLD.`size`!=NEW.`size` OR 
							   OLD.`difficulty`!=NEW.`difficulty` OR 
							   OLD.`terrain`!=NEW.`terrain` OR 
							   OLD.`logpw`!=BINARY NEW.`logpw` OR 
							   OLD.`search_time`!=NEW.`search_time` OR 
							   OLD.`way_length`!=NEW.`way_length` OR 
							   OLD.`wp_gc`!=BINARY NEW.`wp_gc` OR
								 /* See notes on wp_gc_maintained in modification-dates.txt. */
							   OLD.`wp_nc`!=BINARY NEW.`wp_nc` OR 
							   OLD.`wp_oc`!=BINARY NEW.`wp_oc` OR 
							   OLD.`default_desclang`!=BINARY NEW.`default_desclang` OR 
							   OLD.`date_activate`!=NEW.`date_activate` OR
								 OLD.`show_cachelists`!=NEW.`show_cachelists` THEN

								SET NEW.`last_modified`=NOW();
							END IF;

							IF NEW.`last_modified` != OLD.`last_modified` THEN
								SET NEW.`listing_last_modified`=NOW();
							END IF;

							IF OLD.`status`!=NEW.`status` THEN
								CALL sp_touch_cache(OLD.`cache_id`, FALSE);
							END IF;
						END IF;

						IF NEW.`wp_gc`<>OLD.`wp_gc` AND
						   (SUBSTR(TRIM(NEW.`wp_gc`),1,2)='GC' OR TRIM(NEW.`wp_gc`)='') THEN
							SET NEW.`wp_gc_maintained`=UCASE(TRIM(NEW.`wp_gc`));
						END IF;

						IF OLD.`longitude`!=NEW.`longitude` OR 
						   OLD.`latitude`!=NEW.`latitude` THEN
							SET NEW.`need_npa_recalc`=1;
						END IF;

						IF OLD.`status`=5 AND NEW.`status`<>5 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`is_publishdate`=1;
						END IF;

						SET @dont_update_listingdate=0;
					END;");

	sql_dropTrigger('cachesAfterUpdate');
	sql("CREATE TRIGGER `cachesAfterUpdate` AFTER UPDATE ON `caches` 
				FOR EACH ROW 
					BEGIN 
						SET @dont_update_listingdate=1;

						IF NEW.`longitude` != OLD.`longitude` OR NEW.`latitude` != OLD.`latitude` THEN 
							INSERT IGNORE INTO `cache_coordinates` (`cache_id`, `date_created`, `longitude`, `latitude`, `restored_by`)
								VALUES (NEW.`cache_id`, NOW(), NEW.`longitude`, NEW.`latitude`, IFNULL(@restoredby,0));
						END IF; 
						IF NEW.`country` != OLD.`country` THEN 
							INSERT IGNORE INTO `cache_countries` (`cache_id`, `date_created`, `country`, `restored_by`)
								VALUES (NEW.`cache_id`, NOW(), NEW.`country`, IFNULL(@restoredby,0));
						END IF;
						IF NEW.`cache_id` = OLD.`cache_id` AND
						   OLD.`status` <> 5 AND
							 OLD.`date_created` < LEFT(NOW(),10) AND
							 (NEW.`name` != OLD.`name` OR NEW.`type` != OLD.`type` OR NEW.`date_hidden` != OLD.`date_hidden` OR NEW.`size` != OLD.`size` OR NEW.`difficulty` != OLD.`difficulty` OR NEW.`terrain` != OLD.`terrain` OR NEW.`search_time` != OLD.`search_time` OR NEW.`way_length` != OLD.`way_length` OR NEW.`wp_gc` != OLD.`wp_gc` OR NEW.`wp_nc` != OLD.`wp_nc`)
							 THEN
							INSERT IGNORE INTO `caches_modified` (`cache_id`, `date_modified`, `name`, `type`, `date_hidden`, `size`, `difficulty`, `terrain`, `search_time`, `way_length`, `wp_gc`, `wp_nc`, `restored_by`) VALUES (OLD.`cache_id`, NOW(), OLD.`name`, OLD.`type`, OLD.`date_hidden`, OLD.`size`, OLD.`difficulty`, OLD.`terrain`, OLD.`search_time`, OLD.`way_length`, OLD.`wp_gc`, OLD.`wp_nc`, IFNULL(@restoredby,0));
							/* logpw needs not to be saved */
							/* for further explanation see restorecaches.php */
						END IF;
						IF NEW.`user_id`!=OLD.`user_id` THEN
							INSERT INTO `cache_adoptions` (`cache_id`,`date`,`from_user_id`,`to_user_id`)
								VALUES (NEW.`cache_id`, NEW.`last_modified`, OLD.`user_id`, NEW.`user_id`);
						END IF;
						IF NEW.`user_id`!=OLD.`user_id` OR NEW.`status`!=OLD.`status` THEN
							CALL sp_update_hiddenstat(OLD.`user_id`, OLD.`status`, TRUE);
							CALL sp_update_hiddenstat(NEW.`user_id`, NEW.`status`, FALSE);
						END IF;
            IF OLD.`status`=5 AND NEW.`status`=1 THEN
              CALL sp_notify_new_cache(NEW.`cache_id`, NEW.`longitude`, NEW.`latitude`, 1);
            END IF;
            IF NEW.`status`<>OLD.`status` THEN
            	INSERT INTO `cache_status_modified` (`cache_id`, `date_modified`, `old_state`, `new_state`, `user_id`) VALUES (NEW.`cache_id`, NOW(), OLD.`status`, NEW.`status`, IFNULL(@STATUS_CHANGE_USER_ID,0));
						END IF; 
						SET @dont_update_listingdate=0;
					END;");

	sql_dropTrigger('cachesAfterDelete');
	sql("CREATE TRIGGER `cachesAfterDelete` AFTER DELETE ON `caches` 
				FOR EACH ROW 
					BEGIN 
						SET @dont_update_listingdate=1;

						/* lots of things are missing here - descs, logs, pictures ...
						   also, the depending deletions should be done BEFORE deleting from caches! */

						DELETE FROM `cache_coordinates` WHERE `cache_id`=OLD.`cache_id`;
						DELETE FROM `cache_countries` WHERE `cache_id`=OLD.`cache_id`;
						DELETE FROM `cache_npa_areas` WHERE `cache_id`=OLD.`cache_id`;
						DELETE FROM `caches_modified` WHERE `cache_id`=OLD.`cache_id`;
						CALL sp_update_hiddenstat(OLD.`user_id`, OLD.`status`, TRUE);
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`cache_id`, OLD.`uuid`, 2, OLD.`node`);

						SET @dont_update_listingdate=0;
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
						
						IF ISNULL(NEW.`uuid`) OR NEW.`uuid`='' THEN
							SET NEW.`uuid`=CREATE_UUID();
						END IF;
					END;");

	sql_dropTrigger('cacheDescAfterInsert');
	sql("CREATE TRIGGER `cacheDescAfterInsert` AFTER INSERT ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_cache_listingdate(NEW.`cache_id`);
						IF (SELECT `date_created` FROM `caches` WHERE `cache_id`=NEW.`cache_id`) < LEFT(NOW(),10) AND 
						   (SELECT `status` FROM `caches` WHERE `caches`.`cache_id`=NEW.`cache_id`) != 5 THEN
							INSERT IGNORE INTO `cache_desc_modified` (`cache_id`, `language`, `date_modified`, `desc`, `restored_by`) VALUES (NEW.`cache_id`, NEW.`language`, NOW(), NULL, IFNULL(@restoredby,0));
						END IF;
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
						IF OLD.`language`!=BINARY NEW.`language` OR OLD.`cache_id`!=NEW.`cache_id` THEN
							IF OLD.`cache_id`!=NEW.`cache_id` THEN
								CALL sp_update_caches_descLanguages(OLD.`cache_id`);
								CALL sp_update_cache_listingdate(OLD.`cache_id`);
							END IF;
							CALL sp_update_caches_descLanguages(NEW.`cache_id`);
						END IF;
						CALL sp_update_cache_listingdate(NEW.`cache_id`);
						/* changes at date of creation are ignored to save archive space */
						IF NEW.`cache_id`=OLD.`cache_id` AND
							 (SELECT `status` FROM `caches` WHERE `caches`.`cache_id`=OLD.`cache_id`) != 5 THEN
							IF (OLD.`date_created` < LEFT(NOW(),10)) THEN
								INSERT IGNORE INTO `cache_desc_modified` (`cache_id`, `language`, `date_modified`, `date_created`, `desc`, `desc_html`, `desc_htmledit`, `hint`, `short_desc`, `restored_by`) VALUES (OLD.`cache_id`, OLD.`language`, NOW(), OLD.`date_created`, OLD.`desc`, OLD.`desc_html`, OLD.`desc_htmledit`, OLD.`hint`, OLD.`short_desc`, IFNULL(@restoredby,0));
							END IF;
							IF NEW.`language`!=BINARY OLD.`language` THEN
								INSERT IGNORE INTO `cache_desc_modified` (`cache_id`, `language`, `date_modified`, `desc`) VALUES (NEW.`cache_id`, NEW.`language`, NOW(), NULL);
							END IF;
						END IF;
					END;");

	sql_dropTrigger('cacheDescAfterDelete');
	sql("CREATE TRIGGER `cacheDescAfterDelete` AFTER DELETE ON `cache_desc` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_cache_listingdate(OLD.`cache_id`);
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`id`, OLD.`uuid`, 3, OLD.`node`);
						/* changes at date of creation are ignored to save archive space */
						IF (OLD.`date_created` < LEFT(NOW(),10)) AND
						   (SELECT `status` FROM `caches` WHERE `caches`.`cache_id`=OLD.`cache_id`) != 5 THEN
							INSERT IGNORE INTO `cache_desc_modified` (`cache_id`, `language`, `date_modified`, `date_created`, `desc`, `desc_html`, `desc_htmledit`, `hint`, `short_desc`, `restored_by`) VALUES (OLD.`cache_id`, OLD.`language`, NOW(), OLD.`date_created`, OLD.`desc`, OLD.`desc_html`, OLD.`desc_htmledit`, OLD.`hint`, OLD.`short_desc`, IFNULL(@restoredby,0));
						END IF;
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
						UPDATE `caches` SET `meta_last_modified`=NOW() WHERE `caches`.`cache_id`=NEW.`cache_id`;
					END;");

	sql_dropTrigger('cacheLocationBeforeUpdate');
	sql("CREATE TRIGGER `cacheLocationBeforeUpdate` BEFORE UPDATE ON `cache_location` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_modified`=NOW();
						UPDATE `caches` SET `meta_last_modified`=NOW() WHERE `caches`.`cache_id`=NEW.`cache_id`;
					END;");

	sql_dropTrigger('cacheLocationAfterDelete');
	sql("CREATE TRIGGER `cacheLocationAfterDelete` AFTER DELETE ON `cache_location`
				FOR EACH ROW
					BEGIN
						UPDATE `caches` SET `meta_last_modified`=NOW() WHERE `caches`.`cache_id`=OLD.`cache_id`;
					END;");

	sql_dropTrigger('cacheLogsBeforeInsert');
	sql("CREATE TRIGGER `cacheLogsBeforeInsert` BEFORE INSERT ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
							SET NEW.`log_last_modified`=NEW.`last_modified`;
						END IF;

						IF ISNULL(NEW.`uuid`) OR NEW.`uuid`='' THEN
							SET NEW.`uuid`=CREATE_UUID();
						END IF;
					END;");

	sql_dropTrigger('cacheLogsAfterInsert');
	sql("CREATE TRIGGER `cacheLogsAfterInsert` AFTER INSERT ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
						DECLARE done INT DEFAULT 0;
						DECLARE notify_user_id INT;
						DECLARE cur1 CURSOR FOR
							/* watches from `cache_watches` */ 
							SELECT `cache_watches`.`user_id` 
							FROM `cache_watches` 
							INNER JOIN `caches` ON `cache_watches`.`cache_id`=`caches`.`cache_id` 
							INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` 
							WHERE `cache_watches`.`cache_id`=NEW.cache_id AND `cache_status`.`allow_user_view`=1
							UNION    /* UNION discards duplicates */
							/* watches from `cache_list_watches` */
							SELECT `clw`.`user_id` FROM `cache_list_watches` `clw` 
							INNER JOIN `cache_list_items` `cli` ON `clw`.`cache_list_id`=`cli`.`cache_list_id`
							INNER JOIN `caches` ON `cli`.`cache_id`=`caches`.`cache_id` 
							INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` 
							WHERE `cli`.`cache_id`=NEW.cache_id AND `cache_status`.`allow_user_view`=1;
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
							   NEW.`uuid`!=BINARY OLD.`uuid` OR
							   NEW.`node`!=OLD.`node` OR
							   NEW.`date_created`!=OLD.`date_created` OR
							   NEW.`cache_id`!=OLD.`cache_id` OR
							   NEW.`user_id`!=OLD.`user_id` OR
							   NEW.`type`!=OLD.`type` OR
							   NEW.`oc_team_comment`!=OLD.`oc_team_comment` OR
							   NEW.`date`!=OLD.`date` OR
							   NEW.`text`!=BINARY OLD.`text` OR
							   NEW.`text_html`!=OLD.`text_html` THEN
								SET NEW.`last_modified`=NOW();
							END IF;
							IF NEW.`picture`!=OLD.`picture` THEN
								SET NEW.`log_last_modified`=NOW();
							END IF;
							IF NEW.`last_modified` > NEW.`log_last_modified` THEN
								SET NEW.`log_last_modified`=NEW.`last_modified`;
							END IF;
						END IF;
					END;");

	sql_dropTrigger('cacheLogsAfterUpdate');
	sql("CREATE TRIGGER `cacheLogsAfterUpdate` AFTER UPDATE ON `cache_logs` 
				FOR EACH ROW 
					BEGIN 
						IF OLD.`cache_id`!=NEW.`cache_id` OR 
						   OLD.`user_id`!=NEW.`user_id` OR 
							 OLD.`type`!=NEW.`type` OR 
							 OLD.`date`!=NEW.`date` THEN
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

	// IF condition is defined to work with both, rating_date field may be NULL or not
	sql_dropTrigger('cacheRatingBeforeInsert');
	sql("CREATE TRIGGER `cacheRatingBeforeInsert` BEFORE INSERT ON `cache_rating`
				FOR EACH ROW
					BEGIN
						IF ISNULL(NEW.`rating_date`) OR NEW.`rating_date` < '2000' THEN
							SET NEW.`rating_date` = NOW(); 
						END IF;
					END;");

	sql_dropTrigger('cacheRatingAfterInsert');
	sql("CREATE TRIGGER `cacheRatingAfterInsert` AFTER INSERT ON `cache_rating` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_topratingstat(NEW.`cache_id`, FALSE);
						CALL sp_update_cachelog_rating(NEW.`cache_id`, NEW.`user_id`, NEW.`rating_date`);
					END;");

	sql_dropTrigger('cacheRatingAfterUpdate');
	sql("CREATE TRIGGER `cacheRatingAfterUpdate` AFTER UPDATE ON `cache_rating` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`cache_id`!=OLD.`cache_id` THEN
							CALL sp_update_topratingstat(OLD.`cache_id`, TRUE);
							CALL sp_update_topratingstat(NEW.`cache_id`, FALSE);
							CALL sp_update_cachelog_rating(OLD.`cache_id`, OLD.`user_id`, OLD.`rating_date`);
							CALL sp_update_cachelog_rating(NEW.`cache_id`, NEW.`user_id`, NEW.`rating_date`);
						END IF;
					END;");

	sql_dropTrigger('cacheRatingAfterDelete');
	sql("CREATE TRIGGER `cacheRatingAfterDelete` AFTER DELETE ON `cache_rating` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_topratingstat(OLD.`cache_id`, TRUE);
						CALL sp_update_cachelog_rating(OLD.`cache_id`, OLD.`user_id`, OLD.`rating_date`);
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
						CALL sp_update_watchstat(NEW.`cache_id`);
					END;");

	sql_dropTrigger('cacheWatchesAfterUpdate');
	sql("CREATE TRIGGER `cacheWatchesAfterUpdate` AFTER UPDATE ON `cache_watches` 
				FOR EACH ROW 
					BEGIN 
						IF NEW.`cache_id`!=OLD.`cache_id` THEN
							CALL sp_update_watchstat(OLD.`cache_id`);
							CALL sp_update_watchstat(NEW.`cache_id`);
						END IF;
					END;");

	sql_dropTrigger('cacheWatchesAfterDelete');
	sql("CREATE TRIGGER `cacheWatchesAfterDelete` AFTER DELETE ON `cache_watches` 
				FOR EACH ROW 
					BEGIN 
						CALL sp_update_watchstat(OLD.`cache_id`);
					END;");

	sql_dropTrigger('cacheListsBeforeInsert');
	sql("CREATE TRIGGER `cacheListsBeforeInsert` BEFORE INSERT ON `cache_lists` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;

						IF ISNULL(NEW.`uuid`) OR NEW.`uuid`='' THEN
							SET NEW.`uuid`=CREATE_UUID();
						END IF;
					END;");

	sql_dropTrigger('cacheListsAfterInsert');
	sql("CREATE TRIGGER `cacheListsAfterInsert` AFTER INSERT ON `cache_lists` 
				FOR EACH ROW
					BEGIN
						INSERT IGNORE INTO `stat_cache_lists` (`cache_list_id`) VALUES (NEW.`id`);
					END;");

	sql_dropTrigger('cacheListsBeforeUpdate');
	sql("CREATE TRIGGER `cacheListsBeforeUpdate` BEFORE UPDATE ON `cache_lists` 
				FOR EACH ROW 
					BEGIN 
					  IF NEW.`id` != OLD.`id` THEN
							CALL error_cache_list_id_must_not_be_changed();
						END IF;
						IF NEW.`uuid` != BINARY OLD.`uuid` OR
						   NEW.`node` != OLD.`node` OR
						   NEW.`user_id` != OLD.`user_id` OR
						   NEW.`name` != BINARY OLD.`name` OR 
							 NEW.`is_public` != OLD.`is_public` OR
							 NEW.`description` != BINARY OLD.`description` OR
							 NEW.`desc_htmledit` != OLD.`desc_htmledit` THEN
							/* dont overwrite date values while XML client is running */
							IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
								SET NEW.`last_modified`=NOW();
							END IF;
						END IF;
						IF OLD.`is_public` != NEW.`is_public` THEN
							SET NEW.`last_state_change`=NOW();   /* for XML interface */
							IF NOT NEW.`is_public` THEN
								DELETE FROM `cache_list_watches` WHERE `cache_list_watches`.`cache_list_id`=NEW.`id` AND `cache_list_watches`.`user_id` != NEW.`user_id`;
							END IF;
						END IF;
					END;");

	sql_dropTrigger('cacheListsBeforeDelete');
	sql("CREATE TRIGGER `cacheListsBeforeDelete` BEFORE DELETE ON `cache_lists` 
				FOR EACH ROW 
					BEGIN 
						SET @DELETING_CACHELIST=TRUE;
						DELETE FROM `cache_list_watches` WHERE `cache_list_watches`.`cache_list_id`=OLD.`id`;
						DELETE FROM `cache_list_items` WHERE `cache_list_items`.`cache_list_id`=OLD.`id`;
						DELETE FROM `stat_cache_lists` WHERE `cache_list_id`=OLD.`id`;
						SET @DELETING_CACHELIST=FALSE;
					END;");

	sql_dropTrigger('cacheListsAfterDelete');
	sql("CREATE TRIGGER `cacheListsAfterDelete` AFTER DELETE ON `cache_lists` 
				FOR EACH ROW
					BEGIN
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`id`, OLD.`uuid`, 8, OLD.`node`);
					END;");

	sql_dropTrigger('cacheListItemsAfterInsert');
	sql("CREATE TRIGGER `cacheListItemsAfterInsert` AFTER INSERT ON `cache_list_items`
				FOR EACH ROW 
					BEGIN
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							UPDATE `cache_lists` SET `last_modified`=NOW(), `last_added`=NOW() WHERE `cache_lists`.`id`=NEW.`cache_list_id`;
							UPDATE `stat_cache_lists` SET `entries`=`entries`+1 WHERE `stat_cache_lists`.`cache_list_id`=NEW.`cache_list_id`;
							IF (SELECT `user_id` FROM `cache_list_watches` `clw` WHERE `clw`.`cache_list_id`=NEW.`cache_list_id` LIMIT 1) IS NOT NULL THEN
								CALL sp_update_watchstat(NEW.`cache_id`);
							END IF;
						END IF; 
					END;");

	sql_dropTrigger('cacheListItemsAfterUpdate');
	sql("CREATE TRIGGER `cacheListItemsAfterUpdate` AFTER UPDATE ON `cache_list_items` 
				FOR EACH ROW 
					BEGIN
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							UPDATE `cache_lists` SET `last_modified`=NOW() WHERE `cache_lists`.`id`=OLD.`cache_list_id`;
							IF NEW.`cache_list_id` != OLD.`cache_list_id` THEN
								UPDATE `stat_cache_lists` SET `entries`=`entries`-1 WHERE `stat_cache_lists`.`cache_list_id`=OLD.`cache_list_id`;  
								UPDATE `stat_cache_lists` SET `entries`=`entries`+1 WHERE `stat_cache_lists`.`cache_list_id`=NEW.`cache_list_id`;
								UPDATE `cache_lists` SET `last_modified`=NOW(), `last_added`=NOW() WHERE `cache_lists`.`id`=NEW.`cache_list_id`;
							END IF;
							IF (SELECT `user_id` FROM `cache_list_watches` `clw` WHERE `clw`.`cache_list_id`=OLD.`cache_list_id` LIMIT 1) IS NOT NULL THEN
								CALL sp_update_watchstat(OLD.`cache_id`);
							END IF;
							IF (SELECT `user_id` FROM `cache_list_watches` `clw` WHERE `clw`.`cache_list_id`=NEW.`cache_list_id` LIMIT 1) IS NOT NULL THEN
								CALL sp_update_watchstat(NEW.`cache_id`);
							END IF;
						END IF;
					END;");

	sql_dropTrigger('cacheListItemsAfterDelete');
	sql("CREATE TRIGGER `cacheListItemsAfterDelete` AFTER DELETE ON `cache_list_items` 
				FOR EACH ROW 
					BEGIN
						/* avoid recursive access to cache_lists; optimization */
						IF NOT IFNULL(@DELETING_CACHELIST,FALSE) THEN
							/* dont overwrite date values while XML client is running */
							IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
								UPDATE `stat_cache_lists` SET `entries`=`entries`-1 WHERE `stat_cache_lists`.`cache_list_id`=OLD.`cache_list_id`;
								UPDATE `cache_lists` SET `last_modified`=NOW() WHERE `cache_lists`.`id`=OLD.`cache_list_id`;
								IF (SELECT `user_id` FROM `cache_list_watches` `clw` WHERE `clw`.`cache_list_id`=OLD.`cache_list_id` LIMIT 1) IS NOT NULL THEN
									CALL sp_update_watchstat(OLD.`cache_id`);
								END IF;
							END IF;
						END IF;
					END;");

	sql_dropTrigger('cacheListWatchesAfterInsert');
	sql("CREATE TRIGGER `cacheListWatchesAfterInsert` AFTER INSERT ON `cache_list_watches` 
				FOR EACH ROW 
					BEGIN
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							UPDATE `stat_cache_lists` SET `watchers`=`watchers`+1 WHERE `stat_cache_lists`.`cache_list_id`=NEW.`cache_list_id`;
							CALL sp_update_list_watchstat(NEW.`cache_list_id`);
						END IF; 
					END;");

	sql_dropTrigger('cacheListWatchesAfterUpdate');
	sql("CREATE TRIGGER `cacheListWatchesAfterUpdate` AFTER UPDATE ON `cache_list_watches` 
				FOR EACH ROW 
					BEGIN
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							IF NEW.`cache_list_id` != OLD.`cache_list_id` THEN
								UPDATE `stat_cache_lists` SET `watchers`=`watchers`-1 WHERE `stat_cache_lists`.`cache_list_id`=OLD.`cache_list_id`;  
								UPDATE `stat_cache_lists` SET `watchers`=`watchers`+1 WHERE `stat_cache_lists`.`cache_list_id`=NEW.`cache_list_id`;
							END IF;
							CALL sp_update_list_watchstat(OLD.`cache_list_id`);
							CALL sp_update_list_watchstat(NEW.`cache_list_id`);
						END IF;
					END;");

	sql_dropTrigger('cacheListWatchesAfterDelete');
	sql("CREATE TRIGGER `cacheListWatchesAfterDelete` AFTER DELETE ON `cache_list_watches` 
				FOR EACH ROW 
					BEGIN
						/* avoid recursive access to cache_lists; optimization */
						IF NOT IFNULL(@DELETING_CACHELIST,FALSE) THEN
							/* dont overwrite date values while XML client is running */
							IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
								UPDATE `stat_cache_lists` SET `watchers`=`watchers`-1 WHERE `stat_cache_lists`.`cache_list_id`=OLD.`cache_list_id`;
								CALL sp_update_list_watchstat(OLD.`cache_list_id`);
							END IF;
						END IF;
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

						IF ISNULL(NEW.`uuid`) OR NEW.`uuid`='' THEN
							SET NEW.`uuid`=CREATE_UUID();
						END IF;
					END;");

	sql_dropTrigger('picturesAfterInsert');
	sql("CREATE TRIGGER `picturesAfterInsert` AFTER INSERT ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						IF @archive_picop AND
							(NEW.`object_type`=1 OR   /* re-insert of owner-deleted other user's logpic */
						   (NEW.`object_type`=2 AND
							  ((SELECT `date_created` FROM `caches` WHERE `cache_id`=NEW.`object_id`) < LEFT(NOW(),10)) AND
							  (SELECT `status` FROM `caches` WHERE `caches`.`cache_id`=NEW.`object_id`) != 5)) THEN
							INSERT IGNORE INTO `pictures_modified` (`id`, `date_modified`, `operation`, `object_type`, `object_id`, `title`, `original_id`, `restored_by`) VALUES (NEW.`id`, NOW(), 'I', NEW.`object_type`, NEW.`object_id`, NEW.`title`, IFNULL(@original_picid,0), IFNULL(@restoredby,0));
						END IF;
						IF NEW.`object_type`=1 THEN
							CALL sp_update_cachelog_picturestat(NEW.`object_id`, FALSE);
						ELSEIF NEW.`object_type`=2 THEN
							CALL sp_update_cache_picturestat(NEW.`object_id`, FALSE);
							CALL sp_update_cache_listingdate(NEW.`object_id`);
						END IF;
					END;");

	sql_dropTrigger('picturesBeforeUpdate');
	sql("CREATE TRIGGER `picturesBeforeUpdate` BEFORE UPDATE ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							IF NEW.`id`!=OLD.`id` OR 
							   NEW.`uuid`!=BINARY OLD.`uuid` OR 
								 NEW.`node`!=OLD.`node` OR 
								 NEW.`date_created`!=OLD.`date_created` OR 
								 NEW.`url`!=BINARY OLD.`url` OR 
								 NEW.`title`!=BINARY OLD.`title` OR 
								 NEW.`object_id`!=OLD.`object_id` OR 
								 NEW.`object_type`!=OLD.`object_type` OR 
								 NEW.`spoiler`!=OLD.`spoiler` OR 
								 NEW.`local`!=OLD.`local` OR 
								 NEW.`unknown_format`!=OLD.`unknown_format` OR 
								 NEW.`display`!=OLD.`display` OR 
								 NEW.`mappreview`!=OLD.`mappreview` THEN
								/* everything except last_url_check, thumb_url and thumb_last_generated */
								SET NEW.`last_modified`=NOW();
							END IF;
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
								CALL sp_update_cache_listingdate(OLD.`object_id`);
							END IF;
							IF NEW.`object_type`=1 THEN
								CALL sp_update_cachelog_picturestat(NEW.`object_id`, FALSE);
							ELSEIF NEW.`object_type`=2 THEN
								CALL sp_update_cache_picturestat(NEW.`object_id`, FALSE);
								CALL sp_update_cache_listingdate(NEW.`object_id`);
							END IF;
						ELSE
							IF NEW.`last_modified` != OLD.`last_modified` THEN
								IF NEW.`object_type`=1 THEN
									IF NOT IFNULL(@dont_update_logdate,FALSE) THEN
										UPDATE `cache_logs` SET `log_last_modified`=NEW.`last_modified` WHERE `id`=NEW.`object_id`;
									END IF;
								ELSE
									CALL sp_update_cache_listingdate(NEW.`object_id`);
								END IF;
							END IF;
							IF @archive_picop AND
						       ( ( NEW.`object_type`=2 AND
						           OLD.`date_created` < LEFT(NOW(),10) AND
						           (SELECT `status` FROM `caches` WHERE `caches`.`cache_id`=OLD.`object_id`) != 5
						       	 ) OR
						         NEW.`object_type`=1 ) AND
						       (NEW.`title` != OLD.`title` OR NEW.`spoiler` != OLD.`spoiler` OR NEW.`display` != OLD.`display`) THEN
								INSERT IGNORE INTO `pictures_modified` (`id`, `date_modified`, `operation`, `date_created`, `url`, `title`, `object_id`, `object_type`, `spoiler`, `unknown_format`, `display`, `restored_by`) VALUES (OLD.`id`, NOW(), 'U', OLD.`date_created`, OLD.`url`, OLD.`title`, OLD.`object_id`, OLD.`object_type`, OLD.`spoiler`, OLD.`unknown_format`, OLD.`display`, IFNULL(@restoredby,0));
								/* mappreview is not archived, can be safely set to 0 on restore */
							END IF;
						END IF;
					END;");

	sql_dropTrigger('picturesAfterDelete');
	sql("CREATE TRIGGER `picturesAfterDelete` AFTER DELETE ON `pictures` 
				FOR EACH ROW 
					BEGIN 
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`id`, OLD.`uuid`, 6, OLD.`node`);
						IF @archive_picop AND
						    (OLD.`object_type`=1 OR
							     /* @archive_picop ensures that type-1 pics here are non-cacheowner's pics */
							   (OLD.`object_type`=2 AND
						      (SELECT `date_created` FROM `caches` WHERE `cache_id`=OLD.`object_id`) < LEFT(NOW(),10) AND
							    (SELECT `status` FROM `caches` WHERE `caches`.`cache_id`=OLD.`object_id`) != 5
							  )) THEN
							INSERT IGNORE INTO `pictures_modified` (`id`, `date_modified`, `operation`, `date_created`, `url`, `title`, `object_id`, `object_type`, `spoiler`, `unknown_format`, `display`, `restored_by`) VALUES (OLD.`id`, NOW(), 'D', OLD.`date_created`, OLD.`url`, OLD.`title`, OLD.`object_id`, OLD.`object_type`, OLD.`spoiler`, OLD.`unknown_format`, OLD.`display`, IFNULL(@restoredby,0));
							/* mappreview is not archived, can be safely set to 0 on restore */
						END IF;
						IF OLD.`object_type`=1 THEN
							CALL sp_update_cachelog_picturestat(OLD.`object_id`, TRUE);
						ELSEIF OLD.`object_type`=2 THEN
							CALL sp_update_cache_picturestat(OLD.`object_id`, TRUE);
							CALL sp_update_cache_listingdate(OLD.`object_id`);
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

	// Triggers for updating listing date (sp_update_cache_listingdate) on mp3 changes
	// are missing. We can't add them because there is only an object_id field and no
	// object_type, so we don't know which mp3 belongs to a cache.

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

						IF ISNULL(NEW.`uuid`) OR NEW.`uuid`='' THEN
							SET NEW.`uuid`=CREATE_UUID();
						END IF;
					END;");

	sql_dropTrigger('userBeforeUpdate');
	sql("CREATE TRIGGER `userBeforeUpdate` BEFORE UPDATE ON `user` 
				FOR EACH ROW 
					BEGIN 
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							IF NEW.`user_id`!=OLD.`user_id` OR 
							   NEW.`uuid`!=BINARY OLD.`uuid` OR 
							   NEW.`node`!=OLD.`node` OR 
							   NEW.`date_created`!=OLD.`date_created` OR 
							   NEW.`username`!=BINARY OLD.`username` OR 
							   NEW.`country`!=BINARY OLD.`country` OR 
							   NEW.`pmr_flag`!=OLD.`pmr_flag` OR
							   NEW.`description`!=BINARY OLD.`description` THEN
							   
								SET NEW.`last_modified`=NOW();
							END IF;
						END IF;
						IF NEW.`email_problems`>0 AND NEW.`first_email_problem` IS NULL THEN
							SET NEW.`first_email_problem` = NEW.`last_email_problem`;
						ELSEIF NEW.`email_problems`=0 THEN
							SET NEW.`first_email_problem` = NULL;
						END IF;
					END;");

	sql_dropTrigger('userBeforeDelete');
	sql("CREATE TRIGGER `userBeforeDelete` BEFORE DELETE ON `user` 
				FOR EACH ROW 
					BEGIN
						DELETE FROM `cache_adoption` WHERE `user_id`=OLD.user_id;
						DELETE FROM `cache_ignore` WHERE `user_id`=OLD.user_id;
						DELETE FROM `cache_rating` WHERE `user_id`=OLD.user_id;
						DELETE FROM `cache_watches` WHERE `user_id`=OLD.user_id;
						DELETE FROM `cache_lists` WHERE `user_id`=OLD.user_id;
						DELETE FROM `stat_user` WHERE `user_id`=OLD.user_id;
						DELETE FROM `user_options` WHERE `user_id`=OLD.user_id;
						DELETE FROM `user_statpic` WHERE `user_id`=OLD.user_id;
						DELETE FROM `watches_waiting` WHERE `user_id`=OLD.user_id;
						DELETE FROM `notify_waiting` WHERE `user_id`=OLD.user_id;
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

	sql_dropTrigger('sysSessionsBeforeInsert');
	sql("CREATE TRIGGER `sysSessionsBeforeInsert` BEFORE INSERT ON `sys_sessions` 
				FOR EACH ROW 
					BEGIN 
						SET NEW.`last_login`=NOW();
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
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=NEW.`cache_id`;
							CALL sp_update_cache_listingdate(NEW.`cache_id`);
						END IF;
						IF (SELECT `status` FROM `caches` WHERE `cache_id`=NEW.`cache_id`) != 5 AND 
						   (SELECT `date_created` FROM `caches` WHERE `cache_id`=NEW.`cache_id`) < LEFT(NOW(),10) THEN
							INSERT IGNORE INTO `caches_attributes_modified` (`cache_id`, `attrib_id`, `date_modified`, `was_set`, `restored_by`) VALUES (NEW.`cache_id`, NEW.`attrib_id`, NOW(), 0, IFNULL(@restoredby,0));
						END IF;
						IF (NEW.`attrib_id`=6 AND (SELECT `status` FROM `caches` WHERE `caches`.`cache_id`=NEW.`cache_id`) <= 2) THEN
							CALL sp_notify_new_cache(
								NEW.`cache_id`,
								(SELECT `longitude` FROM `caches` WHERE `caches`.`cache_id`=NEW.`cache_id`),
								(SELECT `latitude` FROM `caches` WHERE `caches`.`cache_id`=NEW.`cache_id`),
								2);
						END IF;
					END;");

	sql_dropTrigger('cacheAttributesAfterUpdate');
	sql("CREATE TRIGGER `cacheAttributesAfterUpdate` AFTER UPDATE ON `caches_attributes` 
				FOR EACH ROW 
					BEGIN 
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=NEW.`cache_id`;
							CALL sp_update_cache_listingdate(NEW.`cache_id`);
							IF OLD.`cache_id`!=NEW.`cache_id` THEN
								UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=OLD.`cache_id`;
								CALL sp_update_cache_listingdate(OLD.`cache_id`);
							END IF;
						END IF;
						IF (NEW.`attrib_id`=6 AND OLD.`attrib_id`<>6 AND (SELECT `status` FROM `caches` WHERE `caches`.`cache_id`=NEW.`cache_id`) <= 2) THEN
							CALL sp_notify_new_cache(
								NEW.`cache_id`,
								(SELECT `longitude` FROM `caches` WHERE `caches`.`cache_id`=NEW.`cache_id`),
								(SELECT `latitude` FROM `caches` WHERE `caches`.`cache_id`=NEW.`cache_id`),
								2);
						END IF;
						/* is not called, otherweise cache_attributes_modified would have to be updated,
						   which would need an extension to restorecaches.php */
					END;");

	sql_dropTrigger('cacheAttributesAfterDelete');
	sql("CREATE TRIGGER `cacheAttributesAfterDelete` AFTER DELETE ON `caches_attributes` 
				FOR EACH ROW 
					BEGIN 
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=OLD.`cache_id`;
							CALL sp_update_cache_listingdate(OLD.`cache_id`);
						END IF;
						IF (SELECT `status` FROM `caches` WHERE `cache_id`=OLD.`cache_id`) != 5 AND 
						   (SELECT `date_created` FROM `caches` WHERE `cache_id`=OLD.`cache_id`) < LEFT(NOW(),10) THEN
							INSERT IGNORE INTO `caches_attributes_modified` (`cache_id`, `attrib_id`, `date_modified`, `was_set`, `restored_by`) VALUES (OLD.`cache_id`, OLD.`attrib_id`, NOW(), 1, IFNULL(@restoredby,0));
						END IF;
					END;");

	sql_dropTrigger('map2resultAfterDelete');
	sql("CREATE TRIGGER `map2resultAfterDelete` AFTER DELETE ON `map2_result` 
				FOR EACH ROW 
					BEGIN 
						DELETE FROM `map2_data` WHERE `result_id`=OLD.`result_id`;
					END;");

	sql_dropTrigger('coordinatesBeforeInsert');
	sql("CREATE TRIGGER `coordinatesBeforeInsert` BEFORE INSERT ON `coordinates`
				FOR EACH ROW
					BEGIN
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('coordinatesAfterInsert');
	sql("CREATE TRIGGER `coordinatesAfterInsert` AFTER INSERT ON `coordinates`
				FOR EACH ROW
					BEGIN
						IF NEW.`type`=1 THEN
							IF ((ISNULL(@XMLSYNC) OR @XMLSYNC!=1) AND IFNULL(@dont_update_listingdate,0)=0) THEN
							  /* update caches modification date for XML interface handling */
								UPDATE `caches` SET `last_modified`=NEW.`last_modified` WHERE `cache_id`=NEW.`cache_id`;
							END IF;
							CALL sp_update_cache_listingdate(NEW.`cache_id`);
						END IF;
					END;");

	sql_dropTrigger('coordinatesBeforeUpdate');
	sql("CREATE TRIGGER `coordinatesBeforeUpdate` BEFORE UPDATE ON `coordinates`
				FOR EACH ROW
					BEGIN
						/* dont overwrite `last_modified` while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`last_modified`=NOW();
						END IF;
					END;");

	sql_dropTrigger('coordinatesAfterUpdate');
	sql("CREATE TRIGGER `coordinatesAfterUpdate` AFTER UPDATE ON `coordinates`
				FOR EACH ROW
					BEGIN
						IF NEW.`type`=1 THEN
							IF ((ISNULL(@XMLSYNC) OR @XMLSYNC!=1) AND IFNULL(@dont_update_listingdate,0)=0) THEN
							  /* update caches modification date for XML interface handling */
								UPDATE `caches` SET `last_modified`=NEW.`last_modified` WHERE `cache_id`=NEW.`cache_id`;
							END IF;
							CALL sp_update_cache_listingdate(NEW.`cache_id`);
						END IF;
						IF OLD.`cache_id`!=NEW.`cache_id` AND OLD.`type`=1 THEN
							CALL sp_update_cache_listingdate(OLD.`cache_id`);
						END IF;
					END;");

	sql_dropTrigger('coordinatesAfterDelete');
	sql("CREATE TRIGGER `coordinatesAfterDelete` AFTER DELETE ON `coordinates`
				FOR EACH ROW
					BEGIN
						IF OLD.`type`=1 THEN
							IF (ISNULL(@XMLSYNC) OR @XMLSYNC!=1) THEN
							  /* update caches modification date for XML interface handling */
								UPDATE `caches` SET `last_modified`=NOW() WHERE `cache_id`=OLD.`cache_id`;
							END IF;
							CALL sp_update_cache_listingdate(OLD.`cache_id`);
						END IF;
					END;");

	sql_dropTrigger('savedTextsBeforeInsert');
	sql("CREATE TRIGGER `savedTextsBeforeInsert` BEFORE INSERT ON `saved_texts`
				FOR EACH ROW
					BEGIN
						/* dont overwrite creation date while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
						END IF;
					END;");

	sql_dropTrigger('cacheReportsBeforeInsert');
	sql("CREATE TRIGGER `cacheReportsBeforeInsert` BEFORE INSERT ON `cache_reports`
				FOR EACH ROW
					BEGIN
						/* dont overwrite creation date while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							SET NEW.`date_created`=NOW();
						END IF;
					END;");

	sql_dropTrigger('statCachesAfterInsert');
	sql("CREATE TRIGGER `statCachesAfterInsert` AFTER INSERT ON `stat_caches`
				FOR EACH ROW
					BEGIN
						/* meta_last_modified=NOW() is used to trigger an update of okapi_syncbase,
						   if OKAPI is installed. */
						UPDATE caches SET meta_last_modified=NOW() WHERE caches.cache_id=NEW.cache_id;
					END;");

	sql_dropTrigger('statCachesAfterUpdate');
	sql("CREATE TRIGGER `statCachesAfterUpdate` AFTER UPDATE ON `stat_caches`
				FOR EACH ROW
					BEGIN
						IF NEW.found<>OLD.found OR NEW.notfound<>OLD.notfound OR NEW.note<>OLD.note OR
						   NEW.will_attend<>OLD.will_attend OR NEW.last_found<>OLD.last_found OR
						   NEW.watch<>OLD.watch OR NEW.ignore<>OLD.ignore OR NEW.toprating<>OLD.toprating THEN
							/* meta_last_modified=NOW() is used to trigger an update of okapi_syncbase,
							   if OKAPI is installed. */
							UPDATE caches SET meta_last_modified=NOW() WHERE caches.cache_id=NEW.cache_id;
						END IF;
					END;");

	sql_dropTrigger('gkItemWaypointAfterInsert');
	sql("CREATE TRIGGER `gkItemWaypointAfterInsert` AFTER INSERT ON `gk_item_waypoint`
				FOR EACH ROW
					BEGIN
						/* this triggers an update of okapi_syncbase, if OKAPI is installed */
						UPDATE caches SET meta_last_modified=NOW() WHERE caches.wp_oc=NEW.wp;
					END;");

	sql_dropTrigger('gkItemWaypointAfterUpdate');
	sql("CREATE TRIGGER `gkItemWaypointAfterUpdate` AFTER UPDATE ON `gk_item_waypoint`
				FOR EACH ROW
					BEGIN
						/* this triggers an update of okapi_syncbase, if OKAPI is installed */
						UPDATE caches SET meta_last_modified=NOW() WHERE caches.wp_oc=OLD.wp;
						UPDATE caches SET meta_last_modified=NOW() WHERE caches.wp_oc=NEW.wp;
					END;");

	sql_dropTrigger('gkItemWaypointAfterDelete');
	sql("CREATE TRIGGER `gkItemWaypointAfterDelete` AFTER DELETE ON `gk_item_waypoint`
				FOR EACH ROW
					BEGIN
						/* this triggers an update of okapi_syncbase, if OKAPI is installed */
						UPDATE caches SET meta_last_modified=NOW() WHERE caches.wp_oc=OLD.wp;
					END;");


	// Update trigger version function.
	// Keep this at the end of this file.
	sql_dropFunction('dbsvTriggerVersion');
	sql("
		CREATE FUNCTION `dbsvTriggerVersion` () RETURNS INT
		RETURN '&1'",
		current_triggerversion());

?>
