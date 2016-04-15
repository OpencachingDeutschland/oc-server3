<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require('./lib2/web.inc.php');
redirect_na();
exit;

$wp = isset($_REQUEST['wp']) ? mb_trim($_REQUEST['wp']) : '';
$rs = sql(
    "SELECT `caches`.`cache_id` `cache_id`, `caches`.`wp_oc` `wp_oc`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, IF(ISNULL(`cache_maps`.`cache_id`) OR `caches`.`last_modified`>`cache_maps`.`last_refresh`, 1, 0) AS `refresh` FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` LEFT JOIN `cache_maps` ON `caches`.`cache_id`=`cache_maps`.`cache_id` WHERE `cache_status`.`allow_user_view`=1 AND `caches`.`wp_oc`='&1'",
    $wp
);
$r = sql_fetch_assoc($rs);
sql_free_result($rs);

if ($r !== false) {
    $d1 = mb_substr($r['wp_oc'], 2, 1);
    $d2 = mb_substr($r['wp_oc'], 3, 1);
    $file = $opt['logic']['cachemaps']['dir'] . $d1 . '/' . $d2 . '/' . $r['wp_oc'] . '.jpg';

    if (($r['refresh'] == 1) || !is_file($file)) {
        $url = $opt['logic']['cachemaps']['wmsurl'];
        $url = mb_ereg_replace('{min_lon}', $r['longitude'] - $opt['logic']['cachemaps']['size']['lon'] / 2, $url);
        $url = mb_ereg_replace('{max_lon}', $r['longitude'] + $opt['logic']['cachemaps']['size']['lon'] / 2, $url);
        $url = mb_ereg_replace('{min_lat}', $r['latitude'] - $opt['logic']['cachemaps']['size']['lat'] / 2, $url);
        $url = mb_ereg_replace('{max_lat}', $r['latitude'] + $opt['logic']['cachemaps']['size']['lat'] / 2, $url);
        $url = mb_ereg_replace('{wp_oc}', $r['wp_oc'], $url);

        if (!is_dir($opt['logic']['cachemaps']['dir'] . $d1)) {
            mkdir($opt['logic']['cachemaps']['dir'] . $d1);
        }
        if (!is_dir($opt['logic']['cachemaps']['dir'] . $d1 . '/' . $d2)) {
            mkdir($opt['logic']['cachemaps']['dir'] . $d1 . '/' . $d2);
        }

        if (@copy($url, $file)) {
            $im = imagecreatefromjpeg($file);
            if (!$im) {
                redirect_na();
            } // bild ist kein lesbares jpg

            $white = imagecolorallocate($im, 255, 255, 255);
            $green = imagecolorallocate($im, 100, 255, 100);

            imageline(
                $im,
                ($opt['logic']['cachemaps']['pixel']['x'] / 2) - 10,
                ($opt['logic']['cachemaps']['pixel']['y'] / 2) - 1,
                ($opt['logic']['cachemaps']['pixel']['x'] / 2) + 10,
                ($opt['logic']['cachemaps']['pixel']['y'] / 2) - 1,
                $green
            );
            imageline(
                $im,
                ($opt['logic']['cachemaps']['pixel']['x'] / 2) - 1,
                ($opt['logic']['cachemaps']['pixel']['y'] / 2) - 10,
                ($opt['logic']['cachemaps']['pixel']['x'] / 2) - 1,
                ($opt['logic']['cachemaps']['pixel']['y'] / 2) + 10,
                $green
            );

            imageline(
                $im,
                ($opt['logic']['cachemaps']['pixel']['x'] / 2),
                ($opt['logic']['cachemaps']['pixel']['y'] / 2) - 10,
                ($opt['logic']['cachemaps']['pixel']['x'] / 2),
                ($opt['logic']['cachemaps']['pixel']['y'] / 2) + 10,
                $white
            );
            imageline(
                $im,
                ($opt['logic']['cachemaps']['pixel']['x'] / 2) - 10,
                ($opt['logic']['cachemaps']['pixel']['y'] / 2),
                ($opt['logic']['cachemaps']['pixel']['x'] / 2) + 10,
                ($opt['logic']['cachemaps']['pixel']['y'] / 2),
                $white
            );

            imagecolordeallocate($im, $white);
            imagecolordeallocate($im, $green);

            imagejpeg($im, $file);
            imagedestroy($im);

            sql(
                "INSERT INTO `cache_maps` (`cache_id`, `last_refresh`) VALUES ('&1', NOW()) ON DUPLICATE KEY UPDATE `last_refresh`=NOW()",
                $r['cache_id']
            );
        } else {
            redirect_na();
        } // download fehlgeschlagen
    }

    $tpl->redirect($opt['logic']['cachemaps']['url'] . $d1 . '/' . $d2 . '/' . $r['wp_oc'] . '.jpg');
} else {
    redirect_na();
} // wp existiert nicht

function redirect_na()
{
    global $tpl;
    $tpl->redirect('images/cachemaps/na.gif');
}
