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
function smarty_modifier_nbsp($string)
{
    return str_replace(' ', '&nbsp;', $string);
}
