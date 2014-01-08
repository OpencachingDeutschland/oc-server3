<?php

/****************************************************************************
                               _    _                _
 ___ _ __  ___ _ _  __ __ _ __| |_ (_)_ _  __ _   __| |___
/ _ \ '_ \/ -_) ' \/ _/ _` / _| ' \| | ' \/ _` |_/ _` / -_)
\___/ .__/\___|_||_\__\__,_\__|_||_|_|_||_\__, (_)__,_\___|
    |_|                                   |___/

For license information see doc/license.txt   ---   Unicode Reminder メモ

****************************************************************************/

namespace OpenCachingDE\Conversions;

class Coordinates
{
    // decimal longitude to string E/W hhh°mm.mmm
    public static function lonToDegreeStr($lon)
    {
        if ($lon < 0) {
            $retval = 'W ';
            $lon = -$lon;
        } else {
            $retval = 'E ';
        }

        $retval = $retval . sprintf("%03d", floor($lon)) . '° ';
        $lon = $lon - floor($lon);
        $retval = $retval . sprintf("%06.3f", round($lon * 60, 3)) . '\'';

        return $retval;
    }

    // decimal latitude to string N/S hh°mm.mmm
    public static function latToDegreeStr($lat)
    {
        if ($lat < 0) {
            $retval = 'S ';
            $lat = -$lat;
        } else {
            $retval = 'N ';
        }

        $retval = $retval . sprintf("%02d", floor($lat)) . '° ';
        $lat = $lat - floor($lat);
        $retval = $retval . sprintf("%06.3f", round($lat * 60, 3)) . '\'';

        return $retval;
    }

    // decimal longitude to array(direction, h, min)
    public static function lonToArray($lon)
    {
        if ($lon < 0) {
            $dir = 'W';
            $lon = -$lon;
        } else {
            $dir = 'E';
        }

        $h = sprintf("%02d", floor($lon));
        $lon = $lon - floor($lon);
        $min = sprintf("%06.3f", round($lon * 60, 3));

        return array($dir, $h, $min);
    }

    // decimal longitude to array(direction, h_int, min_int, sec_int, min_float)
    public static function lonToArray2($lon)
    {
        list($dir, $lon_h_int, $lon_min_float) = self::lonToArray($lon);

        $lon_min_int = sprintf("%02d", floor($lon_min_float));

        $lon_min_frac = $lon_min_float - $lon_min_int;
        $lon_sec_int = sprintf("%02d", round($lon_min_frac * 60));

        return array($dir, $lon_h_int, $lon_min_int, $lon_sec_int, $lon_min_float);
    }

    // decimal latitude to array(direction, h, min)
    public static function latToArray($lat)
    {
        if ($lat < 0) {
            $dir = 'S';
            $lat = -$lat;
        } else {
            $dir = 'N';
        }

        $h = sprintf("%02d", floor($lat));
        $lat = $lat - floor($lat);
        $min = sprintf("%06.3f", round($lat * 60, 3));

        return array($dir, $h, $min);
    }

    // decimal latitude to array(direction, h_int, min_int, sec_int, min_float)
    public static function latToArray2($lat)
    {
        list($dir, $lat_h_int, $lat_min_float) = self::latToArray($lat);

        $lat_min_int = sprintf("%02d", floor($lat_min_float));

        $lat_min_frac = $lat_min_float - $lat_min_int;
        $lat_sec_int = sprintf("%02d", round($lat_min_frac * 60));

        return array($dir, $lat_h_int, $lat_min_int, $lat_sec_int, $lat_min_float);
    }

    // create qth locator
    public function latlongToQTH($lat, $lon)
    {
        $lon += 180;
        $l[0] = floor($lon/20);

        $lon -= 20*$l[0];
        $l[2] = floor($lon/2);

        $lon -= 2 *$l[2];
        $l[4] = floor($lon*60/5);

        $lat += 90;
        $l[1] = floor($lat/10);

        $lat -= 10*$l[1];
        $l[3] = floor($lat);

        $lat -= $l[3];
        $l[5] = floor($lat*120/5);

        return sprintf("%c%c%c%c%c%c", $l[0]+65, $l[1]+65, $l[2]+48, $l[3]+48, $l[4]+65, $l[5]+65);
    }
}
