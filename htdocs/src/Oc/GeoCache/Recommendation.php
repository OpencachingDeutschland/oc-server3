<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace Oc\GeoCache;

class Recommendation
{
    /**
     * @param $logId
     */
    public static function discardRecommendation($logId)
    {
        $rsLog = sql(
            "SELECT
            `cache_logs`.`cache_id`,
            `cache_logs`.`user_id`,
            `cache_rating`.`rating_date` IS NOT NULL AS `is_rating_log`
        FROM `cache_logs`
        LEFT JOIN `cache_rating`
            ON `cache_rating`.`cache_id`=`cache_logs`.`cache_id`
            AND `cache_rating`.`user_id`=`cache_logs`.`user_id`
            AND `cache_rating`.`rating_date`=`cache_logs`.`date`
        WHERE
            `cache_logs`.`id`='&1' AND `cache_logs`.`type` IN (1,7)",
            $logId
        );

        if ($rLog = sql_fetch_assoc($rsLog)) {
            $rsFirstOtherFound = sql(
                "SELECT `date` FROM `cache_logs`
             WHERE `cache_id`='&1' AND `user_id`='&2' AND `id`<>'&3' AND `type` IN (1,7)
             ORDER BY `date`
             LIMIT 1",
                $rLog['cache_id'],
                $rLog['user_id'],
                $logId
            );
            $rFirstOtherFound = sql_fetch_assoc($rsFirstOtherFound);
            sql_free_result($rsFirstOtherFound);

            if ($rFirstOtherFound && $rLog['is_rating_log']) {
                sql(
                    "UPDATE `cache_rating`
                 SET `rating_date`='&3'
                 WHERE `cache_id`='&1' AND `user_id`='&2'",
                    $rLog['cache_id'],
                    $rLog['user_id'],
                    $rFirstOtherFound['date']
                );
                // This will trigger an cache_logs.last_modified update of the corresponding
                // log, so that XML interface will resend it with the updated
                // "recommendation" flag.
            } elseif (!$rFirstOtherFound) {
                // This is also called for $rLog['is_rating_log'] == false, so that
                // even a rating record with inconsistent date gets deleted.
                sql(
                    "DELETE FROM `cache_rating` WHERE `cache_id` = '&1' AND `user_id` = '&2'",
                    $rLog['cache_id'],
                    $rLog['user_id']
                );
            }
        }
        sql_free_result($rsLog);
    }
}
