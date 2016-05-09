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
 * Name:     round<br>
 * Example:  {$number|sprintf:"%0.3f"}
 */
function smarty_modifier_sprintf($text, $format)
{
    return sprintf($format, $text);
}

/* vim: set expandtab: */
