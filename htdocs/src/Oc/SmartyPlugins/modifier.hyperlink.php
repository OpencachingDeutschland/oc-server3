<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 *
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     hyperlink<br>
 * Example:  {$text|hyperlink}
 * @version  1.0
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_hyperlink($text)
{
    $texti = mb_strtolower($text);
    $retval = '';
    $curpos = 0;
    $starthttp = mb_strpos($texti, 'http://', $curpos);
    $starthttps = mb_strpos($texti, 'https://', $curpos);
    if ($starthttp === false || ($starthttps !== false && $starthttps < $starthttp)) {
        $starthttp = $starthttps;
    }
    $endhttp = false;
    while (($starthttp !== false) || ($endhttp >= mb_strlen($text))) {
        $endhttp1 = mb_strpos($text, ' ', $starthttp);
        if ($endhttp1 === false) {
            $endhttp1 = mb_strlen($text);
        }
        $endhttp2 = mb_strpos($text, "\n", $starthttp);
        if ($endhttp2 === false) {
            $endhttp2 = mb_strlen($text);
        }
        $endhttp3 = mb_strpos($text, "\r", $starthttp);
        if ($endhttp3 === false) {
            $endhttp3 = mb_strlen($text);
        }
        $endhttp4 = mb_strpos($text, '<', $starthttp);
        if ($endhttp4 === false) {
            $endhttp4 = mb_strlen($text);
        }
        $endhttp5 = mb_strpos($text, '] ', $starthttp);
        if ($endhttp5 === false) {
            $endhttp5 = mb_strlen($text);
        }
        $endhttp6 = mb_strpos($text, ')', $starthttp);
        if ($endhttp6 === false) {
            $endhttp6 = mb_strlen($text);
        }
        $endhttp7 = mb_strpos($text, '. ', $starthttp);
        if ($endhttp7 === false) {
            $endhttp7 = mb_strlen($text);
        }

        $endhttp = min($endhttp1, $endhttp2, $endhttp3, $endhttp4, $endhttp5, $endhttp6, $endhttp7);

        $retval .= mb_substr($text, $curpos, $starthttp - $curpos);
        $url = mb_substr($text, $starthttp, $endhttp - $starthttp);
        $retval .= '<a href="' . $url . '" alt="" target="_blank">' . $url . '</a>';

        $curpos = $endhttp;
        if ($curpos >= mb_strlen($text)) {
            break;
        }
        $starthttp = mb_strpos(mb_strtolower($text), 'http://', $curpos);
        $starthttps = mb_strpos($texti, 'https://', $curpos);
        if ($starthttp === false || ($starthttps !== false && $starthttps < $starthttp)) {
            $starthttp = $starthttps;
        }
    }

    $retval .= mb_substr($text, $curpos);

    return $retval;
}

/* vim: set expandtab: */
