<?php
/****************************************************************************
 * For license information see LICENSE.md
 *
 *
 * functions for the full text search-engine
 ****************************************************************************/

/* begin conversion rules */

$ftSearchSimpleRules[] = [
    'qu',
    'k'
];
$ftSearchSimpleRules[] = [
    'ts',
    'z'
];
$ftSearchSimpleRules[] = [
    'tz',
    'z'
];
$ftSearchSimpleRules[] = [
    'alp',
    'alb'
];
$ftSearchSimpleRules[] = [
    'y',
    'i'
];
$ftSearchSimpleRules[] = [
    'ai',
    'ei'
];
$ftSearchSimpleRules[] = [
    'ou',
    'u'
];
$ftSearchSimpleRules[] = [
    'th',
    't'
];
$ftSearchSimpleRules[] = [
    'ph',
    'f'
];
$ftSearchSimpleRules[] = [
    'oh',
    'o'
];
$ftSearchSimpleRules[] = [
    'ah',
    'a'
];
$ftSearchSimpleRules[] = [
    'eh',
    'e'
];
$ftSearchSimpleRules[] = [
    'aux',
    'o'
];
$ftSearchSimpleRules[] = [
    'eau',
    'o'
];
$ftSearchSimpleRules[] = [
    'eux',
    'oe'
];
$ftSearchSimpleRules[] = [
    '^ch',
    'sch'
];
$ftSearchSimpleRules[] = [
    'ck',
    'k'
];
$ftSearchSimpleRules[] = [
    'ie',
    'i'
];
$ftSearchSimpleRules[] = [
    'ih',
    'i'
];
$ftSearchSimpleRules[] = [
    'ent',
    'end'
];
$ftSearchSimpleRules[] = [
    'uh',
    'u'
];
$ftSearchSimpleRules[] = [
    'sh',
    'sch'
];
$ftSearchSimpleRules[] = [
    'ver',
    'wer'
];
$ftSearchSimpleRules[] = [
    'dt',
    't'
];
$ftSearchSimpleRules[] = [
    'hard',
    'hart'
];
$ftSearchSimpleRules[] = [
    'egg',
    'ek'
];
$ftSearchSimpleRules[] = [
    'eg',
    'ek'
];
$ftSearchSimpleRules[] = [
    'cr',
    'kr'
];
$ftSearchSimpleRules[] = [
    'ca',
    'ka'
];
$ftSearchSimpleRules[] = [
    'ce',
    'ze'
];
$ftSearchSimpleRules[] = [
    'x',
    'ks'
];
$ftSearchSimpleRules[] = [
    've',
    'we'
];
$ftSearchSimpleRules[] = [
    'va',
    'wa'
];

/* end conversion rules */

/**
 * @param $str
 *
 * @return array
 */
function ftsearch_hash(&$str)
{
    $astr = ftsearch_split($str, true);
    foreach ($astr as $k => $s) {
        if (strlen($s) > 2) {
            $astr[$k] = sprintf("%u", crc32($s));
        } else {
            unset($astr[$k]);
        }
    }

    return $astr;
}

/**
 * str = single word
 * @param $str
 * @param $simple
 *
 * @return array
 */
function ftsearch_split(&$str, $simple)
{
    global $ftsearch_ignores;

    // interpunktion
    $str = mb_ereg_replace('\\?', ' ', $str);
    $str = mb_ereg_replace('\\)', ' ', $str);
    $str = mb_ereg_replace('\\(', ' ', $str);
    $str = mb_ereg_replace('\\.', ' ', $str);
    $str = mb_ereg_replace('´', ' ', $str);
    $str = mb_ereg_replace('`', ' ', $str);
    $str = mb_ereg_replace('\'', ' ', $str);
    $str = mb_ereg_replace('/', ' ', $str);
    $str = mb_ereg_replace(':', ' ', $str);
    $str = mb_ereg_replace(',', ' ', $str);
    $str = mb_ereg_replace("\r\n", ' ', $str);
    $str = mb_ereg_replace("\n", ' ', $str);
    $str = mb_ereg_replace("\r", ' ', $str);

    $ostr = '';
    while ($ostr != $str) {
        $ostr = $str;
        $str = mb_ereg_replace('  ', ' ', $str);
    }

    $astr = mb_split(' ', $str);
    $str = '';

    ftsearch_load_ignores();
    for ($i = count($astr) - 1; $i >= 0; $i --) {
        // ignore?
        if (array_search(mb_strtolower($astr[$i]), $ftsearch_ignores) !== false) {
            unset($astr[$i]);
        } else {
            if ($simple) {
                $astr[$i] = ftsearch_text2simple($astr[$i]);
            }

            if ($astr[$i] == '') {
                unset($astr[$i]);
            }
        }
    }

    return $astr;
}

function ftsearch_load_ignores()
{
    global $ftsearch_ignores;
    global $ftsearch_ignores_loaded;

    if ($ftsearch_ignores_loaded != true) {
        $ftsearch_ignores = [];

        $rs = sql('SELECT `word` FROM `search_ignore`');
        while ($r = sql_fetch_assoc($rs)) {
            $ftsearch_ignores[] = $r['word'];
        }
        sql_free_result($rs);

        $ftsearch_ignores_loaded = true;
    }
}

