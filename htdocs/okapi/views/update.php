<?php

namespace okapi\views\update;

use Exception;

use okapi\Okapi;
use okapi\Cache;
use okapi\Db;
use okapi\OkapiRequest;
use okapi\OkapiRedirectResponse;
use okapi\OkapiHttpResponse;
use okapi\ParamMissing;
use okapi\InvalidParam;
use okapi\OkapiServiceRunner;
use okapi\OkapiInternalRequest;
use okapi\Settings;
use okapi\OkapiLock;
use okapi\cronjobs\CronJobController;

require_once($GLOBALS['rootpath']."okapi/cronjobs.php");

class View
{
	public static function get_current_version()
	{
		try
		{
			return Okapi::get_var('db_version', 0) + 0;
		}
		catch (Exception $e)
		{
			if (strpos($e->getMessage(), "okapi_vars' doesn't exist") !== false)
				return 0;
			throw $e;
		}
	}

	public static function get_max_version()
	{
		$max_db_version = 0;
		foreach (get_class_methods(__CLASS__) as $name)
		{
			if (strpos($name, "ver") === 0)
			{
				$ver = substr($name, 3) + 0;
				if ($ver > $max_db_version)
					$max_db_version = $ver;
			}
		}
		return $max_db_version;
	}

	public static function out($str)
	{
		print $str;
		# No ob_flush(). Output buffering should not be started (see controller.php).
		# Therefore, calling ob_flush would give an error.
		flush();
	}

	public static function call()
	{
		# First, let's acquire a lock to make sure the update isn't already running.

		$lock = OkapiLock::get('db-update');
		$lock->acquire();

		try
		{
			self::_call();
			$lock->release();
		}
		catch (Exception $e)
		{
			# Error occured. Make sure the lock is released and rethrow.

			$lock->release();
			throw $e;
		}
	}

	private static function _call()
	{
		ignore_user_abort(true);
		set_time_limit(0);

		header("Content-Type: text/plain; charset=utf-8");

		$current_ver = self::get_current_version();
		$max_ver = self::get_max_version();
		self::out("Current OKAPI database version: $current_ver\n");
		if ($current_ver == 0 && ((!isset($_GET['install'])) || ($_GET['install'] != 'true')))
		{
			self::out("Current OKAPI settings are:\n\n".Settings::describe_settings()."\n\n".
				"Make sure they are correct, then append '?install=true' to your query.");
			return;
		}
		elseif ($max_ver == $current_ver)
		{
			self::out("It is up-to-date.\n\n");
		}
		elseif ($max_ver < $current_ver)
			throw new Exception();
		else
		{
			self::out("Updating to version $max_ver... PLEASE WAIT\n\n");

			while ($current_ver < $max_ver)
			{
				$version_to_apply = $current_ver + 1;
				self::out("Applying mutation #$version_to_apply...");
				try {
					call_user_func(array(__CLASS__, "ver".$version_to_apply));
					self::out(" OK!\n");
					Okapi::set_var('db_version', $version_to_apply);
					$current_ver += 1;
				} catch (Exception $e) {
					self::out(" ERROR\n\n");
					throw $e;
				}
			}
			self::out("\nDatabase updated.\n\n");
		}

		self::out("Registering new cronjobs...\n");
		# Validate all cronjobs (some might have been added).
		Okapi::set_var("cron_nearest_event", 0);
		Okapi::execute_prerequest_cronjobs();

		self::out("\nUpdate complete.\n");
	}

