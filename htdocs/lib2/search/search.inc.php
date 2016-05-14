<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * functions for the search-engine
 ****************************************************************************/

/* begin conversion rules */

$search_simplerules[] = [
    'qu',
    'k'
];
$search_simplerules[] = [
    'ts',
    'z'
];
$search_simplerules[] = [
    'tz',
    'z'
];
$search_simplerules[] = [
    'alp',
    'alb'
];
$search_simplerules[] = [
    'y',
    'i'
];
$search_simplerules[] = [
    'ai',
    'ei'
];
$search_simplerules[] = [
    'ou',
    'u'
];
$search_simplerules[] = [
    'th',
    't'
];
$search_simplerules[] = [
    'ph',
    'f'
];
$search_simplerules[] = [
    'oh',
    'o'
];
$search_simplerules[] = [
    'ah',
    'a'
];
$search_simplerules[] = [
    'eh',
    'e'
];
$search_simplerules[] = [
    'aux',
    'o'
];
$search_simplerules[] = [
    'eau',
    'o'
];
$search_simplerules[] = [
    'eux',
    'oe'
];
$search_simplerules[] = [
    '^ch',
    'sch'
];
$search_simplerules[] = [
    'ck',
    'k'
];
$search_simplerules[] = [
    'ie',
    'i'
];
$search_simplerules[] = [
    'ih',
    'i'
];
$search_simplerules[] = [
    'ent',
    'end'
];
$search_simplerules[] = [
    'uh',
    'u'
];
$search_simplerules[] = [
    'sh',
    'sch'
];
$search_simplerules[] = [
    'ver',
    'wer'
];
$search_simplerules[] = [
    'dt',
    't'
];
$search_simplerules[] = [
    'hard',
    'hart'
];
$search_simplerules[] = [
    'egg',
    'ek'
];
$search_simplerules[] = [
    'eg',
    'ek'
];
$search_simplerules[] = [
    'cr',
    'kr'
];
$search_simplerules[] = [
    'ca',
    'ka'
];
$search_simplerules[] = [
    'ce',
    'ze'
];
$search_simplerules[] = [
    'x',
    'ks'
];
$search_simplerules[] = [
    've',
    'we'
];
$search_simplerules[] = [
    'va',
    'wa'
];

/* end conversion rules */

function search_text2simple($str)
{
    global $search_simplerules;

    $str = search_text2sort($str);

    // regeln anwenden
    foreach ($search_simplerules as $rule) {
        $str = mb_ereg_replace($rule[0], $rule[1], $str);
    }

    // doppelte chars ersetzen
    for ($c = ord('a'); $c <= ord('z'); $c ++) {
        $str = mb_ereg_replace(chr($c) . chr($c), chr($c), $str);
    }

    return $str;
}

