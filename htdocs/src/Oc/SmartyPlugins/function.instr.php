<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 * Smarty {instr haystack=$string needle=$string} function plugin
 */
function smarty_function_instr($params, &$smarty)
{
    return (strpos($params['haystack'], $params['needle']) !== false);
}
