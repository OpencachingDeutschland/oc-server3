<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 * Smarty {nccacheid wp=$wpnc} function plugin
 */
function smarty_function_nccacheid($params, &$smarty)
{
    return hexdec(mb_substr($params['wp'], 1));
}
