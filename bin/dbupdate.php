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
  
  $rootpath = dirname(__FILE__) . '/../';
  require($rootpath . 'htdocs/lib/settings.inc.php');
	
  echo "importing data.sql ...\n";
  system('cat ' . $rootpath . 'htdocs/doc/sql/static-data/data.sql | mysql -u' . $dbusername . ' --password=' . $dbpasswd . ' ' . $dbname);

  echo "importing triggers ...\n";
  chdir ($rootpath . 'htdocs/doc/sql/stored-proc');
  system('php maintain.php');

  echo "resettings webcache ...\n";
  chdir ($rootpath . "bin");
  system('php clear-webcache.php');
  
?>