<?php
 /***************************************************************************

		Unicode Reminder メモ

		update functions and procedures for cache lists

	***************************************************************************/

	// We run this via maintain.php instead of dbsv-update.php because the
	// latter one has no sufficient privileges yet for updating functions
	// (should be changed / may have been changed when you are reading this.)

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

	sql_dropTrigger('cacheListsAfterInsert');
	sql("CREATE TRIGGER `cacheListsAfterInsert` AFTER INSERT ON `cache_lists` 
				FOR EACH ROW
					BEGIN
						INSERT INTO `stat_cache_lists` (`cache_list_id`) VALUES (NEW.`id`);
					END;");

	sql_dropTrigger('cacheListsBeforeDelete');
	sql("CREATE TRIGGER `cacheListsBeforeDelete` BEFORE DELETE ON `cache_lists` 
				FOR EACH ROW 
					BEGIN 
						SET @DELETING_CACHELIST=TRUE;
						DELETE FROM `stat_cache_lists` WHERE `cache_list_id`=OLD.`id`;
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

?>
