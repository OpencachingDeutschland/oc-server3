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

  echo "updating db structure\n";
  require('dbsv-update.php');

  echo "importing data.sql\n";
  system('cat ' . $rootpath . 'doc/sql/static-data/data.sql |' .
	       ' mysql -h' . $opt['db']['servername'] . ' -u' . $opt['db']['username'] . ' --password=' . $opt['db']['password'] . ' ' . $opt['db']['placeholder']['db']);

  echo "importing triggers\n";
  chdir ($rootpath . 'doc/sql/stored-proc');
  system('php maintain.php');

  // We do *two* tests for OKAPI presence to get some robustness agains internal OKAPI changes.
  //
  // This should be replaced by a facade function call, but current OKAPI implementation
  // does not work well when called from the command line, due to exception handling problems
  // (see http://code.google.com/p/opencaching-api/issues/detail?id=243).
  $okapi_vars = sql_table_exists('okapi_vars');
  $okapi_syncbase = sql_field_exists('caches','okapi_syncbase');
  if ($okapi_vars != $okapi_syncbase)
  {
    echo "!! unknown OKAPI configuration; either dbupdate.php needs an update or your database configuration is wrong\n";
  }
  else if ($okapi_vars)
  {
    echo "updating OKAPI database\n";
    chdir ($rootpath . '../bin');
    system('php okapi-update.php | grep -i -e mutation');
  }

  echo "resetting webcache:\n";
  chdir ($rootpath . '../bin');
  system('php clear-webcache.php');

?>