/**
 * str = single word
 * @param $str
 *
 * @return mixed|string
 */
function ftsearch_text2simple($str)
{
    global $ftSearchSimpleRules;

    $str = ftsearch_text2sort($str);

    // regeln anwenden
    foreach ($ftSearchSimpleRules as $rule) {
        $str = mb_ereg_replace($rule[0], $rule[1], $str);
    }

    // doppelte chars ersetzen
    $ordZ = ord('z');
    for ($c = ord('a'); $c <= $ordZ; $c ++) {
        $old_str = '';
        while ($old_str !== $str) {
            $old_str = $str;
            $str = mb_ereg_replace(chr($c) . chr($c), chr($c), $str);
        }
    }

    return $str;
}

/**
 * str = single word
 * @param $str
 *
 * @return mixed|string
 */
function ftsearch_text2sort($str)
{
    $str = mb_strtolower($str);

    // deutsches
    $str = mb_ereg_replace('ä', 'ae', $str);
    $str = mb_ereg_replace('ö', 'oe', $str);
    $str = mb_ereg_replace('ü', 'ue', $str);
    $str = mb_ereg_replace('Ä', 'ae', $str);
    $str = mb_ereg_replace('Ö', 'oe', $str);
    $str = mb_ereg_replace('Ü', 'ue', $str);
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

    // sonstiges
    $str = mb_ereg_replace('[^A-Za-z ]', '', $str);

    return $str;
}

function ftsearch_refresh()
{
    ftsearch_refresh_all_caches();
    ftsearch_refresh_all_cache_descs();
    ftsearch_refresh_all_pictures();
    ftsearch_refresh_all_cache_logs();
}

function ftsearch_refresh_all_caches()
{
    $rs = sql(
        'SELECT `caches`.`cache_id`
         FROM `caches`
         LEFT JOIN `search_index_times`
             ON `caches`.`cache_id`=`search_index_times`.`object_id`
             AND 2 =`search_index_times`.`object_type`
         WHERE `caches`.`status`!= 5
         AND ISNULL(`search_index_times`.`object_id`)
         UNION DISTINCT
         SELECT `caches`.`cache_id`
         FROM `caches`
         INNER JOIN `search_index_times`
             ON `search_index_times`.`object_type`=2
             AND `caches`.`cache_id`=`search_index_times`.`object_id`
         WHERE `caches`.`last_modified` > `search_index_times`.`last_refresh`
             AND `caches`.`status`!=5'
    );
    while ($r = sql_fetch_assoc($rs)) {
        ftsearch_refresh_cache($r['cache_id']);
    }
    sql_free_result($rs);
}

/**
 * @param $cache_id
 */
function ftsearch_refresh_cache($cache_id)
{
    $rs = sql("SELECT `name`, `last_modified` FROM `caches` WHERE `cache_id`='&1'", $cache_id);
    if ($r = sql_fetch_assoc($rs)) {
        ftsearch_set_entries(2, $cache_id, $cache_id, $r['name'], $r['last_modified']);
    }
    sql_free_result($rs);
}


function ftsearch_refresh_all_cache_descs()
{
    $rs = sql(
        'SELECT `cache_desc`.`id`
         FROM `cache_desc`
         INNER JOIN `caches`
             ON `caches`.`cache_id`=`cache_desc`.`cache_id`
         LEFT JOIN `search_index_times`
             ON `cache_desc`.`id`=`search_index_times`.`object_id`
             AND 3=`search_index_times`.`object_type`
         WHERE `caches`.`status`!= 5
             AND ISNULL(`search_index_times`.`object_id`)
         UNION DISTINCT
         SELECT `cache_desc`.`id`
         FROM `cache_desc`
         INNER JOIN `caches`
             ON `caches`.`cache_id`=`cache_desc`.`cache_id`
         INNER JOIN `search_index_times`
             ON `search_index_times`.`object_type` = 3
             AND `cache_desc`.`id`=`search_index_times`.`object_id`
         WHERE `cache_desc`.`last_modified`>`search_index_times`.`last_refresh`
             AND `caches`.`status`!=5'
    );
    while ($r = sql_fetch_assoc($rs)) {
        ftsearch_refresh_cache_desc($r['id']);
    }
    sql_free_result($rs);
}

/**
 * @param $id
 */
function ftsearch_refresh_cache_desc($id)
{
    $rs = sql(
        "
        SELECT
          `cache_id`,
          CONCAT(`desc`, ' ', `short_desc`) AS `desc`,
          `last_modified`
        FROM `cache_desc`
        WHERE `id`='&1'",
        $id
    );
    if ($r = sql_fetch_assoc($rs)) {
        $r['desc'] = ftsearch_strip_html($r['desc']);
        ftsearch_set_entries(3, $id, $r['cache_id'], $r['desc'], $r['last_modified']);
    }
    sql_free_result($rs);
}

