<?php
/**
 * Smarty plugin
 *
 * Smarty {count array=$array} function plugin
 *
 * @param array $params
 * @param \OcSmarty $smarty
 *
 * @return int
 */
function smarty_function_count(array $params, \OcSmarty &$smarty)
{
    return count($params['array']);
}
