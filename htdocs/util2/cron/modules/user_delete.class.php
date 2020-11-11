<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 *
 *  Publish new geocaches that are marked for timed publish
 ***************************************************************************/

checkJob(new UserDelete());

class UserDelete
{
    public $name = 'user_delete';
    public $interval = 86400;

    public function run(): void
    {
        sql('SET @allowdelete=1');
        sql(
            'DELETE FROM `user`
            WHERE `date_created`<DATE_ADD(NOW(), INTERVAL -21 DAY)
            AND `is_active_flag`=0 AND `activation_code`!=\'\''
        );
    }
}
