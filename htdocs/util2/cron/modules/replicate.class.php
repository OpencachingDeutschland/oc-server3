<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Replicate database contents between different OC installations/nodes
 ***************************************************************************/

checkJob(new replicate());

class replicate
{
    public $name = 'replicate';
    public $interval = 3600;

    public function run()
    {
        global $opt;

        if ($opt['cron']['replicate']['delete_hidden_caches']['url']) {
            // This is used to remove unwanted data from test.opencaching.de
            // (where any users may have admin rights and see hidden caches).

            $hidden_caches = file_get_contents($opt['cron']['replicate']['delete_hidden_caches']['url']);
            $hc = explode("\n", trim($hidden_caches));
            $hc_imploded_and_escaped = "'" . implode("','", array_map('sql_escape', $hc)) . "'";
            sql("SET @allowdelete = 1");
            sql("DELETE FROM `caches` WHERE `wp_oc` IN (" . $hc_imploded_and_escaped . ")");
            // All dependent data in other tables is deleted via trigger.
        }
    }
}
