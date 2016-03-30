<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 ***
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     base64encode<br>
 * Example:  {$text|base64encode}
 * @version  1.0
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_base64encode($string)
{
    return base64_encode($string);
}

/* vim: set expandtab: */
