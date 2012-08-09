#!/usr/bin/php
<?php
	/*
	 * update of static data and triggers in developer system
	 *
	 * Tables must be updated manually, see htdocs/doc/sql/db-changes.txt
	 */ 
  
	require('htdocs/util/mysql_root/settings.inc.php');
	
  echo "importing data.sql ...\n";
  chdir ('htdocs/doc/sql');
	system('cat static-data/data.sql | mysql -uroot --password=' . $db_root_password . ' opencaching');

  echo "importing triggers ...\n";
  chdir('stored-proc');
  system('php maintain.php');

  echo "resettings webcache ...\n";
  chdir('../../../../bin');
  system('php clear-webcache.php');
  
?>
