<?php
/**
 * Smarty {repeat string="&nbsp;" count=2} function plugin
 */
function smarty_function_repeat($params, &$smarty)
{
    return str_repeat($params['string'], $params['count']);
}
