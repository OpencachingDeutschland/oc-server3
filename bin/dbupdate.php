#!/usr/bin/php
<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
  *  Unicode Reminder メモ
***************************************************************************/

	/*
	 * update of static data and triggers in developer system
	 *
	 * Tables must be updated manually, see htdocs/doc/sql/db-changes.txt
	 */
 
  $rootpath = $opt['rootpath'] = dirname(__FILE__) . '/../htdocs/';
  chdir($rootpath);
  require_once('lib2/cli.inc.php');

  echo "importing data.sql ...\n";
  system('cat ' . $rootpath . 'doc/sql/static-data/data.sql |' .
	       ' mysql -h' . $opt['db']['servername'] . ' -u' . $opt['db']['username'] . ' --password=' . $opt['db']['password'] . ' ' . $opt['db']['placeholder']['db']);

  echo "updating db structure  ...\n";
  require('dbsv-update.php');

  echo "importing triggers ...\n";
  chdir ($rootpath . 'doc/sql/stored-proc');
  system('php maintain.php');

  echo "resettings webcache ...\n";
  chdir ($rootpath . '../bin');
  system('php clear-webcache.php');

?>