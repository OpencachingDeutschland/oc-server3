<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 * Smarty {nccacheid wp=$wpnc} function plugin
 */
/**
 * @param $params
 * @return float|int
 */
function smarty_function_nccacheid($params)
{
    return hexdec(mb_substr($params['wp'], 1));
}
