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

checkJob(new cache_npa_areas());

class cache_npa_areas
{
    public $name = 'cache_npa_areas';
    public $interval = 600;

    public function run()
    {
        $rsCache = sql('SELECT `cache_id`, `latitude`, `longitude` FROM `caches` WHERE `need_npa_recalc`=1');
        while ($rCache = sql_fetch_assoc($rsCache)) {
            sql("DELETE FROM `cache_npa_areas` WHERE `cache_id`='&1' AND `calculated`=1", $rCache['cache_id']);

            $rsLayers = sql(
                "SELECT `id`, `type_id`, AsText(`shape`) AS `geometry`
                FROM `npa_areas` WHERE `exclude`=0 AND MBRWITHIN(GeomFromText('&1'), `shape`)",
                'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')'
            );
            while ($rLayers = sql_fetch_assoc($rsLayers)) {
                if (gis::ptInLineRing(
                    $rLayers['geometry'],
                    'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')'
                )
                ) {
                    $bExclude = false;

                    // prüfen, ob in ausgesparter Fläche
                    $rsExclude = sql(
                        "SELECT `id`, AsText(`shape`) AS `geometry`
                        FROM `npa_areas`
                        WHERE `exclude` = 1
                        AND `type_id`='&1'
                        AND MBRWITHIN(GeomFromText('&2'), `shape`)",
                        $rLayers['type_id'],
                        'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')'
                    );
                    while (($rExclude = sql_fetch_assoc($rsExclude)) && ($bExclude == false)) {
                        if (gis::ptInLineRing(
                            $rExclude['geometry'],
                            'POINT(' . $rCache['longitude'] . ' ' . $rCache['latitude'] . ')'
                        )
                        ) {
                            $bExclude = true;
                        }
                    }
                    sql_free_result($rsExclude);

                    if ($bExclude == false) {
                        sql(
                            "INSERT INTO `cache_npa_areas` (`cache_id`, `npa_id`, `calculated`)
                            VALUES ('&1', '&2', 1) ON DUPLICATE KEY UPDATE `calculated`=1",
                            $rCache['cache_id'],
                            $rLayers['id']
                        );
                    }
                }
            }
            sql_free_result($rsLayers);

            sql("UPDATE `caches` SET `need_npa_recalc`=0 WHERE `cache_id`='&1'", $rCache['cache_id']);
        }
        sql_free_result($rsCache);
    }
}
