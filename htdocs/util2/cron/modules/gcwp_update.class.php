<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Update GC waypoint data from external sources
 ***************************************************************************/

checkJob(new gcwp_update());


class gcwp_update
{
	var $name = 'gcwp_update';
	var $interval = 3600;  // every hour


	function run()
	{
		global $opt;

		foreach ($opt['cron']['gcwp']['sources'] as $source)
		{
			$wpdata = @file($source);
			if ($wpdata === FALSE)
				echo "gcwp_update: error reading " . $source . "\n";
			else
				foreach ($wpdata as $line)
				{
					$waypoints = explode(",",trim($line));
					if (count($waypoints) == 2)
						sql("UPDATE `caches` SET `wp_gc_maintained`='&2' WHERE `wp_oc`='&1'",
						    $waypoints[0], $waypoints[1]);
				}
		}
	}
}

?>
