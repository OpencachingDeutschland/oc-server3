#!/usr/local/bin/php -q
<?php
 /***************************************************************************
		
		Unicode Reminder メモ

		Location of PHP binary (see first line) may need adjustment
		
 ***************************************************************************/

	$opt['rootpath'] = dirname(__FILE__) . '/../../../';
	require_once($opt['rootpath'] . 'lib/clicompatbase.inc.php');
	require_once($opt['rootpath'] . 'util/mysql_root/sql_root.inc.php');

	// retrieve DB password
	if ($db_root_password == '')
	{
		if (in_array('--flush',$argv))
		{
			echo "\nenter DB $db_root_username password:\n";
			flush();
		}
		else
			echo "enter DB $db_root_username password:";

		$fh = fopen('php://stdin', 'r');
		$db_root_password = trim(fgets($fh, 1024));
		fclose($fh);
		if ($db_root_password == '')
		  die("no DB password - aborting.\n");
	}

	// connect to database
	db_root_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}

	// include the requested maintain version file
	$dbsv = in_array('--dbsv',$argv);
	if ($dbsv)
	{
		$versionfile = 'maintain-'.$argv[$dbsv+1].'.inc.php';
		if (!file_exists(dirname(__FILE__).'/'.$versionfile))
			die($versionfile." not found\n");
		else
			require $versionfile;
		unlink($opt['rootpath'] . 'cache2/dbsv-running');
	}
	else
		require 'maintain-current.inc.php';

?>