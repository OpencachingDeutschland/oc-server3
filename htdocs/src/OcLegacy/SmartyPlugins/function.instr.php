<?php
/**
 * Smarty plugin
 *
 * @param mixed $params
 */
/**
 * @param $params
 * @return bool
 */
function smarty_function_instr($params)
{
    return (strpos($params['haystack'], $params['needle']) !== false);
}
