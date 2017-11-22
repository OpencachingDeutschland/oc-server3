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
function smarty_modifier_base64encode($string)
{
    return base64_encode($string);
}
