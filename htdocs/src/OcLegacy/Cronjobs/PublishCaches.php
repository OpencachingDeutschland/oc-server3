<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace OcLegacy\Cronjobs;

class PublishCaches
{
    /**
     * @var string
     */
    public $name = 'publish_caches';

    /**
     * @var int
     */
    public $interval = 60;

    public function run(): void
    {
        $rsPublish = sql(
            'SELECT `cache_id`, `user_id`
            FROM `caches`
            WHERE `status`=5
            AND NOT ISNULL(`date_activate`)
            AND `date_activate`<=NOW()'
        );

        while ($rPublish = sql_fetch_array($rsPublish)) {
            $userId = $rPublish['user_id'];
            $cacheId = $rPublish['cache_id'];

            // update cache status to active
            sql("SET @STATUS_CHANGE_USER_ID='&1'", $userId);
            sql("UPDATE `caches` SET `status`=1, `date_activate` = NULL WHERE `cache_id`='&1'", $cacheId);
        }

        sql_free_result($rsPublish);
    }
}
