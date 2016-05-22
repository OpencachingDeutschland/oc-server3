<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'imagebrowser';
$tpl->popup = true;

$login->verify();

$cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] + 0 : 0;

$rs = sql(
    "SELECT `caches`.`name`
    FROM `caches`
    INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
    WHERE `caches`.`user_id`='&1'
    AND `cache_id`='&2'",
    $login->userid,
    $cacheid
);
$rCache = sql_fetch_assoc($rs);
sql_free_result($rs);

if ($rCache === false) {
    $tpl->error(ERROR_NO_ACCESS);
}

$tpl->assign('cachename', $rCache['name']);

$rsPictures = sql(
    'SELECT `uuid`, `url`, `title`
    FROM `pictures`
    WHERE `object_id`=&1
    AND `object_type`=2
    ORDER BY `seq`',
    $cacheid
);
$pictures = array();
while ($rPicture = sql_fetch_assoc($rsPictures)) {
    // TinyMCE will create a relative link only of the protocol of the image URL matches.
    // This also avoides MSIE warnings in https mode.
    $rPicture['url'] = use_current_protocol($rPicture['url']);
    $pictures[] = $rPicture;
}
$tpl->assign('pictures', $pictures);
sql_free_result($rsPictures);

$tpl->assign('thumbwidth', $opt['logic']['pictures']['thumb_max_width']);

$tpl->display();
