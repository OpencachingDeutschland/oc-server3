<?php
/**
 * Smarty plugin
 *
 * Smarty {nccacheid wp=$wpnc} function plugin
 */
function smarty_function_nccacheid($params, &$smarty)
{
    return hexdec(mb_substr($params['wp'], 1));
}
