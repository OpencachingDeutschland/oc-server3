<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty escape modifier plugin
 *
 * Type:     modifier<br>
 * Name:     escapejs<br>
 * @return string
 */
function smarty_modifier_escapejs($string)
{
	return str_replace('"', '&quot;', $string);
}
?>