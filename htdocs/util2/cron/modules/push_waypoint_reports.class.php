<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

checkJob(new push_waypoint_reports());

class push_waypoint_reports
{
    public $name = 'push_waypoint_reports';
    public $interval = 120;

    public function run()
    {
        global $opt;

        if ($opt['cron']['gcwp']['report']) {
            $rs = sql(
                'SELECT * FROM `waypoint_reports`
                 WHERE `gcwp_processed`=0
                 ORDER BY `date_reported`'
            );
            while ($r = sql_fetch_assoc($rs)) {
                if (substr($r['wp_external'], 0, 2) != 'GC') {
                    $result = 'ok';
                } else {
                    $result = @file_get_contents(
                        $opt['cron']['gcwp']['report'] .
                        '?ocwp=' . urlencode($r['wp_oc']) .
                        '&gcwp=' . urlencode($r['wp_external']) .
                        '&source=' . urlencode($r['source'])
                    );
                    $result = trim($result);
                }
                if ($result != 'ok') {
                    echo "could not push GC waypoint report (id " . $r['report_id'] . "): " . $result . "\n";
                    break;
                } else {
                    sql(
                        "
                        UPDATE `waypoint_reports`
                        SET `gcwp_processed`=1
                        WHERE `report_id`='&1'",
                        $r['report_id']
                    );
                }
            }
            sql_free_result($rs);
        }
    }
}
