<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  retrieves data from pictures table for log picture stats and galleries;
 *  data is mostly processed within res_logpictures.tpl
 ***************************************************************************/

class LogPics
{
    // This kind of purpose definition may be suboptimal. If functions need
    // to be reused of other purpose, change naming according to the retrieved
    // data set instead of destination view.

    const FOR_STARTPAGE_GALLERY = 1;
    const FOR_NEWPICS_GALLERY = 2;
    const FOR_USER_STAT = 3;
    const FOR_USER_GALLERY = 4;   // params: userid
    const FOR_MYHOME_GALLERY = 5;
    const FOR_CACHE_STAT = 6;     // params: cacheid
    const FOR_CACHE_GALLERY = 7;  // params: cacheid

    const MAX_PICTURES_PER_GALLERY_PAGE = 48;   // must be multiple of 6


    public static function get($purpose, $userid = 0, $cacheid = 0)
    {
        global $login;

        $fields =
          "`pics`.`uuid` AS `pic_uuid`,
           `pics`.`url` AS `pic_url`,
           `pics`.`title`,
           `pics`.`date_created`,
           `logs`.`user_id`,
           `logs`.`cache_id`,
           `logs`.`date` AS `logdate`,
           `pics`.`date_created` < LEFT(NOW(),4) AS `oldyear`,
           `logs`.`id` AS `logid`,
           `logs`.`type` AS `logtype`";

        $join_logs = "INNER JOIN `cache_logs` `logs` ON `logs`.`id`=`pics`.`object_id`";
        $join_caches = "INNER JOIN `caches` ON `caches`.`cache_id`=`logs`.`cache_id`";
        $join_cachestatus =
            "INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` AND `allow_user_view`=1";
        $join_user = "INNER JOIN `user` ON `user`.`user_id`=`logs`.`user_id`";

        $rs = false;

        switch ($purpose) {
            case self::FOR_STARTPAGE_GALLERY:
                // one pic per user and day,
                // one pic per cache and day
                // no spoilers, no bad data, no invisible or unpublished caches

                // The group-by via nested query make this whole thing sufficiently performant.
                // Direct group-bys combined with the wheres are awful slow, and no kind of
                // index seems to be good enough to speed it up.

                // Indexing the for the inner WHERE seems rather useless, as it filters out
                // only a few percent of caches. We must rely on fast data caching.

                $rs = sql_slave(
                    "SELECT
                        $fields,
                        `user`.`username`,
                        `pics`.`date_created` AS `picdate`
                     FROM
                        (SELECT *
                         FROM
                            (SELECT `uuid`, `url`, `title`, `date_created`, `object_id`
                             FROM `pictures`
                             WHERE
                                `local`=1 AND `display`=1 AND `spoiler`=0 AND `unknown_format`=0 AND `object_type`=1
                             ORDER BY `date_created` DESC
                             LIMIT 240
                            ) `piics`
                            /* 20 times reserve for filtering out user dups, cache dups and invisibles */
                         GROUP BY
                            `object_id`,
                            LEFT(`date_created`,10)
                        ) `pics`   /* max. 1 pic per cache and day */
                     $join_logs
                     $join_caches
                     $join_cachestatus
                     $join_user
                     GROUP BY
                        `user`.`user_id`,
                        LEFT(`pics`.`date_created`,10)  /* max. 1 pic per user and day */
                     ORDER BY `pics`.`date_created` DESC
                     LIMIT 6"
                );
                break;

            case self::FOR_NEWPICS_GALLERY:
                // like above, without the "one pic per cache and day" condition
                // This saves us one grouped subquery.

                $rs = sql_slave(
                    "SELECT $fields, `user`.`username`, `pics`.`date_created` AS `picdate`
                     FROM
                        (SELECT `uuid`, `url`, `title`, `date_created`, `object_id`
                         FROM `pictures`
                         WHERE
                            `local`=1 AND `display`=1 AND `spoiler`=0 AND `unknown_format`=0 AND `object_type`=1
                         ORDER BY `date_created` DESC
                         LIMIT 600
                        ) `pics`
                        /* 10 times reserve for filtering out user dups and invisibles */
                     $join_logs
                     $join_caches
                     $join_cachestatus
                     $join_user
                     GROUP BY `user`.`user_id`, LEFT(`pics`.`date_created`,10)
                     ORDER BY `date_created` DESC
                     LIMIT &1",
                    self::MAX_PICTURES_PER_GALLERY_PAGE
                );
                break;

            case self::FOR_USER_STAT:
                // Consistent with the log statistics, we count all pictures of the
                // user, also in logs for not publically visible caches.

                $result = sql_value_slave(
                    "SELECT COUNT(*)
                     FROM `pictures` `pics`
                     $join_logs
                     WHERE `pics`.`object_type`=1 AND `logs`.`user_id`='&1'",
                    0,
                    $userid
                );
                break;

            case self::FOR_USER_GALLERY:
                // all pics of one user, except spoilers and invisibles

                $rs = sql(
                    "SELECT $fields, `logs`.`date` AS `picdate`
                     FROM `pictures` `pics`
                     $join_logs
                     $join_caches
                     $join_cachestatus
                     WHERE `object_type`=1 AND `logs`.`user_id`='&1' AND NOT `spoiler`
                     ORDER BY `logs`.`order_date` DESC",
                    $userid
                );
                break;

            case self::FOR_MYHOME_GALLERY:
                // all picture of one user, with the only exception of zombie pix hanging
                // by an old log deletion (we should remove those ...)

                $rs = sql(
                    "SELECT $fields, `logs`.`date` AS `picdate`
                     FROM `pictures` AS `pics`
                     $join_logs
                     WHERE `object_type`=1 AND `logs`.`user_id`='&1'
                     ORDER BY `logs`.`order_date` DESC",
                    $login->userid
                );

                break;

            case self::FOR_CACHE_STAT:
                // all pictures for a cache except license-replacement pics;
                // need no option to exclude invisible caches, as this is only displayed
                // in listing view

                $result = sql_value(
                    "SELECT COUNT(*)
                     FROM `pictures` AS `pics`
                     $join_logs
                     $join_user
                     WHERE
                        `object_type`=1 AND `logs`.`cache_id`='&1'
                        AND NOT (`data_license` IN ('&2', '&3'))",
                    0,
                    $cacheid,
                    NEW_DATA_LICENSE_ACTIVELY_DECLINED,
                    NEW_DATA_LICENSE_PASSIVELY_DECLINED
                );
                break;

            case self::FOR_CACHE_GALLERY:
                // all picture for a cache except license-replacement pics
                // for all users except owner: also excluding invisble caches;
                // need no option to exclude invisible caches, as this is only displayed
                // in listing view

                $rs = sql(
                    "SELECT $fields, `user`.`username`, `logs`.`date` AS `picdate`
                     FROM `pictures` AS `pics`
                     $join_logs
                     $join_user
                     WHERE
                        `object_type`=1 AND `logs`.`cache_id`='&1'
                        AND NOT (`data_license` IN ('&2', '&3'))
                     ORDER BY `logs`.`order_date` DESC",
                    $cacheid,
                    NEW_DATA_LICENSE_ACTIVELY_DECLINED,
                    NEW_DATA_LICENSE_PASSIVELY_DECLINED
                );
                break;

            default:
                $result = null;
        }

        if ($rs !== false) {
            $result = sql_fetch_assoc_table($rs);
            foreach ($result as &$logpic) {
                $logpic['pic_url'] = use_current_protocol($logpic['pic_url']);
            }
        }

        return $result;
    }


    // Set all template variables needed to display a browsed log pictures page;
    // all displaying is done in res_logpictures.tpl

    public static function setPaging($purpose, $userid, $cacheid, $url)
    {
        global $tpl;

        $startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] + 0 : 0;

        $pictures = self::get($purpose, $userid, $cacheid);
        $tpl->assign('pictures', array_slice($pictures, $startat, self::MAX_PICTURES_PER_GALLERY_PAGE));

        $pager = new pager($url . "&startat={offset}");
        $pager->make_from_offset($startat, count($pictures), self::MAX_PICTURES_PER_GALLERY_PAGE);
    }

}
