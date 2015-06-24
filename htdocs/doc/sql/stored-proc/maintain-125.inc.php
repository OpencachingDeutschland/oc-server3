<?php
 /***************************************************************************

		Unicode Reminder メモ

		update functions and procedures for cache lists

	***************************************************************************/

	// We run this via maintain.php instead of dbsv-update.php because the
	// latter one has no sufficient privileges yet for updating functions
	// (should be changed / may have been changed when you are reading this.)

	// added node, description, desc_htmledit and last_to_private fields
	sql_dropTrigger('cacheListsBeforeUpdate');
	sql("CREATE TRIGGER `cacheListsBeforeUpdate` BEFORE UPDATE ON `cache_lists` 
				FOR EACH ROW 
					BEGIN 
					  IF NEW.`id` != OLD.`id` THEN
							CALL error_cache_list_id_must_not_be_changed();
						END IF;
						IF NEW.`uuid` != OLD.`uuid` OR
						   NEW.`node` != OLD.`node` OR
						   NEW.`user_id` != OLD.`user_id` OR
						   NEW.`name` != OLD.`name` OR 
							 NEW.`is_public` != OLD.`is_public` OR
							 NEW.`description` != OLD.`description` OR
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

	// new trigger
	sql_dropTrigger('cacheListsAfterDelete');
	sql("CREATE TRIGGER `cacheListsAfterDelete` AFTER DELETE ON `cache_lists` 
				FOR EACH ROW
					BEGIN
						INSERT IGNORE INTO `removed_objects` (`localId`, `uuid`, `type`, `node`) VALUES (OLD.`id`, OLD.`uuid`, 8, OLD.`node`);
					END;");

?>
