<?php
/**
 * Smarty plugin
 *
 * @param mixed $text
 * @param mixed $format
 */
function smarty_modifier_sprintf($text, $format)
{
    return sprintf($format, $text);
}
