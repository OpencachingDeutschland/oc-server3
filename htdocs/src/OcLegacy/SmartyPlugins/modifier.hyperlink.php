<?php
/**
 * Smarty plugin
 *
 * @version  1.0
 *
 * @param string
 * @param mixed $text
 *
 * @return string
 */
function smarty_modifier_hyperlink($text)
{
    $texti = mb_strtolower($text);
    $returnValue = '';
    $curPos = 0;
    $startHttp = mb_strpos($texti, 'http://', $curPos);
    $startHttps = mb_strpos($texti, 'https://', $curPos);
    if ($startHttp === false || ($startHttps !== false && $startHttps < $startHttp)) {
        $startHttp = $startHttps;
    }
    $endHttp = false;
    while (($startHttp !== false) || ($endHttp >= mb_strlen($text))) {
        $endHttp1 = mb_strpos($text, ' ', $startHttp);
        if ($endHttp1 === false) {
            $endHttp1 = mb_strlen($text);
        }
        $endHttp2 = mb_strpos($text, "\n", $startHttp);
        if ($endHttp2 === false) {
            $endHttp2 = mb_strlen($text);
        }
        $endHttp3 = mb_strpos($text, "\r", $startHttp);
        if ($endHttp3 === false) {
            $endHttp3 = mb_strlen($text);
        }
        $endHttp4 = mb_strpos($text, '<', $startHttp);
        if ($endHttp4 === false) {
            $endHttp4 = mb_strlen($text);
        }
        $endHttp5 = mb_strpos($text, '] ', $startHttp);
        if ($endHttp5 === false) {
            $endHttp5 = mb_strlen($text);
        }
        $endHttp6 = mb_strpos($text, ')', $startHttp);
        if ($endHttp6 === false) {
            $endHttp6 = mb_strlen($text);
        }
        $endHttp7 = mb_strpos($text, '. ', $startHttp);
        if ($endHttp7 === false) {
            $endHttp7 = mb_strlen($text);
        }

        $endHttp = min($endHttp1, $endHttp2, $endHttp3, $endHttp4, $endHttp5, $endHttp6, $endHttp7);

        $returnValue .= mb_substr($text, $curPos, $startHttp - $curPos);
        $url = mb_substr($text, $startHttp, $endHttp - $startHttp);
        $returnValue .= '<a href="' . $url . '" alt="" target="_blank">' . $url . '</a>';

        $curPos = $endHttp;
        if ($curPos >= mb_strlen($text)) {
            break;
        }
        $startHttp = mb_strpos(mb_strtolower($text), 'http://', $curPos);
        $startHttps = mb_strpos($texti, 'https://', $curPos);
        if ($startHttp === false || ($startHttps !== false && $startHttps < $startHttp)) {
            $startHttp = $startHttps;
        }
    }

    $returnValue .= mb_substr($text, $curPos);

    return $returnValue;
}
