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
	 *
	 * See http://wiki.opencaching.de/index.php/Entwicklung/Datenbankversionierung
	 * (German) and the comments in this file for further documentation.
	 */

	if (!isset($opt['rootpath']))
		$opt['rootpath'] = dirname(__FILE__) . '/../htdocs/';
	require_once($opt['rootpath'] . 'lib2/cli.inc.php');
	require_once($opt['rootpath'] . 'lib2/search/search.inc.php');

	if (!sql_field_exists('cache_attrib','gc_id'))
	{
		die(
			"  ERROR: Database structure too old. You must first do a manual update\n" .
			"  up to commit 467aae4 (March 27, 2013) to enable automatic updates.\n" .
			"  See htdocs/doc/sql/db-changes.txt.\n");
		// Do not continue with dbupdate.php, because the current data.sql and
		// maintain.php will not fit either.
	}

	if (!sql_procedure_exists('sp_touch_cache'))
	{
		// We need a consistent starting point including triggers & functions, and it's
		// safer not to decide HERE which trigger version to install.
		echo "Triggers / DB functions are not installed (yet) - skipping DB versioning.\n";
		exit;
			// continue with dbupdate.php if called from there and let's hope
			// maintain.php matches the installed tables' DB version ...
	}

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
	//
	// Of course, a trigger update mutation can also be inserted directly before a
	// mutation which needs it. (But take care that maintain.php at that point does
	// not depend on database changes which will be done by that mutation ...)

	function update_triggers()
	{
		global $opt, $db_version;

		// For the case we re-run an old mutation for some accident, we must make
		// sure that we are not downgrading to an old trigger version (which may be
		// incompatible with the current database structures.
		if (sql_function_exists('dbsvTriggerVersion'))
			$trigger_version = sql_value('SELECT dbsvTriggerVersion()',0);
		else
			$trigger_version = 0;

		if ($trigger_version < $db_version)
		{
			$syncfile = $opt['rootpath'] . 'cache2/dbsv-running';
			file_put_contents($syncfile,'dbsv is running');

			system('php ' . $opt['rootpath'] . 'doc/sql/stored-proc/maintain.php --dbsv '.$db_version.' --flush');
				// This will also update dbsvTriggerVersion.

			if (file_exists($syncfile))
			{
				die("\nmaintain.php was not properly executed\n");
				unlink($syncfile);
			}
		}
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

	function dbv_113()  // preventive, initial trigger update
	{
		// The if-condition ensures that we will not downgrade to an old trigger
		// version for the case this function is re-run by some accident.
		// For future trigger updates, this will be ensured by the version
		// number returned by dbsvTriggerVersion().

		if (!sql_function_exists('dbsvTriggerVersion'))
			update_triggers();
	}

	function dbv_114()  // add dbsvTriggerVersion
	{
		// dbsvTriggerVersion was introduced AFTER defining mutation #113 (it was
		// inserted there later). So we need to additionally install it on installations
		// which already updated to v113:

		update_triggers();
	}

	function dbv_115()  // remove obsolete functions
	{
		update_triggers();
	}

	function dbv_116()	// optimize index for sorting logs
	{
		sql("ALTER TABLE `cache_logs` DROP INDEX `date`,
		     ADD INDEX `date` (`cache_id`,`date`,`date_created`)");
	}

	function dbv_117()	// add user profile flag for OConly notifications
	{
		if (!sql_field_exists('user','notify_oconly'))
		{
			sql("ALTER TABLE `user` ADD COLUMN `notify_oconly` tinyint(1) NOT NULL default '1' AFTER `notify_radius`");
			sql("UPDATE `user` SET `notify_oconly`=0");
				// is default-enabled for new users but default-disabled for old users
		}
	}

	function dbv_118()	// resize field password to fit to the new hashed passwords
	{
		sql("ALTER TABLE `user` MODIFY COLUMN `password` VARCHAR(128) default NULL");
	}

	function dbv_119()	// resize admin status field to enable more detailed rights
	{
		sql("ALTER TABLE `user` MODIFY COLUMN `admin` smallint(5) unsigned NOT NULL default '0'");
	}

	function dbv_120()	// remove obsolete tables of very old, discarded map code
	{
		sql("DROP TABLE IF EXISTS `mapresult`");
		sql("DROP TABLE IF EXISTS `mapresult_data`");
	}
	
	function dbv_121()	// add user profile flag for receiving newsletter
	{
		if (!sql_field_exists('user','accept_mailing'))
		{
			sql("ALTER TABLE `user` ADD COLUMN `accept_mailing` tinyint(1) NOT NULL default '1' AFTER `mailing_problems`");
		}
	}

	function dbv_122()	// add user profile flag for default setting of send-my-email option
	{
		if (!sql_field_exists('user','usermail_send_addr'))
		{
			sql("ALTER TABLE `user` ADD COLUMN `usermail_send_addr` tinyint(1) NOT NULL default '0' AFTER `accept_mailing`");
		}
	}

	function dbv_123()  // add tables, fields and procs for cache lists and list watches
	{
		if (!sql_table_exists('cache_lists'))
		{
			sql("
				CREATE TABLE `cache_lists` (
				  `id` int(10) NOT NULL auto_increment,
				  `uuid` varchar(36) NOT NULL,
				  `user_id` int(10) NOT NULL,
				  `date_created` datetime NOT NULL,
				  `last_modified` datetime NOT NULL,
				  `last_added` datetime default NULL,
				  `name` varchar(80) NOT NULL,
				  `is_public` tinyint(1) NOT NULL default '0',
				  `entries` int(6) NOT NULL default '0' COMMENT 'via trigger in cache_list_items',
				  `watchers` int(10) NOT NULL default '0' COMMENT 'via trigger in cache_list_watches',
				  PRIMARY KEY  (`id`),
				  UNIQUE KEY `uuid` (`uuid`),
				  KEY `name` (`name`),
				  KEY `user_id` (`user_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		}
		if (!sql_table_exists('cache_list_items'))
		{
			sql("
				CREATE TABLE `cache_list_items` (
				  `cache_list_id` int(10) NOT NULL,
				  `cache_id` int(10) NOT NULL,
				  UNIQUE KEY `cache_list_id` (`cache_list_id`,`cache_id`),
				  KEY `cache_id` (`cache_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		}
		if (!sql_table_exists('cache_list_watches'))
		{
			sql("
				CREATE TABLE `cache_list_watches` (
				  `cache_list_id` int(10) NOT NULL,
				  `user_id` int(10) NOT NULL,
				  UNIQUE KEY `cache_list_id` (`cache_list_id`,`user_id`),
				  KEY `user_id` (`user_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		}

		if (!sql_field_exists('caches','show_cachelists'))
		{
			sql("ALTER TABLE `caches` ADD COLUMN `show_cachelists` tinyint(1) NOT NULL default '1'");
		}
		if (sql_field_exists('cache_watches','last_executed'))  // obsolete pre-OC3 field
		{
			sql("ALTER TABLE `cache_watches` DROP COLUMN `last_executed`"); 
		}

		update_triggers();		// runs maintain-123.inc.php
	}

	function dbv_124()  // update cache lists implementation
	{
		if (!sql_table_exists('stat_cache_lists'))
		{
			sql("
				CREATE TABLE `stat_cache_lists` (
				  `cache_list_id` int(10) NOT NULL,
				  `entries` int(6) NOT NULL default '0' COMMENT 'via trigger in cache_list_items',
				  `watchers` int(6) NOT NULL default '0' COMMENT 'via trigger in cache_list_watches',
				  PRIMARY KEY (`cache_list_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8
				SELECT `id` `cache_list_id`, `entries`, `watchers` FROM `cache_lists`");
		}
		if (sql_field_exists('cache_lists','entries'))
			sql("ALTER TABLE `cache_lists` DROP COLUMN `entries`");
		if (sql_field_exists('cache_lists','watchers'))
			sql("ALTER TABLE `cache_lists` DROP COLUMN `watchers`");
		if (!sql_field_exists('cache_lists','description'))
			sql("ALTER TABLE `cache_lists` ADD COLUMN `description` mediumtext NOT NULL");
		if (!sql_field_exists('cache_lists','desc_htmledit'))
			sql("ALTER TABLE `cache_lists` ADD COLUMN `desc_htmledit` tinyint(1) unsigned NOT NULL default '1'");

		update_triggers();		// runs maintain-124.inc.php
	}

	function dbv_125()  // update cache lists implementation; preparation of XML interface export
	{
		global $opt;

		if (!sql_field_exists('cache_lists','node'))
		{
			sql("ALTER TABLE `cache_lists` ADD COLUMN `node` tinyint(3) unsigned NOT NULL default '0' AFTER `uuid`");
			sql("UPDATE `cache_lists` SET `node`='&1'", $opt['logic']['node']['id']);
		}
		if (!sql_field_exists('cache_lists','last_state_change'))
			sql("ALTER TABLE `cache_lists` ADD COLUMN `last_state_change` datetime default NULL AFTER `last_added`");

		update_triggers();		// runs maintain-125.inc.php
	}

	function dbv_126()  // clean up data of disabled accounts
	{
		sql("DELETE FROM `cache_adoption` WHERE `user_id` IN (SELECT `user_id` FROM `user` WHERE `is_active_flag`=0)");
		sql("DELETE FROM `cache_ignore`   WHERE `user_id` IN (SELECT `user_id` FROM `user` WHERE `is_active_flag`=0)");
		sql("DELETE FROM `cache_watches`  WHERE `user_id` IN (SELECT `user_id` FROM `user` WHERE `is_active_flag`=0)");
	}

	function dbv_127()  // fix name of Dessau-Köthen
	{
		sql("UPDATE `nuts_codes` SET `name`='Köthen' WHERE `code`='DEE15'");
		sql("UPDATE `cache_location` SET `adm4`='Köthen' WHERE `code4`='DEE15'");
	}

	function dbv_128()  // see util2/gns/mksearchindex.php; fix for #175/3
	{
		sql('DELETE FROM `gns_search`');
		if (sql_field_exists('gns_search','id'))
			sql("ALTER TABLE `gns_search` DROP COLUMN `id`");
			// unused, does not make sense; will also drop primary index

		$rs = sql("SELECT `uni`, `full_name_nd` FROM `gns_locations` WHERE `dsg` LIKE 'PPL%'");
		while ($r = sql_fetch_array($rs))
		{
			$text = search_text2sort($r['full_name_nd'], true);
			if (preg_match("/[a-z]+/", $text))
			{
				$simpletext = search_text2simple($text);
				sql("INSERT INTO `gns_search` (`uni_id`, `sort`, `simple`, `simplehash`) VALUES ('&1', '&2', '&3', '&4')", $r['uni'], $text, $simpletext, sprintf("%u", crc32($simpletext)));
			}
		}
		mysql_free_result($rs);
	}

	function dbv_129()  // cache list passwords & bookmarking
	{
		if (!sql_field_exists('cache_lists','password'))
			sql("ALTER TABLE `cache_lists` ADD COLUMN `password` varchar(80) NOT NULL");
		if (!sql_table_exists('cache_list_bookmarks'))
		{
			sql("
				CREATE TABLE `cache_list_bookmarks` (
				  `cache_list_id` int(10) NOT NULL,
				  `user_id` int(10) NOT NULL,
				  `password` varchar(80) NOT NULL,
				  UNIQUE KEY `cache_list_id` (`cache_list_id`,`user_id`),
				  KEY `user_id` (`user_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		}
	}

	function dbv_130()  // discarded text editor mode (#236)
	{
		sql("ALTER TABLE `cache_desc` CHANGE COLUMN `desc_html` `desc_html` tinyint(1) NOT NULL default '1' COMMENT 'obsolete'");
		sql("ALTER TABLE `cache_desc` CHANGE COLUMN `desc_htmledit` `desc_htmledit` tinyint(1) NOT NULL default '1'");
		sql("ALTER TABLE `cache_logs` CHANGE COLUMN `text_html` `text_html` tinyint(1) NOT NULL default '1' COMMENT 'obsolete'");
		sql("ALTER TABLE `cache_logs` CHANGE COLUMN `text_htmledit` `text_htmledit` tinyint(1) NOT NULL default '1'");
		sql("ALTER TABLE `user` CHANGE COLUMN `no_htmledit_flag` `no_htmledit_flag` tinyint(1) NOT NULL default '0' COMMENT 'inverted meaning'");
	}

	function dbv_131()  // add native language names (#109)
	{
		if (!sql_field_exists('languages','native_name'))
			sql("ALTER TABLE `languages` ADD COLUMN `native_name` VARCHAR(60) NOT NULL AFTER `trans_id`");
	}


	// When adding new mutations, take care that they behave well if run multiple
	// times. This improves robustness of database versioning.
	//
	// Please carefully decide if a new mutation relies on any triggers.
	// If so, check if triggers need to be updated first - they may have changed
	// since the last trigger update mutation (like #113) - or emulate the trigger
	// behaviour by additional SQL statements which restore table consistency.
	//
	// Trigger updates can be directly included in a mutation, or can be done via
	// a separate trigger update mutation (see #113 and maintain-113.inc.php).
	// See also http://wiki.opencaching.de/index.php/Entwicklung/Datenbankversionierung.

?>