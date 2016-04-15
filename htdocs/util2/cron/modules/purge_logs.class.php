<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Delete old log entries with personal user data for data privacy
 ***************************************************************************/

checkJob(new purge_logs());

class purge_logs
{
    public $name = 'purge_logs';
    public $interval = 86400;    // daily

    public function run()
    {
        global $opt;

        if ($opt['logic']['logs']['purge_email'] > 0) {
            sql(
                "DELETE FROM `email_user` WHERE date_created < NOW() - INTERVAL &1 DAY",
                $opt['logic']['logs']['purge_email']
            );
            sql(
                "DELETE FROM `logentries` WHERE date_created < NOW() - INTERVAL &1 DAY AND eventid IN (1,2,3,8)",
                $opt['logic']['logs']['purge_email']
            );
        }

        if ($opt['logic']['logs']['purge_userdata'] > 0) {
            sql(
                "DELETE FROM `logentries` WHERE date_created < NOW() - INTERVAL &1 DAY AND eventid IN (6,7)",
                $opt['logic']['logs']['purge_userdata']
            );
        }

        // Type 5 events = adoptions are still recorded here and preliminary archived,
        // but may be discarded after verifying that they are not used anywhere.
        // Adoptions are now in cache_adoptions table.
    }
}
