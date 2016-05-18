<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Returns a list of all caches which have been hidden after publish.
 *  This allows an easier synchronization of this information on a
 *  replicated system than the XML interface.
 ***************************************************************************/

$opt['rootpath'] = __DIR__ . '/../';
require $opt['rootpath'] . 'lib2/web.inc.php';

header('Content-type: text/plain; charset=utf-8');

$rs = sql(
    'SELECT `wp_oc`
     FROM `caches`
     JOIN `cache_status` ON `cache_status`.`id`=`caches`.`status`
     WHERE `cache_status`.`allow_user_view`= 0
     AND `caches`.`status` != 5
     ORDER BY `cache_id`'
);
$wp_ocs = sql_fetch_column($rs);
foreach ($wp_ocs as $wp_oc) {
    echo $wp_oc . "\n";
}
