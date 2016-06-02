<?php
/**
 * Smarty plugin
 *
 * Smarty {rand} function plugin
 *
 * @param array $params
 * @param \OcSmarty $smarty
 *
 * @return int
 */
function smarty_function_rand(array $params, \OcSmarty &$smarty)
{
    $min = isset($params['min']) ? $params['min'] + 0 : 0;
    $max = isset($params['max']) ? $params['max'] + 0 : 0;

    return mt_rand($min, $max);
}
