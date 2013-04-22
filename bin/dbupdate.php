#!/usr/bin/php
<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	/*
	 * update of tables, indexes, triggers/storedprocs and static data in developer system
	 */
 
  $rootpath = $opt['rootpath'] = dirname(__FILE__) . '/../htdocs/';
  chdir($rootpath);
  require_once('lib2/cli.inc.php');

  echo "updating db structure  ...\n";
  require('dbsv-update.php');

  echo "importing data.sql ...\n";
  system('cat ' . $rootpath . 'doc/sql/static-data/data.sql |' .
	       ' mysql -h' . $opt['db']['servername'] . ' -u' . $opt['db']['username'] . ' --password=' . $opt['db']['password'] . ' ' . $opt['db']['placeholder']['db']);

  echo "importing triggers ...\n";
  chdir ($rootpath . 'doc/sql/stored-proc');
  system('php maintain.php');

  echo "resettings webcache ...\n";
  chdir ($rootpath . '../bin');
  system('php clear-webcache.php');

?>