<?php
/***************************************************************************
													    ./lib/cache_icon.inc.php
															--------------------
		begin                : Sun october 9 2005
		copyright            : (C) 2005 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

		Unicode Reminder メモ

	function to generate the name of the needed cache-icon

 ****************************************************************************/

function getCacheIcon($user_id, $cache_id, $cache_status, $cache_userid, $iconname)
{
	global $dblink;

	$cacheicon_searchable = false;
	$cacheicon_type = "";
	$inactive = false;

	// mark if found
	if(isset($user_id))
	{
		$found = 0;
		$resp = sql("SELECT `type` FROM `cache_logs` WHERE `cache_id`='&1' AND `user_id`='&2' ORDER BY `type`", $cache_id, $user_id);
		while($row = sql_fetch_assoc($resp))
		{
			if($found <= 0)
			{
				switch($row['type'])
				{
				  case 1:
				  case 7: $found = $row['type']; $cacheicon_type = "-found"; $inactive = true; break;
				  case 2: $found = $row['type']; $cacheicon_type = "-dnf"; break;
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
			case 5: $cacheicon_searchable = "-s"; break;      // fix for RT ticket #3403
			case 6: $cacheicon_searchable = "-a"; break;
			case 7: $cacheicon_searchable = "-a"; break;
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
			case 6: $inactive = true; $cacheicon_searchable = "-a"; break;
			case 7: $inactive = true; $cacheicon_searchable = "-a"; break;
		}
	}

	// cacheicon
	$iconext = "." . mb_eregi_replace("^.*\.", "", $iconname);
	$iconname = mb_eregi_replace("\..*", "", $iconname);
	$iconname .= $cacheicon_searchable . $cacheicon_type . $iconext;

	return array($iconname, $inactive);
}

function getSmallCacheIcon($iconname)
{
	$iconname = mb_eregi_replace('([^/]+)$', '16x16-\1', $iconname);
        return $iconname;
}
?>