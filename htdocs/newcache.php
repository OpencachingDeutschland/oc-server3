<?php
/***************************************************************************
 * for license information see LICENSE.md
 *  submit a new cache
 *  used template(s): newcache
 ***************************************************************************/

use Oc\GeoCache\StatisticPicture;
use OcLegacy\Editor\EditorConstants;

require __DIR__ . '/lib2/web.inc.php';
require_once __DIR__ . '/lib2/edithelper.inc.php';
require_once __DIR__ . '/lib2/logic/attribute.class.php';

// lib2 equivalents of legacy globals
$locale = $opt['template']['locale'];
$oc_nodeid = $opt['logic']['node']['id'];

// t() translation helper — needed by newcache.inc.php (defined in lib/common.inc.php
// for legacy pages; replicated here for lib2 pages)
if (!function_exists('t')) {
    function t($str)
    {
        global $translate;
        $str = $translate->t($str, '', basename(__FILE__), __LINE__);
        $args = func_get_args();
        for ($nIndex = count($args) - 1; $nIndex > 0; $nIndex--) {
            $str = str_replace('%' . $nIndex, $args[$nIndex], $str);
        }
        return $str;
    }
}

// must be logged in
if (!$login->logged_in()) {
    $tpl->redirect('login.php?target=newcache.php');
}

$tpl->name = 'newcache';

// load template-specific language variables
$lang = strtolower($locale);
$style = $opt['template']['style'];
require_once __DIR__ . '/lang/' . $lang . '/' . $style . '/newcache.inc.php';

// allow HTML input (XSS protection header)
header('X-XSS-Protection: 0');

$error = false;

//set template replacements — default empty error messages
$tpl->assign('reset', ''); // obsolete
$tpl->assign('submit', $submit);
$tpl->assign('general_error', '');
$tpl->assign('hidden_since_message', '');
$tpl->assign('activate_on_message', '');
$tpl->assign('tos_message', '');
$tpl->assign('name_message', '');
$tpl->assign('desc_message', '');
$tpl->assign('effort_message', '');
$tpl->assign('size_message', '');
$tpl->assign('wpgc_message', '');
$tpl->assign('type_message', '');
$tpl->assign('diff_message', '');
$tpl->assign('safari_message', '');

$sel_type = $_POST['type'] ?? 0; // Ocprop
if (!isset($_POST['size'])) {
    if ($sel_type == 4 || $sel_type == 5) {
        $sel_size = 7;
    } else {
        $sel_size = -1;
    }
} else {
    $sel_size = $_POST['size'] ?? -1; // Ocprop
}
$sel_lang = $_POST['desc_lang'] ?? $default_lang;
$sel_country = $_POST['country'] ?? $login->getUserCountry(); // Ocprop
$show_all_countries = $_POST['show_all_countries'] ?? 0;
$show_all_langs = $_POST['show_all_langs'] ?? 0;

//coords — read new field names (coordinate_input.tpl), fall back to old for OCProp
$latNS = $_POST['lat_hem'] ?? $_POST['latNS'] ?? $default_NS; // Ocprop
$lon_EW = $_POST['lon_hem'] ?? $_POST['lonEW'] ?? $default_EW; // Ocprop
$lat_h = trim($_POST['lat_deg'] ?? $_POST['lat_h'] ?? '0'); // Ocprop
$lon_h = trim($_POST['lon_deg'] ?? $_POST['lon_h'] ?? '0'); // Ocprop
$lat_min = trim($_POST['lat_min'] ?? '00.000'); // Ocprop
$lon_min = trim($_POST['lon_min'] ?? '00.000'); // Ocprop

// hidden fields from unified coordinate input
$posted_latitude = $_POST['latitude'] ?? '';
$posted_longitude = $_POST['longitude'] ?? '';

// If unified input provided valid decimal coords, sync 6-field values from them
if ($posted_latitude !== '' && $posted_longitude !== ''
    && is_numeric($posted_latitude) && is_numeric($posted_longitude)
    && ((float)$posted_latitude != 0 || (float)$posted_longitude != 0)
) {
    $coord_lat = (float)$posted_latitude;
    $coord_lon = (float)$posted_longitude;
    $latNS = $coord_lat >= 0 ? 'N' : 'S';
    $lon_EW = $coord_lon >= 0 ? 'E' : 'W';
    $absLat = abs($coord_lat);
    $absLon = abs($coord_lon);
    $lat_h = (string)floor($absLat);
    $lat_min = sprintf('%02.3f', ($absLat - floor($absLat)) * 60);
    $lon_h = (string)floor($absLon);
    $lon_min = sprintf('%02.3f', ($absLon - floor($absLon)) * 60);
} else {
    // Compute decimal from 6-field values
    $h_lat = (float)$lat_h;
    $m_lat = is_numeric($lat_min) ? (float)$lat_min : 0;
    $h_lon = (float)$lon_h;
    $m_lon = is_numeric($lon_min) ? (float)$lon_min : 0;
    $coord_lat = $h_lat + $m_lat / 60;
    if ($latNS == 'S') {
        $coord_lat = -$coord_lat;
    }
    $coord_lon = $h_lon + $m_lon / 60;
    if ($lon_EW == 'W') {
        $coord_lon = -$coord_lon;
    }
}

