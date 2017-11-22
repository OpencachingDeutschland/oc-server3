<?php
/**
 * Smarty plugin
 *
 * @version  1.0
 *
 * @param string
 * @param mixed $string
 *
 * @return string
 */
function smarty_modifier_stripcrlf($string)
{
    return str_replace(["\r", "\n"], '', $string);
}
