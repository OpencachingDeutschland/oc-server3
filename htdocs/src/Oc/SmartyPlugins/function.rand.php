<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 ***
 * Smarty {rand} function plugin
 */
function smarty_function_rand($params, &$smarty)
{
    $min = isset($params['min']) ? $params['min'] + 0 : 0;
    $max = isset($params['max']) ? $params['max'] + 0 : 0;

    return mt_rand($min, $max);
}