function ftsearch_refresh_all_pictures()
{
    $rs = sql(
        '
        SELECT `pictures`.`id`
        FROM `pictures`
        LEFT JOIN `search_index_times`
            ON `pictures`.`id`=`search_index_times`.`object_id`
            AND 6=`search_index_times`.`object_type`
        WHERE ISNULL(`search_index_times`.`object_id`)
        UNION DISTINCT
        SELECT `pictures`.`id`
        FROM `pictures`
        INNER JOIN `search_index_times`
            ON `search_index_times`.`object_type`= 6
            AND `pictures`.`id`=`search_index_times`.`object_id`
        WHERE `pictures`.`last_modified`>`search_index_times`.`last_refresh`'
    );
    while ($r = sql_fetch_assoc($rs)) {
        ftsearch_refresh_picture($r['id']);
    }
    sql_free_result($rs);
}

/**
 * @param $id
 */
function ftsearch_refresh_picture($id)
{
    $rs = sql(
        "
        SELECT
            `caches`.`cache_id`,
            `pictures`.`title`,
            `pictures`.`last_modified`
        FROM `pictures`
        INNER JOIN `caches`
            ON `pictures`.`object_type`=2
            AND `caches`.`cache_id`=`pictures`.`object_id`
        WHERE `pictures`.`id`='&1'
        UNION DISTINCT
        SELECT
            `cache_logs`.`cache_id`,
            `pictures`.`title`,
            `pictures`.`last_modified`
        FROM `pictures`
        INNER JOIN `cache_logs`
            ON `pictures`.`object_type`= 1
            AND `cache_logs`.`id`=`pictures`.`object_id`
        WHERE `pictures`.`id`='&1'
        LIMIT 1",
        $id
    );
    if ($r = sql_fetch_assoc($rs)) {
        ftsearch_set_entries(6, $id, $r['cache_id'], $r['title'], $r['last_modified']);
    }
    sql_free_result($rs);
}

function ftsearch_refresh_all_cache_logs()
{
    $rs = sql(
        '
        SELECT `cache_logs`.`id`
        FROM `cache_logs`
        LEFT JOIN `search_index_times`
            ON `cache_logs`.`id`=`search_index_times`.`object_id`
            AND 1=`search_index_times`.`object_type`
        WHERE ISNULL(`search_index_times`.`object_id`)
        UNION DISTINCT
        SELECT `cache_logs`.`id`
        FROM `cache_logs`
        INNER JOIN `search_index_times`
            ON `search_index_times`.`object_type`= 1
            AND `cache_logs`.`id`=`search_index_times`.`object_id`
        WHERE `cache_logs`.`last_modified`>`search_index_times`.`last_refresh`'
    );
    while ($r = sql_fetch_assoc($rs)) {
        ftsearch_refresh_cache_logs($r['id']);
    }
    sql_free_result($rs);
}

function ftsearch_refresh_cache_logs($id)
{
    $rs = sql("SELECT `cache_id`, `text`, `last_modified` FROM `cache_logs` WHERE `id`='&1'", $id);
    if ($r = sql_fetch_assoc($rs)) {
        $r['text'] = ftsearch_strip_html($r['text']);
        ftsearch_set_entries(1, $id, $r['cache_id'], $r['text'], $r['last_modified']);
    }
    sql_free_result($rs);
}

/**
 * @param $object_type
 * @param $object_id
 * @param $cache_id
 */
function ftsearch_delete_entries($object_type, $object_id, $cache_id)
{
    sql("DELETE FROM `search_index` WHERE `object_type`='&1' AND `cache_id`='&2'", $object_type, $cache_id);
    sql("DELETE FROM `search_index_times` WHERE `object_type`='&1' AND `object_id`='&2'", $object_type, $object_id);
}

/**
 * @param $object_type
 * @param $object_id
 * @param $cache_id
 * @param $text
 * @param $last_modified
 */
function ftsearch_set_entries($object_type, $object_id, $cache_id, &$text, $last_modified)
{
    ftsearch_delete_entries($object_type, $object_id, $cache_id);

    $ahash = ftsearch_hash($text);
    foreach ($ahash as $k => $h) {
        sql(
            "INSERT INTO `search_index` (`object_type`, `cache_id`, `hash`, `count`)
            VALUES ('&1', '&2', '&3', '&4') ON DUPLICATE KEY UPDATE `count`=`count`+1",
            $object_type,
            $cache_id,
            $h,
            1
        );
    }
    sql(
        "INSERT INTO `search_index_times` (`object_id`, `object_type`, `last_refresh`)
        VALUES ('&1', '&2', '&3') ON DUPLICATE KEY UPDATE `last_refresh`='&3'",
        $object_id,
        $object_type,
        $last_modified
    );
}

/**
 * @param $text
 *
 * @return mixed|string
 */
function ftsearch_strip_html($text)
{
    $text = str_replace(["\n", "\r", '<br />', '<br/>', '<br>'], ' ', $text);
    $text = strip_tags($text);
    $text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');

    return $text;
}
