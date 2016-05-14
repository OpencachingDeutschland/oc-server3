<?php
/****************************************************************************
 * ./lib/clicompatbase.inc.php
 * Unicode Reminder メモ
 *
 * used in both lib1 and lib2;
 * merge these functions into cache.class.php when no longer needed in lib1
 ****************************************************************************/

function get_cache_condition_history($cache_id)
{
    $rs = sql(
        "SELECT `date`, `needs_maintenance`, `listing_outdated`
         FROM `cache_logs`
         WHERE `cache_id`='&1' AND (`needs_maintenance` IS NOT NULL OR `listing_outdated` IS NOT NULL)
         ORDER BY `date`, `id`",
        $cache_id
    );
    $nm = $lo = 0;
    $cond = array(
        array(
            'needs_maintenance' => $nm,
            'listing_outdated' => $lo,
            'date' => '0000-00-00 00:00:00'
        )
    );
    while ($r = sql_fetch_assoc($rs)) {
        if ($r['needs_maintenance'] != $nm || $r['listing_outdated'] != $lo) {
            $nm = $r['needs_maintenance'];
            $lo = $r['listing_outdated'];
            $cond[] = array(
                'needs_maintenance' => $nm,
                'listing_outdated' => $lo,
                'date' => $r['date']
            );
        }
    }
    sql_free_result($rs);
    $cond[] = array(
        'needs_maintenance' => $nm,
        'listing_outdated' => $lo,
        'date' => '9999-12-31 23:59:59'
    );

    return $cond;
}
