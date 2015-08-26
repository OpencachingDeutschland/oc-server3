<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Helper tool for tracking down Geokrety table inconsistencies;
 *  creates backups of gk_ tables and initializes archive directory.
 ***************************************************************************/

	$opt['rootpath'] = __DIR__ . '/../../';
	require_once($opt['rootpath'] . 'lib2/cli.inc.php');

	if (count($argv) != 2 || ($argv[1] != 'init' && $argv[1] != 'discard'))
	{
		die("\n".
		    "Please verify that runcron.php is disabled in crontab;\n".
		    "then run this script with\n".
		    "\n".
				"   php save-gkstate.php init      to initialize or reset GK archive\n".
				"   php save-gkstate.php discard   to discard GK archive\n".
				"\n".
		    "Don't forget to re-enable cronjobs afterwards!\n".
		    "\n");
	}

	if ($opt['cron']['geokrety']['xml_archive'] != ($argv[1] == 'init'))
		die("Error: Geokrety XML archiving is " . ($opt['cron']['geokrety']['xml_archive'] ? 'enabled' : 'disabled') . " in settings\n");

	echo "deleting old archive if exists\n";

	sql("DROP TABLE IF EXISTS _backup_gk_item");
	sql("DROP TABLE IF EXISTS _backup_gk_item_waypoint");
	sql("DROP TABLE IF EXISTS _backup_gk_move");
	sql("DROP TABLE IF EXISTS _backup_gk_move_waypoint");
	sql("DROP TABLE IF EXISTS _backup_gk_user");

	foreach (glob($opt['rootpath'] . "cache2/geokrety/import-*.xml") as $f)
		unlink($f);

	if ($argv[1] == 'init')
	{
		echo "initializing new archive\n";

		sql("CREATE TABLE _backup_gk_item (SELECT * FROM gk_item)");
		sql("CREATE TABLE _backup_gk_item_waypoint (SELECT * FROM gk_item_waypoint)");
		sql("CREATE TABLE _backup_gk_move (SELECT * FROM gk_move)");
		sql("CREATE TABLE _backup_gk_move_waypoint (SELECT * FROM gk_move_waypoint)");
		sql("CREATE TABLE _backup_gk_user (SELECT * FROM gk_user)");
	}

?>
