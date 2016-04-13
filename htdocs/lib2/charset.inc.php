<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * charset related functions
 ****************************************************************************/


// replacement table for Unicode 0x100 to 0x1FF
$utf_xlatin = "A a A a A a C c C c C c C c D d D d E e E e E e E e E e G g G g " .
    "G g G g H h H h I i I i I i I i I i IJijJ j K k K L l L l L l L " .
    "l L l N n N n N n n n n O o O o Ö ö OEoeR r R r R r S s S s S s " .
    "S s T t T t T t U u U u U u U u Ü ü U u W w Y y Y Z z Z z Z z   ";

// replacement table for Unicode 0x2000 to 0x203F
$utf_punct = "                ------|_'','\"\"\"\"++*>....        %%´\"\"`\"\"^<> !?- ";


// convert utf-8 string to iso-8859-1 and use replacemend characters if possible

function utf8ToIso88591($s)
{
    global $utf_xlatin, $utf_punct;

    $pos = 0;
    $result = "";

    while ($pos < strlen($s)) {
        $c1 = ord($s[$pos ++]);
        if ($c1 < 0xC0) {
            $result .= chr($c1);
        } elseif ($pos < strlen($s)) {
            $c2 = ord($s[$pos ++]);
            if ($c1 < 0xE0) {
                $code = 0x40 * ($c1 & 0x1F) + ($c2 & 0x3F);
                if ($code < 0x100) {
                    $result .= chr($code);
                } elseif ($code < 0x200) {
                    $result .= $utf_xlatin[2 * ($code - 0x100)];
                    if ($utf_xlatin[2 * ($code - 0x100) + 1] != ' ') {
                        $result .= $utf_xlatin[2 * ($code - 0x100) + 1];
                    }
                } else {
                    $result .= "?";
                }
            } elseif ($pos < strlen($s)) {
                $c3 = ord($s[$pos ++]);
                $code = 0x1000 * ($c1 & 0x0F) + 0x40 * ($c2 & 0x3F) + ($c3 & 0x3F);
                switch ($code) {
                    case 0x2026:
                        $result .= "...";
                        break;
                    case 0x2025:
                        $result .= "..";
                        break;
                    case 0x20AC:
                        $result .= "Euro";
                        break;
                    case 0x2605:
                        $result .= "*";
                        break;
                    default:
                        if ($code >= 0x2000 && $code <= 0x203F) {
                            $result .= $utf_punct[$code - 0x2000];
                        } else {
                            $result .= "?";
                        }
                }
            }
        }
    }

    return $result;
}
