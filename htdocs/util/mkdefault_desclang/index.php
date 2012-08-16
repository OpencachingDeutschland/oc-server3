#!/usr/local/bin/php -q
<?php
/***************************************************************************
 *	For license information see doc/license.txt
 *
		Unicode Reminder メモ
 ***************************************************************************/

	$rootpath = '../../';
  require_once($rootpath . 'lib/settings.inc.php');
  require_once($rootpath . 'lib/clicompatbase.inc.php');

/* begin db connect */
	db_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}
/* end db connect */

	$i = 0;
	$rs = sql('SELECT `cache_id` FROM `caches`');
	while ($r = sql_fetch_array($rs))
	{
		setCacheDefaultDescLang($r['cache_id']);
		
		$i++;
		if (($i % 25) == 0) echo $i . ' Caches bearbeitet' . "\n";
	}
	mysql_free_result($rs);
?>