<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Rot13 hint decoder
 ***************************************************************************/

header('Content-type: text/html; charset=utf-8');

if (isset($_REQUEST['text'])) {
    $text = $_REQUEST['text'];
    $text = str_replace(
        [
            '<br>',
            '<br/>',
            '<br />'
        ],
        "\n",
        $text
    );
    $text = hint_rot13($text);
    $text = htmlentities($text);
    $text = nl2br($text);
    echo $text;
}

function hint_rot13($in)
{
    $out = '';
    $decode = true;
    $max = strlen($in);
    for ($i = 0; $i < $max; ++ $i) {
        $c = $in[$i];
        if ($decode && $c == '[') {
            $out .= '[';
            $decode = false;
        } elseif (!$decode && $c == ']') {
            $out .= ']';
            $decode = true;
        } elseif (!$decode) {
            $out .= $c;
        } elseif ($c >= 'A' && $c <= 'Z') {
            $c = chr(ord($c) + 13);
            if ($c > 'Z') {
                $c = chr(ord($c) - 26);
            }
            $out .= $c;
        } elseif ($c >= 'a' && $c <= 'z') {
            $c = chr(ord($c) + 13);
            if ($c > 'z') {
                $c = chr(ord($c) - 26);
            }
            $out .= $c;
        } else {
            $out .= $c;
        }
    }

    return $out;
}