function search_text2sort($str, $gns_syntax = false)
{
    $str = mb_strtolower($str);

    // alles was nicht a-z ist ersetzen
    $str = mb_ereg_replace('0', '', $str);
    $str = mb_ereg_replace('1', '', $str);
    $str = mb_ereg_replace('2', '', $str);
    $str = mb_ereg_replace('3', '', $str);
    $str = mb_ereg_replace('4', '', $str);
    $str = mb_ereg_replace('5', '', $str);
    $str = mb_ereg_replace('6', '', $str);
    $str = mb_ereg_replace('7', '', $str);
    $str = mb_ereg_replace('8', '', $str);
    $str = mb_ereg_replace('9', '', $str);

    // deutsches
    if ($gns_syntax) {
        $str = mb_ereg_replace('ä', 'a', $str);
        $str = mb_ereg_replace('ö', 'o', $str);
        $str = mb_ereg_replace('ü', 'u', $str);
        $str = mb_ereg_replace('Ä', 'a', $str);
        $str = mb_ereg_replace('Ö', 'o', $str);
        $str = mb_ereg_replace('Ü', 'u', $str);
    } else {
        $str = mb_ereg_replace('ä', 'ae', $str);
        $str = mb_ereg_replace('ö', 'oe', $str);
        $str = mb_ereg_replace('ü', 'ue', $str);
        $str = mb_ereg_replace('Ä', 'ae', $str);
        $str = mb_ereg_replace('Ö', 'oe', $str);
        $str = mb_ereg_replace('Ü', 'ue', $str);
    }
    $str = mb_ereg_replace('ß', 'ss', $str);

    // akzente usw.
    $str = mb_ereg_replace('à', 'a', $str);
    $str = mb_ereg_replace('á', 'a', $str);
    $str = mb_ereg_replace('â', 'a', $str);
    $str = mb_ereg_replace('è', 'e', $str);
    $str = mb_ereg_replace('é', 'e', $str);
    $str = mb_ereg_replace('ë', 'e', $str);
    $str = mb_ereg_replace('É', 'e', $str);
    $str = mb_ereg_replace('ô', 'o', $str);
    $str = mb_ereg_replace('ó', 'o', $str);
    $str = mb_ereg_replace('ò', 'o', $str);
    $str = mb_ereg_replace('ê', 'e', $str);
    $str = mb_ereg_replace('ě', 'e', $str);
    $str = mb_ereg_replace('û', 'u', $str);
    $str = mb_ereg_replace('ç', 'c', $str);
    $str = mb_ereg_replace('c', 'c', $str);
    $str = mb_ereg_replace('ć', 'c', $str);
    $str = mb_ereg_replace('î', 'i', $str);
    $str = mb_ereg_replace('ï', 'i', $str);
    $str = mb_ereg_replace('ì', 'i', $str);
    $str = mb_ereg_replace('í', 'i', $str);
    $str = mb_ereg_replace('ł', 'l', $str);
    $str = mb_ereg_replace('š', 's', $str);
    $str = mb_ereg_replace('Š', 's', $str);
    $str = mb_ereg_replace('u', 'u', $str);
    $str = mb_ereg_replace('ý', 'y', $str);
    $str = mb_ereg_replace('ž', 'z', $str);
    $str = mb_ereg_replace('Ž', 'Z', $str);

    $str = mb_ereg_replace('Æ', 'ae', $str);
    $str = mb_ereg_replace('æ', 'ae', $str);
    $str = mb_ereg_replace('œ', 'oe', $str);

    // interpunktion
    $str = mb_ereg_replace('\\?', '', $str);
    $str = mb_ereg_replace('\\)', '', $str);
    $str = mb_ereg_replace('\\(', '', $str);
    $str = mb_ereg_replace('\\.', ' ', $str);
    $str = mb_ereg_replace('´', ' ', $str);
    $str = mb_ereg_replace('`', ' ', $str);
    $str = mb_ereg_replace('\'', ' ', $str);

    // sonstiges
    $str = str_replace('', '', $str);

    // der rest
    $str = mb_ereg_replace('[^a-z]', '', $str);

    return $str;
}


// select the preferable description language for the cache in $rCache
// and replace the desc texts in $rCache by the prefered description's data;
// see http://redmine.opencaching.de/issues/852

function get_locale_desc($rCache)
{
    global $opt;

    $desclangs = ',' . $rCache['desc_languages'] . ',';
    $desclang = $rCache['desc_language'];

    if (strpos($desclangs, ',' . $opt['template']['locale'] . ',') !== false) {
        $desclang = $opt['template']['locale'];
    } elseif (strpos($desclangs, ',' . $opt['template']['default']['locale'] . ',') !== false) {
        $desclang = $opt['template']['default']['locale'];
    } elseif (strpos($desclangs, ',' . $opt['template']['default']['fallback_locale'] . ',') !== false) {
        $desclang = $opt['template']['default']['fallback_locale'];
    } elseif (strpos($desclangs, ',EN,') !== false) {
        $desclang = 'EN';
    }

    if ($desclang != $rCache['desc_language']) {
        $rs = sql(
            "
            SELECT `desc`, `short_desc`, `hint`
            FROM `cache_desc`
            WHERE `cache_id`='&1'
            AND `language`='&2'",
            $rCache['cacheid'],
            $desclang
        );
        if ($r = sql_fetch_assoc($rs)) {
            $rCache['desc'] = $r['desc'];
            $rCache['short_desc'] = $r['short_desc'];
            $rCache['hint'] = $r['hint'];
        }
        sql_free_result($rs);
    }

    return $rCache;
}
