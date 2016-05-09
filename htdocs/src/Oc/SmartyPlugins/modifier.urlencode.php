<?php

/**
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     urlencode<br>
 * Example:  {$text|urlencode}
 *
 * @version  1.0
 *
 * @param string
 *
 * @return string
 */
function smarty_modifier_urlencode($string)
{
    return urlencode($string);
}
