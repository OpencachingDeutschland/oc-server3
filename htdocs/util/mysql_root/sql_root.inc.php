<?php
 /***************************************************************************
		
		Unicode Reminder メモ

		Ggf. muss die Location des php-Binaries angepasst werden.
		
		SQL-Funktionen für DB-Verwaltung
		
	***************************************************************************/

  require_once($opt['rootpath'] . 'lib/clicompatbase.inc.php');
  require_once($opt['rootpath'] . 'util/mysql_root/settings.inc.php');

function db_root_connect()
{
	global $dbusername, $dbpasswd;
	global $db_root_username, $db_root_password;

	$sOldUsername = $dbusername;
	$sOldPassword = $dbpasswd;

	$dbusername = $db_root_username;
	$dbpasswd = $db_root_password;

	db_connect();

	$dbusername = $sOldUsername;
	$dbpasswd = $sOldPassword;
}

function sql_dropTrigger($triggername)
{
	$rs = sql("SHOW TRIGGERS");
	while ($r = sql_fetch_assoc($rs))
	{
		if ($r['Trigger'] == $triggername)
		{
			sql('DROP TRIGGER `&1`', $triggername);
			return;
		}
	}
	sql_free_result($rs);
}

function sql_dropFunction($name)
{
	$rs = sql("SHOW FUNCTION STATUS LIKE '&1'", $name);
	while ($r = sql_fetch_assoc($rs))
	{
		if ($r['Name'] == $name && $r['Type'] == 'FUNCTION')
		{
			sql('DROP FUNCTION `&1`', $name);
			return;
		}
	}
	sql_free_result($rs);
}

function sql_dropProcedure($name)
{
	$rs = sql("SHOW PROCEDURE STATUS LIKE '&1'", $name);
	while ($r = sql_fetch_assoc($rs))
	{
		if ($r['Name'] == $name && $r['Type'] == 'PROCEDURE')
		{
			sql('DROP PROCEDURE `&1`', $name);
			return;
		}
	}
	sql_free_result($rs);
}
?>