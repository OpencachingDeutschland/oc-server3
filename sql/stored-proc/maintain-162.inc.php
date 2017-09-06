<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

// reinstall the cache location update trigger

sql_dropTrigger('cacheLocationBeforeUpdate');
sql(
    'CREATE TRIGGER `cacheLocationBeforeUpdate` BEFORE UPDATE ON `cache_location`
     FOR EACH ROW BEGIN
        SET NEW.`last_modified`=NOW();
        UPDATE `caches` SET `meta_last_modified`=NOW() WHERE `caches`.`cache_id`=NEW.`cache_id`;
     END;'
);
