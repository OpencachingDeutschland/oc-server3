<?php
/**
 * Smarty plugin
 *
 * @param mixed $params
 */
/**
 * @param $params
 * @return float|int
 */
function smarty_function_nccacheid($params)
{
    return hexdec(mb_substr($params['wp'], 1));
}
