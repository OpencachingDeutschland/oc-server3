<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  This script helps to recover from email losses after an email delivery
 *  problem on the host. It will resend the emails if possible, and inform
 *  about information which cannot be recovered automatically.
 *
 *  The following will be ignored here, because changes cannot be reliably
 *  detected for a time interval:
 *
 *    - password change requests
 *    - email address change requests
 *    - new OConly attribute notifications
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$opt['rootpath'] = __DIR__ . '/../../htdocs/';
require $opt['rootpath'] . 'lib2/cli.inc.php';

if ($argc < 3 || $argc > 4) {
    die(
        "usage:    php email_recovery.php \"from-datetime\" \"to-datetime\" [option]\n" .
        "\n" .
        "          Datetimes must be in YYYY-MM-DD HH:MM:SS format and are inclusive.\n" .
        "\n" .
        "options:  <none>          show information about all lost information\n" .
        "          watchlist       resend log notifications\n" .
        "          newcaches       resend new caches notifications\n" .
        "          registrations   resend activation codes\n"
    );
}

$er = new EmailRecovery($argv[1], $argv[2], $errormsg);
if ($errormsg != '') {
    die($errormsg . "\n");
}

if ($argc < 4) {
    $er->showLosses();
} else {
    $option = $argv[3];
    switch ($option) {
        case 'watchlist':
            $er->resendLogNotifications();
            break;
        case 'newcaches':
            $er->resendCacheNotifications();
            break;
        case 'registrations':
            $er->resendActivationCodes();
            break;
        default:
            die('invalid option: '. $option . "\n");
    }
}


class EmailRecovery
{
    private $fromDateTime;
    private $toDateTime;

    public function __construct($fromDT, $toDT, &$errormsg)
    {
        if (!self::verifyDateTime($fromDT)) {
            $errormsg = 'invalid from-datetime (must be SQL format): "' . $fromDT . '"';
        } elseif (!self::verifyDateTime($toDT)) {
            $errormsg = 'invalid from-datetime (must be SQL format): "' . $toDT . '"';
        } else {
            $this->fromDateTime = $fromDT;
            $this->toDateTime = $toDT;
            $errormsg = '';
        }
    }


    # helper functions

    private static function verifyDateTime($datetime)
    {
        return preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/", $datetime);
    }

    private static function message($level, $text)
    {
        echo str_repeat(' ', 2 * $level) . $text . "\n";
    }

    private function getDateCondition($table, $field)
    {
        return
            "`" . $table . "`.`" . $field . "` >= '" . sql_escape($this->fromDateTime) . "'" .
            " AND `" . $table . "`.`" . $field . "` <= '" . sql_escape($this->toDateTime) . "'";
    }

    private function showObjectCount($table, $dateField, $description, $andWhere = '')
    {
        $objectCount = sql_value(
            "SELECT COUNT(*)
             FROM `" . $table . "`
             WHERE " . $this->getDateCondition($table, $dateField) .
                       ($andWhere ? ' AND (' . $andWhere . ')' : ''),
            0
        );
        self::message(1, $objectCount . ' ' . $description);
    }


    # display summary of information that was not sent to the users

    public function showLosses()
    {
        self::message(0, 'In the given time interval');

        $this->showObjectCount(
            'cache_logs',
            'date_created',
            'logs have been submitted'
        );
        $this->showObjectCount(
            'caches',
            'date_created',
            'caches have been published'
        );
        $this->showObjectCount(
            'cache_logs_archived',
            'deletion_date',
            'logs have been deleted'
        );
        $this->showObjectCount(
            'user',
            'date_created',
            'new users have registered with pending activation',
            "last_login IS NULL AND `activation_code` <> ''"
        );
        $this->showObjectCount(
            'email_user',
            'date_created',
            'personal emails have been sent'
        );
        
        echo "\n";
        self::message(0, 'Deleted logs:');
        $rs = sql(
            "SELECT
                `caches`.`wp_oc`,
                `user1`.`username` `logger_name`,
                `user2`.`username` `deleter_name`,
                `cache_logs_archived`.`date` `logdate`
             FROM `cache_logs_archived`
             JOIN `user` `user1` ON `user1`.`user_id` = `cache_logs_archived`.`user_id`
             JOIN `user` `user2` ON `user2`.`user_id` = `cache_logs_archived`.`deleted_by`
             JOIN `caches` ON `caches`.`cache_id` = `cache_logs_archived`.`cache_id`
             WHERE " . $this->getDateCondition('cache_logs_archived', 'deletion_date') . "
             ORDER BY `cache_logs_archived`.`deletion_date` DESC"
        );
        while ($r = sql_fetch_assoc($rs)) {
            self::message(
                2,
                $r['wp_oc']
                . ' logged:' . $r['logdate']
                . ' logger:' . $r['logger_name']
                . ' deleter:' . $r['deleter_name']
            );
        }
        sql_free_result($rs);

        echo "\n";
        self::message(0, 'Registered users:');
        $rs = sql(
            "SELECT
                `date_created` `registration_date`,
                `username`
             FROM `user`
             WHERE " .
                $this->getDateCondition('user', 'date_created') . "
                AND last_login IS NULL AND `activation_code` <> ''
             ORDER BY `date_created` DESC"
        );
        while ($r = sql_fetch_assoc($rs)) {
            self::message(2, $r['registration_date'] . ' ' . $r['username']);
        }
        sql_free_result($rs);

        echo "\n";
        self::message(0, 'Private emails sent:');
        $rs = sql(
            "SELECT
                `email_user`.`date_created` `emaildate`,
                `user`.`username` `sender_name`
             FROM `email_user`
             JOIN `user` ON `user`.`user_id` = `email_user`.`from_user_id`
             WHERE " . $this->getDateCondition('email_user', 'date_created') . "
             ORDER BY `email_user`.`date_created` DESC"
        );
        while ($r = sql_fetch_assoc($rs)) {
            self::message(2, $r['emaildate'] . ' from:' . $r['sender_name']);
        }
        sql_free_result($rs);
    }


