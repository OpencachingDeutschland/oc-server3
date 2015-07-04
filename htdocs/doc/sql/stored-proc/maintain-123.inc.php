<?php
 /***************************************************************************

		Unicode Reminder メモ

		install/update functions and procedures for cache lists & list watching

	***************************************************************************/

	// We run this via maintain.php instead of dbsv-update.php because the
	// latter one has no sufficient privileges yet for updating functions
	// (should be changed / may have been changed when you are reading this.)

	sql_dropProcedure('sp_updateall_cachelist_counts');
	sql("CREATE PROCEDURE sp_updateall_cachelist_counts (OUT nModified INT)
	     BEGIN
				UPDATE `cache_lists` SET `entries`=
					(SELECT COUNT(*) from `cache_list_items` WHERE `cache_list_items`.`cache_list_id`=`cache_lists`.`id`); 
				SET nModified = ROW_COUNT();
				UPDATE `cache_lists` SET `watchers`=
					(SELECT COUNT(*) from `cache_list_watches` WHERE `cache_list_watches`.`cache_list_id`=`cache_lists`.`id`); 
				SET nModified = nModified + ROW_COUNT();
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

	sql_dropTrigger('cacheListsBeforeUpdate');
	sql("CREATE TRIGGER `cacheListsBeforeUpdate` BEFORE UPDATE ON `cache_lists` 
				FOR EACH ROW 
					BEGIN 
					  IF NEW.`id` != OLD.`id` OR
					     NEW.`uuid` != OLD.`uuid` OR
						   NEW.`user_id` != OLD.`user_id` OR
						   NEW.`name` != OLD.`name` OR 
							 NEW.`is_public` != OLD.`is_public` THEN
							/* dont overwrite date values while XML client is running */
							IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
								SET NEW.`last_modified`=NOW();
							END IF;
						END IF;
						IF OLD.`is_public` AND NOT NEW.`is_public` THEN
							DELETE FROM `cache_list_watches` WHERE `cache_list_watches`.`cache_list_id`=NEW.`id` AND `cache_list_watches`.`user_id` != NEW.`user_id`;
						END IF;
					END;");

	sql_dropTrigger('cacheListsBeforeDelete');
	sql("CREATE TRIGGER `cacheListsBeforeDelete` BEFORE DELETE ON `cache_lists` 
				FOR EACH ROW 
					BEGIN 
						SET @DELETING_CACHELIST=TRUE;
						DELETE FROM `cache_list_watches` WHERE `cache_list_watches`.`cache_list_id`=OLD.`id`;
						DELETE FROM `cache_list_items` WHERE `cache_list_items`.`cache_list_id`=OLD.`id`;
						SET @DELETING_CACHELIST=FALSE;
					END;");

	sql_dropTrigger('cacheListItemsAfterInsert');
	sql("CREATE TRIGGER `cacheListItemsAfterInsert` AFTER INSERT ON `cache_list_items`
				FOR EACH ROW 
					BEGIN
						/* dont overwrite date values while XML client is running */
						IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
							UPDATE `cache_lists` SET `last_modified`=NOW(), `last_added`=NOW(), `entries`=`entries`+1 WHERE `cache_lists`.`id`=NEW.`cache_list_id`;
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
								UPDATE `cache_lists` SET `entries`=`entries`-1 WHERE `cache_lists`.`id`=OLD.`cache_list_id`;  
								UPDATE `cache_lists` SET `last_modified`=NOW(), `last_added`=NOW(), `entries`=`entries`+1 WHERE `cache_lists`.`id`=NEW.`cache_list_id`;
							END IF;
							IF NEW.`cache_id` != OLD.`cache_id` THEN
								UPDATE `cache_lists` SET `last_added`=NOW() WHERE `cache_lists`.`id`=NEW.`cache_list_id`;
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
						/* avoid recursive access to cache_lists */
						IF NOT IFNULL(@DELETING_CACHELIST,FALSE) THEN
							/* dont overwrite date values while XML client is running */
							IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
								UPDATE `cache_lists` SET `last_modified`=NOW(), `entries`=`entries`-1 WHERE `cache_lists`.`id`=OLD.`cache_list_id`;
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
							UPDATE `cache_lists` SET `watchers`=`watchers`+1 WHERE `cache_lists`.`id`=NEW.`cache_list_id`;
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
								UPDATE `cache_lists` SET `watchers`=`watchers`-1 WHERE `cache_lists`.`id`=OLD.`cache_list_id`;  
								UPDATE `cache_lists` SET `watchers`=`watchers`+1 WHERE `cache_lists`.`id`=NEW.`cache_list_id`;
							END IF;
							CALL sp_update_list_watchstat(OLD.`cache_list_id`);
							CALL sp_update_list_watchstat(NEW.`cache_list_id`);
						END IF;
					END;");

	sql_dropTrigger('cacheListWatchesAfterDelete');
	sql("CREATE TRIGGER `cacheListWatchesAfterDelete` AFTER DELETE ON `cache_list_watches` 
				FOR EACH ROW 
					BEGIN
						/* avoid recursive access to cache_lists */
						IF NOT IFNULL(@DELETING_CACHELIST,FALSE) THEN
							/* dont overwrite date values while XML client is running */
							IF ISNULL(@XMLSYNC) OR @XMLSYNC!=1 THEN
								UPDATE `cache_lists` SET `watchers`=`watchers`-1 WHERE `cache_lists`.`id`=OLD.`cache_list_id`;
							CALL sp_update_list_watchstat(OLD.`cache_list_id`);
							END IF;
						END IF;
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

?>
