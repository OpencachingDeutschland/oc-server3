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
 * Name:     format_hour<br>
 * Example:  {$value|format_hour}
 * @version  1.0
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_format_hour($value)
{
    $hour = floor($value);

    return $hour . ':' . sprintf('%02.0F', ($value - $hour) * 60);
}

/* vim: set expandtab: */
