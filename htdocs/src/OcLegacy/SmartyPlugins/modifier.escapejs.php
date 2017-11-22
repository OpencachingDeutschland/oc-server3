<?php
/**
 * Smarty plugin
 *
 * @param $string
 * @return string
 */
function smarty_modifier_escapejs($string)
{
    $string = str_replace(
        ['\\', '\'', '"'],
        ['\\\\', '\\\'', '&quot;'],
        $string
    );

    return $string;
}
