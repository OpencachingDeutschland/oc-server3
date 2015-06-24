<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     stripcrlf<br>
 * Example:  {$text|stripcrlf}
 * @version  1.0
 * @param string
 * @return string
 */
function smarty_modifier_stripcrlf($string)
{
    return str_replace(array("\r", "\n"), '', $string);
}

/* vim: set expandtab: */

?>
