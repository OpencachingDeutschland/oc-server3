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
 * Name:     nbsp<br>
 * Example:  {$text|nsbp}
 * @version  1.0
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_nbsp($string)
{
    return str_replace(' ', '&nbsp;', $string);
}

/* vim: set expandtab: */
