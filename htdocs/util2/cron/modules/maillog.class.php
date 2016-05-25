<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Process system maillog to detect email delivery problems
 ***************************************************************************/

checkJob(new maillog());

class maillog
{
    public $name = 'maillog';
    public $interval = 600;  // every 10 minutes


    public function run()
    {
        global $opt;
        if ($opt['system']['maillog']['syslog_db_host'] != '') {
            $this->process_syslog();
        }
    }


    public function process_syslog()
    {
        global $opt;

        $dbc = mysql_connect(
            $opt['system']['maillog']['syslog_db_host'],
            $opt['system']['maillog']['syslog_db_user'],
            $opt['system']['maillog']['syslog_db_password'],
            true
        );  // use separate connection even if on same DB host
        if ($dbc === false) {
            echo $this->name . ": could not connect to syslog database\n";

            return;
        }
        if (@mysql_query("USE " . $opt['system']['maillog']['syslog_db_name'], $dbc) === false) {
            echo $this->name . ": could not open syslog database: " . mysql_error() . "\n";

            return;
        }

        $col_id = mysql_real_escape_string($opt['system']['maillog']['column']['id']);
        $col_message = mysql_real_escape_string($opt['system']['maillog']['column']['message']);
        $col_created = mysql_real_escape_string($opt['system']['maillog']['column']['created']);
        $col_hostname = mysql_real_escape_string($opt['system']['maillog']['column']['host_name']);
        $col_program = mysql_real_escape_string($opt['system']['maillog']['column']['program']);

        $maillog_where =
            "`" . $col_hostname . "`='" . mysql_real_escape_string($opt['system']['maillog']['syslog_oc_host']) . "' AND
            `" . $col_program . "` like '" . mysql_real_escape_string($opt['system']['maillog']['syslog_mta']) . "'";

        $rs = @mysql_query(
            "
            SELECT TIMESTAMPDIFF(DAY, MAX(" . $col_created . "), NOW())
            FROM `" . mysql_real_escape_string($opt['system']['maillog']['syslog_db_table']) . "`
            WHERE " . $maillog_where
        );
        $r = mysql_fetch_row($rs);
        mysql_free_result($rs);
        if ($r[0] >= $opt['system']['maillog']['inactivity_warning']) {
            echo "email syslog has stalled\n";

            return;
        }

        $last_id = sql_value("SELECT `value` FROM `sysconfig` WHERE `name`='syslog_maillog_lastid'", 0);
        $last_date = sql_value("SELECT `value` FROM `sysconfig` WHERE `name`='syslog_maillog_lastdate'", "");

        // We check for both, new IDs and new creation dates, so that it still works
        // if the syslog DB is re-setup and IDs restarted from 1 (dates are not unique).
        //
        // It happened that old log entries were re-added as duplicates to the syslog table.
        // We solve this problem by ignoring entries with higher ID and older timestamp. Worst
        // case some entries with same timestamp as $last_date will be processed redundantly.

        $rs = @mysql_query(
            "SELECT `" . $col_id . "` `id`,
                      `" . $col_message . "` `message`,
                      `" . $col_created . "` `created`
                 FROM `" . mysql_real_escape_string($opt['system']['maillog']['syslog_db_table']) . "`
                WHERE `" . $col_created . "`>='" . mysql_real_escape_string($last_date) . "'
                  AND (`" . $col_id . "`>'" . mysql_real_escape_string(
                $last_id
            ) . "' OR `" . $col_created . "`>'" . mysql_real_escape_string($last_date) . "')
                  AND  " . $maillog_where . "
             ORDER BY `" . $col_created . "`,`" . $col_id . "`",
            $dbc
        );
        if ($rs === false) {
            echo $this->name . ": syslog query error (" . mysql_errno() . "): " . mysql_error() . "\n";

            return;
        }

        while ($logentry = mysql_fetch_assoc($rs)) {
            $message = $logentry['message'];   // latin-1 charset
            $delivered = strpos($message, 'status=sent') > 0;
            $bounced = strpos($message, 'status=bounced') > 0;
            if ($delivered || $bounced) {
                if (preg_match('/ to=<(.+)>,/U', $message, $matches)) {
                    $emailadr = $matches[1];
                    if ($delivered) {
                        sql(
                            "UPDATE `user` SET `email_problems`=0
                              WHERE `email`='&1'",
                            $emailadr
                        );
                    } else {
                        if ($bounced) {  // maximum one bounce per day is counted, to filter out temporary problems
                            sql(
                                "UPDATE `user` SET `email_problems`=`email_problems`+1, `last_email_problem`='&2'
                              WHERE `email`='&1' AND IFNULL(`last_email_problem`,'') < '&2'",
                                $emailadr,
                                $logentry['created']
                            );
                        }
                    }
                } else {
                    echo $this->name . ": no email address found for record ID " . $logentry['id'] . "\n";
                }
            }
            $last_id = $logentry['id'];
            $last_date = $logentry['created'];
        }
        mysql_free_result($rs);

        sql(
            "INSERT INTO `sysconfig` (`name`, `value`) VALUES ('syslog_maillog_lastid','&1')
             ON DUPLICATE KEY UPDATE `value`='&1'",
            $last_id
        );
        sql(
            "INSERT INTO `sysconfig` (`name`, `value`) VALUES ('syslog_maillog_lastdate','&1')
             ON DUPLICATE KEY UPDATE `value`='&1'",
            $last_date
        );
    }
}
