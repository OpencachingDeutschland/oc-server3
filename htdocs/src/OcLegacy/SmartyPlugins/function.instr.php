<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 * Smarty {instr haystack=$string needle=$string} function plugin
 */
/**
 * @param $params
 * @return bool
 */
function smarty_function_instr($params)
{
    return (strpos($params['haystack'], $params['needle']) !== false);
}
