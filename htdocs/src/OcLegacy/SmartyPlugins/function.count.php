<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 ***
 * Smarty {count array=$array} function plugin
 */
/**
 * @param $params
 * @return int
 */
function smarty_function_count($params)
{
    return count($params['array']);
}
