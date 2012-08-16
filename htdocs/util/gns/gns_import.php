#!/usr/local/bin/php -q
<?php
 /***************************************************************************
											./util/gns/gns_import.php
											-------------------------
		begin                : Mon October 31 2005

		For license information see doc/license.txt
 ****************************************************************************/

 /***************************************************************************

	Dieses Script liest Dateien von GEOnet Names Server (GNS) ein und importiert 
	diese in die Table gns_locations.
	Homepage:		http://earth-info.nga.mil/gns/html/
	Downloadseite:	http://earth-info.nga.mil/gns/html/cntry_files.html
	Aktuell eingelesene Dateien:
					http://earth-info.nga.mil/gns/html/cntyfile/au.zip
					http://earth-info.nga.mil/gns/html/cntyfile/gm.zip
					http://earth-info.nga.mil/gns/html/cntyfile/sz.zip
	***************************************************************************/

  $rootpath = '../../';
	require($rootpath . 'lib/clicompatbase.inc.php');

	/* defaults */
	$importfiles = array("gm.txt", "au.txt", "sz.txt");

	/* begin db connect */
	db_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}
	/* end db connect */

	sql("TRUNCATE TABLE gns_locations");

	foreach($importfiles as $filename)
		importGns($filename, $dblink);

	function importGns($filename, $dblink)
	{
		echo "Importing '$filename'...\n";
		$file = fopen($filename, "r");
		$cnt = 0;
		while($line = fgets($file, 4096))
		{
			if($cnt++ == 0)	// skip first line
				continue;
	
			$gns =  mb_split("\t", $line);
			
			$sql = "INSERT IGNORE INTO gns_locations SET
					rc = '" . sql_escape($gns[0]) . "',
					ufi = '" . sql_escape($gns[1]) . "',
					uni = '" . sql_escape($gns[2]) . "',
					lat = '" . sql_escape($gns[3]) . "',
					lon = '" . sql_escape($gns[4]) . "',
					dms_lat = '" . sql_escape($gns[5]) . "',
					dms_lon = '" . sql_escape($gns[6]) . "',
					utm = '" . sql_escape($gns[7]) . "',
					jog = '" . sql_escape($gns[8]) . "',
					fc = '" . sql_escape($gns[9]) . "',
					dsg = '" . sql_escape($gns[10]) . "',
					pc = '" . sql_escape($gns[11]) . "',
					cc1 = '" . sql_escape($gns[12]) . "',
					adm1 = '" . sql_escape($gns[13]) . "',
					adm2 = _utf8'" . sql_escape($gns[14]) . "',
					dim = '" . sql_escape($gns[15]) . "',
					cc2 = '" . sql_escape($gns[16]) . "',
					nt = '" . sql_escape($gns[17]) . "',
					lc = '" . sql_escape($gns[18]) . "',
					SHORT_FORM = _utf8'" . sql_escape($gns[19]) . "',
					GENERIC = _utf8'" . sql_escape($gns[20]) . "',
					SORT_NAME = _utf8'" . sql_escape($gns[21]) . "',
					FULL_NAME = _utf8'" . sql_escape($gns[22]) . "',
					FULL_NAME_ND = _utf8'" . sql_escape($gns[23]) . "',
					MOD_DATE = '" . sql_escape($gns[24]) . "'";
	
			if(!$resp = sql($sql, $dblink))
			{
				echo mysql_error($dblink); echo "\n";
			}
		}
		fclose($file);

		echo "$cnt Records imported\n";
		
		// ein paar Querschläger gleich korrigieren ...
		sql("UPDATE gns_locations SET full_name='Zeluce' WHERE uni=100528 LIMIT 1");
		sql("UPDATE gns_locations SET full_name='Zitaraves' WHERE uni=-2780984 LIMIT 1");
		sql("UPDATE gns_locations SET full_name='Zvabek' WHERE uni=105075 LIMIT 1");
	}
?>