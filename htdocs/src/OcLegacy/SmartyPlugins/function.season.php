<?php
/**
 * Smarty plugin
 *
 * @param $params
 * @return mixed
 */
function smarty_function_season($params)
{
    $season = date('z');
    if (isset($params['season'])) {
        $season = $params['season'];
    }

    if ($season <= 81 || $season >= 355) {
        return $params['winter'];
    }

    if ($season >= 82 && $season <= 173) {
        return $params['spring'];
    }
    if ($season >= 174 && $season <= 264) {
        return $params['summer'];
    }
    if ($season >= 265 && $season <= 354) {
        return $params['autumn'];
    }
}