// Assign coordinate_input.tpl variables
$tpl->assign('lat_hem', $latNS);
$tpl->assign('lon_hem', $lon_EW);
$tpl->assign('lat_deg', htmlspecialchars($lat_h, ENT_COMPAT, 'UTF-8'));
$tpl->assign('lon_deg', htmlspecialchars($lon_h, ENT_COMPAT, 'UTF-8'));
$tpl->assign('lat_min', htmlspecialchars($lat_min, ENT_COMPAT, 'UTF-8'));
$tpl->assign('lon_min', htmlspecialchars($lon_min, ENT_COMPAT, 'UTF-8'));
$tpl->assign('coord_latitude', $coord_lat);
$tpl->assign('coord_longitude', $coord_lon);

//name
$name = trim($_POST['name'] ?? ''); // Ocprop
$tpl->assign('name', htmlspecialchars($name, ENT_COMPAT, 'UTF-8'));

//shortdesc
$short_desc = trim($_POST['short_desc'] ?? '');
$tpl->assign('short_desc', htmlspecialchars($short_desc, ENT_COMPAT, 'UTF-8'));

// descMode auslesen, falls nicht gesetzt aus dem Profil laden
if (isset($_POST['descMode'])) {
    // Ocprop
    $descMode = (int) $_POST['descMode'];
    if (($descMode < EditorConstants::HTML_MODE) || ($descMode > EditorConstants::EDITOR_MODE)) {
        $descMode = EditorConstants::EDITOR_MODE;
    }
    if (isset($_POST['oldDescMode'])) {
        $oldDescMode = (int) $_POST['oldDescMode'];
        if (($oldDescMode < EditorConstants::HTML_MODE) || ($oldDescMode > EditorConstants::EDITOR_MODE)) {
            $oldDescMode = $descMode;
        }
    } else {
        $oldDescMode = $descMode;
    }
} else {
    $descMode = EditorConstants::EDITOR_MODE;
    $oldDescMode = $descMode;
}

// Text / normal HTML / HTML editor
$tpl->assign('use_tinymce', ($descMode == EditorConstants::EDITOR_MODE) ? 1 : 0);
$tpl->assign('descMode', $descMode);
$tpl->assign('show_htmlnotice', $descMode == EditorConstants::HTML_MODE);

//desc
if (isset($_POST['desc'])) {
    $desc = trim(processEditorInput($oldDescMode, $descMode, $_POST['desc'], $representDesc));
} else {
    $desc = '';
    $representDesc = '';
}

$tpl->assign('desc', htmlspecialchars($representDesc, ENT_COMPAT, 'UTF-8'));

// Add editor scripts via Smarty header mechanism
if ($descMode == EditorConstants::EDITOR_MODE) {
    $tpl->add_header_javascript('resource2/tinymce/tiny_mce_gzip.js');
    $tpl->add_header_javascript(
        'resource2/tinymce/config/desc.js.php?cacheid=0&lang=' . strtolower($locale)
    );
}
$tpl->add_header_javascript(editorJsPath());

//effort
$search_time = $_POST['search_time'] ?? 0;
$way_length = $_POST['way_length'] ?? 0;

$search_time = mb_ereg_replace(',', '.', $search_time);
$way_length = mb_ereg_replace(',', '.', $way_length);

if (mb_strpos($search_time, ':') == mb_strlen($search_time) - 3) {
    $st_hours = mb_substr($search_time, 0, mb_strpos($search_time, ':'));
    $st_minutes = mb_substr($search_time, mb_strlen($st_hours) + 1);

    if (is_numeric($st_hours) && is_numeric($st_minutes)) {
        if (($st_minutes >= 0) && ($st_minutes < 60)) {
            $search_time = $st_hours + $st_minutes / 60;
        }
    }
}

$st_hours = floor((float)$search_time);
$st_minutes = sprintf('%02.0F', ($search_time - $st_hours) * 60);

$tpl->assign('search_time', $st_hours . ':' . $st_minutes);
$tpl->assign('way_length', $way_length);


//hints
$hints = trim($_POST['hints'] ?? '');
$tpl->assign('hints', htmlspecialchars($hints, ENT_COMPAT, 'UTF-8'));

// fuer alte Versionen von OCProp
if (isset($_POST['submit']) && !isset($_POST['version2'])) {
    $hints = iconv("ISO-8859-1", "UTF-8", $hints);
}

