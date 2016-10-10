#!/usr/local/bin/php -q
<?php
/***************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * Dieses Script berechnet andhand von Geodb-Daten die adm1..adm4-Daten
 * für die GNS-DB.
 ***************************************************************************/

$opt['rootpath'] = '../../';
require_once __DIR__ . '/../../lib2/cli.inc.php';
require_once __DIR__ . '/../../lib2/search/search.inc.php';


$rsLocations = sql("SELECT `uni`, `lat`, `lon`, `rc`, `cc1`, `adm1` FROM `gns_locations` WHERE `dsg` LIKE 'PPL%'");
while ($rLocations = sql_fetch_array($rsLocations)) {
    $minLat = geomath::getMinLat($rLocations['lon'], $rLocations['lat'], 10, 1);
    $maxLat = geomath::getMaxLat($rLocations['lon'], $rLocations['lat'], 10, 1);
    $minLon = geomath::getMinLon($rLocations['lon'], $rLocations['lat'], 10, 1);
    $maxLon = geomath::getMaxLon($rLocations['lon'], $rLocations['lat'], 10, 1);

    // den nächsgelegenen Ort in den geodb ermitteln
    $sql =
        'SELECT ' .
            geomath::getSqlDistanceFormula(
                $rLocations['lon'],
                $rLocations['lat'],
                10,
                1,
                'lon',
                'lat',
                'geodb_coordinates'
            ) . " `distance`,
            `geodb_coordinates`.`loc_id` `loc_id`
         FROM `geodb_coordinates`
         WHERE
            `lon` > '" . sql_escape($minLon) . "' AND `lon` < '" . sql_escape($maxLon) . "' AND
            `lat` > '" . sql_escape($minLat) . "' AND `lat` < '" . sql_escape($maxLat) . "'
         HAVING `distance` < 10
         ORDER BY `distance` ASC
         LIMIT 1";
    $rs = sql($sql);

    if (mysql_num_rows($rs) == 1) {
        $r = sql_fetch_array($rs);
        mysql_free_result($rs);

        $locId = $r['loc_id'];

        $admTxt1 = GeoDb::landFromLocid($locId);
        if ($admTxt1 == '0') {
            $admTxt1 = '';
        }

        // bundesland ermitteln
        $rsAdm2 = sql(
            "SELECT `full_name`, `short_form`
            FROM `gns_locations`
            WHERE `rc`='&1'
            AND `fc`='A'
            AND `dsg`='ADM1'
            AND `cc1`='&2'
            AND `adm1`='&3'
            AND `nt`='N'
            LIMIT 1",
            $rLocations['rc'],
            $rLocations['cc1'],
            $rLocations['adm1']
        );
        if (mysql_num_rows($rsAdm2) == 1) {
            $rAdm2 = sql_fetch_array($rsAdm2);
            $admTxt2 = $rAdm2['short_form'];

            if ($admTxt2 === '') {
                $admTxt2 = $rAdm2['full_name'];
            }
        } else {
            $admTxt2 = '';
        }

        $admTxt3 = GeoDb::regierungsbezirkFromLocid($locId);
        if ($admTxt3 == '0') {
            $admTxt3 = '';
        }

        $admTxt4 = GeoDb::landkreisFromLocid($locId);
        if ($admTxt4 == '0') {
            $admTxt4 = '';
        }
        sql(
            "UPDATE `gns_locations` SET `admtxt1`='&1', `admtxt2`='&2', `admtxt3`='&3', `admtxt4`='&4'
            WHERE uni='&5'",
            $admTxt1,
            $admTxt2,
            $admTxt3,
            $admTxt4,
            $rLocations['uni']
        );
    }
}

mysql_free_result($rsLocations);
