<?php
	/***************************************************************************
		For license information see doc/license.txt

		Unicode Reminder メモ

		(X)HTML search output
		Used by Ocprop
	****************************************************************************/

	require_once($stylepath . '/search1/icons.inc.php');
	require_once('lib/search1/cache_icon.inc.php');

	$search_output_file_download = false;

	$sAddFields .= ', `caches`.`name`, `caches`.`difficulty`, `caches`.`terrain`,
	                  `caches`.`desc_languages`, `caches`.`date_created`,
	                  `user`.`username`,
	                  `cache_type`.`icon_large`,
	                  `cache_type`.`name` `cacheTypeName`,
	                  IFNULL(`stat_caches`.`found`, 0) `founds`,
	                  IFNULL(`stat_caches`.`toprating`, 0) `topratings`,
	                  IF(ISNULL(`tbloconly`.`cache_id`), 0, 1) AS `oconly`';

	$sAddJoin .= 'INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
	              INNER JOIN `cache_type` ON `cache_type`.`id`=`caches`.`type`
	               LEFT JOIN `caches_attributes` AS `tbloconly`
	                      ON `caches`.`cache_id`=`tbloconly`.`cache_id` AND `tbloconly`.`attrib_id`=6';


function search_output()
{
	global $sqldebug, $stylepath, $tplname, $logdateformat, $usr, $bgcolor1, $bgcolor2;
	global $string_by, $caches_olddays, $caches_newstring, $caches_oconlystring, $showonmap;
	global $options, $lat_rad, $lon_rad, $distance_unit, $startat, $caches_per_page, $sql;

	$tplname = 'search1/search.result.caches';
	$cache_line = read_file($stylepath . '/search1/search.result.caches.row.tpl.php');
	$cache_line = mb_ereg_replace('{string_by}', $string_by, $cache_line);
	$caches_output = '';

	// output range
	$startat = floor($startat / $caches_per_page) * $caches_per_page;
	$sql .= ' LIMIT ' . $startat . ', ' . $caches_per_page;

	// run SQL query
	$nRowIndex = 0;
	$rs_caches = sql_slave("SELECT SQL_BUFFER_RESULT SQL_CALC_FOUND_ROWS " . $sql, $sqldebug);
	$resultcount = sql_value_slave('SELECT FOUND_ROWS()', 0);
	tpl_set_var('results_count', $resultcount);

	while ($caches_record = sql_fetch_array($rs_caches))
	{
		$tmpline = $cache_line;

		list($iconname, $inactive) = getCacheIcon($usr['userid'], $caches_record['cache_id'], $caches_record['status'],
										 $caches_record['user_id'], $caches_record['icon_large']);

		$tmpline = mb_ereg_replace('{icon_large}', $iconname, $tmpline);

		$tmpline = mb_ereg_replace('{cachetype}', htmlspecialchars(t($caches_record['cacheTypeName']), ENT_COMPAT, 'UTF-8'), $tmpline);

		// short_desc ermitteln TODO: nicht die erste sondern die richtige wählen
		$rsdesc = sql_slave("SELECT `short_desc` FROM `cache_desc` WHERE `cache_id`='&1' LIMIT 1", $caches_record['cache_id']);
		$desc_record = sql_fetch_array($rsdesc);
		mysql_free_result($rsdesc);

		$tmpline = mb_ereg_replace('{short_desc}', htmlspecialchars($desc_record['short_desc'], ENT_COMPAT, 'UTF-8'), $tmpline);

		$dDiff = abs(dateDiff('d', $caches_record['date_created'], date('Y-m-d')));
		if ($dDiff < $caches_olddays)
			$tmpline = mb_ereg_replace('{new}', $caches_newstring, $tmpline);
		else
			$tmpline = mb_ereg_replace('{new}', '', $tmpline);

		$tmpline = mb_ereg_replace('{diffpic}', icon_difficulty("diff", $caches_record['difficulty']), $tmpline);
		$tmpline = mb_ereg_replace('{terrpic}', icon_difficulty("terr", $caches_record['terrain']), $tmpline);
		$tmpline = mb_ereg_replace('{ratpic}', icon_rating($caches_record['founds'], $caches_record['topratings']), $tmpline);

		if ($caches_record['oconly'] == 1)
			$tmpline = mb_ereg_replace('{oconly}', $caches_oconlystring, $tmpline);
		else
			$tmpline = mb_ereg_replace('{oconly}', '', $tmpline);

		// get last logs
		if ($options['sort'] != 'bymylastlog' || $usr === false)
			$ownlogs = "";
		else
			$ownlogs = " AND `cache_logs`.`user_id`='" . sql_escape($usr['userid']) . "'";
		$sql = 'SELECT `cache_logs`.`id` `id`, `cache_logs`.`type` `type`, `cache_logs`.`date` `date`, `log_types`.`icon_small` `icon_small`
				FROM `cache_logs`, `log_types`
				WHERE `cache_logs`.`cache_id`=\'' . sql_escape($caches_record['cache_id']) . '\'
				AND `log_types`.`id`=`cache_logs`.`type`' . $ownlogs . '
				ORDER BY `cache_logs`.`date` DESC LIMIT 6';
		$result = sql_slave($sql);

		if ($row = sql_fetch_array($result))
		{
			$loglink = '<a href=\'viewlogs.php?cacheid='.htmlspecialchars($caches_record['cache_id'], ENT_COMPAT, 'UTF-8').'#log'.htmlspecialchars($row['id'], ENT_COMPAT, 'UTF-8').'\'>';
			$tmpline = mb_ereg_replace('{logimage1}',
				$loglink . icon_log_type($row['icon_small'], ""). '</a>{gray_s}' . $loglink. date($logdateformat, strtotime($row['date'])) . '{gray_e}</a>', $tmpline);
			$tmpline = mb_ereg_replace('{logdate1}', "", $tmpline);
		}
		else
		{
			$tmpline = mb_ereg_replace('{logimage1}', "<img src='images/trans.gif' border='0' width='16' height='16' />", $tmpline);
			$tmpline = mb_ereg_replace('{logdate1}', "--.--.----", $tmpline);
		}

		$lastlogs = "";
		while ($row = sql_fetch_array($result))
		{
			$lastlogs .= '<a href=\'viewlogs.php?cacheid=' . urlencode($caches_record['cache_id']) . '#log' . htmlspecialchars($row['id'], ENT_COMPAT, 'UTF-8') . '\'>' . icon_log_type($row['icon_small'], '') . '</a>&nbsp;';
		}
		$tmpline = mb_ereg_replace('{lastlogs}', $lastlogs, $tmpline);

		// und jetzt noch die Richtung ...
		if ($caches_record['distance'] > 0)
		{
			$tmpline = mb_ereg_replace('{direction}', Bearing2Text(calcBearing($lat_rad / 3.14159 * 180, $lon_rad / 3.14159 * 180, $caches_record['latitude'], $caches_record['longitude']), 1), $tmpline);
		}
		else
			$tmpline = mb_ereg_replace('{direction}', '', $tmpline);

		$desclangs = '';
		$aLangs = mb_split(',', $caches_record['desc_languages']);
		foreach ($aLangs AS $thislang)
		{
			$desclangs .= '<a href="viewcache.php?cacheid=' . urlencode($caches_record['cache_id']) . '&desclang=' . urlencode($thislang) . '" style="text-decoration:none;"><b><font color="blue">' . htmlspecialchars($thislang, ENT_COMPAT, 'UTF-8') . '</font></b></a> ';
		}

		// strikeout inavtive caches
		// see also res_cachestatus_span.tpl
		$status_style = "";  // (colored) strike-through for inactive caches
		$line_style = "";    // color of the linked cache name
		$name_style = "";    // color of "by <username>"
		switch ($caches_record['status'])
		{
			case 2: // disabled
			        $status_style = "text-decoration: line-through;";
			        break;
			case 3: // archived
			case 6: // locked
			        $status_style = "text-decoration: line-through; color: #c00000;";
			        // $line_style = "color:grey";
			        break;
			case 7: // locked, invisible
			        $status_style = "text-decoration: line-through; color: #e00000";
			        $name_style = "color: #e00000";
			        // $line_style = "color:grey";
			        break;
			case 5: // not published yet
			        $name_style = "color: #e00000";
			        break;
			default: $status_style = $line_style = "";
		}

		$tmpline = mb_ereg_replace('{line_style}', $line_style, $tmpline);
		$tmpline = mb_ereg_replace('{status_style}', $status_style, $tmpline);
		$tmpline = mb_ereg_replace('{name_style}', $name_style, $tmpline);
		$tmpline = mb_ereg_replace('{desclangs}', $desclangs, $tmpline);
		$tmpline = mb_ereg_replace('{cachename}', htmlspecialchars($caches_record['name'], ENT_COMPAT, 'UTF-8'), $tmpline);
		$tmpline = mb_ereg_replace('{urlencode_cacheid}', htmlspecialchars(urlencode($caches_record['cache_id']), ENT_COMPAT, 'UTF-8'), $tmpline);
		$tmpline = mb_ereg_replace('{urlencode_userid}', htmlspecialchars(urlencode($caches_record['user_id']), ENT_COMPAT, 'UTF-8'), $tmpline);
		$tmpline = mb_ereg_replace('{username}', htmlspecialchars($caches_record['username'], ENT_COMPAT, 'UTF-8'), $tmpline);
		$tmpline = mb_ereg_replace('{position}', $nRowIndex + $startat + 1, $tmpline);

		if ($caches_record['distance'] == NULL)
			$tmpline = mb_ereg_replace('{distance}', '', $tmpline);
		else
			$tmpline = mb_ereg_replace('{distance}', htmlspecialchars(sprintf("%01.1f", $caches_record['distance']), ENT_COMPAT, 'UTF-8'), $tmpline);

		// backgroundcolor of line
		if (($nRowIndex % 2) == 1) 	$bgcolor = $bgcolor2;
		else				$bgcolor = $bgcolor1;

		if ($inactive)
		{
			//$bgcolor = $bgcolor_inactive;
			$tmpline = mb_ereg_replace('{gray_s}', "<span class='text_gray'>", $tmpline);
			$tmpline = mb_ereg_replace('{gray_e}', "</span>", $tmpline);
		}
		else
		{
			$tmpline = mb_ereg_replace('{gray_s}', "", $tmpline);
			$tmpline = mb_ereg_replace('{gray_e}', "", $tmpline);
		}

		$tmpline = mb_ereg_replace('{bgcolor}', $bgcolor, $tmpline);

		$nRowIndex++;
		$caches_output .= $tmpline;
	}
	mysql_free_result($rs_caches);

	tpl_set_var('results', $caches_output);

	// more than one page?
	if ($resultcount <= $caches_per_page)
		$pages = '';
	else
	{
		if ($startat > 0)  // Ocprop:  queryid=([0-9]+)
			$pages = '<a href="search1.php?queryid=' . $options['queryid'] . '&startat=0"><img src="resource2/ocstyle/images/navigation/16x16-browse-first.png" width="16" height="16"></a> <a href="search1.php?queryid=' . $options['queryid'] . '&startat=' . ($startat - $caches_per_page) . '"><img src="resource2/ocstyle/images/navigation/16x16-browse-prev.png" width="16" height="16"></a></a> ';
		else
			$pages = ' <img src="resource2/ocstyle/images/navigation/16x16-browse-first-inactive.png" width="16" height="16"></a> <img src="resource2/ocstyle/images/navigation/16x16-browse-prev-inactive.png" width="16" height="16"></a> ';

		$frompage = ($startat / $caches_per_page) - 3;
		if ($frompage < 1) $frompage = 1;
		$maxpage = ceil($resultcount / $caches_per_page);
		$topage = $frompage + 8;
		if ($topage > $maxpage) $topage = $maxpage;

		for ($i = $frompage; $i <= $topage; $i++)
		{
			if (($startat / $caches_per_page + 1) == $i)
				$pages .= ' <b>' . $i . '</b>';
			else
				$pages .= ' <a href="search1.php?queryid=' . $options['queryid'] . '&startat=' . (($i - 1) * $caches_per_page) . '">' . $i . '</a>';
		}

		if ($startat / $caches_per_page < ($maxpage - 1))
			$pages .= ' <a href="search1.php?queryid=' . $options['queryid'] . '&startat=' . ($startat + $caches_per_page) . '"><img src="resource2/ocstyle/images/navigation/16x16-browse-next.png" width="16" height="16"></a> <a href="search1.php?queryid=' . $options['queryid'] . '&startat=' . (($maxpage - 1) * $caches_per_page) . '"><img src="resource2/ocstyle/images/navigation/16x16-browse-last.png" width="16" height="16"></a> ';
		else
			$pages .= ' <img src="resource2/ocstyle/images/navigation/16x16-browse-next-inactive.png" width="16" height="16"> <img src="resource2/ocstyle/images/navigation/16x16-browse-last-inactive.png" width="16" height="16"></a>';
	}

	//'<a href="search1.php?queryid=' . $options['queryid'] . '&startat=20">20</a> 40 60 80 100';
	//$caches_per_page
	//count($caches) - 1
	tpl_set_var('pages', $pages);
	tpl_set_var('showonmap', $showonmap);

	// downloads
	tpl_set_var('queryid', $options['queryid']);
	tpl_set_var('startat', $startat);

	tpl_set_var('startatp1', min($resultcount,$startat + 1));

	if (($resultcount - $startat) < 500)
		tpl_set_var('endat', $startat + $resultcount - $startat);
	else
		tpl_set_var('endat', $startat + 500);

	// kompatibilität!
	if ($distance_unit == 'sm')
		tpl_set_var('distanceunit', 'mi');
	else if ($distance_unit == 'nm')
		tpl_set_var('distanceunit', 'sm');
	else
		tpl_set_var('distanceunit', $distance_unit);

	tpl_set_var('displaylastlogs', $options['sort'] == 'bymylastlog' ? 'none' : 'inline');
	tpl_set_var('displayownlogs', $options['sort'] == 'bymylastlog' ? 'inline' : 'none');

	if ($sqldebug == true)
		sqldbg_end();
	else
		tpl_BuildTemplate();
}

?>