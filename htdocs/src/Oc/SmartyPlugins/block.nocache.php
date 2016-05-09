<?php


/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************
 *
 * /* block nocache
 *
 * usage
 *
 * {nocache}...{/nocache}
 *
 * OR
 *
 * {nocache name="<unique blockname>" <varname1>=$<value1> [...]}...{/nocache}
 */
function smarty_block_nocache($param, $content, &$smarty, &$repeat)
{
    static $counter = [];

    if ($repeat) {
        if (!isset($param['name'])) {
            return $content;
        }

        $name = $param['name'];
        unset($param['name']);

        if (!isset($counter[$name])) {
            $counter[$name] = 0;
        }
        $counter[$name] ++;

        if ($smarty->_cache_including) {
            $param = isset($smarty->_cache_info['cached_vars'][$name][$counter[$name]]) ? $smarty->_cache_info['cached_vars'][$name][$counter[$name]] : [];
        } else {
            $smarty->_cache_info['cached_vars'][$name][$counter[$name]] = $param;
        }

        foreach ($param as $k => $v) {
            $smarty->_tpl_vars[$k] = $v;
        }
    }

    return $content;
}
