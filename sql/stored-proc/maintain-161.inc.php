<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

// rebuild cache country stats
sql_dropProcedure('sp_rebuild_country_stat');
sql(
    "CREATE PROCEDURE sp_rebuild_country_stat (OUT nModified INT)
     BEGIN
        UPDATE `stat_cache_countries` SET `active_caches` =
          (SELECT COUNT(*) FROM `caches` WHERE `caches`.`country`=`stat_cache_countries`.`country` AND `status`=1);
        SET nModified = ROW_COUNT();

        INSERT IGNORE INTO `stat_cache_countries`
          (SELECT `country`, COUNT(*)  FROM `caches` WHERE `status`=1 AND `country`<>'  ' GROUP BY `country`);
        SET nModified = nModified + ROW_COUNT();

        DELETE FROM `stat_cache_countries` WHERE `country` NOT IN
          (SELECT DISTINCT `country` FROM `caches` WHERE `status`=1);
        SET nModified = nModified + ROW_COUNT();
     END;"
);
