<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Currently not in use
 ***************************************************************************/

// CheckThrottle();

function CheckThrottle()
{
    global $opt, $tpl;

    $ip_string = $_SERVER['REMOTE_ADDR'];
    $ip_blocks = mb_split('\\.', $ip_string);
    $ip_numeric = $ip_blocks[3] + $ip_blocks[2] * 256 + $ip_blocks[1] * 65536 + $ip_blocks[0] * 16777216;

    sql(
        'CREATE TABLE IF NOT EXISTS &tmpdb.`sys_accesslog`
        (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `ip` INT UNSIGNED NOT NULL,
         `access_time` TIMESTAMP NOT NULL, INDEX (`access_time`), INDEX (`ip`)) ENGINE = MEMORY'
    );

    $rsStaus = sql("SHOW STATUS LIKE 'Threads_connected'");
    $rStatus = sql_fetch_array($rsStaus);
    sql_free_result($rsStaus);

    if ($rStatus) {
        if ($rStatus[1] > $opt['db']['throttle_connection_count']) {
            $access_count = sql_value("SELECT COUNT(*) FROM &tmpdb.`sys_accesslog` WHERE ip ='&1'", 0, $ip_numeric);
            if ($access_count > $opt['db']['throttle_access_count']) {
                $tpl->error(ERROR_THROOTLE);
            }
        }
    }

    // remove old entries every 100st call
    if (mt_rand(0, 100) == 50) {
        sql(
            "DELETE FROM &tmpdb.`sys_accesslog` WHERE `access_time`<CURRENT_TIMESTAMP()-'&2'",
            $ip_numeric,
            $opt['db']['throttle_access_time']
        );
    }

    sql(
        "INSERT INTO &tmpdb.`sys_accesslog` (`ip`, `access_time`) VALUES ('&1', CURRENT_TIMESTAMP())",
        $ip_numeric
    );
}
