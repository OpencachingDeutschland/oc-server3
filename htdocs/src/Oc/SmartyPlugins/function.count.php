<?php
/**
 * Smarty plugin
 *
 ***
 * Smarty {count array=$array} function plugin
 */
function smarty_function_count($params, &$smarty)
{
    return count($params['array']);
}
