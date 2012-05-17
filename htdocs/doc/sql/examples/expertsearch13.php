<?php

  // Unicode Reminder メモ

$rootpath = '../../../';
require($rootpath . 'lib/common.inc.php');
require_once($rootpath . 'lib/sqldebugger.inc.php');
sqldbg_begin();
$sql_debug = true;

/*
	Sortiert: nach Entfernung
	Caches ausblenden: Eigene, Gefundene, Inaktive, Ignorierte
	Cacheart: normaler Cache
	Land: Deutschland
	Alle Caches um N 48.0 E 9.0
*/

/* SQL-Command Nr 4 */
sql("CREATE TEMPORARY TABLE result_caches ENGINE=MEMORY SELECT (acos(cos(0.73304) * cos((90-`caches`.`latitude`) * 3.14159 / 180) + sin(0.73304) * sin((90-`caches`.`latitude`) * 3.14159 / 180) * cos((9.00000-`caches`.`longitude`) * 3.14159 / 180)) * 6370) `distance`, `caches`.`cache_id` `cache_id` FROM `caches` FORCE INDEX (`latitude`) WHERE `longitude` > 6.98618696855 AND `longitude` < 11.0138130314 AND `latitude` > 46.6501079914 AND `latitude` < 49.3498920086 AND user_id != '1' AND `caches`.`status`=1 AND `caches`.`country`='DE' AND `caches`.`type`='2' HAVING `distance` < 150");
//sql("CREATE TEMPORARY TABLE result_caches ENGINE=MEMORY SELECT (acos(cos(0.73304) * cos((90-`caches`.`latitude`) * 3.14159 / 180) + sin(0.73304) * sin((90-`caches`.`latitude`) * 3.14159 / 180) * cos((9.00000-`caches`.`longitude`) * 3.14159 / 180)) * 6370) `distance`, `caches`.`cache_id` `cache_id` FROM `caches` WHERE user_id != '1' AND `caches`.`status`=1 AND `caches`.`country`='DE' AND `caches`.`type`='2'");

/* SQL-Command Nr 5 */
sql("ALTER TABLE result_caches ADD PRIMARY KEY ( `cache_id` )");

/* SQL-Command Nr 6 */
/*
sql("SELECT COUNT(`result_caches`.`cache_id`) `count` FROM `result_caches`, `caches` 
     WHERE `caches`.`cache_id`=`result_caches`.`cache_id` AND 
           `caches`.`cache_id` NOT IN (
                    
           )");
*/

sql("CREATE TEMPORARY TABLE remove_caches (cache_id INT(11), PRIMARY KEY (cache_id)) ENGINE=MEMORY 
     SELECT result_caches.cache_id 
     FROM result_caches 
     INNER JOIN cache_logs ON result_caches.cache_id=cache_logs.cache_id
     WHERE `cache_logs`.`user_id`='1' AND `cache_logs`.`type`=1
    ");
sql("INSERT IGNORE INTO remove_caches 
     SELECT `cachelists_caches`.`cache_id` FROM `result_caches`, `cachelists_caches`, `cachelist_user`, `cachelists` 
     WHERE `result_caches`.`cache_id`=`cachelists_caches`.`cache_id` AND
           `cachelists`.`id`=`cachelists_caches`.`list_id` AND 
           `cachelists`.`id`=`cachelist_user`.`list_id` AND 
           `cachelists`.`type`='1' AND 
           `cachelist_user`.`user_id`='1'");
sql("DELETE result_caches FROM result_caches, remove_caches WHERE result_caches.cache_id=remove_caches.cache_id");

sql("SELECT COUNT(*) FROM result_caches");

/* SQL-Command Nr 7 */

sql("SELECT acos(cos(0.73304) * cos((90-`caches`.`latitude`) * 3.14159 / 180) + sin(0.73304) * sin((90-`caches`.`latitude`) * 3.14159 / 180) * cos((9.00000-`caches`.`longitude`) * 3.14159 / 180)) * 6370 `distance`, `caches`.`name` `name`, `caches`.`status` `status`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, `caches`.`difficulty` `difficulty`, `caches`.`terrain` `terrain`, `caches`.`desc_languages` `desc_languages`, `caches`.`date_created` `date_created`, `caches`.`type` `type`, `caches`.`cache_id` `cache_id`, `user`.`username` `username`, `user`.`user_id` `user_id`, `cache_type`.`icon_large` `icon_large` 
     FROM `result_caches`, `caches`, `user`, `cache_type` 
     WHERE `result_caches`.`cache_id`=`caches`.`cache_id` AND 
           `caches`.`user_id`=`user`.`user_id` AND 
           `cache_type`.`id`=`caches`.`type` ORDER BY distance ASC LIMIT 0, 20");


sqldbg_end();
?>