<?php
/**
 * Smarty {repeat string="&nbsp;" count=2} function plugin
 * @param mixed $params
 */
/**
 * @param $params
 * @return string
 */
function smarty_function_repeat($params)
{
    return str_repeat($params['string'], $params['count']);
}