//tos
$tos = isset($_POST['TOS']) ? 1 : 0; // Ocprop
if ($tos === 1) {
    $tpl->assign('toschecked', ' checked="checked"');
} else {
    $tpl->assign('toschecked', '');
}

//hidden_since
$hidden_day = $_POST['hidden_day'] ?? date('d'); // Ocprop
$hidden_month = $_POST['hidden_month'] ?? date('m'); // Ocprop
$hidden_year = $_POST['hidden_year'] ?? date('Y'); // Ocprop
$tpl->assign('hidden_day', htmlspecialchars($hidden_day, ENT_COMPAT, 'UTF-8'));
$tpl->assign('hidden_month', htmlspecialchars($hidden_month, ENT_COMPAT, 'UTF-8'));
$tpl->assign('hidden_year', htmlspecialchars($hidden_year, ENT_COMPAT, 'UTF-8'));

//activation date
$activate_day = $_POST['activate_day'] ?? date('d');
$activate_month = $_POST['activate_month'] ?? date('m');
$activate_year = $_POST['activate_year'] ?? date('Y');
$tpl->assign('activate_day', htmlspecialchars($activate_day, ENT_COMPAT, 'UTF-8'));
$tpl->assign('activate_month', htmlspecialchars($activate_month, ENT_COMPAT, 'UTF-8'));
$tpl->assign('activate_year', htmlspecialchars($activate_year, ENT_COMPAT, 'UTF-8'));

$tpl->assign('publish_now_checked', '');
$tpl->assign('publish_later_checked', '');
$tpl->assign('publish_notnow_checked', '');

$publish = $_POST['publish'] ?? 'notnow'; // Ocprop
if ($publish == 'now2') {
    $tpl->assign('publish_now_checked', 'checked');
} elseif ($publish == 'later') {
    $tpl->assign('publish_later_checked', 'checked');
} else { // notnow
    $publish = 'notnow';
    $tpl->assign('publish_notnow_checked', 'checked');
}

// fill activate hours
$activate_hour = (int)($_POST['activate_hour'] ?? date('H'));
$activation_hours_arr = [];
for ($i = 0; $i <= 23; $i++) {
    $activation_hours_arr[] = [
        'value' => $i,
        'label' => $i,
        'selected' => ($activate_hour === $i),
    ];
}
$tpl->assign('activation_hours', $activation_hours_arr);

//log-password
$log_pw = isset($_POST['log_pw']) ? mb_substr(trim($_POST['log_pw']), 0, 20) : '';
$tpl->assign('log_pw', htmlspecialchars($log_pw, ENT_COMPAT, 'UTF-8'));

// gc- and nc-waypoints
// fix #4356: gc waypoints are frequently copy&pasted with leading spaces
$wp_gc = isset($_POST['wp_gc']) ? strtoupper(trim($_POST['wp_gc'])) : ''; // Ocprop
$tpl->assign('wp_gc', htmlspecialchars($wp_gc, ENT_COMPAT, 'UTF-8'));

//difficulty
$difficulty = $_POST['difficulty'] ?? 1; // Ocprop
$difficulty_arr = [['value' => 1, 'label' => $sel_message, 'selected' => false]];
for ($i = 2; $i <= 10; $i++) {
    $difficulty_arr[] = [
        'value' => $i,
        'label' => $i / 2,
        'selected' => ($difficulty == $i),
    ];
}
$tpl->assign('difficulty_options', $difficulty_arr);

//terrain
$terrain = $_POST['terrain'] ?? 1; // Ocprop
$terrain_arr = [['value' => 1, 'label' => $sel_message, 'selected' => false]];
for ($i = 2; $i <= 10; $i++) {
    $terrain_arr[] = [
        'value' => $i,
        'label' => $i / 2,
        'selected' => ($terrain == $i),
    ];
}
$tpl->assign('terrain_options', $terrain_arr);

//sizeoptions
$size_arr = [['value' => -1, 'label' => t('Please select!'), 'selected' => ($sel_size == -1)]];
$rsSizes = sql(
    "SELECT `cache_size`.`id`,
            IFNULL(`sys_trans_text`.`text`,
            `cache_size`.`name`) AS `name`
     FROM `cache_size`
     LEFT JOIN `sys_trans`
       ON `cache_size`.`trans_id`=`sys_trans`.`id`
     LEFT JOIN `sys_trans_text`
       ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
       AND `sys_trans_text`.`lang`='" . sql_escape($locale) . "'
     ORDER BY `cache_size`.`ordinal` ASC"
);
while ($rSize = sql_fetch_assoc($rsSizes)) {
    $size_arr[] = [
        'value' => $rSize['id'],
        'label' => $rSize['name'],
        'selected' => ($rSize['id'] == $sel_size),
    ];
}
sql_free_result($rsSizes);
$tpl->assign('size_options', $size_arr);

