<?php
/***************************************************************************
	For license information see doc/license.txt

	Unicode Reminder メモ

	This module handles everything which results in the output of a list of
	caches, including output formatting. It also handles search requests from
	external tools like the Mozilla Firefox plugin.

	Search options will be loaded from
		- a saved query in queries table, if either 'queryid' parameter or
		    'lastqueryid' cookie is present and the query exists; otherwise from
		- supplied HTTP parameters or
		- hard-coded default values

	showresult=1 produces an SQL query from search options, executes it and
	  calls the output formatting module as specified by the 'output' parameter. 
		If 'showresult' != 1, the search options form is presented to the user.

		Note that 'showresult' is also stored in saved queries, so it can be
		automatically included when the 'queryid' parameter is given.

  search type options:
    searchbyname
		searchbydistance
		searchbyowner
		searchbyfinder
		searchbyplz
		searchbyort
		searchbyfulltext
		searchbynofilter
		searchbycacheid
		searchbywp
		searchbylist
		searchall  (needs login)

	output options:
		html         display browsable search results list
		xml          undocumented - very old API
		txt          plain-text cache listing, zipped if more than one cache
		map2         internally used for map display and filtering

		gpx          common geocache data files
		loc
		ovl
		ov2
		kml


	To do:
		- port attributes code to res_attribgroup.tpl (see outputSearchForm)
		- move output data list generation from prepareLocSelectionForm and
		    outputLocidSelectionForm to search_selectlocid.tpl.
		- wtf is "expert mode"?

 ****************************************************************************/

	require 'lib2/web.inc.php';
	require 'lib2/logic/data-license.inc.php';
	require 'lib2/search/search.inc.php';
	require 'templates2/'.$opt['template']['style'].'/search.tpl.inc.php';


	//=========================================================
	//  1. initialize searching and template variables
	//=========================================================

	$tpl->name = 'search';
	$tpl->menuitem = MNU_CACHES_SEARCH;

	// distance constants
	$DEFAULT_DISTANCE_UNIT = 'km';
	$DEFAULT_SEARCH_DISTANCE = 75;

	$multiplier['km'] = 1;
	$multiplier['sm'] = 0.62137;
	$multiplier['nm'] = 0.53996;

	$homecoords = ($login->logged_in() &&
	               sql_value_slave("SELECT `latitude`+`longitude` FROM user WHERE `user_id`='&1'",  0, $login->userid) <> 0);

	// Determine if search.php was called by a search function ('Caches' menu,
	// stored query etc.) or for other purpose (e.g. user profile cache lists):
	$called_by_search = isset($_REQUEST['calledbysearch']) ? $_REQUEST['calledbysearch'] <> 0 : true;
	$called_by_profile_query = false;

	if (isset($_REQUEST['queryid']) || isset($_REQUEST['showresult']))
	{  // Ocprop: showresult, queryid
		$bCookieQueryid = false;
		$queryid = isset($_REQUEST['queryid']) ? $_REQUEST['queryid'] : 0;
		if ($queryid &&
		    sql_value("SELECT `user_id` FROM `queries` WHERE `id`='&1'", 0, $queryid))
		{
			$called_by_profile_query = true;
		}
	}
	else
	{
		$bCookieQueryid = true;
		$queryid = $cookie->get('lastqueryid',false);
		if ($queryid === false ||
			  sql_value("SELECT COUNT(*) FROM `queries` WHERE id='&1'", 0, $queryid) == 0)
		{
			$queryid = 0;
		}

	newquery:
		if ($queryid == 0)
		{
			// initialize search form with defaults, as we have no parameters
			// or saved query to start from

			$_REQUEST['cache_attribs'] = '';
			$rs = sql('SELECT `id` FROM `cache_attrib` WHERE `default`=1 AND NOT IFNULL(`hidden`, 0)=1');
			while ($r = sql_fetch_assoc($rs))
			{
				if ($_REQUEST['cache_attribs'] != '') $_REQUEST['cache_attribs'] .= ';';
				$_REQUEST['cache_attribs'] .= $r['id'];
			}
			sql_free_result($rs);

			$_REQUEST['cache_attribs_not'] = '';
			$rs = sql('SELECT `id` FROM `cache_attrib` WHERE `default`=2 AND NOT IFNULL(`hidden`, 0)=1');
			while ($r = sql_fetch_assoc($rs))
			{
				if ($_REQUEST['cache_attribs_not'] != '') $_REQUEST['cache_attribs_not'] .= ';';
				$_REQUEST['cache_attribs_not'] .= $r['id'];
			}
			sql_free_result($rs);
		}
	}

	$queryid += 0;  // safety measure: force $queryid to be numeric


	//=========================================================
	//  2. Build search options ($options) array
	//=========================================================

	if ($queryid != 0)
	{
		// load search options from stored query

		$query_rs = sql("
			SELECT `user_id`, `options`
			FROM `queries`
			WHERE id='&1' AND (`user_id`=0 OR `user_id`='&2')",
			$queryid, $login->userid);

		if (sql_num_rows($query_rs) == 0)
		{
			// can happen if logged out after query was created (fix for RT #3915)
			$queryid = 0;
			goto newquery;  // goto needs PHP 5.3
			/*
			$tpl->error($error_query_not_found);
			*/
		}
		else
		{
			$record = sql_fetch_array($query_rs);
			$options = unserialize($record['options']);
			if ($record['user_id'] != 0)
				$options['userid'] = $record['user_id'];
			sql_free_result($query_rs);

			$options['queryid'] = $queryid;

			sql("UPDATE `queries` SET `last_queried`=NOW() WHERE `id`='&1'", $queryid);

			// overwrite variable options
			if (isset($_REQUEST['output']))
				$options['output'] =  $_REQUEST['output'];

			if (isset($_REQUEST['showresult']))
			{
				$options['showresult'] = $_REQUEST['showresult'];
			}
			else
			{
				if ($bCookieQueryid)
					$options['showresult'] = 0;
			}

			// get findername from finderid
			$options['finderid'] = isset($options['finderid']) ? $options['finderid'] + 0 : 0;  // Ocprop
			if (isset($options['finder']) && $options['finderid'] > 0)
			{
				$rs_name = sql("SELECT `username` FROM `user` WHERE `user_id`='&1'", $options['finderid']);
				if (sql_num_rows($rs_name) == 1)
				{
					$record_name = sql_fetch_array($rs_name);
					$options['finder'] = $record_name['username'];
				}
				unset($record_name);
				sql_free_result($rs_name);
			}

			// get ownername from ownerid
			$options['ownerid'] = isset($options['ownerid']) ? $options['ownerid'] + 0 : 0;  // Ocprop
			if (isset($options['owner']) && $options['ownerid'] > 0)
			{
				$rs_name = sql("SELECT `username` FROM `user` WHERE `user_id`='&1'", $options['ownerid']);
				if (sql_num_rows($rs_name) == 1)
				{
					$record_name = sql_fetch_array($rs_name);
					$options['owner'] = $record_name['username'];
				}
				unset($record_name);
				sql_free_result($rs_name);
			}
		}
	}
	else  // $queryid == 0
	{
		// build search options from GET/POST parameters or default values

		// hack
		if (isset($_REQUEST['searchto']) && ($_REQUEST['searchto'] != ''))
		{
			unset($_REQUEST['searchbyname']);
			unset($_REQUEST['searchbydistance']);
			unset($_REQUEST['searchbyowner']);
			unset($_REQUEST['searchbyfinder']);
			unset($_REQUEST['searchbyplz']);
			unset($_REQUEST['searchbyort']);
			unset($_REQUEST['searchbyfulltext']);
			unset($_REQUEST['searchbynofilter']);
			unset($_REQUEST['searchall']);
			$_REQUEST[$_REQUEST['searchto']] = "hoho";
		}

		// get the search options parameters and store them in the queries table (to view "the next page")
		$options['f_userowner'] = isset($_REQUEST['f_userowner']) ? $_REQUEST['f_userowner'] : 0;  // Ocprop
		$options['f_userfound'] = isset($_REQUEST['f_userfound']) ? $_REQUEST['f_userfound'] : 0;  // Ocprop
		$options['f_disabled'] = isset($_REQUEST['f_disabled']) ? $_REQUEST['f_disabled'] : 0;
		$options['f_inactive'] = isset($_REQUEST['f_inactive']) ? $_REQUEST['f_inactive'] : 1;  // Ocprop
			// f_inactive formerly was used for both, archived and disabled caches.
			// After adding the separate f_disabled option, it is used only for archived
			// caches, but keeps its name for compatibility with existing stored or
			// external searches.
		$options['f_ignored'] = isset($_REQUEST['f_ignored']) ? $_REQUEST['f_ignored'] : 1;
		$options['f_otherPlatforms'] = isset($_REQUEST['f_otherPlatforms']) ? $_REQUEST['f_otherPlatforms'] : 0;
		$options['f_geokrets'] = isset($_REQUEST['f_geokrets']) ? $_REQUEST['f_geokrets'] : 0;
		$options['expert'] = isset($_REQUEST['expert']) ? $_REQUEST['expert'] : 0;  // Ocprop: 0
		$options['showresult'] = isset($_REQUEST['showresult']) ? $_REQUEST['showresult'] : 0;
		$options['output'] = isset($_REQUEST['output']) ? $_REQUEST['output'] : 'HTML';  // Ocprop: HTML
		$options['bbox'] = isset($_REQUEST['bbox']) ? $_REQUEST['bbox'] : false;

		if (isset($_REQUEST['cache_attribs']))
		{
			if ($_REQUEST['cache_attribs'] != '')
			{
				$aAttribs = mb_split(';', $_REQUEST['cache_attribs']);
				for ($i = 0; $i < count($aAttribs); $i++)
					$options['cache_attribs'][$aAttribs[$i]+0] = $aAttribs[$i]+0;
				unset($aAttribs);
			}
			else
				$options['cache_attribs'] = array();
		}
		else
			$options['cache_attribs'] = array();

		if (isset($_REQUEST['cache_attribs_not']))
		{
			if ($_REQUEST['cache_attribs_not'] != '')
			{
				$aAttribs = mb_split(';', $_REQUEST['cache_attribs_not']);
				for ($i = 0; $i < count($aAttribs); $i++)
					$options['cache_attribs_not'][$aAttribs[$i]+0] = $aAttribs[$i]+0;
				unset($aAttribs);
			}
			else
				$options['cache_attribs_not'] = array();
		}
		else
			$options['cache_attribs_not'] = array();

		if (!isset($_REQUEST['unit']))
		{
			$options['unit'] = 'km';
		}
		elseif (mb_strtolower($_REQUEST['unit']) == 'sm')
		{
			$options['unit'] = 'sm';
		}
		elseif (mb_strtolower($_REQUEST['unit']) == 'nm')
		{
			$options['unit'] = 'nm';
		}
		else
		{
			$options['unit'] = $DEFAULT_DISTANCE_UNIT;
		}

		if (isset($_REQUEST['searchbyname']))
		{
			$options['searchtype'] = 'byname';
			$options['cachename'] = isset($_REQUEST['cachename']) ? stripslashes($_REQUEST['cachename']) : '';
       if (!isset($_REQUEST['utf8']))
         $options['cachename'] = iconv("ISO-8859-1", "UTF-8", $options['cachename']);
		}
		elseif (isset($_REQUEST['searchbyowner']))  // Ocprop
		{
			$options['searchtype'] = 'byowner';

			$options['ownerid'] = isset($_REQUEST['ownerid']) ? $_REQUEST['ownerid'] : 0;
			$options['owner'] = isset($_REQUEST['owner']) ? stripslashes($_REQUEST['owner']) : '';
		}
		elseif (isset($_REQUEST['searchbyfinder']))  // Ocprop
		{
			$options['searchtype'] = 'byfinder';

			$options['finderid'] = isset($_REQUEST['finderid']) ? $_REQUEST['finderid'] : 0;
			$options['finder'] = isset($_REQUEST['finder']) ? stripslashes($_REQUEST['finder']) : '';
			$options['logtype'] = isset($_REQUEST['logtype']) ? $_REQUEST['logtype'] : '1,7';  // Ocprop
		}
		elseif (isset($_REQUEST['searchbyort']))
		{
			$options['searchtype'] = 'byort';

			$options['ort'] = isset($_REQUEST['ort']) ? stripslashes($_REQUEST['ort']) : '';
			$options['locid'] = isset($_REQUEST['locid']) ? $_REQUEST['locid'] : 0;
			$options['locid'] = $options['locid'] + 0;
		}
		elseif (isset($_REQUEST['searchbyplz']))
		{
			$options['searchtype'] = 'byplz';

			$options['plz'] = isset($_REQUEST['plz']) ? stripslashes($_REQUEST['plz']) : '';
			$options['locid'] = isset($_REQUEST['locid']) ? $_REQUEST['locid'] : 0;
			$options['locid'] = $options['locid'] + 0;
		}
		elseif (isset($_REQUEST['searchbydistance']))
		{
			$options['searchtype'] = 'bydistance';

			// Ocprop: all of the following options
			if (isset($_REQUEST['lat']) && isset($_REQUEST['lon']))
			{
				$options['lat'] = $_REQUEST['lat']+0;
				$options['lon'] = $_REQUEST['lon']+0;
			}
			else
			{
				$options['latNS'] = isset($_REQUEST['latNS']) ? $_REQUEST['latNS'] : 'N';
				$options['lonEW'] = isset($_REQUEST['lonEW']) ? $_REQUEST['lonEW'] : 'E';

				$options['lat_h'] = isset($_REQUEST['lat_h']) ? $_REQUEST['lat_h'] : 0;
				$options['lon_h'] = isset($_REQUEST['lon_h']) ? $_REQUEST['lon_h'] : 0;
				$options['lat_min'] = isset($_REQUEST['lat_min']) ? $_REQUEST['lat_min'] : 0;
				$options['lon_min'] = isset($_REQUEST['lon_min']) ? $_REQUEST['lon_min'] : 0;
			}

			$options['distance'] = isset($_REQUEST['distance']) ? $_REQUEST['distance'] : 0;
		}
		elseif (isset($_REQUEST['searchbyfulltext']))
		{
			$options['searchtype'] = 'byfulltext';

			$options['ft_name'] = isset($_REQUEST['ft_name']) ? $_REQUEST['ft_name']+0 : 0;
			$options['ft_desc'] = isset($_REQUEST['ft_desc']) ? $_REQUEST['ft_desc']+0 : 0;
			$options['ft_logs'] = isset($_REQUEST['ft_logs']) ? $_REQUEST['ft_logs']+0 : 0;
			$options['ft_pictures'] = isset($_REQUEST['ft_pictures']) ? $_REQUEST['ft_pictures']+0 : 0;

			$options['fulltext'] = isset($_REQUEST['fulltext']) ? $_REQUEST['fulltext'] : '';
		}
		elseif (isset($_REQUEST['searchbycacheid']))
		{
			$options['searchtype'] = 'bycacheid';
			$options['cacheid'] = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : 0;
			if (!is_numeric($options['cacheid'])) $options['cacheid'] = 0;
		}
		elseif (isset($_REQUEST['searchbywp']))
		{
			$options['searchtype'] = 'bywp';
			$options['wp'] = isset($_REQUEST['wp']) ? $_REQUEST['wp'] : '';
		}
		elseif (isset($_REQUEST['searchbynofilter']))
		{
			$options['searchtype'] = 'bynofilter';
		}
		elseif (isset($_REQUEST['searchbylist']))
		{
			$options['searchtype'] = 'bylist';
			$options['listid'] = isset($_REQUEST['listid']) ? $_REQUEST['listid'] + 0 : 0;

			$password = isset($_REQUEST['listkey']) ? $_REQUEST['listkey'] : '';
			$list = new cachelist($options['listid']);
			if (!$list->allowView($password))
				$tpl->redirect("cachelists.php");
			$options['cachelist'] = cachelist::getListById($options['listid']);  // null for invalid ID
			$options['cachelist_pw'] = $password;
		}
		elseif (isset($_REQUEST['searchall']))
		{
			if (!$login->logged_in())
			{
				// This operation is very expensive and therefore available only
				// for logged-in users.
				$tpl->error(ERROR_LOGIN_REQUIRED);
			}
			else
				$options['searchtype'] = 'all';
		}
		else
		{
			if (isset($_REQUEST['showresult']))
				$tpl->error('unknown search option');
			else
			{
				// Set default search type; this prevents errors in outputSearchForm()
				// when initializing searchtype-dependent options:
				$options['searchtype'] = 'byname';
				$options['cachename'] = '';
			}
		}

		$options['sort'] = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : ($homecoords ? 'bydistance' : 'byname');

		if (isset($_REQUEST['orderRatingFirst']) && $_REQUEST['orderRatingFirst']==1)
			$options['orderRatingFirst'] = true;

		$options['country'] = isset($_REQUEST['country']) ? $_REQUEST['country'] : '';
		$options['adm2'] = isset($_REQUEST['adm2']) ? $_REQUEST['adm2'] : '';
		$options['cachetype'] = isset($_REQUEST['cachetype']) ? $_REQUEST['cachetype'] : '';

		$options['cachesize'] = isset($_REQUEST['cachesize']) ? $_REQUEST['cachesize'] : '';
		$options['difficultymin'] = isset($_REQUEST['difficultymin']) ? $_REQUEST['difficultymin']+0 : 0;
		$options['difficultymax'] = isset($_REQUEST['difficultymax']) ? $_REQUEST['difficultymax']+0 : 0;
		$options['terrainmin'] = isset($_REQUEST['terrainmin']) ? $_REQUEST['terrainmin']+0 : 0;
		$options['terrainmax'] = isset($_REQUEST['terrainmax']) ? $_REQUEST['terrainmax']+0 : 0;
		$options['recommendationmin'] = isset($_REQUEST['recommendationmin']) ? $_REQUEST['recommendationmin']+0 : 0;

		if (in_array($options['searchtype'], array('byort','byplz','bydistance')))
		{
			// For distance-based searches, sort by distance instead of name.
			if ($options['sort'] == 'byname')
				$options['sort'] = 'bydistance';
		}
		else
		{
			// For non-distance-based searches, sort by name instead of distance if
			// no reference coords exist.
			if (!isset($options['lat']) || !isset($options['lon']) || $options['lat']+$options['lon'] == 0)
				if (!$homecoords)
					$options['sort'] = 'byname';
		}

		$options['queryid'] = 0;
	}  // $queryid == 0


	//=========================================================
	//  3. query caching
	//=========================================================

	$bRememberQuery = isset($_REQUEST['skipqueryid']) ? !$_REQUEST['skipqueryid'] : true;
		// This is used by the map, which implements its own query-caching.
	if ($bRememberQuery)
	{
		if ($queryid == 0 && $options['showresult'] != 0)  // 'showresult' = "execute query"
		{
			sql("INSERT INTO `queries` (`user_id`, `options`, `last_queried`) VALUES (0, '&1', NOW())", serialize($options));
			$options['queryid'] = sql_insert_id();
		}
		$cookie->set('lastqueryid', $options['queryid']);
	}

	// remove old queries (after 1 hour without use);
	// execute only every 50 search calls
	if (rand(1, 50) == 1)
	{
		sql("DELETE FROM `queries` WHERE `last_queried` < NOW() - INTERVAL 1 HOUR AND `user_id`=0");
	}


	//=========================================================
	//  4. set defaults for new search options
	//     which may not be present in a stored query
	//=========================================================

	if (!isset($options['orderRatingFirst'])) $options['orderRatingFirst'] = false;
	if (!isset($options['f_otherPlatforms'])) $options['f_otherPlatforms'] = 0;
	if (!isset($options['difficultymin'])) $options['difficultymin'] = 0;
	if (!isset($options['difficultymax'])) $options['difficultymax'] = 0;
	if (!isset($options['terrainmin'])) $options['terrainmin'] = 0;
	if (!isset($options['terrainmax'])) $options['terrainmax'] = 0;
	if (!isset($options['recommendationmin'])) $options['recommendationmin'] = 0;
	if (!isset($options['cachetype'])) $options['cachetype'] = '';
	if (!isset($options['cachesize'])) $options['cachesize'] = '';
	if (!isset($options['bbox'])) $options['bbox'] = false;
	if (!isset($options['f_disabled'])) $options['f_disabled'] = 0;
	if (!isset($options['f_geokrets'])) $options['f_geokrets'] = 0;

	if (!isset($options['showresult'])) $options['showresult'] = 0;
	if ($options['showresult'] == 1)
	{

		//===============================================================
		//  X5. build basic SQL statement dependend on search type
		//      and filtering options
		//===============================================================

		sql_drop_temp_table_slave('result_caches');
		$cachesFilter = '';

		if (!isset($options['output'])) $options['output']='';
		if ((mb_strpos($options['output'], '.') !== false) ||
		    (mb_strpos($options['output'], '/') !== false) ||
		    (mb_strpos($options['output'], '\\') !== false)
		   )
		{
			$options['output'] = 'HTML';
		}

		// make a list of cache-ids that are in the result
		if (!isset($options['expert']))
			$options['expert'] = 0;
		if ($options['expert'] == 0)
		{
			$sql_select = array();
			$sql_from = '';
			$sql_innerjoin = array();
			$sql_leftjoin = array();
			$sql_where = array();
			$sql_having = array();
			$sql_group = array();

			//check the entered data and build SQL
			if (!isset($options['searchtype'])) $options['searchtype']='';
			if ($options['searchtype'] == 'byname')
			{
				$sql_select[] = '`caches`.`cache_id` `cache_id`';
				$sql_from = '`caches`';
				$sql_where[] = '`caches`.`name` LIKE \'%' . sql_escape($options['cachename']) . '%\'';
			}
			elseif ($options['searchtype'] == 'byowner')
			{
				if ($options['ownerid'] != 0)
				{
					$sql_select[] = '`caches`.`cache_id` `cache_id`';
					$sql_from = '`caches`';
					$sql_where[] = '`user_id`=\'' . sql_escape($options['ownerid']) . '\'';
				}
				else
				{
					$sql_select[] = '`caches`.`cache_id` `cache_id`';
					$sql_from = '`caches`';
					$sql_innerjoin[] = '`user` ON `caches`.`user_id`=`user`.`user_id`';
					$sql_where[] = '`user`.`username`=\'' . sql_escape($options['owner']) . '\'';
				}
			}
			elseif (($options['searchtype'] == 'byplz') || ($options['searchtype'] == 'byort'))
			{
				$locid = $options['locid'];

				if ($options['searchtype'] == 'byplz')
				{
					if ($locid == 0)
					{
						$plz = $options['plz'];

						$sql = "SELECT `loc_id` FROM `geodb_textdata` WHERE `text_type`=500300000 AND `text_val`='" . sql_escape($plz) . "'";
						$rs = sql($sql);
						if (sql_num_rows($rs) == 0)
						{
							sql_free_result($rs);
							$options['error_plz'] = true;
							outputSearchForm($options);
							exit;
						}
						elseif (sql_num_rows($rs) == 1)
						{
							$r = sql_fetch_array($rs);
							sql_free_result($rs);
							$locid = $r['loc_id'];
						}
						else
						{
							sql_free_result($rs);
							// ok, viele locations ... alle auflisten ...
							outputLocidSelectionForm($sql, $options);
							exit;
						}
					}

					// ok, wir haben einen ort ... koordinaten ermitteln
					$locid = $locid + 0;
					$rs = sql('SELECT `lon`, `lat` FROM `geodb_coordinates` WHERE `loc_id`=' . $locid . ' AND coord_type=200100000');
					if ($r = sql_fetch_array($rs))
					{
						// ok ... wir haben koordinaten ...

						$lat = $r['lat'] + 0;
						$lon = $r['lon'] + 0;

						$distance_unit = $DEFAULT_DISTANCE_UNIT;
						$distance = $DEFAULT_SEARCH_DISTANCE;

						// ab hier selber code wie bei bydistance ... TODO: in funktion auslagern

						//all target caches are between lat - max_lat_diff and lat + max_lat_diff
						$max_lat_diff = $distance / (111.12 * $multiplier[$distance_unit]);

						//all target caches are between lon - max_lon_diff and lon + max_lon_diff
						//TODO: check!!!
						$max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180 )) * 6378 * $multiplier[$distance_unit] * 3.14159);

						$lon_rad = $lon * 3.14159 / 180;
						$lat_rad = $lat * 3.14159 / 180;

						sql_temp_table_slave('result_caches');
						$cachesFilter =
											 'CREATE TEMPORARY TABLE &result_caches ENGINE=MEMORY
												SELECT
													(' . geomath::getSqlDistanceFormula($lon, $lat, $distance, $multiplier[$distance_unit]) . ') `distance`,
													`caches`.`cache_id` `cache_id`
												FROM `caches` FORCE INDEX (`latitude`)
												WHERE `longitude` > ' . ($lon - $max_lon_diff) . '
													AND `longitude` < ' . ($lon + $max_lon_diff) . '
													AND `latitude` > ' . ($lat - $max_lat_diff) . '
													AND `latitude` < ' . ($lat + $max_lat_diff) . '
												HAVING `distance` < ' . ($distance+0);
						sql_slave($cachesFilter);
						sql_slave('ALTER TABLE &result_caches ADD PRIMARY KEY ( `cache_id` )');

						$sql_select[] = '&result_caches.`cache_id`';
						$sql_from = '&result_caches';
						$sql_innerjoin[] = '`caches` ON `caches`.`cache_id`=&result_caches.`cache_id`';
					}
					else
					{
						$options['error_locidnocoords'] = true;
						outputSearchForm($options);
						exit;
					}
				}
				else if ($options['searchtype'] == 'byort')
				{
					if ($locid == 0)
					{
						$ort = $options['ort'];
						$simpletexts = search_text2sort($ort,true);
						$simpletextsarray = explode_multi($simpletexts, ' -/,');

						$sqlhashes = '';
						$wordscount = 0;
						foreach ($simpletextsarray AS $text)
						{
							if ($text != '')
							{
								$searchstring = search_text2simple($text);

								if ($sqlhashes != '') $sqlhashes .= ' OR ';
								$sqlhashes .= '`gns_search`.`simplehash`=' . sprintf("%u", crc32($searchstring));

								$wordscount++;
							}
						}

						if ($sqlhashes == '')
						{
							$options['error_noort'] = true;
							outputSearchForm($options);
						}

						// temporäre tabelle erstellen und dann einträge entfernen, die nicht mindestens so oft vorkommen wie worte gegeben wurden
						sql_drop_temp_table_slave('tmpuniids');
						sql_temp_table_slave('tmpuniids');
						sql_slave('CREATE TEMPORARY TABLE &tmpuniids (`uni_id` int(11) NOT NULL, `cnt` int(11) NOT NULL, `olduni` int(11) NOT NULL, `simplehash` int(11) NOT NULL) ENGINE=MEMORY SELECT `gns_search`.`uni_id` `uni_id`, 0 `cnt`, 0 `olduni`, `simplehash` FROM `gns_search` WHERE ' . $sqlhashes);
						sql_slave('ALTER TABLE &tmpuniids ADD INDEX (`uni_id`)');

					//	BUGFIX: dieser Code sollte nur ausgeführt werden, wenn mehr als ein Suchbegriff eingegeben wurde
					//					damit alle Einträge gefiltert, die nicht alle Suchbegriffe enthalten
					//					nun wird dieser Quellcode auch ausgeführt, um mehrfache uni_id's zu filtern
					//          Notwendig, wenn nach Baden gesucht wird => Baden-Baden war doppelt in der Liste
					//	if ($wordscount > 1)
					//	{
							sql_temp_table_slave('tmpuniids2');
							sql_slave('CREATE TEMPORARY TABLE &tmpuniids2 (`uni_id` int(11) NOT NULL, `cnt` int(11) NOT NULL, `olduni` int(11) NOT NULL) ENGINE=MEMORY SELECT `uni_id`, COUNT(*) `cnt`, 0 olduni FROM &tmpuniids GROUP BY `uni_id` HAVING `cnt` >= ' . $wordscount);
							sql_slave('ALTER TABLE &tmpuniids2 ADD INDEX (`uni_id`)');
							sql_drop_temp_table_slave('tmpuniids');
							sql_rename_temp_table_slave('tmpuniids2', 'tmpuniids');
					//	}

					//    add: SELECT g2.uni FROM &tmpuniids JOIN gns_locations g1 ON &tmpuniids.uni_id=g1.uni JOIN gns_locations g2 ON g1.ufi=g2.ufi WHERE g1.nt!='N' AND g2.nt='N'
					// remove: SELECT g1.uni FROM &tmpuniids JOIN gns_locations g1 ON &tmpuniids.uni_id=g1.uni JOIN gns_locations g2 ON g1.ufi=g2.ufi WHERE g1.nt!='N' AND g2.nt='N'

						// und jetzt noch alle englischen bezeichnungen durch deutsche ersetzen (wo möglich) ...
						sql_temp_table_slave('tmpuniidsAdd');
						sql_slave('CREATE TEMPORARY TABLE &tmpuniidsAdd (`uni` int(11) NOT NULL, `olduni` int(11) NOT NULL, PRIMARY KEY  (`uni`)) ENGINE=MEMORY SELECT g2.uni uni, g1.uni olduni FROM &tmpuniids JOIN gns_locations g1 ON &tmpuniids.uni_id=g1.uni JOIN gns_locations g2 ON g1.ufi=g2.ufi WHERE g1.nt!=\'N\' AND g2.nt=\'N\' GROUP BY uni');
						sql_temp_table_slave('tmpuniidsRemove');
						sql_slave('CREATE TEMPORARY TABLE &tmpuniidsRemove (`uni` int(11) NOT NULL, PRIMARY KEY  (`uni`)) ENGINE=MEMORY SELECT DISTINCT g1.uni uni FROM &tmpuniids JOIN gns_locations g1 ON &tmpuniids.uni_id=g1.uni JOIN gns_locations g2 ON g1.ufi=g2.ufi WHERE g1.nt!=\'N\' AND g2.nt=\'N\'');
						sql_slave('DELETE FROM &tmpuniids WHERE uni_id IN (SELECT uni FROM &tmpuniidsRemove)');
						sql_slave('DELETE FROM &tmpuniidsAdd WHERE uni IN (SELECT uni_id FROM &tmpuniids)');
						sql_slave('INSERT INTO &tmpuniids (uni_id, olduni) SELECT uni, olduni FROM &tmpuniidsAdd');
						sql_drop_temp_table_slave('tmpuniidsAdd');
						sql_drop_temp_table_slave('tmpuniidsRemove');

						$rs = sql_slave('SELECT `uni_id` FROM &tmpuniids');
						if (sql_num_rows($rs) == 0)
						{
							sql_free_result($rs);

							$options['error_ort'] = true;
							outputSearchForm($options);
							exit;
						}
						elseif (sql_num_rows($rs) == 1)
						{
							$r = sql_fetch_array($rs);
							sql_free_result($rs);

							// wenn keine 100%ige übereinstimmung nochmals anzeigen
							$locid = $r['uni_id'] + 0;
							$rsCmp = sql_slave('SELECT `full_name` FROM `gns_locations` WHERE `uni`=' . $locid . ' LIMIT 1');
							$rCmp = sql_fetch_array($rsCmp);
							sql_free_result($rsCmp);

							if (mb_strtolower($rCmp['full_name']) != mb_strtolower($ort))
							{
								outputUniidSelectionForm('SELECT `uni_id`, `olduni` FROM `&tmpuniids`', $options);
							}
						}
						else
						{
							sql_free_result($rs);
							outputUniidSelectionForm('SELECT `uni_id`, `olduni` FROM `&tmpuniids`', $options);
							exit;
						}
					}


					// ok, wir haben einen ort ... koordinaten ermitteln
					$locid = $locid + 0;
					$rs = sql_slave('SELECT `lon`, `lat` FROM `gns_locations` WHERE `uni`=' . $locid . ' LIMIT 1');
					if ($r = sql_fetch_array($rs))
					{
						// ok ... wir haben koordinaten ...

						$lat = $r['lat'] + 0;
						$lon = $r['lon'] + 0;

						$lon_rad = $lon * 3.14159 / 180;
						$lat_rad = $lat * 3.14159 / 180;

						$distance_unit = $DEFAULT_DISTANCE_UNIT;
						$distance = $DEFAULT_SEARCH_DISTANCE;

						// ab hier selber code wie bei bydistance ... TODO: in funktion auslagern

						//all target caches are between lat - max_lat_diff and lat + max_lat_diff
						$max_lat_diff = $distance / (111.12 * $multiplier[$distance_unit]);

						//all target caches are between lon - max_lon_diff and lon + max_lon_diff
						//TODO: check!!!
						$max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180 )) * 6378 * $multiplier[$distance_unit] * 3.14159);

						sql_temp_table_slave('result_caches');
						$cachesFilter =
											 'CREATE TEMPORARY TABLE &result_caches ENGINE=MEMORY
												SELECT
													(' . geomath::getSqlDistanceFormula($lon, $lat, $distance, $multiplier[$distance_unit]) . ') `distance`,
													`caches`.`cache_id` `cache_id`
												FROM `caches` FORCE INDEX (`latitude`)
												WHERE `longitude` > ' . ($lon - $max_lon_diff) . '
													AND `longitude` < ' . ($lon + $max_lon_diff) . '
													AND `latitude` > ' . ($lat - $max_lat_diff) . '
													AND `latitude` < ' . ($lat + $max_lat_diff) . '
												HAVING `distance` < ' . ($distance+0);
						sql_slave($cachesFilter);
						sql_slave('ALTER TABLE &result_caches ADD PRIMARY KEY ( `cache_id` )');

						$sql_select[] = '&result_caches.`cache_id`';
						$sql_from = '&result_caches';
						$sql_innerjoin[] = '`caches` ON `caches`.`cache_id`=&result_caches.`cache_id`';
					}
					else
					{
						$options['error_locidnocoords'] = true;
						outputSearchForm($options);
						exit;
					}
				}
			}
			elseif ($options['searchtype'] == 'byfinder')
			{
				if ($options['finderid'] != 0)
				{
					$finder_id = $options['finderid'];
				}
				else
				{
					//get the userid
					$rs = sql_slave("SELECT `user_id` FROM `user` WHERE `username`='&1'", $options['finder']);
					$finder_record = sql_fetch_array($rs);
					$finder_id = $finder_record['user_id'];
					sql_free_result($rs);
				}

				if (!isset($options['logtype'])) $options['logtype'] = '1,7';

				$sql_select[] = 'distinct `caches`.`cache_id` `cache_id`';
					// needs distinct because there can be multiple matching logs per cache
				$sql_from = '`caches`';
				$sql_innerjoin[] = '`cache_logs` ON `caches`.`cache_id`=`cache_logs`.`cache_id`';
				$sql_where[] = '`cache_logs`.`user_id`=\'' . sql_escape($finder_id) . '\'';

				if ($options['logtype'] != '0')  // 0 = all types
				{
					$ids = explode(',', $options['logtype']);
					$idNumbers = '0';
					foreach ($ids AS $id)
					{
						if ($idNumbers != '') $idNumbers .= ',';
						$idNumbers .= ($id+0);
					}
					$sql_where[] = '`cache_logs`.`type` IN (' . $idNumbers . ')';
				}
			}
			elseif ($options['searchtype'] == 'bydistance')   // Ocprop
			{
				//check the entered data
				if (isset($options['lat']) && isset($options['lon']))
				{
					$lat = $options['lat']+0;
					$lon = $options['lon']+0;
				}
				else
				{
					$latNS = $options['latNS'];
					$lonEW = $options['lonEW'];

					$lat_h = $options['lat_h'];
					$lon_h = $options['lon_h'];
					$lat_min = $options['lat_min'];
					$lon_min = $options['lon_min'];

					if (is_numeric($lon_h) && is_numeric($lon_min))
					{
						if (($lon_h >= 0) && ($lon_h < 180) && ($lon_min >= 0) && ($lon_min < 60))
						{
							$lon = $lon_h + $lon_min / 60;
							if ($lonEW == 'W') $lon = -$lon;
						}
					}

					if (is_numeric($lat_h) && is_numeric($lat_min))
					{
						if (($lat_h >= 0) && ($lat_h < 90) && ($lat_min >= 0) && ($lat_min < 60))
						{
							$lat = $lat_h + $lat_min / 60;
							if ($latNS == 'S') $lat = -$lat;
						}
					}
				}

				$distance = $options['distance'];
				$distance_unit = $options['unit'];

				if ((!isset($lon)) || (!isset($lat)) || (!is_numeric($distance)))
				{
					outputSearchForm($options);
					exit;
				}

				//make the sql-String

				//all target caches are between lat - max_lat_diff and lat + max_lat_diff
				$max_lat_diff = $distance / (111.12 * $multiplier[$distance_unit]);

				//all target caches are between lon - max_lon_diff and lon + max_lon_diff
				//TODO: check!!!
				$max_lon_diff = $distance * 180 / (abs(sin((90 - $lat) * 3.14159 / 180 )) * 6378 * $multiplier[$distance_unit] * 3.14159);

				$lon_rad = $lon * 3.14159 / 180;
				$lat_rad = $lat * 3.14159 / 180;

				sql_temp_table_slave('result_caches');
				$cachesFilter =
									 'CREATE TEMPORARY TABLE &result_caches ENGINE=MEMORY
										SELECT
											(' . geomath::getSqlDistanceFormula($lon, $lat, $distance, $multiplier[$distance_unit]) . ') `distance`,
											`caches`.`cache_id` `cache_id`
										FROM `caches` FORCE INDEX (`latitude`)
										WHERE `longitude` > ' . ($lon - $max_lon_diff) . '
											AND `longitude` < ' . ($lon + $max_lon_diff) . '
											AND `latitude` > ' . ($lat - $max_lat_diff) . '
											AND `latitude` < ' . ($lat + $max_lat_diff) . '
										HAVING `distance` < ' . ($distance+0);
				sql_slave($cachesFilter);
				sql_slave('ALTER TABLE &result_caches ADD PRIMARY KEY ( `cache_id` )');

				$sql_select[] = '&result_caches.`cache_id`';
				$sql_from = '&result_caches';
				$sql_innerjoin[] = '`caches` ON `caches`.`cache_id`=&result_caches.`cache_id`';
			}
			elseif ($options['searchtype'] == 'bycacheid')
			{
				$sql_select[] = '`caches`.`cache_id` `cache_id`';
				$sql_from = '`caches`';
				$sql_where[] = '`caches`.`cache_id`=\'' . sql_escape($options['cacheid']) . '\'';
			}
			elseif ($options['searchtype'] == 'bywp')
			{
				$sql_select[] = '`caches`.`cache_id` `cache_id`';
				$sql_from = '`caches`';
				$sql_where[] = '`caches`.`wp_oc`=\'' . sql_escape($options['wp']) . '\'';
			}
			elseif ($options['searchtype'] == 'byfulltext')
			{
				require_once($opt['rootpath'] . 'lib2/search/ftsearch.inc.php');

				$fulltext = $options['fulltext'];
				$hashes = ftsearch_hash($fulltext);

				if (count($hashes) == 0)
				{
					$options['error_nofulltext'] = true;
					outputSearchForm($options);
				}
				else if (count($hashes) > 50)
				{
					$options['error_fulltexttoolong'] = true;
					outputSearchForm($options);
				}

				$ft_types = array();
				if (isset($options['ft_name']) && $options['ft_name'])
					$ft_types[] = 2;
				if (isset($options['ft_logs']) && $options['ft_logs'])
					$ft_types[] = 1;
				if (isset($options['ft_desc']) && $options['ft_desc'])
					$ft_types[] = 3;
				if (isset($options['ft_pictures']) && $options['ft_pictures'])
					$ft_types[] = 6;
				if (count($ft_types) == 0)
					$ft_types[] = 0;

				$sql_select[] = '`caches`.`cache_id` `cache_id`';
				$sql_from = '`caches`';

				$n = 1;
				foreach ($hashes AS $k => $h)
				{
					if ($n > 1)
						$sql_innerjoin[] = '`search_index` AS `s' . $n . '` ON `s' . ($n-1) . '`.`cache_id`=`s' . $n . '`.`cache_id`';
					else
						$sql_innerjoin[] = '`search_index` AS `s1` ON `s1`.`cache_id`=`caches`.`cache_id`';

					$sql_where[] = '`s' . $n . '`.`hash`=\'' . sql_escape($h) . '\'';
					$sql_where[] = '`s' . $n . '`.`object_type` IN (' . implode(',', $ft_types) . ')';

					$n++;
				}

				$sqlFilter = 'SELECT DISTINCT ' . implode(',', $sql_select) .
						' FROM ' . $sql_from .
						' INNER JOIN ' . implode(' INNER JOIN ', $sql_innerjoin) .
						' WHERE ' . implode(' AND ', $sql_where);

				sql_drop_temp_table_slave('tmpFTCaches');
				sql_temp_table_slave('tmpFTCaches');
				sql_slave('CREATE TEMPORARY TABLE &tmpFTCaches (`cache_id` int (11) PRIMARY KEY) ' . $sqlFilter);

				$sql_select = array();
				$sql_from = '';
				$sql_innerjoin = array();
				$sql_leftjoin = array();
				$sql_where = array();

				$sql_select[] = '`caches`.`cache_id` `cache_id`';
				$sql_from = '&tmpFTCaches';
				$sql_innerjoin[] = '`caches` ON `caches`.`cache_id`=&tmpFTCaches.`cache_id`';
			}
			elseif ($options['searchtype'] == 'bynofilter')
			{
				$sql_select[] = '`caches`.`cache_id` `cache_id`';
				$sql_from = '`caches`';
			}
			elseif ($options['searchtype'] == 'bylist')
			{
				sql_temp_table_slave('result_caches');
				$list = new cachelist($options['listid']);
				if ($list->allowView($options['cachelist_pw']))
				{
					$cachesFilter =
									 "CREATE TEMPORARY TABLE &result_caches ENGINE=MEMORY
										SELECT `cache_id` FROM `cache_list_items`
										LEFT JOIN `cache_lists` ON `cache_lists`.`id`=`cache_list_items`.`cache_list_id`
									  WHERE `cache_list_id`=" . sql_escape($options['listid']);
					sql_slave($cachesFilter);
					sql_slave('ALTER TABLE &result_caches ADD PRIMARY KEY ( `cache_id` )');

					$sql_select[] = '&result_caches.`cache_id`';
					$sql_from = '&result_caches';
					$sql_innerjoin[] = '`caches` ON `caches`.`cache_id`=&result_caches.`cache_id`';
				}
				else
				{
					// should not happen, but just for the case ...
					$sql_select[] = '`caches`.`cache_id` `cache_id`';
					$sql_from = '`caches`';
					$sql_where[] = 'FALSE';
				}
			}
			else if ($options['searchtype'] == 'all')
			{
				$sql_select[] = '`caches`.`cache_id` `cache_id`';
				$sql_from = '`caches`';
				$sql_where[] = 'TRUE'; 
			}
			else
			{
				$tpl->error($unknown_searchtype);
			}

			// additional options
			if (!isset($options['f_userowner'])) $options['f_userowner']='0';   // Ocprop
			if ($options['f_userowner'] != 0) $sql_where[] = '`caches`.`user_id`!=\'' . $login->userid .'\'';

			if (!isset($options['f_userfound'])) $options['f_userfound']='0';  // Ocprop
			if ($options['f_userfound'] != 0)
			{
				$sql_where[] = '`caches`.`cache_id` NOT IN (SELECT `cache_logs`.`cache_id` FROM `cache_logs` WHERE `cache_logs`.`user_id`=\'' . sql_escape($login->userid) . '\' AND `cache_logs`.`type` IN (1, 7))';
			}
			if (!isset($options['f_inactive'])) $options['f_inactive']='0';  // Ocprop
			if ($options['f_inactive'] != 0)  $sql_where[] = '`caches`.`status` NOT IN (3,6,7)';
				// f_inactive formerly was used for both, archived and disabled caches.
				// After adding the separate f_disabled option, it is used only for archived
				// caches, but keeps its name for compatibility with existing stored or
				// external searches.
			if (!isset($options['f_disabled'])) $options['f_disabled']='0';
			if ($options['f_disabled'] != 0)  $sql_where[] = '`caches`.`status`<>2';

			if ($login->logged_in())
			{
				if (!isset($options['f_ignored'])) $options['f_ignored']='0';
				if ($options['f_ignored'] != 0)
				{
					// only use this filter, if it is realy needed - this enables better caching in map2.php with ignored-filter
					if (sql_value_slave("SELECT COUNT(*) FROM `cache_ignore` WHERE `user_id`='" . sql_escape($login->userid) . "'", 0) > 0)
					{
						$sql_where[] = '`caches`.`cache_id` NOT IN (SELECT `cache_ignore`.`cache_id`
												FROM `cache_ignore`
												WHERE `cache_ignore`.`user_id`=\'' . sql_escape($login->userid) . '\')';
					}
				}
			}
			if (!isset($options['f_otherPlatforms'])) $options['f_otherPlatforms']='0';
			if ($options['f_otherPlatforms'] != 0)
			{
				// $sql_where[] = '`caches`.`wp_nc`=\'\' AND `caches`.`wp_gc`=\'\'';
				// ignore NC listings, which are mostly unmaintained or dead
				$sql_where[] = "`caches`.`wp_gc_maintained`=''";
			}

			if (!isset($options['f_geokrets'])) $options['f_geokrets']='0';
			if ($options['f_geokrets'] != 0)
			{
				$sql_where[] = "(SELECT COUNT(*) FROM `gk_item_waypoint` WHERE `wp`=`caches`.`wp_oc`)";
			}

			if (!isset($options['country'])) $options['country']='';
			if ($options['country'] != '')
			{
				$sql_where[] = '`caches`.`country`=\'' . sql_escape($options['country']) . '\'';
			}

			if (!isset($options['adm2'])) $options['adm2']='';
			if ($options['adm2'] != '')
			{
				$sql_innerjoin[] = '`cache_location` ON `cache_location`.`cache_id`=`caches`.`cache_id`';
				$sql_where[] = '`cache_location`.`code2`=\'' . sql_escape($options['adm2']) . '\'';
			}

			if ($options['cachetype'] != '')
			{
				$types = explode(';', $options['cachetype']);
				if (count($types) < sql_value_slave("SELECT COUNT(*) FROM `cache_type`", 0))
				{
					for ($i = 0; $i < count($types); $i++) $types[$i] = "'" . sql_escape($types[$i]) . "'";
					$sql_where[] = '`caches`.`type` IN (' . implode(',', $types) . ')';
				}
			}

			if ($options['cachesize'] != '')
			{
				$sizes = explode(';', $options['cachesize']);
				if (count($sizes) < sql_value_slave("SELECT COUNT(*) FROM `cache_size`", 0))
				{
					for ($i = 0; $i < count($sizes); $i++) $sizes[$i] = "'" . sql_escape($sizes[$i]) . "'";
					$sql_where[] = '`caches`.`size` IN (' . implode(',', $sizes) . ')';
				}
			}

			if ($options['difficultymin'] != 0)
			{
				$sql_where[] = '`caches`.`difficulty`>=\'' . sql_escape($options['difficultymin']) . '\'';
			}
			if ($options['difficultymax'] != 0)
			{
				$sql_where[] = '`caches`.`difficulty`<=\'' . sql_escape($options['difficultymax']) . '\'';
			}
			if ($options['terrainmin'] != 0)
			{
				$sql_where[] = '`caches`.`terrain`>=\'' . sql_escape($options['terrainmin']) . '\'';
			}
			if ($options['terrainmax'] != 0)
			{
				$sql_where[] = '`caches`.`terrain`<=\'' . sql_escape($options['terrainmax']) . '\'';
			}
			if ($options['recommendationmin'] > 0)
			{
				$sql_innerjoin[] = '`stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id`';
				$sql_where[] = '`stat_caches`.`toprating`>=\'' . sql_escape($options['recommendationmin']) . '\'';
			}

			if (isset($options['cache_attribs']) && count($options['cache_attribs']) > 0)
			{
				foreach ($options['cache_attribs'] AS $attr)
				{
					$sql_innerjoin[] = '`caches_attributes` AS `a' . ($attr+0) . '` ON `a' . ($attr+0) . '`.`cache_id`=`caches`.`cache_id`';
					$sql_where[] = '`a' . ($attr+0) . '`.`attrib_id`=' . ($attr+0);
				}
			}

			if (isset($options['cache_attribs_not']) && count($options['cache_attribs_not']) > 0)
			{
				foreach ($options['cache_attribs_not'] AS $attr)
				{
					$sql_where[] = 'NOT EXISTS (SELECT `caches_attributes`.`cache_id` FROM `caches_attributes` WHERE `caches_attributes`.`cache_id`=`caches`.`cache_id` AND `caches_attributes`.`attrib_id`=\'' . sql_escape($attr+0) . '\')';
				}
			}

			if (isset($options['bbox']) && ($options['bbox']!==false))
			{
				// bbox=<lon_from>,<lat_from>,<lon_to>,<lat_to>
				$coords = explode(',', $options['bbox']);
				if (count($coords) == 4)
				{
					$sql_where[] = '`caches`.`longitude`>=' . ($coords[0]+0) . ' AND `caches`.`latitude`>=' . ($coords[1]+0) . ' AND `caches`.`longitude`<=' . ($coords[2]+0) . ' AND `caches`.`latitude`<=' . ($coords[3]+0);
				}
			}

			$sql_innerjoin[] = '`cache_status` ON `caches`.`status`=`cache_status`.`id`';
			if ($login->logged_in())
				$sql_where[] = '(`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`=' . sql_escape($login->userid) . ' OR (`caches`.`status`<>5 AND '. ($login->hasAdminPriv(ADMIN_USER) ? '1' : '0') . '))';
			else
				$sql_where[] = '`cache_status`.`allow_user_view`=1';

			//do the search
			$innerjoin = sizeof($sql_innerjoin) ? ' INNER JOIN ' . implode(' INNER JOIN ', $sql_innerjoin) : '';
			$leftjoin = sizeof($sql_leftjoin) ? ' LEFT JOIN ' . implode(' LEFT JOIN ', $sql_leftjoin) : '';
			$group = sizeof($sql_group) ? ' GROUP BY ' . implode(', ', $sql_group) : '';
			$having = sizeof($sql_having) ? ' HAVING ' . implode(' AND ', $sql_having) : '';

			$sqlFilter = 'SELECT ' . implode(',', $sql_select) .
					' FROM ' . $sql_from .
					$innerjoin .
					$leftjoin .
					' WHERE ' . implode(' AND ', $sql_where) .
					$group .
					$having;

			// echo "DEBUG ".$sqlFilter." DEBUG<br>";
		}
		else
		{
			$tpl->error($unknown_searchtype);
		}

		//=================================================================
		//  X6. load output module and output-dependent options
		//=================================================================

		$output_module = mb_strtolower($options['output']);  // Ocprop: HTML, gpx

		$map2_bounds = ($output_module == 'map2bounds');
		if ($map2_bounds)
			$output_module = 'map2';

		if ($map2_bounds && $options['queryid'] == 0)
		{
			$tpl->error('map2bounds requires queryid');
		}
		elseif (!file_exists($opt['rootpath'] . 'lib2/search/search.' . $output_module . '.inc.php'))
		{
			$tpl->error($outputformat_notexist);
		}

		$caches_per_page = 20;

		// Default parameters; may be modified by output modules
		$content_type_plain = 'application/octet-stream';
		$content_type_zipped = 'application/zip';
		$zip_threshold = $caches_per_page;
		$add_to_zipfile = true;
		$sAddJoin = '';
		$sAddGroupBy = '';
		$sAddFields = '';
		$sAddWhere = '';
		$sGroupBy = '';

		// disallow mapping other users' logs for data protection reasons
		$enable_mapdisplay = ($options['searchtype'] != 'byfinder') ||
		                     ($options['finderid'] == $login->userid);

		// *** load output module ***
		//
		// (map2 module will execute and exit; it will use the variables
		// $cachesFilter, $sqlFilter and $map2_bounds and $options['queryid'].)

		require($opt['rootpath'] . 'lib2/search/search.' . $output_module . '.inc.php');

		if (!isset($search_output_file_download))
			die("search_output_file_download flag not set for '$output_module' search");

		//=================================================================
		//  X7. complete SQL statement with output-dependend options,
		//      sorting and Limits
		//=================================================================

		$sql = '';

		// If no distance unit is preselected by distance search, use 'km'.
		// The unit will be shown e.g. in HTML and XML search results.
		if (!isset($distance_unit))
			$distance_unit = $DEFAULT_DISTANCE_UNIT;

		if (isset($lat_rad) && isset($lon_rad))
		{
			$sql .= geomath::getSqlDistanceFormula($lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
		}
		else
		{
			if (!$login->logged_in())
			{
				$sql .= 'NULL distance, ';
			}
			else
			{
				// get the user's home coords
				$rs_coords = sql_slave("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`='&1'",$login->userid);
				$record_coords = sql_fetch_array($rs_coords);

				if ($record_coords['latitude'] == 0 && $record_coords['longitude'] == 0)
				{
					$sql .= 'NULL distance, ';
				}
				else
				{
					$lon_rad = $record_coords['longitude'] * 3.14159 / 180;
					$lat_rad = $record_coords['latitude'] * 3.14159 / 180;

					$sql .= geomath::getSqlDistanceFormula($record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
				}
				sql_free_result($rs_coords);
			}
		}

		if ($options['sort'] == 'bylastlog' || $options['sort'] == 'bymylastlog')
		{
			$sAddFields .= ', MAX(`cache_logs`.`date`) AS `lastLog`';
			$sAddJoin .= ' LEFT JOIN `cache_logs` ON `caches`.`cache_id`=`cache_logs`.`cache_id`';
			if ($options['sort'] == 'bymylastlog')
				$sAddJoin .= ' AND `cache_logs`.`user_id`=' . sql_escape($login->userid);
			$sGroupBy .= ' GROUP BY `caches`.`cache_id`';
		}

		$sql .=   '`caches`.`cache_id`,
							 `caches`.`status`,
							 `caches`.`type`,
							 `caches`.`size`,
							 `caches`.`longitude`, `caches`.`latitude`,
							 `caches`.`user_id`,
							 IF(IFNULL(`stat_caches`.`toprating`,0)>3, 4, IFNULL(`stat_caches`.`toprating`, 0)) `ratingvalue`' .
							 $sAddFields
			 . ' FROM `caches`
		  LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id`' .
			          $sAddJoin
		  . ' WHERE `caches`.`cache_id` IN (' . $sqlFilter . ')' .
					      $sAddWhere . ' ' .
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

		if (isset($lat_rad) && isset($lon_rad) && $sortby == 'bydistance')  
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

		// range of output
		$startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
		if (!is_numeric($startat)) $startat = 0;

		if (isset($_REQUEST['count']))  // Ocprop
			$count = $_REQUEST['count'];
		else
			$count = $caches_per_page;
		if ($count == 'max') $count = 500;
		if (!is_numeric($count)) $count = 0;
		if ($count < 1) $count = 1;
		if ($count > 500) $count = 500;

		$sqlLimit = ' LIMIT ' . $startat . ', ' . $count;

		if ($search_output_file_download)
		{
			//===================================================================
			//  X8a. run query and output for file downloads (GPX, KML, OVL ...)
			//===================================================================

			sql_drop_temp_table_slave('searchtmp');
				// for the case something went wrong and it was not propery cleaned up

			sql_temp_table_slave('searchtmp');
			sql_slave('CREATE TEMPORARY TABLE &searchtmp SELECT ' . $sql . $sqlLimit);

			$count = sql_value_slave('SELECT COUNT(*) FROM &searchtmp',0);
			if ($count == 1)
			{
				$sFilebasename = sql_value_slave('
					SELECT `caches`.`wp_oc`
					FROM &searchtmp, `caches`
					WHERE &searchtmp.`cache_id`=`caches`.`cache_id` LIMIT 1',
					'?'
				);
			}
			else
				$sFilebasename = 'ocde' . $options['queryid'];

			$bUseZip = ($count > $zip_threshold) ||
			           (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
			if ($bUseZip)
			{
				$phpzip = new ss_zip('',6);
			}

			if (!$db['debug'])
			{
				if ($bUseZip)
				{
					header('Content-type: ' . $content_type_zipped);
					header('Content-disposition: attachment; filename="' . $sFilebasename . '.zip"');
				}
				else
				{
					header('Content-type: '.$content_type_plain);
					header('Content-disposition: attachment; filename="' . $sFilebasename . '.' . $output_module .'"');
				}
			}

			// helper function for output modules
			function append_output($str)
			{
				global $db, $content, $bUseZip;

				if (!$db['debug'])
				{
					if ($bUseZip)
						$content .= $str;
					else
						echo $str;
				}
			}

			// *** run output module ***
			//
			// Modules will use these variables from search.php:
			//
			//   $phpzip
			//   $bUseZip

			$content = '';
			search_output();

			sql_drop_temp_table_slave('searchtmp');

			// output zip file
			if ($bUseZip && !$db['debug'])
			{
				if ($add_to_zipfile)
				{
					$phpzip->add_data($sFilebasename . '.' . $output_module, $content);
				}
				echo $phpzip->save($sFilebasename . '.zip', 'r');
			}
		}
		else
		{
			//===================================================================
			//  X8b. run other output module (XML, HTML)
			//
			//  The following variables from search.php are used by output modules:
			//
			//    $called_by_search
			//    $called_by_profile_query
			//    $distance_unit
			//    $lat_rad, $lon_rad
			//    $startat
			//    $count
			//    $caches_per_page
			//    $sql
			//    $sqlLimit
			//    $options['sort']
			//    $options['queryid']
			//    $enable_mapdisplay
			//=================================================================

			search_output();
		}

		if ($db['debug'])
			$tpl->display();
		else
			exit;
	}
	else  // $options['showresult'] == 0
	{
		//=============================================================
		//  F5. present search options form to the user
		//=============================================================

		if ($options['expert'] == 1)
		{
			// "expert mode" - what is this?
			$tpl->assign('formmethod', 'post');
			$tpl->display();
		}
		else
		{
			outputSearchForm($options);
		}
	}


//=============================================================
//  F6. build and output search options form
//=============================================================

function outputSearchForm($options)
{
	global $tpl, $login, $opt;
	global $error_plz, $error_locidnocoords, $error_ort, $error_noort, $error_nofulltext, $error_fulltexttoolong;
	global $cache_attrib_jsarray_line, $cache_attrib_group, $cache_attrib_img_line1, $cache_attrib_img_line2;
	global $DEFAULT_SEARCH_DISTANCE;

	$tpl->assign('formmethod', 'get');

	// checkboxen
	$tpl->assign('logged_in', $login->logged_in());

	if (isset($options['sort']))
		$bBynameChecked = ($options['sort'] == 'byname');  // Ocprop
	else
		$bBynameChecked = (!$login->logged_in());
	$tpl->assign('byname_checked', $bBynameChecked);

	if (isset($options['sort']))
		$bBydistanceChecked = ($options['sort'] == 'bydistance');
	else
		$bBydistanceChecked = ($login->logged_in());
	$tpl->assign('bydistance_checked', $bBydistanceChecked);

	if (isset($options['sort']))
		$bBycreatedChecked = ($options['sort'] == 'bycreated');
	else
		$bBycreatedChecked = (!$login->logged_in());
	$tpl->assign('bycreated_checked', $bBycreatedChecked);

	if (isset($options['sort']))
		$bBylastlogChecked = ($options['sort'] == 'bylastlog');
	else
		$bBylastlogChecked = ($login->logged_in());
	$tpl->assign('bylastlog_checked', $bBylastlogChecked);

	if (isset($options['sort']))
		$bBymylastlogChecked = ($options['sort'] == 'bymylastlog');
	else
		$bBymylastlogChecked = ($login->logged_in());
	$tpl->assign('bymylastlog_checked', $bBymylastlogChecked);

	$tpl->assign('hidopt_sort', $options['sort']);

	$tpl->assign('orderRatingFirst_checked', $options['orderRatingFirst']);
	$tpl->assign('hidopt_orderRatingFirst', $options['orderRatingFirst'] ? '1' : '0');

	$tpl->assign('f_userowner_checked', $login->logged_in() &&($options['f_userowner'] == 1));
	$tpl->assign('hidopt_userowner', ($options['f_userowner'] == 1) ? '1' : '0');

	$tpl->assign('f_userfound_checked', $login->logged_in() && ($options['f_userfound'] == 1));
	$tpl->assign('hidopt_userfound', ($options['f_userfound'] == 1) ? '1' : '0');

	$tpl->assign('f_ignored_checked', $login->logged_in() && ($options['f_ignored'] == 1));
	$tpl->assign('hidopt_ignored', ($options['f_ignored'] == 1) ? '1' : '0');

	$tpl->assign('f_disabled_checked', $options['f_disabled'] == 1);
	$tpl->assign('hidopt_disabled', ($options['f_disabled'] == 1) ? '1' : '0');

	// archived is called "disabled" here for backward compatibility
	$tpl->assign('f_inactive_checked', $options['f_inactive'] == 1);
	$tpl->assign('hidopt_inactive', ($options['f_inactive'] == 1) ? '1' : '0');

	$tpl->assign('f_otherPlatforms_checked', $options['f_otherPlatforms'] == 1);
	$tpl->assign('hidopt_otherPlatforms', ($options['f_otherPlatforms'] == 1) ? '1' : '0');

	$tpl->assign('f_geokrets_checked', $options['f_geokrets'] == 1);
	$tpl->assign('hidopt_geokrets', ($options['f_geokrets'] == 1) ? '1' : '0');

	if (isset($options['country']))
	{
		$tpl->assign('country', htmlspecialchars($options['country'], ENT_COMPAT, 'UTF-8'));
	}
	else
	{
		$tpl->assign('country', '');
	}

	if (isset($options['cachetype']))
	{
		$tpl->assign('cachetype', htmlspecialchars($options['cachetype'], ENT_COMPAT, 'UTF-8'));
	}
	else
	{
		$tpl->assign('cachetype', '');
	}

	// cachename
	$tpl->assign('cachename', isset($options['cachename']) ? htmlspecialchars($options['cachename'], ENT_COMPAT, 'UTF-8') : '');

	// koordinaten
	if (!isset($options['lat_h']))
	{
		if ($login->logged_in())
		{
			$rs = sql('SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`=\'' . sql_escape($login->userid) . '\'');
			$record = sql_fetch_array($rs);
			$lon = $record['longitude'];
			$lat = $record['latitude'];
			sql_free_result($rs);

			$tpl->assign('lonE_sel', $lon >= 0);
			$tpl->assign('lonW_sel', $lon < 0);
			$tpl->assign('latN_sel', $lat >= 0);
			$tpl->assign('latS_sel', $lat < 0);

			$lon_h = floor($lon);
			$lat_h = floor($lat);
			$lon_min = ($lon - $lon_h) * 60;
			$lat_min = ($lat - $lat_h) * 60;

			$tpl->assign('lat_h', $lat_h);
			$tpl->assign('lon_h', $lon_h);
			$tpl->assign('lat_min', sprintf("%02.3f", $lat_min));
			$tpl->assign('lon_min', sprintf("%02.3f", $lon_min));
		}
		else
		{
			$tpl->assign('lat_h', '00');
			$tpl->assign('lon_h', '000');
			$tpl->assign('lat_min', '00.000');
			$tpl->assign('lon_min', '00.000');
		}
	}
	else
	{
		$tpl->assign('lat_h', isset($options['lat_h']) ? $options['lat_h'] : '00');
		$tpl->assign('lon_h', isset($options['lon_h']) ? $options['lon_h'] : '000');
		$tpl->assign('lat_min', isset($options['lat_min']) ? $options['lat_min'] : '00.000');
		$tpl->assign('lon_min', isset($options['lon_min']) ? $options['lon_min'] : '00.000');

		if ($options['lonEW'] == 'W')
		{
			$tpl->assign('lonE_sel', '');
			$tpl->assign('lonW_sel', 'selected="selected"');
		}
		else
		{
			$tpl->assign('lonE_sel', 'selected="selected"');
			$tpl->assign('lonW_sel', '');
		}

		if ($options['latNS'] == 'S')
		{
			$tpl->assign('latS_sel', 'selected="selected"');
			$tpl->assign('latN_sel', '');
		}
		else
		{
			$tpl->assign('latS_sel', '');
			$tpl->assign('latN_sel', 'selected="selected"');
		}
	}
	$tpl->assign('distance', isset($options['distance']) ? $options['distance'] : $DEFAULT_SEARCH_DISTANCE);

	if (!isset($options['unit'])) $options['unit'] = $DEFAULT_DISTANCE_UNIT;
	$tpl->assign('sel_km', $options['unit'] == 'km');
	$tpl->assign('sel_sm', $options['unit'] == 'sm');
	$tpl->assign('sel_nm', $options['unit'] == 'nm');

	// plz
	$tpl->assign('plz', isset($options['plz']) ? htmlspecialchars($options['plz'], ENT_COMPAT, 'UTF-8') : '');
	$tpl->assign('ort', isset($options['ort']) ? htmlspecialchars($options['ort'], ENT_COMPAT, 'UTF-8') : '');

	// owner
	$tpl->assign('owner', isset($options['owner']) ? htmlspecialchars($options['owner'], ENT_COMPAT, 'UTF-8') : '');

	// finder
	$tpl->assign('finder', isset($options['finder']) ? htmlspecialchars($options['finder'], ENT_COMPAT, 'UTF-8') : '');

	// country options
	$rs = sql("
		SELECT
			IFNULL(`sys_trans_text`.`text`, `countries`.`name`) AS `name`,
			`countries`.`short`,
			`countries`.`short`='&2' AS `selected`
		FROM
			`countries`
			LEFT JOIN `sys_trans` ON `countries`.`trans_id`=`sys_trans`.`id`AND `sys_trans`.`text`=`countries`.`name`
			LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1'
		WHERE
			`countries`.`short` IN (SELECT DISTINCT `country` FROM `caches`) ORDER BY `name` ASC",
		$opt['template']['locale'], $options['country']);
	$tpl->assign_rs('countryoptions',$rs);
	sql_free_result($rs);

	// cachetype
	$rs = sql("SELECT `id` FROM `cache_type` ORDER BY `ordinal`");
	$rCachetypes = sql_fetch_assoc_table($rs);
	foreach ($rCachetypes as &$rCachetype)
	{
		$rCachetype['checked'] =  ($options['cachetype']=='') || (strpos(';' . $options['cachetype'] . ';', ';' . $rCachetype['id'] . ';') !== false);
		$rCachetype['unchecked'] = !$rCachetype['checked'];
	}
	$tpl->assign('cachetypes',$rCachetypes);
	$tpl->assign('cachetype', $options['cachetype']);

	// cachesize
	$cachesizes = array();
	$rs = sql("SELECT `id` FROM `cache_size`");
	while ($r = sql_fetch_assoc($rs))
		$cachesizes[$r['id']]['checked'] = (strpos(';' . $options['cachesize'] . ';', ';' . $r['id'] . ';') !== false) || ($options['cachesize']=='');
	sql_free_result($rs);
	$tpl->assign('cachesizes', $cachesizes);
	$tpl->assign('cachesize', $options['cachesize']);

	// difficulty + terrain
	$tpl->assign('difficultymin', $options['difficultymin']);
	$tpl->assign('difficultymax', $options['difficultymax']);
	$tpl->assign('difficulty_options', array(0,2,3,4,5,6,7,8,9,10));
	$tpl->assign('terrainmin', $options['terrainmin']);
	$tpl->assign('terrainmax', $options['terrainmax']);
	$tpl->assign('terrain_options', array(0,2,3,4,5,6,7,8,9,10));

	// logtypen
	if (isset($options['logtype']))
		$logtypes = explode(',', $options['logtype']);
	else
		$logtypes = array();

	$rs = sql("
		SELECT `id`,
		IFNULL(`sys_trans_text`.`text`, `log_types`.`name`) AS `name`,
		`id`='&2' as `selected`
		FROM (
			SELECT `id`,`name`,`trans_id` FROM `log_types`
			UNION
			SELECT 0,'all',(SELECT id FROM sys_trans WHERE `text`='all')
		) `log_types`
		LEFT JOIN `sys_trans_text` ON `sys_trans_text`.`trans_id`=`log_types`.`trans_id` AND `sys_trans_text`.`lang`='&1'
	  ORDER BY `log_types`.`id` ASC",
		$opt['template']['locale'], $logtypes ? $logtypes[0] : 0);

	$tpl->assign_rs('logtype_options',$rs);
	sql_free_result($rs);

	// cache-attributes
	$attributes_jsarray = '';

	$bBeginLine2 = true;
	$nPrevLineAttrCount2 = 0;
	$nLineAttrCount2 = 0;
	$attributes_img2 = '';

	/* perpare 'all attributes' */
	$rsAttrGroup = sql("SELECT `attribute_groups`.`id`, IFNULL(`sys_trans_text`.`text`, `attribute_groups`.`name`) AS `name`, `attribute_categories`.`color` FROM `attribute_groups` INNER JOIN `attribute_categories` ON `attribute_groups`.`category_id`=`attribute_categories`.`id` LEFT JOIN `sys_trans` ON `attribute_groups`.`trans_id`=`sys_trans`.`id` AND `sys_trans`.`text`=`attribute_groups`.`name` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' ORDER BY `attribute_groups`.`category_id` ASC, `attribute_groups`.`id` ASC", $opt['template']['locale']);
	while ($rAttrGroup = sql_fetch_assoc($rsAttrGroup))
	{
		$group_line = '';

		$rs = sql("SELECT `cache_attrib`.`id`, IFNULL(`ttname`.`text`, `cache_attrib`.`name`) AS `name`, `cache_attrib`.`icon_large`, `cache_attrib`.`icon_no`, `cache_attrib`.`icon_undef`, `cache_attrib`.`search_default`, IFNULL(`ttdesc`.`text`, `cache_attrib`.`html_desc`) AS `html_desc`
		             FROM `cache_attrib`
		        LEFT JOIN `sys_trans` AS `tname` ON `cache_attrib`.`trans_id`=`tname`.`id` AND `cache_attrib`.`name`=`tname`.`text`
		        LEFT JOIN `sys_trans_text` AS `ttname` ON `tname`.`id`=`ttname`.`trans_id` AND `ttname`.`lang`='&1'
		        LEFT JOIN `sys_trans` AS `tdesc` ON `cache_attrib`.`html_desc_trans_id`=`tdesc`.`id` AND `cache_attrib`.`html_desc`=`tdesc`.`text`
		        LEFT JOIN `sys_trans_text` AS `ttdesc` ON `tdesc`.`id`=`ttdesc`.`trans_id` AND `ttdesc`.`lang`='&1'
		            WHERE `cache_attrib`.`group_id`='&2' AND `selectable`
					  AND NOT IFNULL(`cache_attrib`.`hidden`, 0)=1
		         ORDER BY `cache_attrib`.`id`", $opt['template']['locale'], $rAttrGroup['id']);
		while ($record = sql_fetch_array($rs))
		{
			// icon specified
			$line = $cache_attrib_jsarray_line;
			$line = mb_ereg_replace('{id}', $record['id'], $line);

			if (!isset($options['cache_attribs']))
			{
				$line = mb_ereg_replace('{state}', 0, $line);
			}
			else if (array_search($record['id'], $options['cache_attribs']) === false)
			{
				if (array_search($record['id'], $options['cache_attribs_not']) === false)
					$line = mb_ereg_replace('{state}', 0, $line);
				else
					$line = mb_ereg_replace('{state}', 2, $line);
			}
			else
				$line = mb_ereg_replace('{state}', 1, $line);

			$line = mb_ereg_replace('{text_long}', escape_javascript($record['name']), $line);
			$line = mb_ereg_replace('{icon}', $record['icon_large'], $line);
			$line = mb_ereg_replace('{icon_no}', $record['icon_no'], $line);
			$line = mb_ereg_replace('{icon_undef}', $record['icon_undef'], $line);
			$line = mb_ereg_replace('{search_default}', $record['search_default'], $line);
			if ($attributes_jsarray != '') $attributes_jsarray .= ",\n";
			$attributes_jsarray .= $line;

			$line = $cache_attrib_img_line1;
			$line = mb_ereg_replace('{id}', $record['id'], $line);
			$line = mb_ereg_replace('{text_long}', escape_javascript($record['name']), $line);
			if (!isset($options['cache_attribs']))
			{
				$line = mb_ereg_replace('{icon}', $record['icon_undef'], $line);
			}
			else if (array_search($record['id'], $options['cache_attribs']) === false)
			{
				if (array_search($record['id'], $options['cache_attribs_not']) === false)
					$line = mb_ereg_replace('{icon}', $record['icon_undef'], $line);
				else
					$line = mb_ereg_replace('{icon}', $record['icon_no'], $line);
			}
			else
				$line = mb_ereg_replace('{icon}', $record['icon_large'], $line);

			$line = mb_ereg_replace('{html_desc}', escape_javascript($record['html_desc']), $line);
			$line = mb_ereg_replace('{name}', escape_javascript($record['name']), $line);
			$line = mb_ereg_replace('{color}', $rAttrGroup['color'], $line);

			$group_line .= $line;
			$nLineAttrCount2++;
		}
		sql_free_result($rs);

		if ($group_line != '')
		{
			$group_img = $cache_attrib_group;
			$group_img = mb_ereg_replace('{color}', $rAttrGroup['color'], $group_img);
			$group_img = mb_ereg_replace('{attribs}', $group_line, $group_img);
			$group_img = mb_ereg_replace('{name}', htmlspecialchars($rAttrGroup['name'], ENT_COMPAT, 'UTF-8'), $group_img);

			if ($bBeginLine2 == true)
			{
				$attributes_img2 .= '<div id="attribs2">';
				$bBeginLine2 = false;
			}

			$attributes_img2 .= $group_img;
			$nPrevLineAttrCount2 += $nLineAttrCount2;

			$nLineAttrCount2 = 0;
		}
	}
	sql_free_result($rsAttrGroup);
	if ($bBeginLine2 == false)
		$attributes_img2 .= '</div>';

	/* prepare default attributes */
	$bBeginLine1 = true;
	$nPrevLineAttrCount1 = 0;
	$nLineAttrCount1 = 0;
	$attributes_img1 = '';

	$rsAttrGroup = sql("SELECT `attribute_groups`.`id`, IFNULL(`sys_trans_text`.`text`, `attribute_groups`.`name`) AS `name`, `attribute_categories`.`color` FROM `attribute_groups` INNER JOIN `attribute_categories` ON `attribute_groups`.`category_id`=`attribute_categories`.`id` LEFT JOIN `sys_trans` ON `attribute_groups`.`trans_id`=`sys_trans`.`id` AND `sys_trans`.`text`=`attribute_groups`.`name` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' ORDER BY `attribute_groups`.`category_id` ASC, `attribute_groups`.`id` ASC", $opt['template']['locale']);
	while ($rAttrGroup = sql_fetch_assoc($rsAttrGroup))
	{
		$group_line = '';

		$rs = sql("SELECT `cache_attrib`.`id`, IFNULL(`ttname`.`text`, `cache_attrib`.`name`) AS `name`, `cache_attrib`.`icon_large`, `cache_attrib`.`icon_no`, `cache_attrib`.`icon_undef`, `cache_attrib`.`search_default`, IFNULL(`ttdesc`.`text`, `cache_attrib`.`html_desc`) AS `html_desc`
		             FROM `cache_attrib`
		        LEFT JOIN `sys_trans` AS `tname` ON `cache_attrib`.`trans_id`=`tname`.`id` AND `cache_attrib`.`name`=`tname`.`text`
		        LEFT JOIN `sys_trans_text` AS `ttname` ON `tname`.`id`=`ttname`.`trans_id` AND `ttname`.`lang`='&1'
		        LEFT JOIN `sys_trans` AS `tdesc` ON `cache_attrib`.`html_desc_trans_id`=`tdesc`.`id` AND `cache_attrib`.`html_desc`=`tdesc`.`text`
		        LEFT JOIN `sys_trans_text` AS `ttdesc` ON `tdesc`.`id`=`ttdesc`.`trans_id` AND `ttdesc`.`lang`='&1'
		            WHERE `cache_attrib`.`group_id`='&2'
		              AND `cache_attrib`.`search_default`=1 AND `selectable`
					  AND NOT IFNULL(`cache_attrib`.`hidden`, 0)=1
		         ORDER BY `cache_attrib`.`id`", $opt['template']['locale'], $rAttrGroup['id']);
		while ($record = sql_fetch_array($rs))
		{
			$line = $cache_attrib_img_line2;
			$line = mb_ereg_replace('{id}', $record['id'], $line);
			$line = mb_ereg_replace('{text_long}', escape_javascript($record['name']), $line);
			if (!isset($options['cache_attribs']))
			{
				$line = mb_ereg_replace('{icon}', $record['icon_undef'], $line);
			}
			else if (array_search($record['id'], $options['cache_attribs']) === false)
			{
				if (array_search($record['id'], $options['cache_attribs_not']) === false)
					$line = mb_ereg_replace('{icon}', $record['icon_undef'], $line);
				else
					$line = mb_ereg_replace('{icon}', $record['icon_no'], $line);
			}
			else
				$line = mb_ereg_replace('{icon}', $record['icon_large'], $line);

			$line = mb_ereg_replace('{html_desc}', escape_javascript($record['html_desc']), $line);
			$line = mb_ereg_replace('{name}', escape_javascript($record['name']), $line);
			$line = mb_ereg_replace('{color}', $rAttrGroup['color'], $line);

			$group_line .= $line;
			$nLineAttrCount1++;
		}
		sql_free_result($rs);

		if ($group_line != '')
		{
			$group_img = $cache_attrib_group;
			$group_img = mb_ereg_replace('{color}', $rAttrGroup['color'], $group_img);
			$group_img = mb_ereg_replace('{attribs}', $group_line, $group_img);
			$group_img = mb_ereg_replace('{name}', htmlspecialchars($rAttrGroup['name'], ENT_COMPAT, 'UTF-8'), $group_img);

			if ($bBeginLine1 == true)
			{
				$attributes_img1 .= '<div id="attribs1">';
				$bBeginLine1 = false;
			}

			$attributes_img1 .= $group_img;
			$nPrevLineAttrCount1 += $nLineAttrCount1;

			$nLineAttrCount1 = 0;
		}
	}
	sql_free_result($rsAttrGroup);
	if ($bBeginLine1 == false)
		$attributes_img1 .= '</div>';

	$tpl->assign('cache_attribCat1_list', $attributes_img1);
	$tpl->assign('cache_attribCat2_list', $attributes_img2);
	$tpl->assign('attributes_jsarray', $attributes_jsarray);
	$tpl->assign('hidopt_attribs', isset($options['cache_attribs']) ? implode(';', $options['cache_attribs']) : '');
	$tpl->assign('hidopt_attribs_not', isset($options['cache_attribs_not']) ? implode(';', $options['cache_attribs_not']) : '');

	$tpl->assign('fulltext', '');
	$tpl->assign('ft_desc_checked', true);
	$tpl->assign('ft_name_checked', true);
	$tpl->assign('ft_pictures_checked', false);
	$tpl->assign('ft_logs_checked', false);

	// fulltext options
	if ($options['searchtype'] == 'byfulltext')
	{
		if (!isset($options['fulltext'])) $options['fulltext'] = '';
		$tpl->assign('fulltext', htmlspecialchars($options['fulltext'], ENT_COMPAT, 'UTF-8'));

		if (isset($options['ft_name']))
			$tpl->assign('ft_name_checked',$options['ft_name']==1);

		if (isset($options['ft_desc']))
			$tpl->assign('ft_desc_checked',$options['ft_desc']==1);

		if (isset($options['ft_logs']))
			$tpl->assign('ft_logs_checked',$options['ft_logs']==1);

		if (isset($options['ft_pictures']))
			$tpl->assign('ft_pictures_checked',$options['ft_pictures']==1);
	}

	// errormeldungen
	$tpl->assign('ortserror', '');
	if (isset($options['error_plz']))
		$tpl->assign('ortserror', $error_plz);
	else if (isset($options['error_ort']))
		$tpl->assign('ortserror', $error_ort);
	else if (isset($options['error_locidnocoords']))
		$tpl->assign('ortserror', $error_locidnocoords);
	else if (isset($options['error_noort']))
		$tpl->assign('ortserror', $error_noort);

	$tpl->assign('fulltexterror', '');
	if (isset($options['error_nofulltext']))
		$tpl->assign('fulltexterror', $error_nofulltext);
	else if (isset($options['error_fulltexttoolong']))
		$tpl->assign('fulltexterror', $error_fulltexttoolong);

	$tpl->display();
}


//=============================================================
//  Prompt the user with a list of locations when the entered
//  'ort' or 'plz' is not unique.
//=============================================================

function prepareLocSelectionForm($options)
{
	global $tpl;
	
	$tpl->name = 'search_selectlocid';

	unset($options['queryid']);
	unset($options['locid']);
	$options['searchto'] = 'search' . $options['searchtype'];
	unset($options['searchtype']);

	// urlparams zusammenbauen
	$urlparamString = '';
	foreach ($options AS $name => $param)
	{
		// workaround for attribs
		if (is_array($param))
		{
			$pnew = '';
			foreach ($param AS $p)
				if ($pnew != '')
					$pnew .= ';' . $p;
				else
					$pnew .= $p;

			$param = $pnew;
		}

		if ($urlparamString != '')
			$urlparamString .= '&' . $name . '=' . urlencode($param);
		else
			$urlparamString = $name . '=' . urlencode($param);
	}
	$urlparamString .= '';

	return $urlparamString;
}


function outputUniidSelectionForm($uniSql, $options)
{
	global $tpl;  // settings
	global $locline, $secondlocationname;

	$urlparamString = prepareLocSelectionForm($options);

	sql_temp_table_slave('uniids');
	sql_slave('CREATE TEMPORARY TABLE &uniids ENGINE=MEMORY ' . $uniSql);
	sql_slave('ALTER TABLE &uniids ADD PRIMARY KEY (`uni_id`)');

	// locidsite
	$locidsite = isset($_REQUEST['locidsite']) ? $_REQUEST['locidsite'] : 0;
	if (!is_numeric($locidsite)) $locidsite = 0;

	$count = sql_value_slave('SELECT COUNT(*) FROM &uniids',0);
	$tpl->assign('resultscount', $count);

	// create page browser
	$pager = new pager('search.php?'.$urlparamString.'&locidsite={offset}');
	$pager->make_from_offset($locidsite, ceil($count/20), 1);

	// create locations list
	$rs = sql_slave('SELECT `gns_locations`.`rc` `rc`, `gns_locations`.`cc1` `cc1`, `gns_locations`.`admtxt1` `admtxt1`, `gns_locations`.`admtxt2` `admtxt2`, `gns_locations`.`admtxt3` `admtxt3`, `gns_locations`.`admtxt4` `admtxt4`, `gns_locations`.`uni` `uni_id`, `gns_locations`.`lon` `lon`, `gns_locations`.`lat` `lat`, `gns_locations`.`full_name` `full_name`, &uniids.`olduni` `olduni` FROM `gns_locations`, &uniids WHERE &uniids.`uni_id`=`gns_locations`.`uni` ORDER BY `gns_locations`.`full_name` ASC LIMIT ' . ($locidsite * 20) . ', 20');

	$nr = $locidsite * 20 + 1;
	$locations = '';
	while ($r = sql_fetch_array($rs))
	{
		$thislocation = $locline;

		$locString = '';
		if ($r['admtxt1'] != '')
		{
			if ($locString != '') $locString .= ' &gt; ';
			$locString .= htmlspecialchars($r['admtxt1'], ENT_COMPAT, 'UTF-8');
		}
		if ($r['admtxt2'] != '')
		{
			if ($locString != '') $locString .= ' &gt; ';
			$locString .= htmlspecialchars($r['admtxt2'], ENT_COMPAT, 'UTF-8');
		}
/*		if ($r['admtxt3'] != '')
		{
			if ($locString != '') $locString .= ' &gt; ';
			$locString .= htmlspecialchars($r['admtxt3'], ENT_COMPAT, 'UTF-8');
		}
*/		if ($r['admtxt4'] != '')
		{
			if ($locString != '') $locString .= ' &gt; ';
			$locString .= htmlspecialchars($r['admtxt4'], ENT_COMPAT, 'UTF-8');
		}

		$thislocation = mb_ereg_replace('{parentlocations}', $locString, $thislocation);

		// koordinaten ermitteln
		$coordString = help_latToDegreeStr($r['lat']) . ' ' . help_lonToDegreeStr($r['lon']);
		$thislocation = mb_ereg_replace('{coords}', htmlspecialchars($coordString, ENT_COMPAT, 'UTF-8'), $thislocation);

		if ($r['olduni'] != 0)
		{
			// der alte name wurde durch den native-wert ersetzt
			$thissecloc = $secondlocationname;

			$r['olduni'] = $r['olduni'] + 0;
			$rsSecLoc = sql_slave('SELECT full_name FROM gns_locations WHERE uni=' . $r['olduni']);
			$rSecLoc = sql_fetch_assoc($rsSecLoc);
			$thissecloc = mb_ereg_replace('{secondlocationname}', htmlspecialchars($rSecLoc['full_name'], ENT_COMPAT, 'UTF-8'), $thissecloc);
			sql_free_result($rsSecLoc);

			$thislocation = mb_ereg_replace('{secondlocationname}', $thissecloc, $thislocation);
		}
		else
			$thislocation = mb_ereg_replace('{secondlocationname}', '', $thislocation);

		$thislocation = mb_ereg_replace('{locationname}', htmlspecialchars($r['full_name'], ENT_COMPAT, 'UTF-8'), $thislocation);
		$thislocation = mb_ereg_replace('{urlparams}', $urlparamString . '&locid={locid}', $thislocation);
		$thislocation = mb_ereg_replace('{locid}', urlencode($r['uni_id']), $thislocation);
		$thislocation = mb_ereg_replace('{nr}', $nr, $thislocation);

		$nr++;
		$locations .= $thislocation . "\n";
	}
	sql_free_result($rs);
	sql_drop_temp_table_slave('uniids');

	$tpl->assign('locations', $locations);

	$tpl->display();
	exit;
}


function outputLocidSelectionForm($locSql, $options)
{
	global $tpl;
	global $locline, $bgcolor1, $bgcolor2;

	require_once("lib2/logic/geodb.inc.php");

	$urlparamString = prepareLocSelectionForm($options) . '&locid={locid}';

	sql_temp_table_slave('locids');
	sql_slave('CREATE TEMPORARY TABLE &locids ENGINE=MEMORY ' . $locSql);
	sql_slave('ALTER TABLE &locids ADD PRIMARY KEY (`loc_id`)');

	$rs = sql_slave('SELECT `geodb_textdata`.`loc_id` `loc_id`, `geodb_textdata`.`text_val` `text_val` FROM `geodb_textdata`, &locids WHERE &locids.`loc_id`=`geodb_textdata`.`loc_id` AND `geodb_textdata`.`text_type`=500100000 ORDER BY `text_val`');

	$nr = 1;
	$locations = '';
	while ($r = sql_fetch_array($rs))
	{
		$thislocation = $locline;

		// locationsdings zusammenbauen
		$locString = '';
		$land = geodb_landFromLocid($r['loc_id']);
		if ($land != '') $locString .= htmlspecialchars($land, ENT_COMPAT, 'UTF-8');

		$rb = geodb_regierungsbezirkFromLocid($r['loc_id']);
		if ($rb != '') $locString .= ' &gt; ' . htmlspecialchars($rb, ENT_COMPAT, 'UTF-8');

		$lk = geodb_landkreisFromLocid($r['loc_id']);
		if ($lk != '') $locString .= ' &gt; ' . htmlspecialchars($lk, ENT_COMPAT, 'UTF-8');

		$thislocation = mb_ereg_replace('{parentlocations}', $locString, $thislocation);

		// koordinaten ermitteln
		$r['loc_id'] = $r['loc_id'] + 0;
		$rsCoords = sql_slave('SELECT `lon`, `lat` FROM `geodb_coordinates` WHERE loc_id=' . $r['loc_id'] . ' LIMIT 1');
		if ($rCoords = sql_fetch_array($rsCoords))
			$coordString = help_latToDegreeStr($rCoords['lat']) . ' ' . help_lonToDegreeStr($rCoords['lon']);
		else
			$coordString = '['.$no_location_coords.']';

		$thislocation = mb_ereg_replace('{coords}', htmlspecialchars($coordString, ENT_COMPAT, 'UTF-8'), $thislocation);
		$thislocation = mb_ereg_replace('{locationname}', htmlspecialchars($r['text_val'], ENT_COMPAT, 'UTF-8'), $thislocation);
		$thislocation = mb_ereg_replace('{urlparams}', $urlparamString, $thislocation);
		$thislocation = mb_ereg_replace('{locid}', urlencode($r['loc_id']), $thislocation);
		$thislocation = mb_ereg_replace('{nr}', $nr, $thislocation);
		$thislocation = mb_ereg_replace('{secondlocationname}', '', $thislocation);

		if ($nr % 2)
			$thislocation = mb_ereg_replace('{bgcolor}', $bgcolor1, $thislocation);
		else
			$thislocation = mb_ereg_replace('{bgcolor}', $bgcolor2, $thislocation);

		$nr++;
		$locations .= $thislocation . "\n";
	}

	$tpl->assign('locations', $locations);

	$tpl->assign('resultscount', sql_num_rows($rs));
	$tpl->assign('pages', '');

	sql_free_result($rs);
	sql_drop_temp_table_slave('locids');

	$tpl->display();
	exit;
}

?>
