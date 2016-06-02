<?php
/**
 * Smarty plugin
 *
 *
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     stripcrlf<br>
 * Example:  {$text|stripcrlf}
 *
 * @version  1.0
 *
 * @param string $string
 *
 * @return string
 */
function smarty_modifier_stripcrlf($string)
{
    return str_replace(["\r", "\n"], '', $string);
}

/* vim: set expandtab: */
