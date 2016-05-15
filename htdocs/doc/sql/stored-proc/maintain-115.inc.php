<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Remove obsolete functions which were acidentally added to the master
 *  branch maintain.php (but not made it to stable branch).
 ***************************************************************************/

sql_dropFunction('distance');
sql_dropFunction('projLon');
sql_dropFunction('projLat');
sql_dropFunction('angle');
sql_dropFunction('ptonline');

// Update trigger version function.
// Keep this at the end of this file.
sql_dropFunction('dbsvTriggerVersion');
sql(
    "CREATE FUNCTION `dbsvTriggerVersion` () RETURNS INT
     RETURN '115'"
);
