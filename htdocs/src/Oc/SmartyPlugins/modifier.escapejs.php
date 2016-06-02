<?php
/**
 * Smarty plugin
 *
 * Smarty escape modifier plugin; see also block.t.php
 *
 * Type:     modifier<br>
 * Name:     escapejs<br>
 *
 * @param string $string
 *
 * @return string
 */
function smarty_modifier_escapejs($string)
{
    $string = str_replace('\\', '\\\\', $string);
    $string = str_replace('\'', '\\\'', $string);
    $string = str_replace('"', '&quot;', $string);

    return $string;
}
