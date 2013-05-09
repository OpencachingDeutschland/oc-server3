<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	/*
	 * Database Structure Versioning - update DB structure to current version;
	 * used for developer & production system
	 *
	 * You should normally NOT call this script directly, but via dbupdate.php
	 * (or something similar on a production system). This ensures that
	 * everything takes place in the right order.
	 */

	if (!isset($opt['rootpath']))
		$opt['rootpath'] = dirname(__FILE__) . '/../htdocs/';
	require_once($opt['rootpath'] . 'lib2/cli.inc.php');

	if (!field_exists('cache_attrib','gc_id'))
		die("\n
	       ERROR: Database structure too old. You must first do a manual update
				 up to commit 467aae4 (March 27, 2013) to enable automatic updates.
				 See htdocs/doc/sql/db-changes.txt.\n");

	$db_version = max(99, sql_value("SELECT `value` FROM `sysconfig` WHERE `name`='db_version'",99));

	do
	{
		++$db_version;
		$dbv_function = 'dbv_'.$db_version;
		if (function_exists($dbv_function))
		{
			echo "applying DB mutation #".$db_version;
			call_user_func($dbv_function);
			sql("INSERT INTO `sysconfig` (`name`,`value`) VALUES ('db_version','&1')
			     ON DUPLICATE KEY UPDATE `value`='&1'",
			    $db_version);
			echo " - ok.\n";
		}
		else
			$db_version = -1;
	} while ($db_version > 0);


	// test if a database table exists
	function table_exists($table)
	{
		global $opt;

		return sql_value("SELECT COUNT(*)
		                    FROM `information_schema`.`tables`
		                   WHERE `table_schema`='&1' AND `table_name`='&2'",
										 0, $opt['db']['placeholder']['db'], $table) > 0;
	}

	// test if a database field exists
	function field_exists($table, $field)
	{
		global $opt;

		return sql_value("SELECT COUNT(*)
		                    FROM `information_schema`.`columns`
		                   WHERE `table_schema`='&1' AND `table_name`='&2' AND `column_name`='&3'",
										 0, $opt['db']['placeholder']['db'], $table, $field) > 0;
	}

	// get type of a database field
	function field_type($table, $field)
	{
		global $opt;

		return strtoupper(
						sql_value("SELECT `data_type`
		                    FROM `information_schema`.`columns`
		                   WHERE `table_schema`='&1' AND `table_name`='&2' AND `column_name`='&3'",
										 '', $opt['db']['placeholder']['db'], $table, $field) );
	}


	// Database mutations
	// - must be consecutively numbered
	// - should behave well if run multiple times

	function dbv_100()  // expands log date to datetime, to enable time logging
	{
		if (field_type('cache_logs','date') != 'DATETIME')
			sql("ALTER TABLE `cache_logs` CHANGE COLUMN `date` `date` DATETIME NOT NULL");
		if (field_type('cache_logs_archived','date') != 'DATETIME')
			sql("ALTER TABLE `cache_logs_archived` CHANGE COLUMN `date` `date` DATETIME NOT NULL");
	}

	function dbv_101()  // add fields for fixing OKAPI issue #232
	{
		if (!field_exists('caches','meta_last_modified'))
		{
		  // initialize with '0000-00-00 00:00:00' for existing data, that's ok
			sql("ALTER TABLE `caches` ADD COLUMN `meta_last_modified` DATETIME NOT NULL COMMENT 'via Trigger (cache_logs)' AFTER `listing_last_modified`");
		}
		if (!field_exists('cache_logs','log_last_modified'))
		{
			if (field_exists('cache_logs','okapi_syncbase'))
				$after = 'okapi_syncbase';
			else
				$after = 'last_modified';
			sql("ALTER TABLE `cache_logs` ADD COLUMN `log_last_modified` DATETIME NOT NULL COMMENT 'via Trigger (stat_caches, gk_item_waypoint)' AFTER `".$after."`");
			sql("UPDATE `cache_logs` SET `log_last_modified` = GREATEST(
			         `last_modified`,
			         IFNULL((SELECT MAX(`last_modified`) FROM `pictures` WHERE `pictures`.`object_type`=1 AND `pictures`.`object_id` = `cache_logs`.`id`),'0')
			         )");
		}
		if (!field_exists('cache_logs_archived','log_last_modified'))
		{
			if (field_exists('cache_logs_archived','okapi_syncbase'))
				$after = 'okapi_syncbase';
			else
				$after = 'last_modified';
			sql("ALTER TABLE `cache_logs_archived` ADD COLUMN `log_last_modified` DATETIME NOT NULL AFTER `".$after."`");
			sql("UPDATE `cache_logs_archived` SET `log_last_modified` = `last_modified`");
		}
	}

	function dbv_102()  // remove invisible caches from users' hidden stats
	{
		sql("INSERT IGNORE INTO `stat_user` (`user_id`) SELECT `user_id` FROM `caches` GROUP BY `user_id`");
		sql("UPDATE `stat_user`, (SELECT `user_id`, COUNT(*) AS `count` FROM `caches` INNER JOIN `cache_status` ON `cache_status`.`id`=`caches`.`status` AND `allow_user_view`=1 GROUP BY `user_id`) AS `tblHidden` SET `stat_user`.`hidden`=`tblHidden`.`count` WHERE `stat_user`.`user_id`=`tblHidden`.`user_id`");
		sql("CALL sp_refreshall_statpic()");
	}

	function dbv_103()  // update comments on static tables
	{
		if (table_exists('geodb_areas'))       sql("ALTER TABLE `geodb_areas`       COMMENT = 'not in use'");
		if (table_exists('geodb_changelog'))   sql("ALTER TABLE `geodb_changelog`   COMMENT = 'not in use'");
		if (table_exists('geodb_coordinates')) sql("ALTER TABLE `geodb_coordinates` COMMENT = 'static content'");
		if (table_exists('geodb_floatdata'))   sql("ALTER TABLE `geodb_floatdata`   COMMENT = 'not in use'");
		if (table_exists('geodb_hierarchies')) sql("ALTER TABLE `geodb_hierarchies` COMMENT = 'static content'");
		if (table_exists('geodb_intdata'))     sql("ALTER TABLE `geodb_intdata`     COMMENT = 'not in use'");
		if (table_exists('geodb_locations'))   sql("ALTER TABLE `geodb_locations`   COMMENT = 'static content'");
		if (table_exists('geodb_polygons'))    sql("ALTER TABLE `geodb_polygons`    COMMENT = 'not in use'");
		if (table_exists('geodb_search'))      sql("ALTER TABLE `geodb_search`      COMMENT = 'static content, not in use'");
		if (table_exists('geodb_textdata'))    sql("ALTER TABLE `geodb_textdata`    COMMENT = 'static content'");
		if (table_exists('geodb_type_names'))  sql("ALTER TABLE `geodb_type_names`  COMMENT = 'not in use'");
		if (table_exists('pw_dict'))           sql("ALTER TABLE `pw_dict`           COMMENT = 'static content'");
		sql("ALTER TABLE `npa_areas`  COMMENT = 'static content'");
		sql("ALTER TABLE `npa_types`  COMMENT = 'static content'");
		sql("ALTER TABLE `nuts_codes` COMMENT = 'static content'");
		sql("ALTER TABLE `nuts_layer` COMMENT = 'static content'");
	}

?>