#!/usr/bin/php -q
<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  OC-Prop: Doppelte Logs loeschen
 *
 ***************************************************************************/

/*
  Bilder werden nicht gelöscht!

$opt['rootpath'] = __DIR__ . '/../../';

// chdir to proper directory (needed for cronjobs)
chdir(substr(realpath($_SERVER['PHP_SELF']), 0, strrpos(realpath($_SERVER['PHP_SELF']), '/')));

require $opt['rootpath'] . 'lib2/cli.inc.php';

$rs = sql("SELECT COUNT(*), MAX(`id`) AS `id` FROM `cache_logs` GROUP BY `cache_id`, `user_id`, `type`, `date`, `text` HAVING COUNT(*)>1");
while ($r = sql_fetch_assoc($rs)) {
    echo "Removing log entry with id " . $r['id'] . "\n";
    sql("DELETE FROM `cache_logs` WHERE `id`='&1' LIMIT 1", $r['id']);
}
sql_free_result($rs);

*/
