<?php
/**
 * Smarty plugin
 *
 * Smarty {nccacheid wp=$wpnc} function plugin
 *
 * @param array $params
 * @param \OcSmarty $smarty
 *
 * @return int
 */
function smarty_function_nccacheid(array $params, \OcSmarty &$smarty)
{
    return hexdec(mb_substr($params['wp'], 1));
}
