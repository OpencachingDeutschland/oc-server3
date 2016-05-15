<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  This cronjob fills the table cache_waypoint_pool with waypoints that
 *  can be assigned to new caches. The code is cpu intensive on database
 *  server.
 ***************************************************************************/

checkJob(new cache_waypoint_pool());

class cache_waypoint_pool
{
    public $name = 'cache_waypoint_pool';
    public $interval = 604800; // once a week

    public function run()
    {
        global $opt;
        $nLastInsertsCount = 1;

        // check if the pool needs to be filled up and repeat until the
        $nPoolSize = $this->getCurrentPoolSize();
        if ($nPoolSize < $opt['logic']['waypoint_pool']['min_count']) {
            while (($nPoolSize < $opt['logic']['waypoint_pool']['max_count']) && ($nLastInsertsCount > 0)) {
                $nLastInsertsCount = $this->fill($opt['logic']['waypoint_pool']['max_count'] - $nPoolSize);
                $nPoolSize = $this->getCurrentPoolSize();
            }
        }
    }

    public function getCurrentPoolSize()
    {
        return sql_value('SELECT COUNT(*) FROM `cache_waypoint_pool`', 0);
    }

    public function fill($max_inserts_count)
    {
        global $opt;

        $rowCount = 0;
        $nInsertCount = 0;

        if ($opt['logic']['waypoint_pool']['fill_gaps'] == true) {
            // query the first unused waypoint (between other waypoints)
            $rsStartWp = sql(
                "SELECT SQL_BUFFER_RESULT DECTOWP(WPTODEC(`c`.`wp_oc`, '&1')+1, '&1') AS `free_wp`
                               FROM `caches` AS `c`
                          LEFT JOIN `caches` AS `cNext` ON DECTOWP(WPTODEC(`c`.`wp_oc`, '&1')+1, '&1')=`cNext`.`wp_oc`
                          LEFT JOIN `cache_waypoint_pool` ON DECTOWP(WPTODEC(`c`.`wp_oc` ,'&1')+1, '&1')=`cache_waypoint_pool`.`wp_oc`
                              WHERE `c`.`wp_oc` REGEXP '&2'
                                AND ISNULL(`cNext`.`wp_oc`)
                                AND ISNULL(`cache_waypoint_pool`.`wp_oc`)
                           ORDER BY `free_wp` ASC
                              LIMIT 250",
                $opt['logic']['waypoint_pool']['prefix'],
                '^' . $opt['logic']['waypoint_pool']['prefix'] . '[' . $opt['logic']['waypoint_pool']['valid_chars'] . ']{1,}$'
            );
        } else {
            // query the last used waypoint
            $rsStartWp = sql(
                "SELECT SQL_BUFFER_RESULT DECTOWP(MAX(dec_wp)+1, '&2') AS `free_wp`
                           FROM (
                                   SELECT MAX(WPTODEC(`wp_oc`, '&2')) AS dec_wp
                                     FROM `caches`
                                    WHERE `wp_oc` REGEXP '&1'
                              UNION
                                   SELECT MAX(WPTODEC(`wp_oc`, '&2')) AS dec_wp
                                     FROM `cache_waypoint_pool`
                                 ) AS tbl",
                '^' . $opt['logic']['waypoint_pool']['prefix'] . '[' . $opt['logic']['waypoint_pool']['valid_chars'] . ']{1,}$',
                $opt['logic']['waypoint_pool']['prefix']
            );
        }

        while (($rStartWp = sql_fetch_assoc($rsStartWp)) && ($nInsertCount < $max_inserts_count)) {
            $free_wp = $rStartWp['free_wp'];
            if ($free_wp == '') {
                $free_wp = $opt['logic']['waypoint_pool']['prefix'] . '0001';
            }
            $nInsertCount += $this->fill_turn($free_wp, $max_inserts_count - $nInsertCount);
            $rowCount ++;
        }
        sql_free_result($rsStartWp);

        if ($rowCount == 0) {
            // new database ...
            $nInsertCount += $this->fill_turn($opt['logic']['waypoint_pool']['prefix'] . '0001', $max_inserts_count);
        }

        return $nInsertCount;
    }

    // search for the next free range and use that waypoints to fill the waypoint pool
    public function fill_turn($start_wp, $max_inserts_count)
    {
        global $opt;

        // query the end of this waypoint range
        $end_wp = sql_value(
            "SELECT DECTOWP(MIN(dec_wp), '&3')
                           FROM (
                                   SELECT MIN(WPTODEC(`wp_oc`, '&3')) AS dec_wp
                                     FROM `caches`
                                    WHERE WPTODEC(`wp_oc`, '&3')>WPTODEC('&1', '&3')
                                      AND `wp_oc` REGEXP '&2'
                              UNION
                                   SELECT MIN(WPTODEC(`wp_oc`, '&3')) AS dec_wp
                                     FROM `cache_waypoint_pool`
                                    WHERE WPTODEC(`wp_oc`, '&3')>WPTODEC('&1', '&3')
                                 ) AS tbl",
            $opt['logic']['waypoint_pool']['prefix'] . '100000',
            $start_wp,
            '^' . $opt['logic']['waypoint_pool']['prefix'] . '[' . $opt['logic']['waypoint_pool']['valid_chars'] . ']{1,}$',
            $opt['logic']['waypoint_pool']['prefix']
        );

        // now, we have start and end waypoints ...
        $nWaypointsGenerated = 0;
        while (($nWaypointsGenerated < $max_inserts_count) && ($start_wp != $end_wp)) {
            sql("INSERT INTO `cache_waypoint_pool` (`wp_oc`) VALUES ('&1')", $start_wp);
            $nWaypointsGenerated ++;
            $start_wp = $this->increment_waypoint($start_wp, $opt['logic']['waypoint_pool']['prefix']);
        }

        return $nWaypointsGenerated;
    }

    // see mysql functions in doc/sql/stored-proc/maintain.php for explanation
    public function increment_waypoint($wp, $prefix)
    {
        global $opt;

        $wp_chars = $opt['logic']['waypoint_pool']['valid_chars'];
        $b36_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if (substr($wp, 0, 2) != $prefix) {
            return '';
        }

        $wp_value = substr($wp, 2);
        $b36_value = '';
        for ($i = 0; $i < strlen($wp_value); $i ++) {
            $b36_value .= substr($b36_chars, strpos($wp_chars, substr($wp_value, $i, 1)), 1);
        }

        $dec_value = base_convert($b36_value, strlen($wp_chars), 10) + 1;
        $b36_value = strtoupper(base_convert($dec_value, 10, strlen($wp_chars)));

        $wp_value = '';
        for ($i = 0; $i < strlen($b36_value); $i ++) {
            $wp_value .= substr($wp_chars, strpos($b36_chars, substr($b36_value, $i, 1)), 1);
        }

        if (strlen($wp_value) < 4) {
            return $prefix . substr('0000' . $wp_value, - 4);
        } else {
            return $prefix . $wp_value;
        }
    }
}
