<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty escape modifier plugin; see also block.t.php
 *
 * Type:     modifier<br>
 * Name:     escapejs<br>
 * @return string
 */
function smarty_modifier_escapejs($string)
{
	$string = str_replace('\\', '\\\\', $string);
	$string = str_replace('\'', '\\\'', $string);
	$string = str_replace('"', '&quot;', $string);
	return $string;
}
?>