#!/usr/local/bin/php -q
<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *    OC versions < 3.0.9 allowed entering spaces befor and after cache names.
 *  This tool reparis these names. It should no longer be neeeded from
 *  version 9 on, as newcache.php and editcache.php now trim the names.
 ***************************************************************************/

$opt['rootpath'] = __DIR__ . '/../../';
require __DIR__ . '/../../lib2/cli.inc.php';

$rs = sql("SELECT `cache_id`, `name` FROM `caches` WHERE `name`<'\"' ORDER BY `name` ASC");
while ($r = sql_fetch_array($rs)) {
    $name = trim($r['name']);
    if ($name != $r['name'] && $name != "") {
        echo "ID " . $r['cache_id'] . ": trimmed cache name to '" . $name . "'\n";
        sql("UPDATE `caches` SET `name`='&1' WHERE `cache_id`=&2", $name, $r['cache_id']);
    }
}
sql_free_result($rs);
