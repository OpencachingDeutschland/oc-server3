<?php
/**
 * Smarty plugin
 *
 *
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     round<br>
 * Example:  {$number|sprintf:"%0.3f"}
 *
 * @param string $text
 * @param string $format
 *
 * @return string
 */
function smarty_modifier_sprintf($text, $format)
{
    return sprintf($format, $text);
}

/* vim: set expandtab: */
