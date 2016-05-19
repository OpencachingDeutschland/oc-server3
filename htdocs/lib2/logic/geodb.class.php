<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class GeoDb
{

    public static function setAllCacheLocations()
    {
        $rs = sqll(
            "SELECT `caches`.`cache_id`
             FROM `caches`
             LEFT JOIN `cache_location`
                ON `caches`.`cache_id`=`cache_location`.`cache_id`
             WHERE
                ISNULL (`cache_location`.`cache_id`)
                OR `cache_location`.`last_modified`!=`caches`.`last_modified`"
        );
        while ($r = sql_fetch_assoc($rs)) {
            self::setCacheLocation($r['cache_id']);
        }
        sql_free_result($rs);

        sqll("
            DELETE FROM `cache_location`
            WHERE `cache_id` NOT IN (SELECT `cache_id` FROM `caches`)"
        );
    }

    private static function setCacheLocation($cache_id)
    {
        echo $cache_id . "\n";
        $rs = sqll(
            "SELECT `latitude`, `longitude`, `last_modified`
             FROM `caches`
             WHERE `cache_id`='&1'",
            $cache_id
        );
        if ($r = sql_fetch_array($rs)) {
            $nLocId = self::locidFromCoords($r['longitude'], $r['latitude']);

            if ($nLocId != 0) {
                $sAdm1 = self::landFromLocid($nLocId);
                $sAdm2 = self::regierungsbezirkFromLocid($nLocId);
                $sAdm3 = self::landkreisFromLocid($nLocId);
            } else {
                $sAdm1 = null;
                $sAdm2 = null;
                $sAdm3 = null;
            }

            if ($sAdm1 == '') {
                $sAdm1 = null;
            }
            if ($sAdm2 == '') {
                $sAdm2 = null;
            }
            if ($sAdm3 == '') {
                $sAdm3 = null;
            }

            sqll(
                "INSERT INTO `cache_location` (`cache_id`, `last_modified`, `adm1`, `adm2`, `adm3`)
                 VALUES ('&1', '&2', '&3', '&4', '&5')
                 ON DUPLICATE KEY UPDATE
                    `cache_id`='&1',
                    `last_modified`='&2',
                    `adm1`='&3',
                    `adm2`='&4',
                    `adm3`='&5'",
                $cache_id,
                $r['last_modified'],
                $sAdm1,
                $sAdm2,
                $sAdm3
            );
        }
        sql_free_result($rs);
    }

    private static function locidFromCoords($lon, $lat)
    {
        if (!is_numeric($lon)) {
            return 0;
        }
        if (!is_numeric($lat)) {
            return 0;
        }
        $lon += 0;
        $lat += 0;

        $rs = sqll(
            "SELECT
                `geodb_coordinates`.`loc_id` `loc_id`,
                (( " . $lon . " - `geodb_coordinates`.`lon` ) * ( " . $lon . " - `geodb_coordinates`.`lon` ) +
                 ( " . $lat . " - `geodb_coordinates`.`lat` ) * ( " . $lat . " - `geodb_coordinates`.`lat` )) `dist`
             FROM `geodb_coordinates`
             INNER JOIN `geodb_locations` ON `geodb_coordinates`.`loc_id`=`geodb_locations`.`loc_id`
             WHERE
                `geodb_locations`.`loc_type`=100700000
                AND `geodb_coordinates`.`lon` > " . ($lon - 0.15) . "
                AND `geodb_coordinates`.`lon` < " . ($lon + 0.15) . "
                AND `geodb_coordinates`.`lat` > " . ($lat - 0.15) . "
                AND `geodb_coordinates`.`lat` < " . ($lat + 0.15) . "
             ORDER BY `dist` ASC
             LIMIT 1"
        );
        if ($r = sql_fetch_array($rs)) {
            return $r['loc_id'];
        } else {
            return 0;
        }
    }

    public static function landFromLocid($locid)
    {
        if (!is_numeric($locid)) {
            return 0;
        }
        $locid += 0;

        $rs = sqll(
            "SELECT `ld`.`text_val` `land`
             FROM `geodb_textdata` `ct`, `geodb_textdata` `ld`, `geodb_hierarchies` `hr`
             WHERE
                `ct`.`loc_id`=`hr`.`loc_id`
                 AND `hr`.`id_lvl2`=`ld`.`loc_id`
                 AND `ct`.`text_type`= 500100000
                 AND `ld`.`text_locale`='DE'
                 AND `ld`.`text_type`= 500100000
                 AND `ct`.`loc_id`='&1'
                 AND `hr`.`id_lvl2`!=0",
            $locid
        );
        if ($r = sql_fetch_array($rs)) {
            return $r['land'];
        } else {
            return '';
        }
    }

    public static function regierungsbezirkFromLocid($locid)
    {
        if (!is_numeric($locid)) {
            return 0;
        }
        $locid += 0;

        $rs = sqll(
            "SELECT `rb`.`text_val` `regierungsbezirk`
             FROM `geodb_textdata` `ct`, `geodb_textdata` `rb`, `geodb_hierarchies` `hr`
             WHERE
                `ct`.`loc_id`=`hr`.`loc_id`
                 AND `hr`.`id_lvl4`=`rb`.`loc_id`
                 AND `ct`.`text_type`= 500100000
                 AND `rb`.`text_type`= 500100000
                 AND `ct`.`loc_id`='&1'
                 AND `hr`.`id_lvl4`!=0",
            $locid
        );
        if ($r = sql_fetch_array($rs)) {
            return $r['regierungsbezirk'];
        } else {
            return '';
        }
    }

    public static function landkreisFromLocid($locid)
    {
        if (!is_numeric($locid)) {
            return 0;
        }
        $locid += 0;

        $rs = sqll(
            "SELECT `rb`.`text_val` `regierungsbezirk`
             FROM `geodb_textdata` `ct`, `geodb_textdata` `rb`, `geodb_hierarchies` `hr`
             WHERE
                `ct`.`loc_id`=`hr`.`loc_id`
                 AND `hr`.`id_lvl5`=`rb`.`loc_id`
                 AND `ct`.`text_type`= 500100000
                 AND `rb`.`text_type`= 500100000
                 AND `ct`.`loc_id`='&1'
                 AND `hr`.`id_lvl5`!=0",
            $locid
        );
        if ($r = sql_fetch_array($rs)) {
            return $r['regierungsbezirk'];
        } else {
            return '';
        }
    }

}
