<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * OVL search output for TOP25, TOP50 etc.
 ****************************************************************************/

require_once $opt['rootpath'] . 'lib2/charset.inc.php';

$search_output_file_download = true;
$content_type_plain = 'application/ovl';


function search_output()
{
    $ovlLine = "[Symbol {symbolnr1}]\r\nTyp=6\r\nGroup=1\r\nWidth=20\r\nHeight=20\r\nDir=100\r\nArt=1\r\nCol=3\r\nZoom=1\r\nSize=103\r\nArea=2\r\nXKoord={lon}\r\nYKoord={lat}\r\n[Symbol {symbolnr2}]\r\nTyp=2\r\nGroup=1\r\nCol=3\r\nArea=1\r\nZoom=1\r\nSize=130\r\nFont=1\r\nDir=100\r\nXKoord={lonname}\r\nYKoord={latname}\r\nText={cachename}\r\n";
    $ovlFoot = "[Overlay]\r\nSymbols={symbolscount}\r\n";

    /*
        {symbolnr1}
        {lon}
        {lat}
        {symbolnr2}
        {lonname}
        {latname}
        {cachename}
        {symbolscount}
    */

    $nr = 1;
    $rs = sql_slave(
        '
        SELECT SQL_BUFFER_RESULT
            &searchtmp.`cache_id` `cacheid`,
            &searchtmp.`longitude`,
            &searchtmp.`latitude`,
            `caches`.`name`
        FROM
            &searchtmp,
            `caches`
        WHERE
            &searchtmp.`cache_id`=`caches`.`cache_id`'
    );

    while ($r = sql_fetch_array($rs)) {
        $thisline = $ovlLine;

        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = mb_ereg_replace('{lat}', $lat, $thisline);
        $thisline = mb_ereg_replace('{latname}', $lat, $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = mb_ereg_replace('{lon}', $lon, $thisline);
        $thisline = mb_ereg_replace('{lonname}', $lon, $thisline);

        $thisline = mb_ereg_replace('{cachename}', utf8ToIso88591($r['name']), $thisline);
        $thisline = mb_ereg_replace('{symbolnr1}', $nr, $thisline);
        $thisline = mb_ereg_replace('{symbolnr2}', $nr + 1, $thisline);

        append_output($thisline);
        $nr += 2;
    }
    mysql_free_result($rs);

    $ovlFoot = mb_ereg_replace('{symbolscount}', $nr - 1, $ovlFoot);
    append_output($ovlFoot);
}
