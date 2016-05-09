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
 * Name:     rot13html<br>
 * Example:  {$text|rot13html}
 * @version  1.0
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_rot13html($str)
{
    $delimiter[0][0] = '&'; // start-char
    $delimiter[0][1] = ';'; // end-char
    $delimiter[1][0] = '<';
    $delimiter[1][1] = '>';
    $delimiter[2][0] = '[';
    $delimiter[2][1] = ']';

    $retval = '';

    while (mb_strlen($retval) < mb_strlen($str)) {
        $nNextStart = false;
        $sNextEndChar = '';
        foreach ($delimiter as $del) {
            $nThisStart = mb_strpos($str, $del[0], mb_strlen($retval));

            if ($nThisStart !== false) {
                if (($nNextStart > $nThisStart) || ($nNextStart === false)) {
                    $nNextStart = $nThisStart;
                    $sNextEndChar = $del[1];
                }
            }
        }

        if ($nNextStart === false) {
            $retval .= str_rot13(mb_substr($str, mb_strlen($retval), mb_strlen($str) - mb_strlen($retval)));
        } else {
            // crypted part
            $retval .= str_rot13(mb_substr($str, mb_strlen($retval), $nNextStart - mb_strlen($retval)));

            // uncrypted part
            $nNextEnd = mb_strpos($str, $sNextEndChar, $nNextStart);

            if ($nNextEnd === false) {
                $retval .= mb_substr($str, $nNextStart, mb_strlen($str) - mb_strlen($retval));
            } else {
                $retval .= mb_substr($str, $nNextStart, $nNextEnd - $nNextStart + 1);
            }
        }
    }

    return $retval;
}

/* vim: set expandtab: */
