#!/usr/bin/php
<?php
	/*
	 * update of static data and triggers in developer system
	 *
	 * Tables must be updated manually, see htdocs/doc/sql/db-changes.txt
	 */ 
  
	require('htdocs/lib/settings.inc.php');
	
  echo "importing data.sql ...\n";
  chdir ('htdocs/doc/sql');
	system('cat static-data/data.sql | mysql -u' . $dbusername . ' --password=' . $dbpasswd . ' ' . $dbname);

  echo "importing triggers ...\n";
  chdir('stored-proc');
  system('php maintain.php');

  echo "resettings webcache ...\n";
  chdir('../../../../bin');
  system('php clear-webcache.php');
  
?>
