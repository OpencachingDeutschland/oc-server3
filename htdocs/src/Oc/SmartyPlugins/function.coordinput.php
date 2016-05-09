<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 * Smarty {coordinput prefix="coord" lat=48.12345 lon=9.12345} function plugin
 */
function smarty_function_coordinput($params, &$smarty)
{
    $prefix = $params['prefix'];
    $lat = $params['lat'] + 0;
    $lon = $params['lon'] + 0;

    if ($lat < 0) {
        $bLatNeg = true;
        $lat = - $lat;
    } else {
        $bLatNeg = false;
    }
    $latMin = $lat - floor($lat);
    $lat -= $latMin;

    if ($lon < 0) {
        $bLonNeg = true;
        $lon = - $lon;
    } else {
        $bLonNeg = false;
    }
    $lonMin = $lon - floor($lon);
    $lon -= $lonMin;

    $retval = '<select name="' . htmlspecialchars($prefix, ENT_QUOTES, 'UTF-8') . 'NS">';
    if ($bLatNeg) {
        $retval .= '<option value="N">N</option>';
        $retval .= '<option value="S" selected="selected">S</option>';
    } else {
        $retval .= '<option value="N" selected="selected">N</option>';
        $retval .= '<option value="S">S</option>';
    }
    $retval .= '</select>&nbsp;';

    $retval .= '<input type="text" name="' . htmlspecialchars(
            $prefix,
            ENT_QUOTES,
            'UTF-8'
        ) . 'Lat" value="' . htmlspecialchars(
            sprintf('%02d', $lat),
            ENT_QUOTES,
            'UTF-8'
        ) . '" size="1" maxlength="2" />&deg; ';
    $retval .= '<input type="text" name="' . htmlspecialchars(
            $prefix,
            ENT_QUOTES,
            'UTF-8'
        ) . 'LatMin" value="' . htmlspecialchars(
            sprintf('%06.3f', $latMin * 60),
            ENT_QUOTES,
            'UTF-8'
        ) . '" size="5" maxlength="6" /> \'';

    $retval .= '<br />';

    $retval .= '<select name="' . htmlspecialchars($prefix, ENT_QUOTES, 'UTF-8') . 'EW">';
    if ($bLonNeg) {
        $retval .= '<option value="E">E</option>';
        $retval .= '<option value="W" selected="selected">W</option>';
    } else {
        $retval .= '<option value="E" selected="selected">E</option>';
        $retval .= '<option value="W">W</option>';
    }
    $retval .= '</select>&nbsp;';

    $retval .= '<input type="text" name="' . htmlspecialchars(
            $prefix,
            ENT_QUOTES,
            'UTF-8'
        ) . 'Lon" value="' . htmlspecialchars(
            sprintf('%03d', $lon),
            ENT_QUOTES,
            'UTF-8'
        ) . '" size="2" maxlength="3" />&deg; ';
    $retval .= '<input type="text" name="' . htmlspecialchars(
            $prefix,
            ENT_QUOTES,
            'UTF-8'
        ) . 'LonMin" value="' . htmlspecialchars(
            sprintf('%06.3f', $lonMin * 60),
            ENT_QUOTES,
            'UTF-8'
        ) . '" size="5" maxlength="6" /> \'';

    return $retval;
}
