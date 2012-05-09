<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Publish new geocaches that are marked for timed publish
 ***************************************************************************/

checkJob(new rating_tops());

class rating_tops
{
	var $name = 'rating_tops';
	var $interval = 86400;

	function run()
	{
		sql("DELETE FROM `rating_tops`");

		sql_temp_table('topLocationCaches');
		sql_temp_table('topRatings');
		sql_temp_table('topResult');
		
		sql("CREATE TEMPORARY TABLE &topLocationCaches (`cache_id` INT(11) PRIMARY KEY) ENGINE=MEMORY");
		sql("CREATE TEMPORARY TABLE &topRatings (`cache_id` INT(11) PRIMARY KEY, `ratings` INT(11)) ENGINE=MEMORY");
		sql("CREATE TEMPORARY TABLE &topResult (`idx` INT(11), `cache_id` INT(11) PRIMARY KEY, `ratings` INT(11), `founds` INT(11)) ENGINE=MEMORY");

		$rsAdm1 = sql('SELECT SQL_BUFFER_RESULT SQL_SMALL_RESULT DISTINCT `adm1`, `code1` FROM `cache_location` WHERE NOT ISNULL(`adm1`) ORDER BY `adm1` ASC');
		while ($rAdm1 = sql_fetch_assoc($rsAdm1))
		{
			$rsAdm3 = sql("SELECT SQL_BUFFER_RESULT SQL_SMALL_RESULT DISTINCT `adm3` FROM `cache_location` WHERE `code1`='&1' ORDER BY `adm3` ASC", $rAdm1['code1']);
			while ($rAdm3 = sql_fetch_assoc($rsAdm3))
			{
				sql("TRUNCATE TABLE &topLocationCaches");
				sql("TRUNCATE TABLE &topRatings");
				sql("TRUNCATE TABLE &topResult");

				// Alle Caches für diese Gruppe finden
				if ($rAdm3['adm3'] == null)
					sql("INSERT INTO &topLocationCaches (`cache_id`) SELECT `caches`.`cache_id` FROM `cache_location` INNER JOIN `caches` ON `caches`.`cache_id`=`cache_location`.`cache_id` LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id` WHERE IFNULL(`stat_caches`.`toprating`,0)>0 AND `cache_location`.`adm1`='&1' AND ISNULL(`cache_location`.`adm3`) AND `caches`.`status`=1", $rAdm1['adm1']);
				else
					sql("INSERT INTO &topLocationCaches (`cache_id`) SELECT `caches`.`cache_id` FROM `cache_location` INNER JOIN `caches` ON `caches`.`cache_id`=`cache_location`.`cache_id` LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id` WHERE IFNULL(`stat_caches`.`toprating`,0)>0 AND `cache_location`.`adm1`='&1' AND `cache_location`.`adm3`='&2' AND `caches`.`status`=1", $rAdm1['adm1'], $rAdm3['adm3']);

				sql("INSERT INTO &topRatings (`cache_id`, `ratings`) SELECT `cache_rating`.`cache_id`, COUNT(`cache_rating`.`cache_id`) AS `ratings` FROM `cache_rating` INNER JOIN &topLocationCaches ON `cache_rating`.`cache_id`=&topLocationCaches.`cache_id` INNER JOIN `caches` ON `cache_rating`.`cache_id`=`caches`.`cache_id` WHERE `cache_rating`.`user_id`!=`caches`.`user_id` GROUP BY `cache_rating`.`cache_id`");

				sql("INSERT INTO &topResult (`idx`, `cache_id`, `ratings`, `founds`) 
				     SELECT SQL_SMALL_RESULT (&topRatings.`ratings`+1)*(&topRatings.`ratings`+1)/(IFNULL(`stat_caches`.`found`, 0)/10+1)*100 AS `idx`, 
				            &topRatings.`cache_id`,
				            &topRatings.`ratings`, 
				            IFNULL(`stat_caches`.`found`, 0) AS founds
				       FROM &topRatings
				 INNER JOIN `caches` ON &topRatings.`cache_id`=`caches`.`cache_id`
				  LEFT JOIN `stat_caches` ON `stat_caches`.`cache_id`=`caches`.`cache_id`
				   ORDER BY `idx` DESC LIMIT 15");

				if (sql_value("SELECT COUNT(*) FROM &topResult", 0) > 10)
				{
					$min_idx = sql_value("SELECT `idx` FROM &topResult ORDER BY idx DESC LIMIT 9, 1", 0);
					sql("DELETE FROM &topResult WHERE `idx`<'&1'", $min_idx);
				}

				sql("INSERT INTO `rating_tops` (`cache_id`, `rating`)
				     SELECT SQL_BUFFER_RESULT &topResult.`cache_id`, 
						        &topResult.`idx` AS `rating`
				       FROM &topResult
				   ORDER BY `rating` DESC");
			}
			sql_free_result($rsAdm3);
		}
		sql_free_result($rsAdm1);

		sql_drop_temp_table('topLocationCaches');
		sql_drop_temp_table('topRatings');
		sql_drop_temp_table('topResult');
	}
}
?>