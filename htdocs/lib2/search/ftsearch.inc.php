<?php
/****************************************************************************
 * For license information see LICENSE.md
 *
 *
 * functions for the full text search-engine
 ****************************************************************************/

/* begin conversion rules */

$ftSearchSimpleRules = [
    ['qu', 'k'],
    ['ts', 'z'],
    ['tz', 'z'],
    ['alp', 'alb'],
    ['y', 'i'],
    ['ai', 'ei'],
    ['ou', 'u'],
    ['th', 't'],
    ['ph', 'f'],
    ['oh', 'o'],
    ['ah', 'a'],
    ['eh', 'e'],
    ['aux', 'o'],
    ['eau', 'o'],
    ['eux', 'oe'],
    ['ch', 'sch'],
    ['ck', 'k'],
    ['ie', 'i'],
    ['ih', 'i'],
    ['ent', 'end'],
    ['uh', 'u'],
    ['sh', 'sch'],
    ['ver', 'wer'],
    ['dt', 't'],
    ['hard', 'hart'],
    ['egg', 'ek'],
    ['eg', 'ek'],
    ['cr', 'kr'],
    ['ca', 'ka'],
    ['ce', 'ze'],
    ['x', 'ks'],
    ['ve', 'we'],
    ['va', 'wa'],
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
            $astr[$k] = sprintf('%u', crc32($s));
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

function ftsearch_load_ignores(): void
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

/**
 * @param int $maxentries
 */
function ftsearch_refresh($maxentries = PHP_INT_MAX): void
{
    // The search index needs to calculated for all objects-types-by-cache which
    // are in `search_index_times` table. While we are doing that, new entries may
    // be added to `search_index_times`. To ensure consistency, we create a "snapshot"
    // of the current set of entries, for which we will (re)calculate the index.

    $snapshot_time = sql_value('SELECT NOW()', null);
    sql(
        "UPDATE `search_index_times`
         SET `last_refresh`='&1'
         ORDER BY `last_refresh`
         LIMIT &2",
        $snapshot_time,
        $maxentries
    );

    // Now we will process all the snapshotted entries.

    $rs = sql(
        "SELECT `object_type`, `object_id` AS `cache_id`
         FROM `search_index_times`
         WHERE `last_refresh` = '&1'
         ORDER BY cache_id",
        $snapshot_time
    );

    while ($r = sql_fetch_assoc($rs)) {
        // discard old search index entries for this cache & object type
        sql(
            "DELETE FROM `search_index`
             WHERE `object_type`='&1'
             AND `cache_id`='&2'",
            $r['object_type'],
            $r['cache_id']
        );

        // fetch current texts for this cache & object type
        switch ($r['object_type']) {
            case 2:  // cache titles
                $texts_sql = "SELECT `name` AS `text` FROM `caches` WHERE `cache_id`='&1'";
                break;

            case 1:  // log texts
                $texts_sql = "SELECT `text` FROM `cache_logs` WHERE `cache_id`='&1'";
                break;

            case 3:  // cache description texts
                $texts_sql = "SELECT CONCAT(`desc`, ' ', `short_desc`) AS `text` FROM `cache_desc` WHERE `cache_id`='&1'";
                break;

            case 6:  // picture titles
                $texts_sql =
                    "SELECT `title` AS `text` FROM `pictures` WHERE `object_type`=2 AND `object_id`='&1'
                     UNION
                     SELECT `title` AS `text` FROM `pictures` JOIN `cache_logs` ON `cache_logs`.`id`=`object_id`
                     WHERE `object_type`=1 AND `cache_logs`.`cache_id`='&1'";
                break;
        }
        $texts = sql_fetch_column(sql($texts_sql, $r['cache_id']));

        // insert hashes for these text into the search index
        foreach ($texts as $text) {
            if ($r['object_type'] == 1 || $r['object_type'] == 3) {
                // cache description and log texts are in HTML format
                $text = ftsearch_strip_html($text);
            }
            $hashes = ftsearch_hash($text);
            foreach ($hashes as $hash) {
                sql(
                    "INSERT INTO `search_index` (`object_type`, `cache_id`, `hash`, `count`)
                     VALUES ('&1', '&2', '&3', 1)
                     ON DUPLICATE KEY UPDATE `count`=`count`+1",
                    $r['object_type'],
                    $r['cache_id'],
                    $hash
                );
            }
        }

        // discard the to-be-updated flag, if it was not touched while we were processing it
        sql(
            "DELETE FROM `search_index_times`
             WHERE `object_type` = '&1'
             AND `object_id` = '&2'
             AND `last_refresh` = '&3'",
            $r['object_type'],
            $r['cache_id'],
            $snapshot_time
        );
    }
    sql_free_result($rs);
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
