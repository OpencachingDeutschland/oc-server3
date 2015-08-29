<?php
	/***************************************************************************
		For license information see doc/license.txt

		Unicode Reminder メモ

		HTML search output
		(Used by Ocprop)

	****************************************************************************/

	$search_output_file_download = false;

	$sAddFields .= ', `caches`.`name`, `caches`.`difficulty`, `caches`.`terrain`,
	                  `caches`.`desc_languages`, `caches`.`date_created`,
	                  `user`.`username`,
	                  `cache_type`.`icon_large`,
	                  `stt`.`text` AS `cacheTypeName`,
	                  IFNULL(`stat_caches`.`found`, 0) `founds`,
	                  IFNULL(`stat_caches`.`toprating`, 0) `topratings`,
	                  IF(ISNULL(`tbloconly`.`cache_id`), 0, 1) AS `oconly`';

	$sAddJoin .= ' INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
	               INNER JOIN `cache_type` ON `cache_type`.`id`=`caches`.`type`
	                LEFT JOIN `caches_attributes` AS `tbloconly`
	                       ON `caches`.`cache_id`=`tbloconly`.`cache_id` AND `tbloconly`.`attrib_id`=6
	                LEFT JOIN `sys_trans_text` `stt` ON `stt`.`trans_id`=`cache_type`.`trans_id`';

	$sAddWhere .= ' AND `stt`.`lang`=\'' . sql_escape($opt['template']['locale']) . '\'';


function search_output()
{
	global $opt, $tpl, $login;
	global $enable_mapdisplay;
	global $called_by_search, $called_by_profile_query, $options, $lat_rad, $lon_rad, $distance_unit;
	global $startat, $caches_per_page, $sql;

	$tpl->name = 'search.result.caches';
	$tpl->menuitem = MNU_CACHES_SEARCH_RESULT;

	$startat = floor($startat / $caches_per_page) * $caches_per_page;
	$sql .= ' LIMIT ' . $startat . ', ' . $caches_per_page;

	// run SQL query
	sql_enable_foundrows();
	$rs_caches = sql_slave("SELECT SQL_BUFFER_RESULT SQL_CALC_FOUND_ROWS " . $sql);
	$resultcount = sql_value_slave('SELECT FOUND_ROWS()', 0);
	sql_foundrows_done();
	$tpl->assign('results_count', $resultcount);
	$tpl->assign('startat',$startat);

	$caches = array();
	while ($rCache = sql_fetch_array($rs_caches))
	{
		// select best-fitting short desc for active language
		$rCache['short_desc'] = sql_value_slave("
				SELECT `short_desc`
				FROM `cache_desc`
				WHERE `cache_id`='&1' AND `language`='&2'",
				false, $rCache['cache_id'], $opt['template']['locale']);
		if ($rCache['short_desc'] === false) $rCache['short_desc'] = sql_value_slave("
				SELECT `short_desc`
				FROM `cache_desc`
				WHERE `cache_id`='&1' AND `language`='EN'",
				false, $rCache['cache_id']);
		if ($rCache['short_desc'] === false) $rCache['short_desc'] = sql_value_slave("
				SELECT `short_desc`
				FROM `cache_desc`
				WHERE `cache_id`='&1'
				ORDER BY `date_created`
				LIMIT 1",
				'', $rCache['cache_id']);

		$rCache['desclangs'] = mb_split(',', $rCache['desc_languages']);

		// decide if the cache is new
		$dDiff = dateDiff('d', $rCache['date_created'], date('Y-m-d'));
		$rCache['isnew'] = ($dDiff <= NEWCACHES_DAYS);
		
		// get last logs
		if ($options['sort'] != 'bymylastlog' || !$login->logged_in())
			$ownlogs = "";
		else
			$ownlogs = " AND `cache_logs`.`user_id`='" . sql_escape($login->userid) . "'";
		$sql = "
				SELECT `cache_logs`.`id`, `cache_logs`.`type`, `cache_logs`.`date`, `log_types`.`icon_small`
				FROM `cache_logs`, `log_types`
				WHERE `cache_logs`.`cache_id`='" . sql_escape($rCache['cache_id']) . "'
				      AND `log_types`.`id`=`cache_logs`.`type`" . $ownlogs . "
				ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`date_created` DESC
				LIMIT 6";
		$rs = sql_slave($sql);
		$rCache['logs'] = sql_fetch_assoc_table($rs);
		$rCache['firstlog'] = array_shift($rCache['logs']);

		// get direction from search coordinate 
		if ($rCache['distance'] > 0)
		{
			$direction = geomath::calcBearing($lat_rad / 3.14159 * 180, $lon_rad / 3.14159 * 180, $rCache['latitude'], $rCache['longitude']);
			$rCache['direction_deg'] = round($direction / 22.5) * 22.5;
			$rCache['direction_txt'] = geomath::Bearing2Text($direction, 1);
		}
		else 		
			$rCache['direction_deg'] = false;
		
		// other data
		$rCache['icon'] = getCacheIcon($login->userid, $rCache['cache_id'], $rCache['status'], $rCache['user_id'], $rCache['icon_large']);
		$rCache['redname'] = ($rCache['status']==5 || $rCache['status']==7); 
			
		$caches[] = $rCache;
	}
	mysql_free_result($rs_caches);

	$tpl->assign('caches', $caches);

	$pager = new pager('search.php?queryid=' . $options['queryid'] . '&startat={offset}', 2, 9);
	$pager->make_from_offset($startat, $resultcount, $caches_per_page);

	// downloads
	$tpl->assign('queryid', $options['queryid']);

	$tpl->assign('startatp1', min($resultcount,$startat + 1));
	if (($resultcount - $startat) < 500)
		$tpl->assign('endat', $startat + $resultcount - $startat);
	else
		$tpl->assign('endat', $startat + 500);

	// kompatibilität!
	if ($distance_unit == 'sm')
		$tpl->assign('distanceunit', 'mi');
	else if ($distance_unit == 'nm')
		$tpl->assign('distanceunit', 'sm');
	else
		$tpl->assign('distanceunit', $distance_unit);

	$tpl->assign('displayownlogs', $options['sort'] == 'bymylastlog');
	$tpl->assign('search_headline_caches', $called_by_search);
	$tpl->assign('enable_mapdisplay', $enable_mapdisplay);

	// cachelist data
	if (isset($options['cachelist']))
	{
		$tpl->assign('cachelist', $options['cachelist']);
		$tpl->assign('cachelist_pw', $options['cachelist_pw']);
	}
	else
		$tpl->assign('cachelist', false);

	// disable "edit options" for internally generated searches
	if ($options['searchtype'] == 'bylist')
	  $tpl->assign('disable_edit_options',true);

	$tpl->display();
}


