<?php
/**
 * Smarty {repeat string="&nbsp;" count=2} function plugin
 */
/**
 * @param $params
 * @return string
 */
function smarty_function_repeat($params)
{
    return str_repeat($params['string'], $params['count']);
}
