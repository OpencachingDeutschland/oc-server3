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
function smarty_function_count($params)
{
    return count($params['array']);
}