//typeoptions
$type_arr = [['value' => -1, 'label' => t('Please select!'), 'selected' => ($sel_type == -1)]];
$rsTypes = sql(
    "SELECT `cache_type`.`id`,
            IFNULL(`sys_trans_text`.`text`,
            `cache_type`.`en`) AS `name`
     FROM `cache_type`
     LEFT JOIN `sys_trans`
       ON `cache_type`.`trans_id`=`sys_trans`.`id`
     LEFT JOIN `sys_trans_text`
       ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
       AND `sys_trans_text`.`lang`='" . sql_escape($locale) . "'
     ORDER BY `cache_type`.`ordinal` ASC"
);
while ($rType = sql_fetch_assoc($rsTypes)) {
    $type_arr[] = [
        'value' => $rType['id'],
        'label' => $rType['name'],
        'selected' => ($rType['id'] == $sel_type),
    ];
}
sql_free_result($rsTypes);
$tpl->assign('type_options', $type_arr);

if (isset($_POST['show_all_countries_submit'])) {
    $show_all_countries = 1;
} elseif (isset($_POST['show_all_langs_submit'])) {
    $show_all_langs = 1;
}

//check if selected lang is in list_default
if ($show_all_langs == 0) {
    $rs = sql(
        "SELECT `show` FROM `languages_list_default` WHERE `show`='&1' AND `lang`='&2'",
        $sel_lang,
        $locale
    );
    if (mysqli_num_rows($rs) == 0) {
        $show_all_langs = 1;
    }
    sql_free_result($rs);
}

$lang_arr = [];
if ($show_all_langs == 0) {
    $tpl->assign('show_all_langs', '0');
    $tpl->assign(
        'show_all_langs_submit',
        '<input type="submit" name="show_all_langs_submit" value="' . $show_all . '"/>'
    );

    $rs = sql(
        "SELECT `languages`.`short`,
                IFNULL(`sys_trans_text`.`text`,
                `languages`.`name`) AS `name`
         FROM `languages`
         INNER JOIN `languages_list_default`
           ON `languages`.`short`=`languages_list_default`.`show`
         LEFT JOIN `sys_trans`
           ON `languages`.`trans_id`=`sys_trans`.`id`
         LEFT JOIN `sys_trans_text`
           ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
           AND `sys_trans_text`.`lang`='&1'
         WHERE `languages_list_default`.`lang`='&1' ORDER BY `name` ASC",
        $locale
    );
} else {
    $tpl->assign('show_all_langs', '1');
    $tpl->assign('show_all_langs_submit', '');

    $rs = sql(
        "SELECT `languages`.`short`,
         IFNULL(`sys_trans_text`.`text`, `languages`.`name`) AS `name`
         FROM `languages`
         LEFT JOIN `sys_trans`
           ON `languages`.`trans_id`=`sys_trans`.`id`
         LEFT JOIN `sys_trans_text`
           ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
           AND `sys_trans_text`.`lang`='&1'
         ORDER BY `name` ASC",
        $locale
    );
}

while ($record = sql_fetch_assoc($rs)) {
    $lang_arr[] = [
        'value' => $record['short'],
        'label' => $record['name'],
        'selected' => ($record['short'] == $sel_lang),
    ];
}
sql_free_result($rs);
$tpl->assign('lang_options', $lang_arr);

//countryoptions
//check if selected country is in list_default
if ($show_all_countries == 0) {
    $rs = sql(
        "SELECT `show` FROM `countries_list_default` WHERE `show`='&1' AND `lang`='&2'",
        $sel_country,
        $locale
    );
    if (mysqli_num_rows($rs) == 0) {
        $show_all_countries = 1;
    }
    sql_free_result($rs);
}

$country_arr = [];
if ($show_all_countries == 0) {
    $tpl->assign('show_all_countries', '0');
    $tpl->assign(
        'show_all_countries_submit',
        '<input type="submit" id="showallcountries" class="formbutton" name="show_all_countries_submit" value="' . $show_all . '" onclick="submitbutton(\'showallcountries\')" />'
    );

    $rs = sql(
        "SELECT `countries`.`short`,
                IFNULL(`sys_trans_text`.`text`,
                `countries`.`name`) AS `name`
         FROM `countries`
         INNER JOIN `countries_list_default`
           ON `countries_list_default`.`show`=`countries`.`short`
         LEFT JOIN `sys_trans`
           ON `countries`.`trans_id`=`sys_trans`.`id`
         LEFT JOIN `sys_trans_text`
           ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
           AND `sys_trans_text`.`lang`='&1'
         WHERE `countries_list_default`.`lang`='&1'
         ORDER BY `name` ASC",
        $locale
    );
} else {
    $tpl->assign('show_all_countries', '1');
    $tpl->assign('show_all_countries_submit', '');

    $rs = sql(
        "SELECT `countries`.`short`,
                IFNULL(`sys_trans_text`.`text`,
                `countries`.`name`) AS `name`
         FROM `countries`
         LEFT JOIN `sys_trans`
           ON `countries`.`trans_id`=`sys_trans`.`id`
         LEFT JOIN `sys_trans_text`
           ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
           AND `sys_trans_text`.`lang`='&1'
        ORDER BY `name` ASC",
        $locale
    );
}

