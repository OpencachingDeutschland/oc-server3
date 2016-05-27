<?php
/**
 * Smarty {repeat string="&nbsp;" count=2} function plugin
 *
 * @param array $params
 * @param \OcSmarty $smarty
 *
 * @return string
 */
function smarty_function_repeat(array $params, \OcSmarty &$smarty)
{
    return str_repeat($params['string'], $params['count']);
}