	/**
	 * Return the list of email addresses of developers who used any of the given
	 * method names at least once. If $days is not null, then only consumers which
	 * used the method in last X $days will be returned.
	 */
	public static function get_consumers_of($service_names, $days = null)
	{
		return Db::select_column("
			select distinct c.email
			from
				okapi_consumers c,
				okapi_stats_hourly sh
			where
				sh.consumer_key = c.`key`
				and sh.service_name in ('".implode("','", array_map('mysql_real_escape_string', $service_names))."')
				".(($days != null) ? "and sh.period_start > date_add(now(), interval '".mysql_real_escape_string(-$days)."' day)" : "")."
		");
	}

	private static function ver1()
	{
		ob_start();
		print "Hi!\n\n";
		print "Since this is your first time you run okapi/update, you should be\n";
		print "prepared to receive a bunch of update-history emails. These are all\n";
		print "the emails that all the other OC admins received \"over the years\",\n";
		print "whenever they have updated OKAPI.\n\n";
		print "Each email describes an action which OKAPI performed on your\n";
		print "database, OR which we need YOU to perform.\n\n";
		print "If you receive any error messages during the update process,\n";
		print "please contact me - rygielski@mimuw.edu.pl.\n\n";
		print "-- \n";
		print "Wojciech Rygielski, OKAPI developer";
		Okapi::mail_admins("Starting OKAPI installation", ob_get_clean());

		Db::execute("
			CREATE TABLE okapi_vars (
				var varchar(32) charset ascii collate ascii_bin NOT NULL,
				value text,
				PRIMARY KEY  (var)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");
	}

	private static function ver2()
	{
		Db::execute("
			CREATE TABLE okapi_authorizations (
				consumer_key varchar(20) charset ascii collate ascii_bin NOT NULL,
				user_id int(11) NOT NULL,
				last_access_token datetime default NULL,
				PRIMARY KEY  (consumer_key,user_id)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");
	}

	private static function ver3()
	{
		Db::execute("
			CREATE TABLE okapi_consumers (
				`key` varchar(20) charset ascii collate ascii_bin NOT NULL,
				name varchar(100) collate utf8_general_ci NOT NULL,
				secret varchar(40) charset ascii collate ascii_bin NOT NULL,
				url varchar(250) collate utf8_general_ci default NULL,
				email varchar(70) collate utf8_general_ci default NULL,
				date_created datetime NOT NULL,
				PRIMARY KEY  (`key`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");
	}

	private static function ver4()
	{
		Db::execute("
			CREATE TABLE okapi_nonces (
				consumer_key varchar(20) charset ascii collate ascii_bin NOT NULL,
				`key` varchar(255) charset ascii collate ascii_bin NOT NULL,
				timestamp int(10) NOT NULL,
				PRIMARY KEY  (consumer_key, `key`, `timestamp`)
			) ENGINE=MEMORY DEFAULT CHARSET=utf8;
		");
	}

	private static function ver5()
	{
		Db::execute("
			CREATE TABLE okapi_tokens (
				`key` varchar(20) charset ascii collate ascii_bin NOT NULL,
				secret varchar(40) charset ascii collate ascii_bin NOT NULL,
				token_type enum('request','access') NOT NULL,
				timestamp int(10) NOT NULL,
				user_id int(10) default NULL,
				consumer_key varchar(20) charset ascii collate ascii_bin NOT NULL,
				verifier varchar(10) charset ascii collate ascii_bin default NULL,
				callback varchar(2083) character set utf8 collate utf8_general_ci default NULL,
				PRIMARY KEY  (`key`),
				KEY by_consumer (consumer_key)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");
	}

	private static function ver6()
	{
		# Removed this update. It seemed dangerous to run such updates on unknown OC installations.
	}

	private static function ver7()
	{
		# In fact, this should be "alter cache_logs add column okapi_consumer_key...", but
		# I don't want for OKAPI to mess with the rest of DB. Keeping it separete for now.
		# One day, this table could come in handy. See:
		# http://code.google.com/p/opencaching-api/issues/detail?id=64
		Db::execute("
			CREATE TABLE okapi_cache_logs (
				log_id int(11) NOT NULL,
				consumer_key varchar(20) charset ascii collate ascii_bin NOT NULL,
				PRIMARY KEY  (log_id),
				KEY by_consumer (consumer_key)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");
	}

	private static function ver8()
	{
		Db::execute("
			CREATE TABLE okapi_cache (
				`key` varchar(32) NOT NULL,
				value blob,
				expires datetime,
				PRIMARY KEY  (`key`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");
	}

	private static function ver9() { Db::execute("alter table okapi_consumers modify column `key` varchar(20) not null"); }
	private static function ver10() { Db::execute("alter table okapi_consumers modify column secret varchar(40) not null"); }
	private static function ver11() { Db::execute("alter table okapi_tokens modify column `key` varchar(20) not null"); }
	private static function ver12() { Db::execute("alter table okapi_tokens modify column secret varchar(40) not null"); }
	private static function ver13() { Db::execute("alter table okapi_tokens modify column consumer_key varchar(20) not null"); }
	private static function ver14() { Db::execute("alter table okapi_tokens modify column verifier varchar(10) default null"); }
	private static function ver15() { Db::execute("alter table okapi_authorizations modify column consumer_key varchar(20) not null"); }
	private static function ver16() { Db::execute("alter table okapi_nonces modify column consumer_key varchar(20) not null"); }
	private static function ver17() { Db::execute("alter table okapi_nonces modify column `key` varchar(255) not null"); }
	private static function ver18() { Db::execute("alter table okapi_cache_logs modify column consumer_key varchar(20) not null"); }
	private static function ver19() { Db::execute("alter table okapi_vars modify column `var` varchar(32) not null"); }

	private static function ver20() { Db::execute("alter table okapi_consumers modify column `key` varchar(20) collate utf8_bin not null"); }
	private static function ver21() { Db::execute("alter table okapi_consumers modify column secret varchar(40) collate utf8_bin not null"); }
	private static function ver22() { Db::execute("alter table okapi_tokens modify column `key` varchar(20) collate utf8_bin not null"); }
	private static function ver23() { Db::execute("alter table okapi_tokens modify column secret varchar(40) collate utf8_bin not null"); }
	private static function ver24() { Db::execute("alter table okapi_tokens modify column consumer_key varchar(20) collate utf8_bin not null"); }
	private static function ver25() { Db::execute("alter table okapi_tokens modify column verifier varchar(10) collate utf8_bin default null"); }
	private static function ver26() { Db::execute("alter table okapi_authorizations modify column consumer_key varchar(20) collate utf8_bin not null"); }
	private static function ver27() { Db::execute("alter table okapi_nonces modify column consumer_key varchar(20) collate utf8_bin not null"); }
	private static function ver28() { Db::execute("alter table okapi_nonces modify column `key` varchar(255) collate utf8_bin not null"); }
	private static function ver29() { Db::execute("alter table okapi_cache_logs modify column consumer_key varchar(20) collate utf8_bin not null"); }
	private static function ver30() { Db::execute("alter table okapi_vars modify column `var` varchar(32) collate utf8_bin not null"); }

	private static function ver31()
	{
		Db::execute("
			CREATE TABLE `okapi_stats_temp` (
				`datetime` datetime NOT NULL,
				`consumer_key` varchar(32) NOT NULL DEFAULT 'internal',
				`user_id` int(10) NOT NULL DEFAULT '-1',
				`service_name` varchar(80) NOT NULL,
				`calltype` enum('internal','http') NOT NULL,
				`runtime` float NOT NULL DEFAULT '0'
			) ENGINE=MEMORY DEFAULT CHARSET=utf8
		");
	}

	private static function ver32()
	{
		Db::execute("
			CREATE TABLE `okapi_stats_hourly` (
				`consumer_key` varchar(32) NOT NULL,
				`user_id` int(10) NOT NULL,
				`period_start` datetime NOT NULL,
				`service_name` varchar(80) NOT NULL,
				`total_calls` int(10) NOT NULL,
				`http_calls` int(10) NOT NULL,
				`total_runtime` float NOT NULL DEFAULT '0',
				`http_runtime` float NOT NULL DEFAULT '0',
				PRIMARY KEY (`consumer_key`,`user_id`,`period_start`,`service_name`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8
		");
	}

	private static function ver33()
	{
		try
		{
			Db::execute("alter table cache_logs add key `uuid` (`uuid`)");
		}
		catch (Exception $e)
		{
			// key exists
		}
	}

	private static function ver34()
	{
		Db::execute("
			CREATE TABLE `okapi_clog` (
				id int(10) not null auto_increment,
				data blob default null,
				PRIMARY KEY (id)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8
		");
	}

	private static function ver35()
	{
		# Inform the admin about the new cronjobs.
		Okapi::mail_admins(
			"Additional setup needed: cronjobs support",
			"Hello there, you've just updated OKAPI on your server. Thanks!\n\n".
			"We need you to do one more thing. This version of OKAPI requires\n".
			"additional crontab entry. Please add the following line to your crontab:\n\n".
			"*/5 * * * * wget -O - -q -t 1 ".Settings::get('SITE_URL')."okapi/cron5\n\n".
			"This is required for OKAPI to function properly from now on.\n\n".
			"-- \n".
			"Thanks, OKAPI developers."
		);
	}

	private static function ver36() { Db::execute("alter table okapi_cache modify column `key` varchar(64) not null"); }
	private static function ver37() { Db::execute("delete from okapi_vars where var='last_clog_update'"); }
	private static function ver38() { Db::execute("alter table okapi_clog modify column data mediumblob"); }
	private static function ver39() { Db::execute("delete from okapi_clog"); }
	private static function ver40() { Db::execute("alter table okapi_cache modify column value mediumblob"); }

	private static function ver41()
	{
		# Force changelog reset (will be produced one day back)
		Db::execute("delete from okapi_vars where var='last_clog_update'");

		# Force all cronjobs rerun
		Okapi::set_var("cron_nearest_event", 0);
		Cache::delete('cron_schedule');
	}

	private static function ver42() { Db::execute("delete from okapi_cache where length(value) = 65535"); }

	private static function ver43()
	{
		$emails = self::get_consumers_of(array('services/replicate/changelog', 'services/replicate/fulldump'), 14);
		ob_start();
		print "Hi!\n\n";
		print "We send this email to all developers who used 'replicate' module\n";
		print "in last 14 days. Thank you for testing our BETA-status module.\n\n";
		print "As you probably know, BETA status implies that we may decide to\n";
		print "modify something in a backward-incompatible way. One of such\n";
		print "modifications just happened and it may concern you.\n\n";
		print "We removed 'attrnames' from the list of synchronized fields of\n";
		print "'geocache'-type objects. Watch our blog for updates!\n\n";
		print "-- \n";
		print "OKAPI Team";
		Okapi::mail_from_okapi($emails, "A change in the 'replicate' module.", ob_get_clean());
	}

	private static function ver44() { Db::execute("alter table caches add column okapi_syncbase timestamp not null after last_modified;"); }
	private static function ver45() { Db::execute("update caches set okapi_syncbase=last_modified;"); }
	private static function ver46() { /* no longer necessary */ }

	private static function ver47()
	{
		Db::execute("
			update caches
			set okapi_syncbase=now()
			where cache_id in (
				select cache_id
				from cache_logs
				where date_created > '2012-03-11' -- the day when 'replicate' module was introduced
			);
		");
	}

	private static function ver48()
	{
		ob_start();
		print "Hi!\n\n";
		print "OKAPI just added additional field (along with an index) 'okapi_syncbase'\n";
		print "on your 'caches' table. It is required by OKAPI's 'replicate' module to\n";
		print "function properly.\n\n";
		self::print_common_db_alteration_info();
		print "-- \n";
		print "OKAPI Team";
		Okapi::mail_admins("Database modification notice: caches.okapi_syncbase", ob_get_clean());
	}

	private static function print_common_db_alteration_info()
	{
		print "-- About OKAPI's database modifications --\n\n";
		print "OKAPI takes care of its own tables (the ones with the \"okapi_\"\n";
		print "prefix), but it won't usually alter other tables in your\n";
		print "database. Still, sometimes we may change something\n";
		print "slightly (either to make OKAPI work properly OR as a part of\n";
		print "bigger \"international Opencaching unification\" ideas).\n\n";
		print "We will let you know every time OKAPI alters database structure\n";
		print "(outside of the \"okapi_\" table-scope). If you have any comments\n";
		print "on this procedure, please submit them to our issue tracker.\n\n";
	}

	private static function ver49() { Db::execute("alter table caches add key okapi_syncbase (okapi_syncbase);"); }
	private static function ver50() { /* no longer necessary */ }

	private static function ver51()
	{
		# Before revision 417, OKAPI used to make the following change:
		# - Db::execute("alter table cache_logs modify column last_modified timestamp not null;");
		# It doesn't do that anymore. Instead, it adds a separate column for itself (okapi_syncbase).
	}

	private static function ver52()
	{
		# Before revision 417, OKAPI used to make the following change (on OCDE branch):
		# - Db::execute("alter table cache_logs_archived modify column last_modified timestamp not null;");
		# It doesn't do that anymore. Instead, it adds a separate column for itself (okapi_syncbase).
	}

	private static function ver53() { Db::execute("alter table cache_logs add column okapi_syncbase timestamp not null after last_modified;"); }
	private static function ver54() { Db::execute("update cache_logs set okapi_syncbase=last_modified;"); }

	private static function ver55()
	{
		if (Settings::get('OC_BRANCH') == 'oc.pl')
		{
			# OCPL does not have cache_logs_archived table.
			return;
		}
		Db::execute("alter table cache_logs_archived add column okapi_syncbase timestamp not null after last_modified;");
	}

	private static function ver56()
	{
		if (Settings::get('OC_BRANCH') == 'oc.pl')
		{
			# OCPL does not have cache_logs_archived table.
			return;
		}
		Db::execute("update cache_logs_archived set okapi_syncbase=last_modified;");
	}

	private static function ver57()
	{
		ob_start();
		print "Hi!\n\n";
		print "OKAPI just added additional field (along with an index) 'okapi_syncbase'\n";
		print "on your 'cache_logs' AND 'cache_logs_archived' tables. It is required by\n";
		print "OKAPI's 'replicate' module to function properly.\n\n";
		self::print_common_db_alteration_info();
		print "-- \n";
		print "OKAPI Team";
		Okapi::mail_admins("Database modification notice: caches.okapi_syncbase", ob_get_clean());
	}

	private static function ver58()
	{
		#
		# Starting with revision 417, OKAPI hides all caches with statuses > 3.
		# Hence, we need such caches to be removed from external databases replicated
		# via the "replicate" module. By reseting the "okapi_syncbase" timestamp,
		# we force changelog generator cronjob to issue proper "delete" statements
		# to the changelog.
		#
		Db::execute("
			update caches
			set okapi_syncbase = now()
			where status > 3
		");
	}

	private static function ver59()
	{
		# As above.
		Db::execute("
			update
				cache_logs cl,
				caches c
			set cl.okapi_syncbase = now()
			where
				cl.cache_id = c.cache_id
				and c.status > 3
		");
	}

	private static function ver60()
	{
		# Turns out there can be only one valid TIMESTAMP field in one table!
		# Fields added ver53-ver59 don't work properly *if* ver51-ver52 had been run.
		#
		# We'll check if ver51-ver52 had been run and try to withdraw it AND
		# *rerun* missing ver53-ver59 updates.
		#
		$row = Db::select_row("show create table cache_logs");
		$stmt = $row["Create Table"];
		if (strpos($stmt, "timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'") > 0)
		{
			Db::execute("alter table cache_logs modify column last_modified datetime not null;");
			Db::execute("alter table cache_logs modify column okapi_syncbase timestamp not null;");
			Db::execute("update cache_logs set okapi_syncbase=now() where okapi_syncbase='0000-00-00 00:00:00';");
			if (Settings::get('OC_BRANCH') == 'oc.de')
			{
				Db::execute("alter table cache_logs_archived modify column last_modified datetime not null;");
				Db::execute("alter table cache_logs_archived modify column okapi_syncbase timestamp not null;");
				Db::execute("update cache_logs_archived set okapi_syncbase=now() where okapi_syncbase='0000-00-00 00:00:00';");
			}
		}
	}

	private static function ver61() { Db::execute("alter table cache_logs add key okapi_syncbase (okapi_syncbase);"); }

	private static function ver62()
	{
		if (Settings::get('OC_BRANCH') == 'oc.pl')
		{
			# OCPL does not have cache_logs_archived table.
			return;
		}
		Db::execute("alter table cache_logs_archived add key okapi_syncbase (okapi_syncbase);");
	}

	private static function ver63()
	{
		Db::execute("
			CREATE TABLE `okapi_tile_status` (
				`z` tinyint(2) NOT NULL,
				`x` mediumint(6) unsigned NOT NULL,
				`y` mediumint(6) unsigned NOT NULL,
				`status` tinyint(1) unsigned NOT NULL,
				PRIMARY KEY (`z`,`x`,`y`)
			) ENGINE=MEMORY DEFAULT CHARSET=utf8;
		");
	}

	private static function ver64()
	{
		Db::execute("
			CREATE TABLE `okapi_tile_caches` (
				`z` tinyint(2) NOT NULL,
				`x` mediumint(6) unsigned NOT NULL,
				`y` mediumint(6) unsigned NOT NULL,
				`cache_id` mediumint(6) unsigned NOT NULL,
				`z21x` int(10) unsigned NOT NULL,
				`z21y` int(10) unsigned NOT NULL,
				`status` tinyint(1) unsigned NOT NULL,
				`type` tinyint(1) unsigned NOT NULL,
				`rating` tinyint(1) unsigned DEFAULT NULL,
				`flags` tinyint(1) unsigned NOT NULL,
				PRIMARY KEY (`z`,`x`,`y`,`cache_id`)
			) ENGINE=MEMORY DEFAULT CHARSET=utf8;
		");
	}

	private static function ver65() { Db::execute("alter table okapi_tile_status engine=innodb;"); }
	private static function ver66() { Db::execute("alter table okapi_tile_caches engine=innodb;"); }

	private static function ver67()
	{
		# Remove unused locks (these might have been created in previous versions of OKAPI).

		for ($z=0; $z<=2; $z++)
			for ($x=0; $x<(1<<$z); $x++)
				for ($y=0; $y<(1<<$z); $y++)
				{
					$lockname = "tile-computation-$z-$x-$y";
					if (OkapiLock::exists($lockname))
						OkapiLock::get($lockname)->remove();
				}
	}

	private static function ver68()
	{
		# Once again, remove unused locks.

		for ($z=0; $z<=21; $z++)
		{
			foreach (array("", "-0", "-1") as $suffix)
			{
				$lockname = "tile-$z$suffix";
				if (OkapiLock::exists($lockname))
					OkapiLock::get($lockname)->remove();
			}
		}
	}

	private static function ver69()
	{
		# TileTree border margins changed. We need to recalculate all nodes
		# but the root.

		Db::execute("delete from okapi_tile_caches where z > 0");
		Db::execute("delete from okapi_tile_status where z > 0");
	}

	private static function ver70()
	{
		Db::execute("
			CREATE TABLE `okapi_cache_reads` (
				`cache_key` varchar(64) NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");
	}

	private static function ver71() { Db::execute("alter table okapi_cache add column score float(4,2) default null after `key`"); }
	private static function ver72() { Db::execute("alter table okapi_cache change column expires expires datetime after score"); }
	private static function ver73() { Db::execute("update okapi_cache set score=1, expires=date_add(now(), interval 360 day) where `key` like 'tile/%'"); }
	private static function ver74() { Db::execute("update okapi_cache set score=1, expires=date_add(now(), interval 360 day) where `key` like 'tilecaption/%'"); }
	private static function ver75() { Db::execute("alter table okapi_cache modify column score float default null"); }
	private static function ver76() { Db::execute("update okapi_cache set expires=date_add(now(), interval 100 year) where `key` like 'clog#geocache#%'"); }

	private static function ver77()
	{
		Db::execute("
			CREATE TABLE okapi_search_sets (
				id mediumint(6) unsigned not null auto_increment,
				params_hash varchar(64) not null,
				primary key (id),
				key by_hash (params_hash, id)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		");
	}

	private static function ver78()
	{
		Db::execute("
			CREATE TABLE okapi_search_results (
				set_id mediumint(6) unsigned not null,
				cache_id mediumint(6) unsigned not null,
				primary key (set_id, cache_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	}

	private static function ver79() { Db::execute("alter table okapi_search_results engine=MyISAM"); }
	private static function ver80() { Db::execute("alter table okapi_search_sets add column date_created datetime not null"); }
	private static function ver81() { Db::execute("alter table okapi_search_sets add column expires datetime not null"); }
	private static function ver82() { CronJobController::reset_job_schedule("FulldumpGeneratorJob"); }
	private static function ver83() { Db::execute("alter table okapi_stats_temp engine=InnoDB"); }
	private static function ver84() { Db::execute("truncate okapi_nonces;"); }
	private static function ver85() { Db::execute("alter table okapi_nonces drop primary key;"); }
	private static function ver86() { Db::execute("alter table okapi_nonces change column `key` nonce_hash varchar(32) character set utf8 collate utf8_bin not null;"); }
	private static function ver87() { Db::execute("alter table okapi_nonces add primary key (consumer_key, nonce_hash);"); }
}