// build the "country" dropdown list, preselect $sel_country
while ($record = sql_fetch_array($rs)) {
    $country_arr[] = [
        'value' => $record['short'],
        'label' => $record['name'],
        'selected' => ($record['short'] == $sel_country),
    ];
}
sql_free_result($rs);
$tpl->assign('country_options', $country_arr);

// cache-attributes
$cache_attribs = isset($_POST['cache_attribs']) ? mb_split(';', $_POST['cache_attribs']) : [];

// cache-attributes — build pre-rendered HTML (kept as-is for now)
$bBeginLine = true;
$nPrevLineAttrCount = 0;
$nLineAttrCount = 0;

$cache_attrib_list = '';
$cache_attrib_array = '';
$cache_attribs_string = '';

$rsAttrGroup = sql(
    "SELECT `attribute_groups`.`id`,
            IFNULL(`sys_trans_text`.`text`,
            `attribute_groups`.`name`) AS `name`,
            `attribute_categories`.`color`
     FROM `attribute_groups`
     INNER JOIN `attribute_categories`
       ON `attribute_groups`.`category_id`=`attribute_categories`.`id`
     LEFT JOIN `sys_trans`
       ON `attribute_groups`.`trans_id`=`sys_trans`.`id`
     LEFT JOIN `sys_trans_text`
       ON `sys_trans`.`id`=`sys_trans_text`.`trans_id`
       AND `sys_trans_text`.`lang`='&1'
     ORDER BY `attribute_groups`.`category_id` ASC, `attribute_groups`.`id` ASC",
    $locale
);
while ($rAttrGroup = sql_fetch_assoc($rsAttrGroup)) {
    $group_line = '';

    $rs = sql(
        "SELECT `cache_attrib`.`id`,
                IFNULL(`ttname`.`text`,`cache_attrib`.`name`) AS `name`,
                `cache_attrib`.`icon_undef`,
                `cache_attrib`.`icon_large`,
                IFNULL(`ttdesc`.`text`, `cache_attrib`.`html_desc`) AS `html_desc`
         FROM `cache_attrib`
         LEFT JOIN `sys_trans` AS `tname`
           ON `cache_attrib`.`trans_id`=`tname`.`id`
           AND `cache_attrib`.`name`=`tname`.`text`
         LEFT JOIN `sys_trans_text` AS `ttname`
           ON `tname`.`id`=`ttname`.`trans_id`
           AND `ttname`.`lang`='&1'
         LEFT JOIN `sys_trans` AS `tdesc`
           ON `cache_attrib`.`html_desc_trans_id`=`tdesc`.`id`
           AND `cache_attrib`.`html_desc`=`tdesc`.`text`
         LEFT JOIN `sys_trans_text` AS `ttdesc`
           ON `tdesc`.`id`=`ttdesc`.`trans_id`
           AND `ttdesc`.`lang`='&1'
         WHERE `cache_attrib`.`group_id`=" . ((int)$rAttrGroup['id']) . '
         AND NOT IFNULL(`cache_attrib`.`hidden`, 0) = 1
         AND `cache_attrib`.`selectable` != 0
         ORDER BY `cache_attrib`.`group_id`, `cache_attrib`.`id`',
        $locale
    );
    while ($record = sql_fetch_array($rs)) {
        $line = $cache_attrib_pic;

        $line = mb_ereg_replace('{attrib_id}', $record['id'], $line);
        $line = mb_ereg_replace('{attrib_text}', escape_javascript($record['name']), $line);
        if (in_array($record['id'], $cache_attribs)) {
            $line = mb_ereg_replace('{attrib_pic}', $record['icon_large'], $line);
        } else {
            $line = mb_ereg_replace('{attrib_pic}', $record['icon_undef'], $line);
        }
        $line = mb_ereg_replace('{html_desc}', escape_javascript($record['html_desc']), $line);
        $line = mb_ereg_replace('{name}', escape_javascript($record['name']), $line);
        $line = mb_ereg_replace('{color}', $rAttrGroup['color'], $line);
        $group_line .= $line;
        $nLineAttrCount++;

        $line = $cache_attrib_js;
        $line = mb_ereg_replace('{id}', $record['id'], $line);
        if (in_array($record['id'], $cache_attribs)) {
            $line = mb_ereg_replace('{selected}', 1, $line);
        } else {
            $line = mb_ereg_replace('{selected}', 0, $line);
        }
        $line = mb_ereg_replace('{img_undef}', $record['icon_undef'], $line);
        $line = mb_ereg_replace('{img_large}', $record['icon_large'], $line);
        $line = mb_ereg_replace(
            '{conflicting_attribs}',
            implode(',', OcLib2\attribute::getConflictingAttribIds($record['id'])),
            $line
        );
        if ($cache_attrib_array != '') {
            $cache_attrib_array .= ',';
        }
        $cache_attrib_array .= $line;

        if (in_array($record['id'], $cache_attribs)) {
            if ($cache_attribs_string != '') {
                $cache_attribs_string .= ';';
            }
            $cache_attribs_string .= $record['id'];
        }
    }
    sql_free_result($rs);

    if ($group_line != '') {
        $group_img = $cache_attrib_group;
        $group_img = mb_ereg_replace('{color}', $rAttrGroup['color'], $group_img);
        $group_img = mb_ereg_replace('{attribs}', $group_line, $group_img);
        $group_img = mb_ereg_replace(
            '{name}',
            htmlspecialchars(
                $rAttrGroup['name'],
                ENT_COMPAT,
                'UTF-8'
            ),
            $group_img
        );

        if ($bBeginLine) {
            $cache_attrib_list .= '<div class="attribswide">';
            $bBeginLine = false;
        }

        $cache_attrib_list .= $group_img;
        $nPrevLineAttrCount += $nLineAttrCount;

        $nLineAttrCount = 0;
    }
}
sql_free_result($rsAttrGroup);
if (!$bBeginLine) {
    $cache_attrib_list .= '</div>';
}

