<?php
	/***************************************************************************
															./lib/search.ovl.inc.php
																-------------------
			begin                : November 5 2005 

		For license information see doc/license.txt
	****************************************************************************/

	/****************************************************************************
		         
		Unicode Reminder メモ
                                				                                
	****************************************************************************/

	// result already cached
	if (sql_value_slave("SELECT COUNT(*) FROM `mapresult` WHERE `query_id`=" . ($options['queryid']+0), 0) > 0)
	{
		echo "READY";
		exit;
	}

	sql_slave("CREATE TEMPORARY TABLE `tmpmapresult` (`query_id` INT UNSIGNED NOT NULL DEFAULT " . ($options['queryid']+0) . ", `cache_id` INT UNSIGNED NOT NULL, PRIMARY KEY (`query_id`, `cache_id`)) ENGINE=MEMORY");
	sql_slave("INSERT INTO `tmpmapresult` (`cache_id`) " . $sqlFilter);
	sql_slave("INSERT INTO `mapresult_data` (`query_id`, `cache_id`) SELECT `query_id`, `cache_id` FROM `tmpmapresult`");
	sql_slave("INSERT INTO `mapresult` (`query_id`, `date_created`) VALUES ('&1', NOW())", $options['queryid']);

	echo "READY";
	exit;
?>