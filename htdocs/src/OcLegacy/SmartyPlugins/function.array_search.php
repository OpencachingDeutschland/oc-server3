<?php
/**
 * Smarty plugin
 *
 * @param mixed $params
 * @param & $smarty
 */
/**
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_array_search($params, &$smarty)
{
    if (!is_array($params['haystack'])) {
        $smarty->assign($params['var'], false);

        return '';
    }

    $returnValue = array_search($params['needle'], $params['haystack']);
    $smarty->assign($params['var'], $returnValue);

    return '';
}
