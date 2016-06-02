<?php
/**
 * Smarty plugin
 *
 * Smarty {array_search var=varname needle=value haystack=array} function plugin
 *
 * @param array $params
 * @param \OcSmarty $smarty
 *
 * @return string
 */
function smarty_function_array_search(array $params, \OcSmarty &$smarty)
{
    if (!is_array($params['haystack'])) {
        $smarty->assign($params['var'], false);

        return '';
    }

    $retval = array_search($params['needle'], $params['haystack']);
    $smarty->assign($params['var'], $retval);

    return '';
}
