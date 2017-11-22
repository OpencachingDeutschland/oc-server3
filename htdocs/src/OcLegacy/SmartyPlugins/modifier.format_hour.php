<?php
/**
 * Smarty plugin
 *
 * @version  1.0
 *
 * @param string
 * @param mixed $value
 *
 * @return string
 */
function smarty_modifier_format_hour($value)
{
    $hour = floor($value);

    return $hour . ':' . sprintf('%02.0F', ($value - $hour) * 60);
}
