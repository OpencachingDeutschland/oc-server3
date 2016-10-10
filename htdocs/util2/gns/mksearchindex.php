#!/usr/local/bin/php -q
<?php
/***************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * Dieses Script erstellt den Suchindex für Ortsnamen aus den Daten der
 * GNS-DB.
 ***************************************************************************/

$opt['rootpath'] = '../../';
require_once __DIR__ . '/../../lib2/cli.inc.php';
require_once __DIR__ . '/../../lib2/search/search.inc.php';


$doubleindex['sankt'] = 'st';

sql('DELETE FROM gns_search');

$rs = sql("SELECT `uni`, `full_name_nd` FROM `gns_locations` WHERE `dsg` LIKE 'PPL%'");
while ($r = sql_fetch_array($rs)) {
    $simpleTexts = search_text2sort($r['full_name_nd'], true);
    $simpleTextsArray = explode_multi($simpleTexts, ' -/,');
    // ^^ This should be obsolete, as search_text2sort() removes all non-a..z chars.

    foreach ($simpleTextsArray as $text) {
        if ($text !== '') {
            if (nonAlpha($text)) {
                die($r['uni'] . ' ' . $text . "\n"); // obsolete for the same reason as above
            }

            $simpleText = search_text2simple($text);

            sql(
                "INSERT INTO `gns_search` (`uni_id`, `sort`, `simple`, `simplehash`)
                VALUES ('&1', '&2', '&3', '&4')",
                $r['uni'],
                $text,
                $simpleText,
                sprintf('%u', crc32($simpleText))
            );

            if (isset($doubleindex[$text])) {
                sql(
                    "INSERT INTO `gns_search` (`uni_id`, `sort`, `simple`, `simplehash`)
                    VALUES ('&1', '&2', '&3', '&4')",
                    $r['uni'],
                    $text,
                    $doubleindex[$text],
                    sprintf('%u', crc32($doubleindex[$text]))
                );
            }
        }
    }
}
mysql_free_result($rs);


function nonAlpha($str)
{
    $strLength = mb_strlen($str);
    for ($i = 0; $i < $strLength; $i++) {
        if (!((ord(mb_substr($str, $i, 1)) >= ord('a')) && (ord(mb_substr($str, $i, 1)) <= ord('z')))) {
            return true;
        }
    }

    return false;
}
