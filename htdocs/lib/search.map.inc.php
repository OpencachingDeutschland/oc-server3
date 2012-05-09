<?php
	/***************************************************************************
															./lib/search.ovl.inc.php
																-------------------
			begin                : November 5 2005 
			copyright            : (C) 2005 The OpenCaching Group
			forum contact at     : http://www.opencaching.com/phpBB2

		***************************************************************************/

	/***************************************************************************
		*                                         				                                
		*   This program is free software; you can redistribute it and/or modify  	
		*   it under the terms of the GNU General Public License as published by  
		*   the Free Software Foundation; either version 2 of the License, or	    	
		*   (at your option) any later version.
		*
		***************************************************************************/

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