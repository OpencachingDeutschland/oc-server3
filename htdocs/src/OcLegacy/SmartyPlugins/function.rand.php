<?php
/**
 * Smarty plugin
 *
 * @param mixed $params
 */
/**
 * @param $params
 * @return int
 */
function smarty_function_rand($params)
{
    $min = isset($params['min']) ? $params['min'] + 0 : 0;
    $max = isset($params['max']) ? $params['max'] + 0 : 0;

    return mt_rand($min, $max);
}
