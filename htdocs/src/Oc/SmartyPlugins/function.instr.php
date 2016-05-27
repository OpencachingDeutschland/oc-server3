<?php
/**
 * Smarty plugin
 *
 * Smarty {instr haystack=$string needle=$string} function plugin
 *
 * @param array $params
 * @param \OcSmarty $smarty
 *
 * @return bool
 */
function smarty_function_instr(array $params, \OcSmarty &$smarty)
{
    return strpos($params['haystack'], $params['needle']) !== false;
}
