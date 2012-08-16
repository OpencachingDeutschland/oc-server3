#!/usr/local/bin/php -q
<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	$rootpath = '../../';
	require($rootpath . 'lib/clicompatbase.inc.php');

/* begin db connect */
	db_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}
/* end db connect */

	// zeichen die nicht am beginn eines Caches sein dürfen
	$evils[] = " ";
	$evils[] = "\n";
	$evils[] = "\r";

	$rs = sql("SELECT `cache_id`, `name` FROM `caches` WHERE `name`<'\"' ORDER BY `name` ASC");
	while ($r = sql_fetch_array($rs))
	{
		$name = $r['name'];
	
		$bFound = true;
		while ($bFound == true)
		{
			$bFound = false;
			
			for ($j = 0; $j < count($evils); $j++)
			{
				if (mb_substr($name, 0, 1) == $evils[$j])
				{
					$name = mb_substr($name, 1);
					$bFound = true;
				}
			}
		}

		if ($name != '')
		{
			if ($name != $r['name'])
			{
				echo "Changed name to: " . $name . "\n";
				
				sql("UPDATE `caches` SET `last_modified`=NOW(), `name`='&1' WHERE `cache_id`=&2", $name, $r['cache_id']);
			}
		}
		else
			echo 'new name would be empty, not changing' . "\n";
	}
	sql_free_result($rs);
?>