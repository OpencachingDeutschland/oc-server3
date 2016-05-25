<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$login->verify();

$uuid = isset($_REQUEST['uuid']) ? $_REQUEST['uuid'] : '';
$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] + 0 : 0;
$showspoiler = isset($_REQUEST['showspoiler']) ? $_REQUEST['showspoiler'] + 0 : 0;
$default_object_type = isset($_REQUEST['type']) && ($_REQUEST['type'] == 1 || $_REQUEST['type'] == 2) ? $_REQUEST['type'] + 0 : 1;

if (($opt['debug'] & DEBUG_DEVELOPER) != DEBUG_DEVELOPER) {
    $debug = 0;
}

$rs = sql(
    "SELECT `local`, `spoiler`, `url`, `thumb_last_generated`, `last_modified`, `unknown_format`, `uuid`, `thumb_url`, `object_type`, `object_id` FROM `pictures` WHERE `uuid`='&1'",
    $uuid
);
$r = sql_fetch_array($rs);
sql_free_result($rs);
if ($r) {
    if ($r['object_type'] == 1) {
        if (sql_value(
            "SELECT COUNT(*)
             FROM `cache_logs`
             INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id`
             INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
             WHERE `cache_logs`.`id`='&1'
             AND (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`='&2' OR '&3')",
            0,
            $r['object_id'],
            $login->userid,
            $login->hasAdminPriv(ADMIN_USER) ? 1 : 0
        ) == 0) {
            if ($debug == 1) {
                die('Debug: line ' . __LINE__);
            } else {
                $tpl->redirect(thumbpath('extern', 1));
            }
        }
    } elseif ($r['object_type'] == 2) {
        if (sql_value(
            "SELECT COUNT(*)
             FROM `caches`
             INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
             WHERE `caches`.`cache_id`='&1'
             AND (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`='&2' OR '&3')",
            0,
            $r['object_id'],
            $login->userid,
            $login->hasAdminPriv(ADMIN_USER) ? 1 : 0
        ) == 0) {
            if ($debug == 1) {
                die('Debug: line ' . __LINE__);
            } else {
                $tpl->redirect(thumbpath('extern', 2));
            }
        }
    } else {
        if ($debug == 1) {
            die('Debug: line ' . __LINE__);
        } else {
            $tpl->redirect(thumbpath('intern', $default_object_type));
        }
    }

    if ($r['local'] == 0) {
        if ($debug == 1) {
            die('Debug: line ' . __LINE__);
        } else {
            $tpl->redirect(thumbpath('extern', $r['object_type']));
        }
    }

    if (($r['spoiler'] == 1) && ($showspoiler != 1)) {
        if ($debug == 1) {
            die('Debug: line ' . __LINE__);
        } else {
            $tpl->redirect(thumbpath('spoiler', $r['object_type']));
        }
    }

    $imgurl = $r['url'];
    $urlparts = mb_split('/', $imgurl);

    if (!file_exists($opt['logic']['pictures']['dir'] . '/' . $urlparts[count($urlparts) - 1])) {
        if ($debug == 1) {
            die('Debug: line ' . __LINE__);
        } else {
            $tpl->redirect(thumbpath('intern', $r['object_type']));
        }
    }

    // generate new thumb?
    $bGenerate = false;
    if (strtotime($r['thumb_last_generated']) < strtotime($r['last_modified'])) {
        $bGenerate = true;
    }

    if (!file_exists(
        $opt['logic']['pictures']['thumb_dir'] . '/' . mb_substr(
            $urlparts[count($urlparts) - 1],
            0,
            1
        ) . '/' . mb_substr($urlparts[count($urlparts) - 1], 1, 1) . '/' . $urlparts[count($urlparts) - 1]
    )
    ) {
        $bGenerate = true;
    }

    if ($bGenerate) {
        if ($r['unknown_format'] == 1) {
            if ($debug == 1) {
                die('Debug: line ' . __LINE__);
            } else {
                $tpl->redirect(thumbpath('format', $r['object_type']));
            }
        }

        // ok, let's see if the file format is supported
        $filename = $urlparts[count($urlparts) - 1];
        $filenameparts = mb_split('\\.', $filename);
        $extension = mb_strtolower($filenameparts[count($filenameparts) - 1]);

        if (mb_strpos(';' . $opt['logic']['pictures']['extensions'] . ';', ';' . $extension . ';') === false) {
            sql("UPDATE `pictures` SET `unknown_format`=1 WHERE `uuid`='&1'", $r['uuid']);

            if ($debug == 1) {
                die('Debug: line ' . __LINE__);
            } else {
                $tpl->redirect(thumbpath('format', $r['object_type']));
            }
        }

        if ($extension == 'jpeg') {
            $extension = 'jpg';
        }
        switch ($extension) {
            case 'jpg':
                $im = imagecreatefromjpeg($opt['logic']['pictures']['dir'] . '/' . $filename);
                break;

            case 'gif':
                $im = imagecreatefromgif($opt['logic']['pictures']['dir'] . '/' . $filename);
                break;

            case 'png':
                $im = imagecreatefrompng($opt['logic']['pictures']['dir'] . '/' . $filename);
                break;

            case 'bmp':
                require $opt['rootpath'] . 'lib2/imagebmp.inc.php';
                $im = imagecreatefrombmp($opt['logic']['pictures']['dir'] . '/' . $filename);
                break;
        }

        if ($im == '') {
            sql("UPDATE `pictures` SET `unknown_format`=1 WHERE `uuid`='&1'", $r['uuid']);

            if ($debug == 1) {
                die('Debug: line ' . __LINE__);
            } else {
                $tpl->redirect(thumbpath('format', $r['object_type']));
            }
        }

        $imheight = imagesy($im);
        $imwidth = imagesx($im);

        if ($r['object_type'] == 1) {
            // Log picture gallery in thumbs.php relies on this format!
            // It is large enough to have the pics look nice ...
            $thumb_max_height = 105;
            $thumb_max_width = 105;
        } else {
            $thumb_max_height = $opt['logic']['pictures']['thumb_max_height'];
            $thumb_max_width = $opt['logic']['pictures']['thumb_max_width'];
        }

        if (($imheight > $thumb_max_height) || ($imwidth > $thumb_max_width)) {
            if ($imheight > $imwidth) {
                $thumbheight = $thumb_max_height;
                $thumbwidth = $imwidth * ($thumbheight / $imheight);
            } else {
                $thumbwidth = $thumb_max_width;
                $thumbheight = $imheight * ($thumbwidth / $imwidth);
            }
        } else {
            $thumbwidth = $imwidth;
            $thumbheight = $imheight;
        }

        // Create and save thumb
        $thumbimage = imagecreatetruecolor($thumbwidth, $thumbheight);
        imagecopyresampled($thumbimage, $im, 0, 0, 0, 0, $thumbwidth, $thumbheight, $imwidth, $imheight);

        // Create directory
        if (!file_exists($opt['logic']['pictures']['thumb_dir'] . '/' . mb_substr($filename, 0, 1))) {
            mkdir($opt['logic']['pictures']['thumb_dir'] . '/' . mb_substr($filename, 0, 1));
        }
        if (!file_exists(
            $opt['logic']['pictures']['thumb_dir'] . '/' . mb_substr($filename, 0, 1) . '/' . mb_substr($filename, 1, 1)
        )
        ) {
            mkdir(
                $opt['logic']['pictures']['thumb_dir'] . '/' . mb_substr($filename, 0, 1) . '/' . mb_substr(
                    $filename,
                    1,
                    1
                )
            );
        }

        $savedir =
            $opt['logic']['pictures']['thumb_dir'] . '/'
            . mb_substr($filename, 0, 1) . '/'
            . mb_substr($filename, 1, 1);

        switch ($extension) {
            case 'jpg':
                imagejpeg($thumbimage, $savedir . '/' . $filename);
                break;

            case 'gif':
                imagegif($thumbimage, $savedir . '/' . $filename);
                break;

            case 'png':
                imagepng($thumbimage, $savedir . '/' . $filename);
                break;

            case 'bmp':
                imagebmp($thumbimage, $savedir . '/' . $filename);
                break;
        }

        sql(
            "UPDATE `pictures` SET `thumb_last_generated`=NOW(), `thumb_url`='&1' WHERE `uuid`='&2'",
            $opt['logic']['pictures']['thumb_url'] . '/' . mb_substr($filename, 0, 1) . '/' . mb_substr(
                $filename,
                1,
                1
            ) . '/' . $filename,
            $r['uuid']
        );

        if ($debug == 1) {
            die($opt['logic']['pictures']['thumb_url'] . '/' . $filename);
        } else {
            $tpl->redirect(
                use_current_protocol(
                    $opt['logic']['pictures']['thumb_url'] . '/' . mb_substr($filename, 0, 1) . '/' . mb_substr(
                        $filename,
                        1,
                        1
                    ) . '/' . $filename
                )
            );
        }
    } else {
        if ($debug == 1) {
            die($r['thumb_url']);
        } else {
            $tpl->redirect(use_current_protocol($r['thumb_url']));
        }
    }
} else {
    if ($debug == 1) {
        die('Debug: line ' . __LINE__);
    } else {
        $tpl->redirect(thumbpath('404', $default_object_type));
    }
}


function thumbpath($name, $object_type)
{
    global $opt, $default_object_type;

    if (!in_array($name, ['404', 'intern', 'extern', 'spoiler', 'unknown'])
        || ($object_type != 1 && $object_type != 2)
    ) {
        if ($debug == 1) {
            die('Debug: line ' . __LINE__);
        } else {
            $name = 'intern';
            $object_type = $default_object_type;
        }
    }

    $imgdir = 'resource2/' . $opt['template']['style'] . '/images/thumb/';
    $filename = 'thumb' . $name . '_' . $object_type . '.gif';
    $thumbpath = $imgdir . strtolower($opt['template']['locale']) . '/' . $filename;
    if (!file_exists($thumbpath)) {
        $thumbpath = $imgdir . 'en/' . $filename;
    }

    return $thumbpath;
}
