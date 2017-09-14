<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

// Fix the cache_location triggers: They should not touch caches.meta_last_modified,
// as the location information is completely redundant (calculated from
// caches coordinates).

sql_dropTrigger('cacheLocationBeforeInsert');
sql(
    'CREATE TRIGGER `cacheLocationBeforeInsert` BEFORE INSERT ON `cache_location`
     FOR EACH ROW BEGIN
        SET NEW.`last_modified`=NOW();
     END;'
);

sql_dropTrigger('cacheLocationBeforeUpdate');
sql(
    'CREATE TRIGGER `cacheLocationBeforeUpdate` BEFORE UPDATE ON `cache_location`
     FOR EACH ROW BEGIN
        SET NEW.`last_modified`=NOW();
     END;'
);

sql_dropTrigger('cacheLocationAfterDelete');
