<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Automatic archiving of disabled caches
 ***************************************************************************/

checkJob(new autoarchive());

class autoarchive
{
    public $name = 'autoarchive';
    public $interval = 43200;  // twice per day


    public function run()
    {
        global $opt, $login;

        if ($opt['cron']['autoarchive']['run']) {
            if (!$login->logged_in()) {
                echo $this->name . ": not logged in / no system user configured\n";
            } elseif ($login->hasAdminPriv(ADMIN_USER)) {
                $this->archive_disabled_caches();
                $this->archive_events();
            } else {
                echo $this->name . ": user '" . $opt['logic']['systemuser']['user'] . "' cannot maintain caches\n";
            }
        }
    }

    public function archive_disabled_caches()
    {
        // Logging of status changes in cache_status_modified has started on June 1, 2013.
        // For archiving caches that were disabled earlier, we also check the listing
        // modification date.

        // This statement may be optimized. It typically runs for ~15 seconds at OC.de.
        $rs = sql(
            '
            SELECT `caches`.`cache_id`,
                   `caches`.`user_id`,
                   DATEDIFF(NOW(), `listing_last_modified`) AS `listing_age`,
                   (SELECT MAX(`date_modified`) FROM `cache_status_modified` `csm`
                    WHERE `csm`.`cache_id`=`caches`.`cache_id` AND `csm`.`new_state`=2)
                   `disable_date`,
                   (SELECT MAX(`user_id`) FROM `cache_status_modified` `csm`
                    WHERE `csm`.`cache_id`=`cache_id` AND `csm`.`date_modified`=`disable_date`)
                   `disabled_by`,
                   IFNULL(DATEDIFF(NOW(), `user`.`last_login`), 150) `login_lag`,
                   `ca`.`attrib_id` IS NOT NULL `seasonal_cache`
            FROM `caches`
            LEFT JOIN `user` ON `user`.`user_id`=`caches`.`user_id`
            LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=60
            WHERE `status`=2 AND DATEDIFF(NOW(), `listing_last_modified`) > 184
            ORDER BY `listing_last_modified`'
        );

        $archived = 0;
        while ($rCache = sql_fetch_assoc($rs)) {
            if ($rCache['listing_age'] > 366 ||
                ($rCache['listing_age'] > 184 &&
                    (sql_value("SELECT DATEDIFF(NOW(),'&1')", 0, $rCache['disable_date']) > 366 ||
                        (!$rCache['seasonal_cache'] &&
                            (($rCache['disabled_by'] != 0 && $rCache['disabled_by'] != $rCache['user_id'] && $rCache['login_lag'] > 45)
                                ||
                                ($rCache['disabled_by'] == $rCache['user_id'] && $rCache['login_lag'] >= $rCache['listing_age']))
                            &&
                            sql_value(
                                "SELECT MAX(`date`) FROM `cache_logs` WHERE `cache_logs`.`cache_id`='&1'",
                                '',
                                $rCache['cache_id']
                            ) < $rCache['disable_date']
                        )
                    )
                )
            ) {
                $months = ($rCache['listing_age'] > 366 ? 12 : 6);
                $this->archive_cache(
                    $rCache['cache_id'],
                    'This cache has been "temporarily unavailable" for more than %1 months now; ' .
                    'therefore it is being archived automatically. The owner may decide to ' .
                    'maintain the cache and re-enable the listing.',
                    $months
                );
                ++ $archived;

                // This limit throttles archiving. If something goes wrong, it won't
                // produce too much trouble.
                if ($archived >= 10) {
                    break;
                }
            }
        }
        sql_free_result($rs);
    }

    public function archive_events()
    {
        // To prevent archiving events that were accidentally published with a wrong
        // event date - before the owner notices it - we also apply a limit of one month
        // to the publication date.
        $rs = sql(
            'SELECT `cache_id`
            FROM `caches`
            WHERE `caches`.`type`=6 AND `caches`.`status`=1
            AND GREATEST(`date_hidden`,`date_created`) < NOW() - INTERVAL 35 DAY
            ORDER BY `date_hidden`
            LIMIT 1'
        );
        while ($rCache = sql_fetch_assoc($rs)) {
            $this->archive_cache(
                $rCache['cache_id'],
                'This event took place more than five weeks ago; therefore it is ' .
                'being archived automatically. The owner may re-enable the listing ' .
                'if it should stay active for some exceptional reason.'
            );
        }
        sql_free_result($rs);
    }

    public function archive_cache($cache_id, $comment, $months = 0)
    {
        global $opt, $login, $translate;

        $log = cachelog::createNew($cache_id, $login->userid);
        if ($log === false) {
            echo $this->name . ": cannot create log for cache $cache_id\n";
        } else {
            $cache = new cache($cache_id);
            if (!$cache->setStatus(3) || !$cache->save()) {
                echo $this->name . ": cannot change status of cache $cache_id\n";
            } else {
                // create log
                $log->setType(cachelog::LOGTYPE_ARCHIVED, true);
                $log->setOcTeamComment(true);
                $log->setDate(date('Y-m-d'));
                // Log without time, so that owner reactions will always appear AFTER
                // the system log, no matter if logged with or without date.

                // create log text in appropriate language
                $translated_comment = $translate->t($comment, '', '', 0, '', 1, $cache->getDefaultDescLanguage());
                $translated_comment = str_replace('%1', $months, $translated_comment);
                $log->setText('<p>' . $translated_comment . '</p>');
                $log->setTextHtml(1);

                if (!$log->save()) {
                    echo $this->name . ": could not save archive log for cache $cache_id\n";
                }
            }
        }
    }
}
