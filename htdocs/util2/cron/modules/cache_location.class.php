<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Cleanup the table sys_temptables from entries of dead threads
 *
 *                         run it once a day
 *
 ***************************************************************************/

checkJob(new cache_location());

class cache_location
{
    public $name = 'cache_location';
    public $interval = 0;

    public function run()
    {
        global $opt;

        $rsCache = sql(
            'SELECT `caches`.`cache_id`, `caches`.`latitude`, `caches`.`longitude`
            FROM `caches`
            LEFT JOIN `cache_location`
                ON `caches`.`cache_id`=`cache_location`.`cache_id`
            WHERE ISNULL(`cache_location`.`cache_id`)
            UNION
            SELECT `caches`.`cache_id`, `caches`.`latitude`, `caches`.`longitude`
            FROM `caches`
            INNER JOIN `cache_location`
                ON `caches`.`cache_id`=`cache_location`.`cache_id`
            WHERE `caches`.`last_modified`>`cache_location`.`last_modified`'
        );
        while ($rCache = sql_fetch_assoc($rsCache)) {
            $sCode = '';

            $rsLayers = sql(
                "SELECT `level`, `code`, AsText(`shape`) AS `geometry`
                FROM `nuts_layer`
                WHERE MBRWITHIN(GeomFromText('&1'), `shape`) ORDER BY `level` DESC",
                'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')'
            );
            while ($rLayers = sql_fetch_assoc($rsLayers)) {
                if (gis::ptInLineRing(
                    $rLayers['geometry'],
                    'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')'
                )
                ) {
                    $sCode = $rLayers['code'];
                    break;
                }
            }
            sql_free_result($rsLayers);

            if ($sCode != '') {
                $adm1 = null;
                $code1 = null;
                $adm2 = null;
                $code2 = null;
                $adm3 = null;
                $code3 = null;
                $adm4 = null;
                $code4 = null;

                if (mb_strlen($sCode) > 5) {
                    $sCode = mb_substr($sCode, 0, 5);
                }

                if (mb_strlen($sCode) == 5) {
                    $code4 = $sCode;
                    $adm4 = sql_value("SELECT `name` FROM `nuts_codes` WHERE `code`='&1'", null, $sCode);
                    $sCode = mb_substr($sCode, 0, 4);
                }

                if (mb_strlen($sCode) == 4) {
                    $code3 = $sCode;
                    $adm3 = sql_value("SELECT `name` FROM `nuts_codes` WHERE `code`='&1'", null, $sCode);
                    $sCode = mb_substr($sCode, 0, 3);
                }

                if (mb_strlen($sCode) == 3) {
                    $code2 = $sCode;
                    $adm2 = sql_value("SELECT `name` FROM `nuts_codes` WHERE `code`='&1'", null, $sCode);
                    $sCode = mb_substr($sCode, 0, 2);
                }

                if (mb_strlen($sCode) == 2) {
                    $code1 = $sCode;

                    // try to get localised name first
                    $adm1 = sql_value(
                        "SELECT IFNULL(`sys_trans_text`.`text`, `countries`.`name`)
                        FROM `countries`
                        LEFT JOIN `sys_trans`
                            ON `countries`.`trans_id`=`sys_trans`.`id`
                            AND `countries`.`name`=`sys_trans`.`text`
                        LEFT JOIN `sys_trans_text`
                            ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                            AND `sys_trans_text`.`lang`='&2'
                        WHERE `countries`.`short`='&1'",
                        null,
                        $sCode,
                        $opt['template']['default']['locale']
                    );

                    if ($adm1 == null) {
                        $adm1 = sql_value("SELECT `name` FROM `nuts_codes` WHERE `code`='&1'", null, $sCode);
                    }
                }

                sql(
                    "INSERT INTO `cache_location` (`cache_id`, `adm1`, `adm2`, `adm3`, `adm4`, `code1`, `code2`, `code3`, `code4`)
                    VALUES ('&1', '&2', '&3', '&4', '&5', '&6', '&7', '&8', '&9')
                    ON DUPLICATE KEY UPDATE `adm1`='&2', `adm2`='&3', `adm3`='&4', `adm4`='&5', `code1`='&6', `code2`='&7', `code3`='&8', `code4`='&9'",
                    $rCache['cache_id'],
                    $adm1,
                    $adm2,
                    $adm3,
                    $adm4,
                    $code1,
                    $code2,
                    $code3,
                    $code4
                );
            } else {
                $sCountry = sql_value(
                    "SELECT IFNULL(`sys_trans_text`.`text`, `countries`.`name`)
                    FROM `caches`
                    INNER JOIN `countries`
                        ON `caches`.`country`=`countries`.`short`
                    LEFT JOIN `sys_trans`
                        ON `countries`.`trans_id`=`sys_trans`.`id`
                        AND `countries`.`name`=`sys_trans`.`text`
                    LEFT JOIN `sys_trans_text`
                        ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
                        AND `sys_trans_text`.`lang`='&2'
                    WHERE `caches`.`cache_id`='&1'",
                    null,
                    $rCache['cache_id'],
                    $opt['template']['default']['locale']
                );
                $sCode1 = sql_value(
                    "SELECT `caches`.`country` FROM `caches` WHERE `caches`.`cache_id`='&1'",
                    null,
                    $rCache['cache_id']
                );
                sql(
                    "INSERT INTO `cache_location` (`cache_id`, `adm1`, `code1`)
                    VALUES ('&1', '&2', '&3')
                    ON DUPLICATE KEY UPDATE `adm1`='&2', `adm2`=NULL, `adm3`=NULL, `adm4`=NULL, `code1`='&3', `code2`=NULL, `code3`=NULL, `code4`=NULL",
                    $rCache['cache_id'],
                    $sCountry,
                    $sCode1
                );
            }
        }
        sql_free_result($rsCache);
    }
}