    # resend emails
    
    public function resendLogNotifications()
    {
        # delete notification protocol
        sql(
            "DELETE FROM `watches_notified`
             WHERE
                `object_type` = '&1'
                AND " . $this->getDateCondition('watches_notified', 'date_created'),
            OBJECT_CACHELOG
        );

        # re-notify owners
        sql(
            "UPDATE `cache_logs`
             SET `owner_notified` = 0
             WHERE " . $this->getDateCondition('cache_logs', 'date_created')
        );
        $owner_notifications = sql_affected_rows();

        # Re-notify direct watchers. See also trigger cacheLogsAfterInsert.
        sql(
            "INSERT IGNORE INTO `watches_logqueue` (`log_id`, `user_id`)
                SELECT `cache_logs`.`id` `log_id`, `cache_watches`.`user_id`
                FROM `cache_logs`
                JOIN `caches` ON `caches`.`cache_id` = `cache_logs`.`cache_id`
                JOIN `cache_status` ON `cache_status`.`id` = `caches`.`status`
                JOIN `cache_watches` ON `cache_watches`.`cache_id` = `caches`.`cache_id`
                WHERE
                    " . $this->getDateCondition('cache_logs', 'date_created') . "
                    AND `cache_status`.`allow_user_view` = 1"
        );
        $watcher_notifications = sql_affected_rows();

        # Re-notify list watchers. See also trigger cacheLogsAfterInsert.
        sql(
            "INSERT IGNORE INTO `watches_logqueue` (`log_id`, `user_id`)
                SELECT `cache_logs`.`id` `log_id`, `cache_list_watches`.`user_id`
                FROM `cache_logs`
                JOIN `caches` ON `caches`.`cache_id` = `cache_logs`.`cache_id`
                JOIN `cache_status` ON `cache_status`.`id` = `caches`.`status`
                JOIN `cache_list_items` ON `cache_list_items`.`cache_id` = `cache_logs`.`cache_id`
                JOIN `cache_list_watches` ON `cache_list_watches`.`cache_list_id` = `cache_list_items`.`cache_list_id`
                WHERE
                    " . $this->getDateCondition('cache_logs', 'date_created') . "
                    AND `cache_status`.`allow_user_view` = 1"
        );
        $watcher_notifications += sql_affected_rows();

        self::message(0, $owner_notifications . ' owners will be notified');
        self::message(0, $watcher_notifications . ' watchers will be notified');
    }

    public function resendCacheNotifications()
    {
        $notifies_wating = sql_value("SELECT COUNT(*) FROM `notify_waiting`", 0);

        $rs = sql(
            "SELECT `cache_id`, `latitude`, `longitude`
             FROM `caches`
             JOIN `cache_status` ON `cache_status`.`id` = `caches`.`status`
             WHERE
                " . $this->getDateCondition('caches', 'date_created') . "
                AND `cache_status`.`allow_user_view` = 1"
        );
        while ($r = sql_fetch_assoc($rs)) {
            sql(
                "CALL sp_notify_new_cache('&1', '&2', '&3', 1)",
                $r['cache_id'],
                $r['longitude'],
                $r['latitude']
            );
        }
        sql_free_result($rs);

        $new_notifications =
            sql_value("SELECT COUNT(*) FROM `notify_waiting`", 0) - $notifies_wating;
        self::message(0, $new_notifications . ' new cache notifications will be sent');
    }
    
    public function resendActivationCodes()
    {
        $rs = sql(
            "SELECT `user_id`
             FROM `user`
             WHERE
                " . $this->getDateCondition('user', 'date_created') . "
                AND last_login IS NULL
                AND `activation_code` <> ''"
        );
        $n = 0;
        while ($r = sql_fetch_assoc($rs)) {
            $user = new user($r['user_id']);
            $user->sendRegistrationCode();
            ++ $n;
        }
        sql_free_result($rs);
        self::message(0, $n . ' users have been re-sent the activation code');
    }
}
