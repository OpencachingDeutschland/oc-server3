<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Publish new geocaches that are marked for timed publish
 ***************************************************************************/

checkJob(new user_delete());

class user_delete
{
	var $name = 'user_delete';
	var $interval = 86400;

	function run()
	{
		sql("DELETE FROM `user` WHERE `date_created`<DATE_ADD(NOW(), INTERVAL -21 DAY) AND `is_active_flag`=0 AND `activation_code`!=''");
	}
}
?>