<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Execute search request for map.php
 *  (use caching of the same quries)
 *  TODO:cleanup
 ***************************************************************************/

	global $dblink, $dbslaveid;
	$sqlchecksum = sprintf('%u', crc32($sqlFilter));
	
	/* config */
	$opt['map']['maxcacheage'] = 3600;

	// check if query was already executed within the cache period
	$rsMapCache = sql("SELECT `result_id` FROM `map2_result` WHERE `sqlchecksum`='&1' AND DATE_ADD(`date_created`, INTERVAL '&2' SECOND)>NOW() AND `sqlquery`='&3'", $sqlchecksum, $opt['map']['maxcacheage'], $sqlFilter);
	if ($rMapCache = sql_fetch_assoc($rsMapCache))
	{
		$resultId = $rMapCache['result_id'];
		sql("UPDATE `map2_result` SET `shared_counter`=`shared_counter`+1 WHERE `result_id`='" . ($resultId+0) . "'");
	}
	else
	{
		db_connect_anyslave();

		// ensure that query is performed without errors before reserving the result_id
		sql_slave("CREATE TEMPORARY TABLE `tmpmapresult` (`cache_id` INT UNSIGNED NOT NULL, PRIMARY KEY (`cache_id`)) ENGINE=MEMORY");
		sql_slave("INSERT INTO `tmpmapresult` (`cache_id`) " . $sqlFilter);

		sql("INSERT INTO `map2_result` (`slave_id`, `sqlchecksum`, `sqlquery`, `date_created`, `date_lastqueried`) VALUES ('&1', '&2', '&3', NOW(), NOW())", $dbslaveid, $sqlchecksum, $sqlFilter);
		$resultId = mysql_insert_id($dblink);

		sql_slave("INSERT IGNORE INTO `map2_data` (`result_id`, `cache_id`) SELECT '&1', `cache_id` FROM `tmpmapresult`", $resultId);
		sql_slave("DROP TEMPORARY TABLE `tmpmapresult`");
	}

	echo $resultId;
	exit;
?>