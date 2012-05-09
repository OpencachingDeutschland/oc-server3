<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

function getCacheIcon($user_id, $cache_id, $cache_status, $cache_userid, $iconname)
{
	$cacheicon_searchable = false;
	$cacheicon_type = "";
	$inactive = false;

	// mark if found
	if(isset($user_id))
	{
		$found = 0;
		$resp = sqll("SELECT `type` FROM `cache_logs` WHERE `cache_id`='&1' AND `user_id`='&2' ORDER BY `type`", $cache_id, $user_id);
		while($row = sql_fetch_assoc($resp))
		{
			if($found <= 0)
			{
				switch($row['type'])
				{
					case 1:
					case 7:
						$found = $row['type'];
						$cacheicon_type = "-found";
						$inactive = true;
						break;
					case 2:
						$found = $row['type'];
						$cacheicon_type = "-dnf";
						break;
				}
			}
		}
	}

	if($cache_userid == $user_id)
	{
		$cacheicon_type = "-owner";
		$inactive = true;
		switch($cache_status)
		{
			case 1: $cacheicon_searchable = "-s"; break;
			case 2: $cacheicon_searchable = "-n"; break;
			case 3: $cacheicon_searchable = "-a"; break;
			case 4: $cacheicon_searchable = "-a"; break;
		}

	}
	else
	{
		switch($cache_status)
		{
			case 1: $cacheicon_searchable = "-s"; break;
			case 2: $inactive = true; $cacheicon_searchable = "-n"; break;
			case 3: $inactive = true; $cacheicon_searchable = "-a"; break;
			case 4: $inactive = true; $cacheicon_searchable = "-a"; break;
		}
	}

	// cacheicon
	$iconname = mb_eregi_replace("\..*", "", $iconname);
	$iconname .= $cacheicon_searchable . $cacheicon_type . ".gif";

	return array($iconname, $inactive);
}

function getSmallCacheIcon($iconname)
{
	$iconname = mb_eregi_replace('([^/]+)$', '16x16-\1', $iconname);
        return $iconname;
}
?>