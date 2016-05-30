<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Publish new geocaches that are marked for timed publish
 ***************************************************************************/

checkJob(new user_delete());

class user_delete
{
    public $name = 'user_delete';
    public $interval = 86400;

    public function run()
    {
        sql('set @allowdelete=1');
        sql(
            'DELETE FROM `user`
            WHERE `date_created`<DATE_ADD(NOW(), INTERVAL -21 DAY)
            AND `is_active_flag`=0 AND `activation_code`!=\'\''
        );
    }
}
