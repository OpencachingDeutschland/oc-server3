<?php
	/***************************************************************************
															./lib/search.html.inc.php
																-------------------
			begin                : July 25 2004

		For license information see doc/license.txt
 	****************************************************************************/

	/****************************************************************************

		Unicode Reminder メモ

		(X)HTML search output

		TODO: (1) save the options in the database
		      (2) sort the results and the make the final query

	****************************************************************************/

	global $sqldebug;

	require_once($stylepath . '/lib/icons.inc.php');
	require_once('lib/cache_icon.inc.php');

	//prepare the output
	$tplname = 'search.result.caches';
	$caches_per_page = 20;

	//build lines
	$cache_line = read_file($stylepath . '/search.result.caches.row.tpl.php');
	$cache_line = mb_ereg_replace('{string_by}', $string_by, $cache_line);
	$caches_output = '';

	/*
		$lat_rad
		$lon_rad
		$distance_unit
	*/
	$distance_unit = 'km';

	$sql = 'SELECT SQL_BUFFER_RESULT SQL_CALC_FOUND_ROWS ';

	if (isset($lat_rad) && isset($lon_rad))
	{
		$sql .= getSqlDistanceFormula($lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
	}
	else
	{
		if ($usr === false)
		{
			$sql .= 'NULL distance, ';
		}
		else
		{
			//get the users home coords
			$rs_coords = sql_slave("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
			$record_coords = sql_fetch_array($rs_coords);

			if ((($record_coords['latitude'] == NULL) && ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) && ($record_coords['longitude'] == 0)))
			{
				$sql .= 'NULL distance, ';
			}
			else
			{
				//TODO: load from the users-profile
				$distance_unit = 'km';

				$lon_rad = $record_coords['longitude'] * 3.14159 / 180;   
        $lat_rad = $record_coords['latitude'] * 3.14159 / 180; 

				$sql .= getSqlDistanceFormula($record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
			}
			mysql_free_result($rs_coords);
		}
	}
	$sAddJoin = '';
	$sAddGroupBy = '';
	$sAddField = '';
	$sGroupBy = '';

	if ($options['sort'] == 'bylastlog' || $options['sort'] == 'bymylastlog')
	{
		$sAddField = ', MAX(`cache_logs`.`date`) AS `lastLog`';
		$sAddJoin = ' LEFT JOIN `cache_logs` ON `caches`.`cache_id`=`cache_logs`.`cache_id`';
		if ($options['sort'] == 'bymylastlog')
			$sAddJoin .= ' AND `cache_logs`.`user_id`=' . sql_escape($usr === false? 0 : $usr['userid']);
		$sGroupBy = ' GROUP BY `caches`.`cache_id`';
	}
	$sql .= '	`caches`.`name` `name`, `caches`.`status` `status`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`,
				           `caches`.`difficulty` `difficulty`, `caches`.`terrain` `terrain`, `caches`.`desc_languages` `desc_languages`,
				           `caches`.`date_created` `date_created`, `caches`.`type` `type`, `caches`.`cache_id` `cache_id`,
				           `user`.`username` `username`, `user`.`user_id` `user_id`,
				           `cache_type`.`icon_large` `icon_large`,
				           `cache_type`.`name` `cacheTypeName`,
				           IFNULL(`stat_caches`.`found`, 0) `founds`, 
				           IFNULL(`stat_caches`.`toprating`, 0) `topratings`,
				           IF(IFNULL(`stat_caches`.`toprating`,0)>3, 4, IFNULL(`stat_caches`.`toprating`, 0)) `ratingvalue`,
				           IF(ISNULL(`tbloconly`.`cache_id`), 0, 1) AS `oconly`' . 
				           $sAddField
				  . ' FROM `caches`
				INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
				INNER JOIN `cache_type` ON `cache_type`.`id`=`caches`.`type`
				 LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id`
				 LEFT JOIN `caches_attributes` AS `tbloconly` ON `caches`.`cache_id`=`tbloconly`.`cache_id` AND
				                                                 `tbloconly`.`attrib_id`=6' .
				           $sAddJoin 
				 . ' WHERE `caches`.`cache_id` IN (' . $sqlFilter . ')' .
				           $sGroupBy;
	$sortby = $options['sort'];

	$sql .= ' ORDER BY ';
	if ($options['orderRatingFirst'])
		$sql .= '`ratingvalue` DESC, ';

	if ($sortby == 'bylastlog' || $options['sort'] == 'bymylastlog')
	{
		$sql .= '`lastLog` DESC, ';
		$sortby = 'bydistance';
	}

	if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance'))
	{
		$sql .= '`distance` ASC';
	}
	else if ($sortby == 'bycreated')
	{
		$sql .= '`caches`.`date_created` DESC';
	}
	else // by name
	{
		$sql .= '`caches`.`name` ASC';
	}

	//startat?
	$startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
	if (!is_numeric($startat)) $startat = 0;
	if (!is_numeric($caches_per_page)) $caches_per_page = 20;
	$startat = floor($startat / $caches_per_page) * $caches_per_page;
	$sql .= ' LIMIT ' . $startat . ', ' . $caches_per_page;

	$nRowIndex = 0;
	$rs_caches = sql_slave($sql, $sqldebug);

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
		$line_style = "";
		switch ($caches_record['status'])
		{
			case 2: // disabled
			        $status_style = "text-decoration: line-through;";
			        break;
			case 3: // archived
			case 6: // locked
			        $status_style = "text-decoration: line-through; color: grey";
			        $line_style = "color:grey";
			        break;
			case 7: // locked, invisible
			        $status_style = "text-decoration: line-through; color: #e00000";
			        $line_style = "color:grey";
			        break;
			case 5: // not published yet
			        $status_style = "color: #e00000";
			        break;
			default: $status_style = $line_style = "";
		}

		$tmpline = mb_ereg_replace('{line_style}', $line_style, $tmpline);
		$tmpline = mb_ereg_replace('{status_style}', $status_style, $tmpline);
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

		if($inactive)
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

	//more than one page?
	if ($startat > 0)
	{  // Ocprop:  queryid=([0-9]+)
		$pages = t('Seite:') . ' <a href="search.php?queryid=' . $options['queryid'] . '&startat=0">&lt;&lt;</a> <a href="search.php?queryid=' . $options['queryid'] . '&startat=' . ($startat - $caches_per_page) . '">&lt;</a> ';
	}
	else
	{
		$pages = t('Seite:') . ' &lt;&lt; &lt; ';
	}

	$frompage = ($startat / $caches_per_page) - 3;
	if ($frompage < 1) $frompage = 1;

	$maxpage = ceil($resultcount / $caches_per_page);

	$topage = $frompage + 8;
	if ($topage > $maxpage) $topage = $maxpage;

	for ($i = $frompage; $i <= $topage; $i++)
	{
		if (($startat / $caches_per_page + 1) == $i)
		{
			$pages .= ' <b>' . $i . '</b>';
		}
		else
		{
			$pages .= ' <a href="search.php?queryid=' . $options['queryid'] . '&startat=' . (($i - 1) * $caches_per_page) . '">' . $i . '</a>';
		}
	}

	if ($startat / $caches_per_page < ($maxpage - 1))
	{
		$pages .= ' <a href="search.php?queryid=' . $options['queryid'] . '&startat=' . ($startat + $caches_per_page) . '">&gt;</a> <a href="search.php?queryid=' . $options['queryid'] . '&startat=' . (($maxpage - 1) * $caches_per_page) . '">&gt;&gt;</a> ';
	}
	else
	{
		$pages .= ' &gt; &gt;&gt;';
	}

	//'<a href="search.php?queryid=' . $options['queryid'] . '&startat=20">20</a> 40 60 80 100';
	//$caches_per_page
	//count($caches) - 1
	tpl_set_var('pages', $pages);

	// speichern-link
	if ($usr === false)
		tpl_set_var('safelink', '');
	else
		tpl_set_var('safelink', mb_ereg_replace('{queryid}', $options['queryid'], $safelink));

	// downloads
	tpl_set_var('queryid', $options['queryid']);
	tpl_set_var('startat', $startat);

	tpl_set_var('startatp1', $startat + 1);

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
?>