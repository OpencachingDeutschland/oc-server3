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

	if (!sql_field_exists('cache_attrib','gc_id'))
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


	// Now and then a maintain.php update should be inserted, because multiple
	// mutations may be run in one batch, and future mutations may depend on
	// changed triggers, which may not be obvious.
	// Of course, a trigger update mutation can also be inserted directly before a
	// mutation which needs it. (But take care that maintain.php at that point does
	// not depend on database changes which will be done by that mutation ...)

	function update_triggers()
	{
		global $opt;
		echo " ";  // space for the case of a DB password prompt
		system('php ' . $opt['rootpath'] . 'doc/sql/stored-proc/maintain.php');
	}


	// Database mutations
	// - must be consecutively numbered
	// - should behave well if run multiple times

	function dbv_100()  // expands log date to datetime, to enable time logging
	{
		if (sql_field_type('cache_logs','date') != 'DATETIME')
			sql("ALTER TABLE `cache_logs` CHANGE COLUMN `date` `date` DATETIME NOT NULL");
		if (sql_field_type('cache_logs_archived','date') != 'DATETIME')
			sql("ALTER TABLE `cache_logs_archived` CHANGE COLUMN `date` `date` DATETIME NOT NULL");
	}

	function dbv_101()  // add fields for fixing OKAPI issue #232
	{
		if (!sql_field_exists('caches','meta_last_modified'))
		{
		  // initialize with '0000-00-00 00:00:00' for existing data, that's ok
			sql("ALTER TABLE `caches` ADD COLUMN `meta_last_modified` DATETIME NOT NULL COMMENT 'via Trigger (cache_logs)' AFTER `listing_last_modified`");
		}
		if (!sql_field_exists('cache_logs','log_last_modified'))
		{
			if (sql_field_exists('cache_logs','okapi_syncbase'))
				$after = 'okapi_syncbase';
			else
				$after = 'last_modified';
			sql("ALTER TABLE `cache_logs` ADD COLUMN `log_last_modified` DATETIME NOT NULL COMMENT 'via Trigger (stat_caches, gk_item_waypoint)' AFTER `".$after."`");
			sql("UPDATE `cache_logs` SET `log_last_modified` = GREATEST(
			         `last_modified`,
			         IFNULL((SELECT MAX(`last_modified`) FROM `pictures` WHERE `pictures`.`object_type`=1 AND `pictures`.`object_id` = `cache_logs`.`id`),'0')
			         )");
		}
		if (!sql_field_exists('cache_logs_archived','log_last_modified'))
		{
			if (sql_field_exists('cache_logs_archived','okapi_syncbase'))
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
		if (sql_table_exists('geodb_areas'))       sql("ALTER TABLE `geodb_areas`       COMMENT = 'not in use'");
		if (sql_table_exists('geodb_changelog'))   sql("ALTER TABLE `geodb_changelog`   COMMENT = 'not in use'");
		if (sql_table_exists('geodb_coordinates')) sql("ALTER TABLE `geodb_coordinates` COMMENT = 'static content'");
		if (sql_table_exists('geodb_floatdata'))   sql("ALTER TABLE `geodb_floatdata`   COMMENT = 'not in use'");
		if (sql_table_exists('geodb_hierarchies')) sql("ALTER TABLE `geodb_hierarchies` COMMENT = 'static content'");
		if (sql_table_exists('geodb_intdata'))     sql("ALTER TABLE `geodb_intdata`     COMMENT = 'not in use'");
		if (sql_table_exists('geodb_locations'))   sql("ALTER TABLE `geodb_locations`   COMMENT = 'static content'");
		if (sql_table_exists('geodb_polygons'))    sql("ALTER TABLE `geodb_polygons`    COMMENT = 'not in use'");
		if (sql_table_exists('geodb_search'))      sql("ALTER TABLE `geodb_search`      COMMENT = 'static content, not in use'");
		if (sql_table_exists('geodb_textdata'))    sql("ALTER TABLE `geodb_textdata`    COMMENT = 'static content'");
		if (sql_table_exists('geodb_type_names'))  sql("ALTER TABLE `geodb_type_names`  COMMENT = 'not in use'");
		if (sql_table_exists('pw_dict'))           sql("ALTER TABLE `pw_dict`           COMMENT = 'static content'");
		sql("ALTER TABLE `npa_areas`  COMMENT = 'static content'");
		sql("ALTER TABLE `npa_types`  COMMENT = 'static content'");
		sql("ALTER TABLE `nuts_codes` COMMENT = 'static content'");
		sql("ALTER TABLE `nuts_layer` COMMENT = 'static content'");
	}

	function dbv_104()  // added maintenance logs and OC team comments
	{
		sql("ALTER TABLE `log_types_text` COMMENT = 'obsolete'");
		sql("ALTER TABLE `cache_logtype` COMMENT = 'obsolete'");
		sql("ALTER TABLE `log_types` CHANGE COLUMN `cache_status` `cache_status` tinyint(1) NOT NULL default '0'");
		sql("ALTER TABLE `log_types` CHANGE COLUMN `en` `en` varchar(60) NOT NULL");
		if (!sql_field_exists('stat_caches','maintenance'))
			sql("ALTER TABLE `stat_caches` ADD COLUMN `maintenance` smallint(5) unsigned NOT NULL AFTER `will_attend`");
		if (!sql_field_exists('stat_cache_logs','maintenance'))
			sql("ALTER TABLE `stat_cache_logs` ADD COLUMN `maintenance` smallint(5) unsigned NOT NULL AFTER `will_attend`");
		if (!sql_field_exists('stat_user','maintenance'))
			sql("ALTER TABLE `stat_user` ADD COLUMN `maintenance` smallint(5) unsigned NOT NULL AFTER `will_attend`");
		if (!sql_field_exists('cache_logs','oc_team_comment'))
			sql("ALTER TABLE `cache_logs` ADD COLUMN `oc_team_comment` tinyint(1) NOT NULL default '0' AFTER `type`");
		if (!sql_field_exists('cache_logs_archived','oc_team_comment'))
			sql("ALTER TABLE `cache_logs_archived` ADD COLUMN `oc_team_comment` tinyint(1) NOT NULL default '0' AFTER `type`");
		// The new fields need not to be initialized, as these are new features and all
		// values are initally zero.
	}

	function dbv_105()  // HTML user profile texts
	{
		if (!sql_field_exists('user','desc_htmledit'))
			sql("ALTER TABLE `user` ADD COLUMN `desc_htmledit` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `data_license`");
		if (!sql_field_exists('user','description'))
		{
			sql("ALTER TABLE `user` ADD COLUMN `description` mediumtext NOT NULL AFTER `data_license`");
			$rs = sql("SELECT `user`.`user_id`,`user_options`.`option_value` FROM `user`,`user_options` WHERE `user_options`.`user_id`=`user`.`user_id` AND `user_options`.`option_id`=3");
			while ($r = sql_fetch_array($rs))
			{
				$text = nl2br(htmlspecialchars($r['option_value'], ENT_COMPAT, 'UTF-8'));
				sql("UPDATE `user` SET `description`='&2' WHERE `user_id`='&1'", $r['user_id'], $text);
			}
			sql_free_result($rs);
			// we keep the old entries in user_options for the case something went wrong here.
		}
	}

	function dbv_106()  // Cache status logging
	{
		if (!sql_table_exists('cache_status_modified'))
			sql(
			 "CREATE TABLE `cache_status_modified` (
					`cache_id` int(10) unsigned NOT NULL,
					`date_modified` datetime NOT NULL,
					`old_state` tinyint(2) unsigned NOT NULL,
					`new_state` tinyint(2) unsigned NOT NULL,
					`user_id` int(10) unsigned NOT NULL default '0',
				UNIQUE KEY `cache_id` (`cache_id`,`date_modified`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	}

	function dbv_107()  // sync of table definitions, developer and production system	
	{
		sql("ALTER TABLE `caches` MODIFY `meta_last_modified` datetime NOT NULL COMMENT 'via Trigger (stat_caches, gk_item_waypoint)'");
		sql("ALTER TABLE `countries` MODIFY `en` varchar(128) NOT NULL");
		if (!sql_index_exists('cache_reports', 'userid'))
			sql("ALTER TABLE `cache_reports` ADD INDEX `userid` (`userid`)");
	}

	function dbv_108()  // automatic email-bounce processing
	{
		if (!sql_field_exists('user','last_email_problem'))
			sql("ALTER TABLE `user` ADD COLUMN `last_email_problem` datetime default NULL AFTER `email_problems`");
		if (!sql_field_exists('user','mailing_problems'))
			sql("ALTER TABLE `user` ADD COLUMN `mailing_problems` int(10) unsigned NOT NULL default '0' AFTER `last_email_problem`");
	}

	function dbv_109()  // improved email-bounce processing
	{
		if (!sql_field_exists('user','first_email_problem'))
			sql("ALTER TABLE `user` ADD COLUMN `first_email_problem` date default NULL AFTER `email_problems`");
	}

	function dbv_110()  // move adoption history to separate table
	{
		if (!sql_table_exists('cache_adoptions'))
		{
			sql(
				"CREATE TABLE `cache_adoptions` (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`cache_id` int(10) unsigned NOT NULL,
					`date` datetime NOT NULL,
					`from_user_id` int(10) unsigned NOT NULL,
					`to_user_id` int(10) unsigned NOT NULL,
					PRIMARY KEY (`id`),
					KEY `cache_id` (`cache_id`,`date`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

			// Up to commit d15ee5f9, new cache notification logs were erronously stored with
			// event ID 5 (instead of 8). Therefore we need to check for the module, too:
			$rs = sql("SELECT `id`, `date_created`, `objectid1`, `logtext`
			             FROM `logentries`
			            WHERE `eventid`=5 AND `module`='cache'
			         ORDER BY `date_created`, `id`");
			while ($rLog = sql_fetch_assoc($rs))
			{
				preg_match('/Cache (\d+) has changed the owner from userid (\d+) to (\d+) by (\d+)/',
				           $rLog['logtext'], $matches);
				if (count($matches) != 5)
				{
					sql_free_result($rs);
					sql("DROP TABLE `cache_adoptions`");
					die("\nunknown adoption log entry format for ID " . $rLog['id'] . "\n");
				}
				sql("INSERT INTO `cache_adoptions`
				                 (`cache_id`,`date`,`from_user_id`,`to_user_id`)
						      VALUES ('&1','&2','&3','&4')",
						$rLog['objectid1'], $rLog['date_created'], $matches[2], $matches[3]);
			}
			sql_free_result($rs);

			// We keep the old entries in 'logentries' for the case something went wrong here.
		}
	}

	function dbv_111()  // fix event ID of old publishing notifications
	{
		sql("UPDATE `logentries` SET `eventid`=8
		      WHERE `eventid`=5 AND `module`='notify_newcache'");
	}

	function dbv_112()  // added maintained GC waypoints
	{
		if (!sql_field_exists('caches','wp_gc_maintained'))
		{
			sql("ALTER TABLE `caches` ADD COLUMN `wp_gc_maintained` varchar(7) NOT NULL AFTER `wp_gc`");
			sql("UPDATE `caches` SET `wp_gc_maintained`=UCASE(TRIM(`wp_gc`)) WHERE SUBSTR(TRIM(`wp_gc`),1,2)='GC'");
		}
		if (!sql_index_exists('caches', 'wp_gc_maintained'))
			sql("ALTER TABLE `caches` ADD INDEX `wp_gc_maintained` (`wp_gc_maintained`)");
	}

	function dbv_113()  // preventive trigger update
	{
		update_triggers();
	}

?>