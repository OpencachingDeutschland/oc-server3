<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$tpl->name = 'coordinates';
$tpl->popup = true;

$lat_float = 0;
if (isset($_REQUEST['lat'])) {
    $lat_float += $_REQUEST['lat'];
}

$lon_float = 0;
if (isset($_REQUEST['lon'])) {
    $lon_float += $_REQUEST['lon'];
}

$cache_country = isset($_REQUEST['country']) ? $_REQUEST['country'] : false;
$cache_desclang = isset($_REQUEST['desclang']) ? $_REQUEST['desclang'] : false;

$coord = new coordinate($lat_float, $lon_float);

$tpl->assign('coordDeg', $coord->getDecimal());
$tpl->assign('coordDegMin', $coord->getDecimalMinutes());
$tpl->assign('coordDegMinSec', $coord->getDecimalMinutesSeconds());
$tpl->assign('coordUTM', $coord->getUTM());
$tpl->assign('coordGK', $coord->getGK());
$tpl->assign('coordRD', $coord->getRD());
$tpl->assign('showRD', ($coord->nLat >= 45 && $coord->nLat <= 57 && $coord->nLon >= 0 && $coord->nLon <= 15));
$tpl->assign('coordQTH', $coord->getQTH());
$tpl->assign('coordSwissGrid', $coord->getSwissGrid());

// build priority list of W3W languages to display

// 1. current page locale
$w3w_langs = array();
if ($opt['locale'][$opt['template']['locale']]['what3words']) {
    $w3w_langs[] = $opt['template']['locale'];
}

// 2. language of the cache description
if ($cache_desclang && !in_array($cache_desclang, $w3w_langs)) {
    $w3w_langs[] = $cache_desclang;
}

// 3. primary language of the cache's country
if ($cache_country) {
    foreach ($opt['locale'] as $l => $data) {
        if ($data['what3words'] &&
            in_array($cache_country, $data['primary_lang_of']) &&
            !in_array($l, $w3w_langs)
        ) {
            $w3w_langs[] = $l;
            break;
        }
    }
}

// 4. fallback locale of the site (usually English)
if ($opt['locale'][$opt['template']['default']['fallback_locale']]['what3words'] &&
    !in_array($opt['template']['default']['fallback_locale'], $w3w_langs)
) {
    $w3w_langs[] = $opt['template']['default']['fallback_locale'];
}

// 5. main locale of the site
if ($opt['locale'][$opt['page']['main_locale']]['what3words'] &&
    !in_array($opt['page']['main_locale'], $w3w_langs)
) {
    $w3w_langs[] = $opt['page']['main_locale'];
}

// 6. English
if (!in_array('EN', $w3w_langs)) {
    $w3w_langs[] = 'EN';
}

$tpl->assign('coordW3W1', $coord->getW3W($w3w_langs[0]));
$lang1_name = sql_value("SELECT `name` FROM `languages` WHERE `short`='&1'", '', $w3w_langs[0]);
$tpl->assign('W3Wlang1', $translate->t($lang1_name, '', '', 0));

if (isset($w3w_langs[1])) {
    $tpl->assign('coordW3W2', $coord->getW3W($w3w_langs[1]));
    $lang2_name = sql_value("SELECT `name` FROM `languages` WHERE `short`='&1'", '', $w3w_langs[1]);
    $tpl->assign('W3Wlang2', $translate->t($lang2_name, '', '', 0));
} else {
    $tpl->assign('coordW3W2', false);
}

// wp gesetzt?
$wp = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';
if ($wp != '') {
    $rs = sql(
        "SELECT `caches`.`name`, `user`.`username`
         FROM `caches`
         INNER JOIN `cache_status`
             ON `caches`.`status`=`cache_status`.`id`
         INNER JOIN `user`
             ON `user`.`user_id`=`caches`.`user_id`
         WHERE `cache_status`.`allow_user_view`= 1
         AND `caches`.`wp_oc`='&1'",
        $wp
    );
    if ($r = sql_fetch_array($rs)) {
        $tpl->assign('owner', $r['username']);
        $tpl->assign('cachename', $r['name']);
    }
    sql_free_result($rs);
}
$tpl->assign('wp', $wp);
$childwp = isset($_REQUEST['childwp']) ? $_REQUEST['childwp'] : '';
$tpl->assign('childwp', $childwp);

$tpl->display();