function dateDiff($interval, $dateTimeBegin, $dateTimeEnd)
{
  //Parse about any English textual datetime
  //$dateTimeBegin, $dateTimeEnd

  $dateTimeBegin = strtotime($dateTimeBegin);
  if ($dateTimeBegin === -1)
    return("..begin date Invalid");

  $dateTimeEnd = strtotime($dateTimeEnd);
  if ($dateTimeEnd === -1)
    return("..end date Invalid");

  $dif = $dateTimeEnd - $dateTimeBegin;

  switch($interval)
  {
    case "s"://seconds
      return($dif);

    case "n"://minutes
      return(floor($dif/60)); //60s=1m

    case "h"://hours
      return(floor($dif/3600)); //3600s=1h

    case "d"://days
      return(floor($dif/86400)); //86400s=1d

    case "ww"://Week
      return(floor($dif/604800)); //604800s=1week=1semana

    case "m": //similar result "m" dateDiff Microsoft
      $monthBegin = (date("Y",$dateTimeBegin)*12) + date("n",$dateTimeBegin);
      $monthEnd = (date("Y",$dateTimeEnd)*12) + date("n",$dateTimeEnd);
      $monthDiff = $monthEnd - $monthBegin;
      return($monthDiff);

    case "yyyy": //similar result "yyyy" dateDiff Microsoft
      return(date("Y",$dateTimeEnd) - date("Y",$dateTimeBegin));

    default:
      return(floor($dif/86400)); //86400s=1d
  }
}


function getCacheIcon($user_id, $cache_id, $cache_status, $cache_userid, $iconname)
{
	$iconname = mb_eregi_replace("cache/", "", $iconname);
	$iconext = "." . mb_eregi_replace("^.*\.", "", $iconname);
	$iconname = mb_eregi_replace("\..*", "", $iconname);

	// add status
	switch ($cache_status)
	{
		case 1: $iconname .= "-s"; break;
		case 2: $iconname .= "-n"; break;
		case 3: $iconname .= "-a"; break;
		case 4: $iconname .= "-a"; break;
		case 5: $iconname .= "-s"; break;      // fix for RT ticket #3403
		case 6: $iconname .= "-a"; break;
		case 7: $iconname .= "-a"; break;
	}

	// mark if (not) found
	if ($user_id)
	{
		if ($cache_userid == $user_id)
		{
			$iconname .= "-owner";
		}
		else
		{
			$logtype = sql_value_slave("
				SELECT `type`
				FROM `cache_logs`
				WHERE `cache_id`='&1' AND `user_id`='&2' AND `type` IN (1,2,7)
				ORDER BY `type`
				LIMIT 1",
				0, $cache_id, $user_id);

			if ($logtype == 1 || $logtype == 7)
				$iconname .= '-found';
			elseif ($logtype == 2)
				$iconname .= '-dnf';
		}
	}

	return $iconname . $iconext;
}


?>