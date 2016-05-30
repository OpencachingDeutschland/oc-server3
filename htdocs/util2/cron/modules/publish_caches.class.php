<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Publish new geocaches that are marked for timed publish
 ***************************************************************************/

checkJob(new publish_caches());

class publish_caches
{
    public $name = 'publish_caches';
    public $interval = 60;

    public function run()
    {
        global $login;

        $rsPublish = sql(
            'SELECT `cache_id`, `user_id`
            FROM `caches`
            WHERE `status`=5
            AND NOT ISNULL(`date_activate`)
            AND `date_activate`<=NOW()'
        );
        while ($rPublish = sql_fetch_array($rsPublish)) {
            $userid = $rPublish['user_id'];
            $cacheid = $rPublish['cache_id'];

            // update cache status to active
            sql("SET @STATUS_CHANGE_USER_ID='&1'", $login->userid);
            sql("UPDATE `caches` SET `status`=1, `date_activate`=NULL WHERE `cache_id`='&1'", $cacheid);
        }
        sql_free_result($rsPublish);
    }
}
