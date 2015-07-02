<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty escape modifier plugin; replacement for ../plugins/modifier.escpapejs.php;
 * see also block.t.php
 *
 * Type:     modifier<br>
 * Name:     xescapejs<br>
 * @return string
 */
function smarty_modifier_xescapejs($string)
{
	$string = str_replace('\\', '\\\\', $string);
	$string = str_replace('\'', '\\\'', $string);
	$string = str_replace('"', '&quot;', $string);
	return $string;
}
?>