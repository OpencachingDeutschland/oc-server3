<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  In lib2 this file is always included via common.inc.php.
 *  Also used in lib1.
 ***************************************************************************/

class geomath
{
    public static function calcBearing($lat1, $lon1, $lat2, $lon2)
    {
        // Input sind Breite/Laenge in Altgrad
        // Der Fall lat/lon1 == lat/lon2 sollte vorher abgefangen werden,
        // zB. ueber die Abfrage der Distanz, dass Bearing nur bei Distanz > 5m
        // geholt wird, sonst = false gesetzt wird...
        if ($lat1 == $lat2 && $lon1 == $lon2) {
            return false;
        } else {
            $pi = 3.141592653589793238462643383279502884197;

            if ($lat1 == $lat2) {
                $lat1 += 0.0000166;
            }
            if ($lon1 == $lon2) {
                $lon1 += 0.0000166;
            }

            $rad_lat1 = $lat1 / 180.0 * $pi;
            $rad_lon1 = $lon1 / 180.0 * $pi;
            $rad_lat2 = $lat2 / 180.0 * $pi;
            $rad_lon2 = $lon2 / 180.0 * $pi;

            $delta_lon = $rad_lon2 - $rad_lon1;
            $bearing = atan2(
                sin($delta_lon) * cos($rad_lat2),
                cos($rad_lat1) * sin($rad_lat2) - sin($rad_lat1) * cos($rad_lat2) * cos($delta_lon)
            );
            $bearing = 180.0 * $bearing / $pi;

            // Output Richtung von lat/lon1 nach lat/lon2 in Altgrad von -180 bis +180
            // wenn man Output von 0 bis 360 haben moechte, kann man dies machen:
            if ($bearing < 0.0) {
                $bearing = $bearing + 360.0;
            }

            return $bearing;
        }
    }

    public static function Bearing2Text($parBearing, $parShortText, $language)
    {
        global $opt, $translate;

        if ($parShortText == 0) {
            if ($parBearing === false) {
                return 'N/A';
            } elseif (($parBearing < 11.25) || ($parBearing > 348.75)) {
                $bearing = 'north';
            } elseif ($parBearing < 33.75) {
                $bearing = 'north-northeast';
            } elseif ($parBearing < 56.25) {
                $bearing = 'northeast';
            } elseif ($parBearing < 78.75) {
                $bearing = 'east-northeast';
            } elseif ($parBearing < 101.25) {
                $bearing = 'east';
            } elseif ($parBearing < 123.75) {
                $bearing = 'east-southeast';
            } elseif ($parBearing < 146.25) {
                $bearing = 'southeast';
            } elseif ($parBearing < 168.75) {
                $bearing = 'south-southeast';
            } elseif ($parBearing < 191.25) {
                $bearing = 'south';
            } elseif ($parBearing < 213.75) {
                $bearing = 'south-southwest';
            } elseif ($parBearing < 236.25) {
                $bearing = 'southwest';
            } elseif ($parBearing < 258.75) {
                $bearing = 'west-southwest';
            } elseif ($parBearing < 281.25) {
                $bearing = 'west';
            } elseif ($parBearing < 303.75) {
                $bearing = 'west-northwest';
            } elseif ($parBearing < 326.25) {
                $bearing = 'northwest';
            } elseif ($parBearing <= 348.75) {
                $bearing = 'north-northwest';
            } else {
                return 'N/A';
            }

            return $translate->t($bearing, '', '', 0, '', 1, $language);
        } else {
            if ($parBearing === false) {
                return 'N/A';
            } elseif (($parBearing < 11.25) || ($parBearing > 348.75)) {
                $bearing = 'N';
            } elseif ($parBearing < 33.75) {
                $bearing = 'NNE';
            } elseif ($parBearing < 56.25) {
                $bearing = 'NE';
            } elseif ($parBearing < 78.75) {
                $bearing = 'ENE';
            } elseif ($parBearing < 101.25) {
                $bearing = 'E';
            } elseif ($parBearing < 123.75) {
                $bearing = 'ESE';
            } elseif ($parBearing < 146.25) {
                $bearing = 'SE';
            } elseif ($parBearing < 168.75) {
                $bearing = 'SSE';
            } elseif ($parBearing < 191.25) {
                $bearing = 'S';
            } elseif ($parBearing < 213.75) {
                $bearing = 'SSW';
            } elseif ($parBearing < 236.25) {
                $bearing = 'SW';
            } elseif ($parBearing < 258.75) {
                $bearing = 'WSW';
            } elseif ($parBearing < 281.25) {
                $bearing = 'W';
            } elseif ($parBearing < 303.75) {
                $bearing = 'WNW';
            } elseif ($parBearing < 326.25) {
                $bearing = 'NW';
            } elseif ($parBearing <= 348.75) {
                $bearing = 'NNW';
            } else {
                return 'N/A';
            }
            $tb = '';
            $max = strlen($bearing);
            for ($i = 0; $i < $max; ++ $i) {
                $tb .= $translate->t($bearing[$i], '', '', 0, '', 1, $language);
            }
            // Translation of N S W E does not work, for whatever reason.
            // But this is currently not in use.
            return $tb;
        }
    }

