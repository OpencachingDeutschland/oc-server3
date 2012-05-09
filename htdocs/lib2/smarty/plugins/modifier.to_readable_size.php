<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Format Byte-Value to readable value
 *
 * Type:     modifier<br>
 * Name:     to_readable_size<br>
 * Purpose:  Format Byte-Value to readable value
 * @param size
 * @return string
 */
function smarty_modifier_to_readable_size($size)
{
	if (!is_numeric($size)) return $size;

	if ($size > 1099511627776)
	{
		$size /= 1099511627776;
		$suffix = 'TB';
	}
	elseif ($size > 1073741824)
	{
		$size /= 1073741824;
		$suffix = 'GB';
	}
	elseif ($size > 1048576)
	{
		$size /= 1048576;
		$suffix = 'MB';    
	}
	elseif ($size > 1024)
	{
		$size /= 1024;
		$suffix = 'KB';
	}
	else
		$suffix = 'B';

	return round($size, 2) . ' ' . $suffix;
}

/* vim: set expandtab: */

?>
