<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Publish new geocaches that are marked for timed publish
 ***************************************************************************/

checkJob(new publish_caches());

class publish_caches
{
	var $name = 'publish_caches';
	var $interval = 60;

	function run()
	{
		$rsPublish = sql("SELECT `cache_id`, `user_id` FROM `caches` WHERE `status`=5 AND NOT ISNULL(`date_activate`) AND `date_activate`<=NOW()");
		while($rPublish = sql_fetch_array($rsPublish))
		{
			$userid = $rPublish['user_id'];
			$cacheid = $rPublish['cache_id'];

			// update cache status to active
			sql("UPDATE `caches` SET `status`=1, `date_activate`=NULL WHERE `cache_id`='&1'", $cacheid);
		}
		sql_free_result($rsPublish);
	}
}
?>