    public static function calcDistance($latFrom, $lonFrom, $latTo, $lonTo, $distanceMultiplier = 1)
    {
        return acos(
            cos((90 - $latFrom) * 3.14159 / 180) * cos((90 - $latTo) * 3.14159 / 180) + sin(
                (90 - $latFrom) * 3.14159 / 180
            ) * sin((90 - $latTo) * 3.14159 / 180) * cos(($lonFrom - $lonTo) * 3.14159 / 180)
        ) * 6370 * $distanceMultiplier;
    }

    public static function getSqlDistanceFormula(
        $lonFrom,
        $latFrom,
        $maxDistance,
        $distanceMultiplier = 1,
        $lonField = 'longitude',
        $latField = 'latitude',
        $tableName = 'caches'
    ) {
        $lonFrom += 0;
        $latFrom += 0;
        $maxDistance += 0;
        $distanceMultiplier += 0;

        if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $lonField)) {
            die('Fatal Error: invalid lonField');
        }
        if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $latField)) {
            die('Fatal Error: invalid latField');
        }
        if (!mb_ereg_match('^[a-zA-Z][a-zA-Z0-9_]{0,59}$', $tableName)) {
            die('Fatal Error: invalid tableName');
        }

        $b1_rad = (90 - $latFrom) * 3.14159 / 180;
        $l1_deg = $lonFrom;

        $lonField = '`' . sql_escape_backtick($tableName) . '`.`' . sql_escape_backtick($lonField) . '`';
        $latField = '`' . sql_escape_backtick($tableName) . '`.`' . sql_escape_backtick($latField) . '`';

        $r = 6370 * $distanceMultiplier;

        $retval = 'acos(cos(' . $b1_rad . ') * cos((90-' . $latField . ') * 3.14159 / 180) + sin(' . $b1_rad . ') * sin((90-' . $latField . ') * 3.14159 / 180) * cos((' . $l1_deg . '-' . $lonField . ') * 3.14159 / 180)) * ' . $r;

        return $retval;
    }

    public static function getMaxLat($lon, $lat, $distance, $distanceMultiplier = 1)
    {
        return $lat + $distance / (111.12 * $distanceMultiplier);
    }

    public static function getMinLat($lon, $lat, $distance, $distanceMultiplier = 1)
    {
        return $lat - $distance / (111.12 * $distanceMultiplier);
    }

    public static function getMaxLon($lon, $lat, $distance, $distanceMultiplier = 1)
    {
        return $lon + $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180)) * 6378 * $distanceMultiplier * 3.14159);
    }

    public static function getMinLon($lon, $lat, $distance, $distanceMultiplier = 1)
    {
        return $lon - $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180)) * 6378 * $distanceMultiplier * 3.14159);
    }
}