$tpl->assign('cache_attrib_list', $cache_attrib_list);
$tpl->assign('jsattributes_array', $cache_attrib_array);
$tpl->assign('cache_attribs', $cache_attribs_string);

$tpl->assign('firstcache_note', mb_ereg_replace('%1', $opt['page']['sitename'], $firstcache_note));

if (isset($_POST['submitform'])) {  // Ocprop
    // check the entered data

    // check coordinates
    if ($lat_h != '' || $lat_min != '') {
        if (!mb_ereg_match('^[0-9]{1,3}$', $lat_h)) {
            $tpl->assign('coord_error', $error_lat_not_ok);
            $error = true;
            $lat_h_not_ok = true;
        } else {
            if (($lat_h >= 0) && ($lat_h < 90)) {
                $lat_h_not_ok = false;
            } else {
                $tpl->assign('coord_error', $error_lat_not_ok);
                $error = true;
                $lat_h_not_ok = true;
            }
        }

        if (is_numeric($lat_min)) {
            if (($lat_min >= 0) && ($lat_min < 60)) {
                $lat_min_not_ok = false;
            } else {
                $tpl->assign('coord_error', $error_lat_not_ok);
                $error = true;
                $lat_min_not_ok = true;
            }
        } else {
            $tpl->assign('coord_error', $error_lat_not_ok);
            $error = true;
            $lat_min_not_ok = true;
        }

        $latitude = $lat_h + $lat_min / 60;
        if ($latNS == 'S') {
            $latitude = -$latitude;
        }

        if ($latitude == 0) {
            $tpl->assign('coord_error', $error_lat_not_ok);
            $error = true;
            $lat_min_not_ok = true;
        }
    } else {
        $tpl->assign('coord_error', $error_lat_not_ok);
        $lat_h_not_ok = true;
        $lat_min_not_ok = true;
    }

    if ($lon_h != '' || $lon_min != '') {
        if (!mb_ereg_match('^[0-9]{1,3}$', $lon_h)) {
            $tpl->assign('coord_error', $error_long_not_ok);
            $error = true;
            $lon_h_not_ok = true;
        } else {
            if (($lon_h >= 0) && ($lon_h < 180)) {
                $lon_h_not_ok = false;
            } else {
                $tpl->assign('coord_error', $error_long_not_ok);
                $error = true;
                $lon_h_not_ok = true;
            }
        }

        if (is_numeric($lon_min)) {
            if (($lon_min >= 0) && ($lon_min < 60)) {
                $lon_min_not_ok = false;
            } else {
                $tpl->assign('coord_error', $error_long_not_ok);
                $error = true;
                $lon_min_not_ok = true;
            }
        } else {
            $tpl->assign('coord_error', $error_long_not_ok);
            $error = true;
            $lon_min_not_ok = true;
        }

        $longitude = $lon_h + $lon_min / 60;
        if ($lon_EW == 'W') {
            $longitude = -$longitude;
        }

        if ($longitude == 0) {
            $tpl->assign('coord_error', $error_long_not_ok);
            $error = true;
            $lon_min_not_ok = true;
        }
    } else {
        $tpl->assign('coord_error', $error_long_not_ok);
        $lon_h_not_ok = true;
        $lon_min_not_ok = true;
    }

    $lon_not_ok = (isset($lon_min_not_ok) && $lon_min_not_ok) || (isset($lon_h_not_ok) && $lon_h_not_ok);
    $lat_not_ok = (isset($lat_min_not_ok) && $lat_min_not_ok) || (isset($lat_h_not_ok) && $lat_h_not_ok);

    // check for duplicate coords
    if (!($lon_not_ok || $lat_not_ok)) {
        $duplicate_wpoc = sql_value(
            "SELECT MIN(wp_oc)
             FROM `caches`
             WHERE `status`=1
             AND ROUND(`longitude`,6)=ROUND('&1',6)
             AND ROUND(`latitude`,6)=ROUND('&2',6)",
            null,
            $longitude,
            $latitude
        );
        if ($duplicate_wpoc) {
            $tpl->assign('coord_error', mb_ereg_replace('%1', $duplicate_wpoc, $error_duplicate_coords));
            $lon_not_ok = true;
        }
    }

    //check effort
    $time_not_ok = true;
    if (is_numeric($search_time) || ($search_time == '')) {
        $time_not_ok = false;
    }
    if ($time_not_ok) {
        $tpl->assign('effort_message', $time_not_ok_message);
        $error = true;
    }
    $way_length_not_ok = true;
    if (is_numeric($way_length) || ($search_time == '')) {
        $way_length_not_ok = false;
    }
    if ($way_length_not_ok) {
        $tpl->assign('effort_message', $way_length_not_ok_message);
        $error = true;
    }

    //check hidden_since
    $hidden_date_not_ok = true;
    if (is_numeric($hidden_day) && is_numeric($hidden_month) && is_numeric($hidden_year)) {
        $hidden_date_not_ok = (checkdate($hidden_month, $hidden_day, $hidden_year) == false);
    }
    if ($hidden_date_not_ok) {
        $tpl->assign('hidden_since_message', $date_not_ok_message);
        $error = true;
    } else if ($publish != 'notnow') {
        $hidden_date = mktime(0, 0, 0, $hidden_month, $hidden_day, $hidden_year);
        if ($publish == 'later') {
            // Activation hour can be ignored here. This simplifies checking event dates.
            $publish_date = mktime(0, 0, 0, $activate_month, $activate_day, $activate_year);
        } else {
            $publish_date = time();
        }
        if ($sel_type == 6 && $hidden_date < $publish_date) {
            $tpl->assign('hidden_since_message', $event_before_publish_message);
            $hidden_date_not_ok = true;
            $error = true;
        } elseif ($sel_type != 6 && $hidden_date > $publish_date) {
            $tpl->assign('hidden_since_message', $hide_after_publish_message);
            $hidden_date_not_ok = true;
            $error = true;
        }
    }

    //check GC waypoint
    $wpgc_not_ok = $wp_gc != "" && !preg_match("/^(?:GC|CX)[0-9A-Z]{3,6}$/", $wp_gc);
    if ($wpgc_not_ok) {
        $tpl->assign('wpgc_message', $bad_wpgc_message);
        $error = true;
    }

    //check date_activate
    $activation_date_not_ok = true;
    if (is_numeric($activate_day)
        && is_numeric($activate_month)
        && is_numeric($activate_year)
        && is_numeric($activate_hour)
    ) {
        $activation_date_not_ok = ((checkdate($activate_month, $activate_day, $activate_year) == false)
            || $activate_hour < 0 || $activate_hour > 23);
    }
    if (!$activation_date_not_ok) {
        if (!($publish == 'now2' || $publish == 'later' || $publish == 'notnow')) {
            $activation_date_not_ok = true;
        }
    }
    if ($activation_date_not_ok) {
        $tpl->assign('activate_on_message', $date_not_ok_message);
        $error = true;
    }

    //name
    if ($name == '') {
        $tpl->assign('name_message', $name_not_ok_message);
        $error = true;
        $name_not_ok = true;
    } else {
        $name_not_ok = false;
    }

    //tos
    if ($tos != 1) {
        $tpl->assign('tos_message', $tos_not_ok_message);
        $error = true;
        $tos_not_ok = true;
    } else {
        $tos_not_ok = false;
    }

    //cache-size
    $size_not_ok = false;
    if ($sel_size == -1) {
        $tpl->assign('size_message', $size_not_ok_message);
        $error = true;
        $size_not_ok = true;
    }

    //cache-type
    $type_not_ok = false;
    if ($sel_type == -1) {
        $tpl->assign('type_message', $type_not_ok_message);
        $error = true;
        $type_not_ok = true;
    }

    if ($sel_size != 7 && ($sel_type == 4 || $sel_type == 5)) {
        if (!$size_not_ok) {
            $tpl->assign('size_message', $sizemismatch_message);
        }
        $error = true;
        $size_not_ok = true;
    }

    //difficulty / terrain
    $diff_not_ok = false;
    if ($difficulty < 2 || $difficulty > 10 || $terrain < 2 || $terrain > 10) {
        $tpl->assign('diff_message', $diff_not_ok_message);
        $error = true;
        $diff_not_ok = true;
    }

    // attributes
    $attribs_not_ok = false;
    if (in_array(ATTRIB_ID_SAFARI, $cache_attribs) && $sel_type != 4) {
        $tpl->assign('safari_message', $safari_not_allowed_message);
        $error = true;
        $attribs_not_ok = true;
    } else {
        $tpl->assign('safari_message', '');
    }

    //no errors?
    if (!($tos_not_ok
        || $name_not_ok
        || $hidden_date_not_ok
        || $activation_date_not_ok
        || $lon_not_ok
        || $lat_not_ok
        || $time_not_ok
        || $way_length_not_ok
        || $size_not_ok
        || $type_not_ok
        || $diff_not_ok
        || $attribs_not_ok
        || $wpgc_not_ok)
    ) {
        //sel_status
        $now = getdate();
        $today = mktime(0, 0, 0, $now['mon'], $now['mday'], $now['year']);
        $hidden_date = mktime(0, 0, 0, $hidden_month, $hidden_day, $hidden_year);

        $sel_status = 1; //available
        if (($hidden_date > $today) && ($sel_type != 6)) {
            $sel_status = 2; //currently not available
        }

        if ($publish == 'now2') {
            $activation_date = 'NULL';
        } elseif ($publish == 'later') {
            $sel_status = 5;
            $activation_date =
                "'" . date(
                    'Y-m-d H:i:s',
                    mktime(
                        $activate_hour,
                        0,
                        0,
                        $activate_month,
                        $activate_day,
                        $activate_year
                    )
                ) . "'";
        } elseif ($publish == 'notnow') {
            $sel_status = 5;
            $activation_date = 'NULL';
        } else {
            // should never happen
            $activation_date = 'NULL';
        }

        //add record to caches table
        sql(
            "INSERT INTO `caches` (
                `cache_id`,
                `user_id`,
                `name`,
                `longitude`,
                `latitude`,
                `type` ,
                `status` ,
                `country` ,
                `date_hidden` ,
                `date_activate` ,
                `size` ,
                `difficulty` ,
                `terrain`,
                `logpw`,
                `search_time`,
                `way_length`,
                `wp_gc`,
                `node`
            ) VALUES (
                '',
                '&1',
                '&2',
                '&3',
                '&4',
                '&5',
                '&6',
                '&7',
                '&8',
                $activation_date,
                '&9',
                '&10',
                '&11',
                '&12',
                '&13',
                '&14',
                '&15',
                '&16'
                )",
            $login->userid,
            $name,
            $longitude,
            $latitude,
            $sel_type,
            $sel_status,
            $sel_country,
            date('Y-m-d', $hidden_date),
            $sel_size,
            $difficulty,
            $terrain,
            $log_pw,
            $search_time,
            $way_length,
            $wp_gc,
            $oc_nodeid
        );
        $cache_id = sql_insert_id();

        //add record to cache_desc table
        sql(
            "INSERT INTO `cache_desc` (
                `id`,
                `cache_id`,
                `language`,
                `desc`,
                `desc_html`,
                `hint`,
                `short_desc`,
                `last_modified`,
                `desc_htmledit`,
                `node`
            ) VALUES (
                '',
                '&1',
                '&2',
                '&3',
                '&4',
                '&5',
                '&6',
                NOW(),
                '&7',
                '&8'
            )",
            $cache_id,
            $sel_lang,
            $desc,
            1,
            nl2br(htmlspecialchars($hints, ENT_COMPAT, 'UTF-8')),
            $short_desc,
            ($descMode == EditorConstants::EDITOR_MODE) ? 1 : 0,
            $oc_nodeid
        );

        $cacheAttributesCount = count($cache_attribs);
        // insert cache-attributes
        for ($i = 0; $i < $cacheAttributesCount; $i++) {
            if (((int)$cache_attribs[$i]) > 0) {
                sql(
                    "INSERT INTO `caches_attributes` (
                         `cache_id`,
                         `attrib_id`
                     ) VALUES (
                         '&1',
                         '&2'
                     )",
                    $cache_id,
                    (int)$cache_attribs[$i]
                );
            }
        }

        // only if cache is published NOW or activate_date is in the past
        if ($publish == 'now2' ||
            ($publish == 'later'
                && mktime($activate_hour, 0, 0, $activate_month, $activate_day, $activate_year) <= $today)
        ) {
            StatisticPicture::deleteStatisticPicture($login->userid);
        }

        // redirection
        $tpl->redirect('viewcache.php?cacheid=' . urlencode($cache_id));
    } else {
        $tpl->assign('general_error', $error_general);
    }
}

$tpl->assign('scrollposx', (int)($_REQUEST['scrollposx'] ?? 0));
$tpl->assign('scrollposy', (int)($_REQUEST['scrollposy'] ?? 0));

// make the template and send it out
$tpl->display();
