<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage plugins
 * Smarty {array_search var=varname needle=value haystack=array} function plugin
 */
function smarty_function_array_search($params, &$smarty)
{
    if (!is_array($params['haystack'])) {
        $smarty->assign($params['var'], false);

        return '';
    }

    $retval = array_search($params['needle'], $params['haystack']);
    $smarty->assign($params['var'], $retval);

    return '';
}
