<?php
/****************************************************************************
 * For license information see doc/license.txt
 *
 * Unicode Reminder メモ
 *
 * Plaintext search output, one file per cache
 ****************************************************************************/

require_once 'lib2/translate.class.php';

$search_output_file_download = true;
$content_type_plain = 'text/plain';
$zip_threshold = 1;
$add_to_zipfile = false;


function search_output()
{
    global $opt, $translate, $txt_record;
    global $converted_from_html;
    global $phpzip, $bUseZip;

    $txtLine = $txt_record;

    $txtLogs = "<===================>
{username} / {date} / {type}

{text}
";

    $rs = sql_slave(
        "SELECT SQL_BUFFER_RESULT
            &searchtmp.`cache_id` `cacheid`,
            &searchtmp.`longitude` `longitude`,
            &searchtmp.`latitude` `latitude`,
            `caches`.`wp_oc` `waypoint`,
            `caches`.`date_hidden` `date_hidden`,
            `caches`.`name` `name`,
            `caches`.`terrain` `terrain`,
            `caches`.`difficulty` `difficulty`,
            `caches`.`desc_languages` `desc_languages`,
            `caches`.`needs_maintenance`,
            `caches`.`listing_outdated`,
            IFNULL(`stt_country`.`text`,`countries`.`en`) AS `country`,
            IFNULL(`stt_size`.`text`,`cache_size`.`name`) `size`,
            IFNULL(`stt_type`.`text`,`cache_type`.`en`) `type`,
            IFNULL(`stt_status`.`text`,`cache_status`.`name`) `status`,
            `user`.`username` `username`,
            `cache_desc`.`desc` `desc`,
            `cache_desc`.`short_desc` `short_desc`,
            `cache_desc`.`language` `desc_language`,
            `cache_desc`.`hint` `hint`,
            `cache_desc`.`desc_html` `html`,
            `user`.`user_id`,
            `user`.`username`,
            `user`.`data_license`
         FROM
            &searchtmp
            INNER JOIN `caches` ON &searchtmp.`cache_id`=`caches`.`cache_id`
            INNER JOIN `cache_desc`
                ON `cache_desc`.`cache_id`=`caches`.`cache_id`
                AND `caches`.`default_desclang`=`cache_desc`.`language`
             INNER JOIN `cache_type` ON `cache_type`.`id`=`caches`.`type`
             INNER JOIN `cache_size` ON `cache_size`.`id`=`caches`.`size`
             INNER JOIN `cache_status` ON `cache_status`.`id`=`caches`.`status`
             INNER JOIN `user` ON `user`.`user_id`=`caches`.`user_id`
             LEFT JOIN `countries` ON `countries`.`short`=`caches`.`country`
             LEFT JOIN `sys_trans_text` `stt_type` ON `stt_type`.`trans_id`=`cache_type`.`trans_id` AND `stt_type`.`lang`='&1'
             LEFT JOIN `sys_trans_text` `stt_size` ON `stt_size`.`trans_id`=`cache_size`.`trans_id` AND `stt_size`.`lang`='&1'
             LEFT JOIN `sys_trans_text` `stt_status` ON `stt_status`.`trans_id`=`cache_status`.`trans_id` AND `stt_status`.`lang`='&1'
             LEFT JOIN `sys_trans_text` `stt_country` ON `stt_country`.`trans_id`=`countries`.`trans_id` AND `stt_country`.`lang`='&1'",
        $opt['template']['locale']
    );

    while ($r = sql_fetch_array($rs)) {
        if (strlen($r['desc_languages']) > 2) {
            $r = get_locale_desc($r);
        }

        $thisline = $txtLine;

        $lat = sprintf('%01.5f', $r['latitude']);
        $thisline = mb_ereg_replace('{lat}', help_latToDegreeStr($lat), $thisline);

        $lon = sprintf('%01.5f', $r['longitude']);
        $thisline = mb_ereg_replace('{lon}', help_lonToDegreeStr($lon), $thisline);

        $time = date('d.m.Y', strtotime($r['date_hidden']));
        $thisline = mb_ereg_replace('{time}', $time, $thisline);
        $thisline = mb_ereg_replace('{waypoint}', $r['waypoint'], $thisline);
        $thisline = mb_ereg_replace('{cacheid}', $r['cacheid'], $thisline);
        $thisline = mb_ereg_replace('{cachename}', $r['name'], $thisline);
        $thisline = mb_ereg_replace('{country}', $r['country'], $thisline);

        if ($r['hint'] == '') {
            $thisline = mb_ereg_replace('{hints}', '', $thisline);
        } else {
            $thisline = mb_ereg_replace('{hints}', str_rot13_gc(decodeEntities(strip_tags($r['hint']))), $thisline);
        }

        $thisline = mb_ereg_replace('{shortdesc}', $r['short_desc'], $thisline);

        $license = getLicenseDisclaimer(
            $r['user_id'],
            $r['username'],
            $r['data_license'],
            $r['cacheid'],
            $opt['template']['locale'],
            true,
            false,
            true
        );
        if ($license != "") {
            $license = "\r\n\r\n$license";
        }

        if ($r['html'] == 0) {
            $thisline = mb_ereg_replace('{htmlwarn}', '', $thisline);
            $thisline = mb_ereg_replace('{desc}', decodeEntities(strip_tags($r['desc'])) . $license, $thisline);
        } else {
            $thisline = mb_ereg_replace('{htmlwarn}', " ($converted_from_html)", $thisline);
            $thisline = mb_ereg_replace('{desc}', html2txt($r['desc']) . $license, $thisline);
        }

        $thisline = mb_ereg_replace('{type}', $r['type'], $thisline);
        $thisline = mb_ereg_replace('{container}', $r['size'], $thisline);
        $thisline = mb_ereg_replace('{status}', $r['status'], $thisline);

        $difficulty = sprintf('%01.1f', $r['difficulty'] / 2);
        $thisline = mb_ereg_replace('{difficulty}', $difficulty, $thisline);

        $terrain = sprintf('%01.1f', $r['terrain'] / 2);
        $thisline = mb_ereg_replace('{terrain}', $terrain, $thisline);

        $thisline = mb_ereg_replace('{siteurl}', $opt['page']['default_absolute_url'], $thisline);
        $thisline = mb_ereg_replace('{owner}', $r['username'], $thisline);

        $flags = array();
        if ($r['needs_maintenance'] > 0 || $r['listing_outdated']) {
            if ($r['needs_maintenance'] > 0) {
                $flags[] = 'geocache needs maintenance';
            }
            if ($r['listing_outdated'] > 0) {
                $flags[] = 'description is outdated';
            }
        } else {
            $flags[] = 'ok';
        }
        foreach ($flags as &$flag) {
            $flag = $translate->t($flag, '', basename(__FILE__), __LINE__);
        }
        $thisline = mb_ereg_replace('{condition}', implode(', ', $flags), $thisline);

        // logs ermitteln
        $logentries = '';
        $rsLogs = sql_slave(
            "SELECT
                `cache_logs`.`id`,
                `cache_logs`.`text_html`,
                IFNULL(`stt`.`text`, `log_types`.`en`) `type`,
                `cache_logs`.`date`,
                `cache_logs`.`text`,
                `user`.`username`
             FROM `cache_logs`
             JOIN `user` ON `cache_logs`.`user_id`=`user`.`user_id`
             JOIN `log_types` ON `cache_logs`.`type`=`log_types`.`id`
             LEFT JOIN `sys_trans_text` `stt` ON `stt`.`trans_id`=`log_types`.`trans_id` AND `stt`.`lang`='&2'
             WHERE `cache_logs`.`cache_id`=&1
             ORDER BY
                `cache_logs`.`order_date` DESC,
                `cache_logs`.`date_created` DESC,
                `cache_logs`.`id` DESC
             LIMIT 20",
            $r['cacheid'],
            $opt['template']['locale']
        );

        while ($rLog = sql_fetch_array($rsLogs)) {
            $thislog = $txtLogs;

            $thislog = mb_ereg_replace('{id}', $rLog['id'], $thislog);
            $dateformat = "d.m.Y H:i";
            if (substr($rLog['date'], 11) == "00:00:00") {
                $dateformat = "d.m.Y";
            }
            $thislog = mb_ereg_replace('{date}', date($dateformat, strtotime($rLog['date'])), $thislog);
            $thislog = mb_ereg_replace('{username}', $rLog['username'], $thislog);

            $logtype = $rLog['type'];

            $thislog = mb_ereg_replace('{type}', $logtype, $thislog);
            if ($rLog['text_html'] == 0) {
                $thislog = mb_ereg_replace('{text}', decodeEntities(strip_tags($rLog['text'])), $thislog);
            } else {
                $thislog = mb_ereg_replace('{text}', html2txt($rLog['text']), $thislog);
            }

            $logentries .= $thislog . "\n";
        }
        $thisline = mb_ereg_replace('{logs}', $logentries, $thisline);

        $thisline = lf2crlf($thisline);
        if (!$bUseZip) {
            echo $thisline;
        } else {
            $phpzip->add_data($r['waypoint'] . '.txt', $thisline);
        }
    }
    mysql_free_result($rs);
}


function decodeEntities($str)
{
    return html_entity_decode($str, ENT_COMPAT, "UTF-8");
}

function html2txt($html)
{
    $str = mb_ereg_replace("\r\n", '', $html);
    $str = mb_ereg_replace("\n", '', $str);
    $str = mb_ereg_replace('<br />', "\n", $str);
    $str = strip_tags($str);
    $str = decodeEntities($str);

    return $str;
}

function lf2crlf($str)
{
    return mb_ereg_replace("\r\r\n", "\r\n", mb_ereg_replace("\n", "\r\n", $str));
}
