<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class CacheIcon
{

    public static function get($user_id, $cache_id, $cache_status, $cache_userid, $iconname)
    {
        $iconname = mb_eregi_replace("cache/", "", $iconname);   // for old cache_type table contents
        $iconext = "." . mb_eregi_replace("^.*\.", "", $iconname);
        $iconname = mb_eregi_replace("\..*", "", $iconname);

        // add status
        switch ($cache_status) {
            case 1:
                $iconname .= "-s";
                break;
            case 2:
                $iconname .= "-n";
                break;
            case 3:
                $iconname .= "-a";
                break;
            case 4:
                $iconname .= "-a";
                break;
            case 5:
                $iconname .= "-s";
                break;      // fix for RT ticket #3403
            case 6:
                $iconname .= "-a";
                break;
            case 7:
                $iconname .= "-a";
                break;
        }

        // mark if (not) found
        if ($user_id) {
            if ($cache_userid == $user_id) {
                $iconname .= "-owner";
            } else {
                $logtype = sql_value_slave(
                    "SELECT `type`
                     FROM `cache_logs`
                     WHERE `cache_id`='&1'
                     AND `user_id`='&2'
                     AND `type` IN (1,2,7)
                     ORDER BY `type`
                     LIMIT 1",
                    0,
                    $cache_id,
                    $user_id
                );

                if ($logtype == 1 || $logtype == 7) {
                    $iconname .= '-found';
                } elseif ($logtype == 2) {
                    $iconname .= '-dnf';
                }
            }
        }

        return $iconname . $iconext;
    }

}
