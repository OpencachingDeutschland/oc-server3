<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

// list of new images
// optional script to be released locally into htdocs/api/stat

$opt['rootpath'] = __DIR__ . '/../../htdocs/';
require $opt['rootpath'] . 'lib2/web.inc.php';

if (!isset($_REQUEST['since'])) {
    exit;
}
$since = $_REQUEST['since'];

$rs = sql(
    "SELECT url FROM pictures
            WHERE date_created >= '2013' AND date_created >= '&1'
         ORDER BY date_created",
    $since
);
while ($pic = sql_fetch_assoc($rs)) {
    echo $pic['url'] . "\n";
}
mysql_free_result($rs);
