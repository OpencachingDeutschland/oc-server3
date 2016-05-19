#!/usr/local/bin/php -q
<?php
/***************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * Dieses Script erstellt den Suchindex für Ortsnamen aus den Daten der
 * Opengeodb. (Obsolet, dieser Suchindex wird nicht verwendet.)
 ***************************************************************************/

$opt['rootpath'] = '../../';
require_once __DIR__ . '/../../lib2/cli.inc.php';
require_once __DIR__ . '/../../lib2/search/search.inc.php';


sql('DELETE FROM geodb_search');

$rs = sql(
    "SELECT `loc_id`, `text_val`
    FROM `geodb_textdata`
    WHERE `text_type`=500100000
    AND text_locale IN ('da', 'de', 'en', 'fi', 'fr', 'it', 'nl', 'rm')"
);

while ($r = sql_fetch_array($rs)) {
    $simpletexts = search_text2sort($r['text_val']);
    $simpletextsarray = explode_multi($simpletexts, ' -/,');

    foreach ($simpletextsarray as $text) {
        if ($text != '') {
            if (nonalpha($text)) {
                die($text . "\n");
            }

            $simpletext = search_text2simple($text);

            sql(
                "INSERT INTO `geodb_search` (`loc_id`, `sort`, `simple`, `simplehash`)
                 VALUES ('&1', '&2', '&3', '&4')",
                $r['loc_id'],
                $text,
                $simpletext,
                sprintf("%u", crc32($simpletext))
            );
        }
    }
}
mysql_free_result($rs);

function nonalpha($str)
{
    for ($i = 0; $i < mb_strlen($str); $i ++) {
        if (!((ord(mb_substr($str, $i, 1)) >= ord('a')) && (ord(mb_substr($str, $i, 1)) <= ord('z')))) {
            return true;
        }
    }

    return false;
}
