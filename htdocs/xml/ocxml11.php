<?php
/***************************************************************************
															 ./xml/ocxml11.php
															-------------------
		begin                : December 27, 2005

		For license information see doc/license.txt

		Unicode Reminder メモ

	***************************************************************************/

/* begin configuration */

	if (!isset($ocxmlversion))
	  $ocxmlversion = 11;
	
	$opt['rootpath'] = '../';
	require_once($opt['rootpath'] . 'lib/common.inc.php');
	require_once($opt['rootpath'] . 'lib2/charset.inc.php');
	require_once($opt['rootpath'] . 'lib2/const.inc.php');
	require_once($opt['rootpath'] . 'lib2/logic/data-license.inc.php');
	require_once($opt['rootpath'] . 'lib2/logic/npas.inc.php');

  if ($error == true)
	{
		echo 'Unable to connect to database';
		exit;
	}

/* end configuration */

/* begin with some constants */
	$t1 = "\t";
	$t2 = "\t\t";
	$t3 = "\t\t\t";
	$t4 = "\t\t\t\t";
	$t5 = "\t\t\t\t\t";
	$t6 = "\t\t\t\t\t\t";
	
	$sDateshort = 'Y-m-d';
	$sDateformat = 'Y-m-d H:i:s';
	
/* end with some constants */
	
/* begin parameter reading */
	global $bXmlCData;
	global $sCharset;

	// xml options
	$bOcXmlTag = isset($_REQUEST['ocxmltag']) ? $_REQUEST['ocxmltag'] : '1';
	$bDocType = isset($_REQUEST['doctype']) ? $_REQUEST['doctype'] : '1';
	$bXmlDecl	 = isset($_REQUEST['xmldecl']) ? $_REQUEST['xmldecl'] : '1';
	$sCharset = isset($_REQUEST['charset']) ? mb_strtolower($_REQUEST['charset']) : 'iso-8859-1';
	$bXmlCData = isset($_REQUEST['cdata']) ? $_REQUEST['cdata'] : '1';
	$bAttrlist = isset($_REQUEST['attrlist']) ? $_REQUEST['attrlist'] : '0';
	$bLicense = isset($_REQUEST['license']) ? $_REQUEST['license'] : '0';
	$sLanguage = isset($_REQUEST['language']) ? strtoupper($_REQUEST['language']) : '';
	
	if ((($bOcXmlTag != '0') && ($bOcXmlTag != '1')) || 
			(($bDocType != '0') && ($bDocType != '1')) || 
			(($bXmlCData != '0') && ($bXmlCData != '1')) || 
			(($bAttrlist != '0') && ($bAttrlist != '1')) || 
			(($bXmlDecl != '0') && ($bXmlDecl != '1')) ||
			(($bLicense != '0') && ($bLicense != '1')))
	{
		echo 'Invalid xml options value';
		exit;
	}
	
	if (($sCharset != 'iso-8859-1') && ($sCharset != 'utf-8'))
	{
		echo 'Invalid charset';
		exit;
	}

	// doctype but no ocxml?
	if (($bDocType == '1') && ($bOcXmlTag == '0'))
	{
		echo 'doctype yes but no for ocxml-tag? Are you sure that you know what you are doing?';
		exit;
	}
	
	// xmldecl but no ocxml?
	if (($bXmlDecl == '1') && ($bOcXmlTag == '0'))
	{
		echo 'xmldecl yes but no for ocxml-tag? Are you sure that you know what you are doing?';
		exit;
	}
	
	$ziptype = isset($_REQUEST['zip']) ? $_REQUEST['zip'] : 'zip';
	if (($ziptype != '0') && ($ziptype != 'zip') && ($ziptype != 'gzip') && ($ziptype != 'bzip2'))
	{
		echo 'invalid zip type';
		exit;
	}

	// cleanup ... 1h after last call
	// [2013-04-27 following: down from 24h to 2h due to high usage]
	// [2013-08-04 following: down to 1h for same reason]
	$cleanerdate = date($sDateformat, time() - 3600);
	$rs = sql("SELECT `id` FROM `xmlsession` WHERE `last_use`<'&1' AND `cleaned`=0", $cleanerdate);
	while ($r = sql_fetch_array($rs))
	{
		// This loop can be started simultaneously by multiple synchronous XML
		// requests, which both try to delete entries, files and directories.
		// This must not lead to errors.

		// delete xmlsession_data
		sql('DELETE FROM `xmlsession_data` WHERE `session_id`=&1', $r['id']);

		// delete files
		$path = $zip_basedir . 'ocxml11/' . $r['id'];
		if (is_dir($path))
			unlinkrecursiv($path);
		
		// All code versions up to 3.0.6 archived xmlsession records and just marked
		// them as "cleaned":
		//   sql('UPDATE `xmlsession` SET `cleaned`=1 WHERE `id`=&1', $r['id']);
		// Due to the high usage of XML interface this is no longer feaseable.
		sql('DELETE FROM `xmlsession` WHERE `id`=&1', $r['id']);
	}

	if (isset($_REQUEST['sessionid']))
	{
		$sessionid = $_REQUEST['sessionid'];
		$filenr = isset($_REQUEST['file']) ? $_REQUEST['file'] : '1';

		if (!mb_ereg_match('^[0-9]{1,11}', $sessionid))
			die('sessionid invalid');

		if (!mb_ereg_match('^[0-9]{1,11}', $filenr))
			die('filenr invalid');

		outputXmlSessionFile($sessionid, $filenr, $bOcXmlTag, $bDocType, $bXmlDecl, $ziptype);
	}
	else
	{
		// filter parameters
		$dModifiedsince = isset($_REQUEST['modifiedsince']) ? $_REQUEST['modifiedsince'] : '0';
		
		// selections
		$bCache = isset($_REQUEST['cache']) ? $_REQUEST['cache'] : '0';
		$bCachedesc = isset($_REQUEST['cachedesc']) ? $_REQUEST['cachedesc'] : '0';
		$bCachelog = isset($_REQUEST['cachelog']) ? $_REQUEST['cachelog'] : '0';
		$bUser = isset($_REQUEST['user']) ? $_REQUEST['user'] : '0';
		$bPicture = isset($_REQUEST['picture']) ? $_REQUEST['picture'] : '0';
		$bRemovedObject = isset($_REQUEST['removedobject']) ? $_REQUEST['removedobject'] : '0';
		$bPictureFromCachelog = isset($_REQUEST['picturefromcachelog']) ? $_REQUEST['picturefromcachelog'] : '0';
		
		// validation and parsing
		if (mb_strlen($dModifiedsince) != 14)
		{
			echo 'Invalid modifiedsince value (wrong length)';
			exit;
		}
		
		// convert to time
		$nYear = mb_substr($dModifiedsince, 0, 4);
		$nMonth = mb_substr($dModifiedsince, 4, 2);
		$nDay = mb_substr($dModifiedsince, 6, 2);
		$nHour = mb_substr($dModifiedsince, 8, 2);
		$nMinute = mb_substr($dModifiedsince, 10, 2);
		$nSecond = mb_substr($dModifiedsince, 12, 2);
		
		if ((!is_numeric($nYear)) && (!is_numeric($nMonth)) && (!is_numeric($nDay)) && (!is_numeric($nHour)) && (!is_numeric($nMinute)) && (!is_numeric($nSecond)))
		{
			echo 'Invalid modifiedsince value (non-numeric content)';
			exit;
		}
		
		if (($nYear < 1970) || ($nYear > 2100) 
				|| ($nMonth < 1) || ($nMonth > 12)
				|| ($nDay < 1) || ($nDay > 31)
				|| ($nHour < 0) || ($nHour > 23)
				|| ($nMinute < 0) || ($nMinute > 59)
				|| ($nSecond < 0) || ($nSecond > 59))
		{
			echo 'Invalid modifiedsince value (value out of range)';
			exit;
		}
		$sModifiedSince = date('Y-m-d H:i:s', mktime($nHour, $nMinute, $nSecond, $nMonth, $nDay, $nYear));
		
		if ((($bCache != '0') && ($bCache != '1')) ||
				(($bCachedesc != '0') && ($bCachedesc != '1')) ||
				(($bCachelog != '0') && ($bCachelog != '1')) ||
				(($bUser != '0') && ($bUser != '1')) ||
				(($bPicture != '0') && ($bPicture != '1')) ||
				(($bRemovedObject != '0') && ($bRemovedObject != '1')))
		{
			echo 'Invalid selection value';
			exit;
		}
		
		// selection options
		if (isset($_REQUEST['country']))
		{
			$country = $_REQUEST['country'];

			if (sqlValue('SELECT COUNT(*) FROM `countries` WHERE `short`=\'' . sql_escape($country) . '\'', 0) != 1)
				die('Unknown country');

			$selection['type'] = 1;
			$selection['country'] = $country;
		}
		else if (isset($_REQUEST['lat']) || isset($_REQUEST['lon']) || isset($_REQUEST['distance']))
		{
			if (!(isset($_REQUEST['lat']) && isset($_REQUEST['lon']) && isset($_REQUEST['distance'])))
				die('lat, lon, distance: you have to specify all paramters');
			
			$lat = $_REQUEST['lat'];
			$lon = $_REQUEST['lon'];
			$distance = $_REQUEST['distance'];
			
			if (!is_numeric($lat)) die('lat is no number');
			if (!is_numeric($lon)) die('lon is no number');
			if (!is_numeric($distance)) die('distance is no number');
			
			if (($lat < -180) || ($lat > 180)) die('lat out of range');
			if (($lon < -180) || ($lon > 180)) die('lon out of range');
			if (($distance < 0) || ($distance > 250)) die('distance out of range [0, 250]');
			
			$selection['type'] = 2;
			$selection['lat'] = $lat;
			$selection['lon'] = $lon;
			$selection['distance'] = $distance;
		}
		else if (isset($_REQUEST['cacheid']) || isset($_REQUEST['wp']) || isset($_REQUEST['uuid']))
		{
			$selection['type'] = 3;
			if (isset($_REQUEST['wp']))
			{
				$selection['cacheid'] = sqlValue("SELECT `cache_id` FROM `caches` WHERE `wp_oc`='" . sql_escape($_REQUEST['wp']) . "'", 0);
			}
			else if (isset($_REQUEST['uuid']))
			{
				$selection['cacheid'] = sqlValue("SELECT `cache_id` FROM `caches` WHERE `uuid`='" . sql_escape($_REQUEST['uuid']) . "'", 0);
			}
			else
			{
				$selection['cacheid'] = $_REQUEST['cacheid']+0;
			}
		}
		else
			$selection['type'] = 0;

		if ($selection['type'] != 0)
			if ($bUser == 1)
				die('selection used, user has to be 0');
		
		// session-management verwenden?
		$usesession = isset($_REQUEST['session']) ? $_REQUEST['session'] : 1;
		if (($usesession != 0) && ($usesession != 1))
			die('session-value invalid');
		
		$sAgent = isset($_REQUEST['agent']) ? $_REQUEST['agent'] : '';
		
		$sessionid = startXmlSession($sModifiedSince, $bCache, $bCachedesc, $bCachelog, $bUser, $bPicture, $bRemovedObject, $bPictureFromCachelog, $selection, $sAgent);

		if ($usesession == 1)
		{
			$rs = sql('SELECT `users`, `caches`, `cachedescs`, `cachelogs`, `pictures`, `removedobjects` FROM `xmlsession` WHERE id=&1', $sessionid);
			$recordcount = sql_fetch_array($rs);
			mysql_free_result($rs);
		
			if ($sCharset == 'iso-8859-1')
				header('Content-Type: application/xml; charset=ISO-8859-1');
			else if ($sCharset == 'utf-8')
				header('Content-Type: application/xml; charset=UTF-8');

			$xmloutput = '';
			if ($bXmlDecl == '1')
			{
				if ($sCharset == 'iso-8859-1')
					$xmloutput .= '<?xml version="1.0" encoding="iso-8859-1" standalone="yes" ?>' . "\n";
				else if ($sCharset == 'utf-8')
					$xmloutput .= '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>' . "\n";
			}
			if ($bOcXmlTag == '1') $xmloutput .= '<ocxmlsession>' . "\n";
			$xmloutput .= '  <sessionid>' . $sessionid . '</sessionid>' . "\n";
			$xmloutput .= '  <records user="' . $recordcount['users'] . 
										        '" cache="' . $recordcount['caches'] . 
										    '" cachedesc="' . $recordcount['cachedescs'] . 
										     '" cachelog="' . $recordcount['cachelogs'] . 
										      '" picture="' . $recordcount['pictures'] . 
										 '" removeobject="' . $recordcount['removedobjects'] . '" />' . "\n";
			if ($bOcXmlTag == '1') $xmloutput .= '</ocxmlsession>';

			if ($sCharset == 'iso-8859-1')
				echo utf8ToIso88591($xmloutput);
			else if ($sCharset == 'utf-8')
				echo $xmloutput;

			exit;
		}
		else
		{
			// return all records
			sql('CREATE TEMPORARY TABLE `tmpxml_users` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`=&1 AND `object_type`=4', $sessionid);
			sql('CREATE TEMPORARY TABLE `tmpxml_caches` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`=&1 AND `object_type`=2', $sessionid);
			sql('CREATE TEMPORARY TABLE `tmpxml_cachedescs` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`=&1 AND `object_type`=3', $sessionid);
			sql('CREATE TEMPORARY TABLE `tmpxml_cachelogs` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`=&1 AND `object_type`=1', $sessionid);
			sql('CREATE TEMPORARY TABLE `tmpxml_pictures` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`=&1 AND `object_type`=6', $sessionid);
			sql('CREATE TEMPORARY TABLE `tmpxml_removedobjects` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`=&1 AND `object_type`=7', $sessionid);
			
			outputXmlFile($sessionid, 0, $bXmlDecl, $bOcXmlTag, $bDocType, $ziptype);
		}
	}

	exit;

/* end parameter reading */


function outputXmlFile($sessionid, $filenr, $bXmlDecl, $bOcXmlTag, $bDocType, $ziptype)
{
	global $zip_basedir, $zip_wwwdir, $sDateformat, $sDateshort, $t1, $t2, $t3, $safemode_zip, $safemode_zip, $sCharset, $bAttrlist;
	global $absolute_server_URI, $bLicense, $sLanguage;
	global $ocxmlversion;
	// alle records aus tmpxml_* übertragen
	
	if (!mb_ereg_match('^[0-9]{1,11}', $sessionid))
		die('sessionid invalid');

	if (!mb_ereg_match('^[0-9]{1,11}', $filenr))
		die('filenr invalid');

	/* begin now a few dynamically loaded constants */

	$logtypes = array();
	$rs = sql('SELECT `id`, `de` FROM log_types');
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$r = sql_fetch_array($rs);
		$logtypes[$r['id']] = $r['de'];
	}
	mysql_free_result($rs);

	$cachetypes = array();
	$rs = sql('SELECT `id`, `short`, `de` FROM cache_type');
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$r = sql_fetch_array($rs);
		$cachetypes[$r['id']]['de'] = $r['de'];
		$cachetypes[$r['id']]['short'] = $r['short'];
	}
	mysql_free_result($rs);

	$cachestatus = array();
	$rs = sql('SELECT `id`, `de` FROM cache_status');
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$r = sql_fetch_array($rs);
		$cachestatus[$r['id']]['de'] = $r['de'];
	}
	mysql_free_result($rs);

	$counties = array();
	$rs = sql('SELECT `short`, `de` FROM countries');
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$r = sql_fetch_array($rs);
		$counties[$r['short']]['de'] = $r['de'];
	}
	mysql_free_result($rs);

	$cachesizes = array();
	$rs = sql('SELECT `id`, `de` FROM cache_size');
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$r = sql_fetch_array($rs);
		$cachesizes[$r['id']]['de'] = $r['de'];
	}
	mysql_free_result($rs);

	$languages = array();
	$rs = sql('SELECT `short`, `de` FROM languages');
	for ($i = 0; $i < mysql_num_rows($rs); $i++)
	{
		$r = sql_fetch_array($rs);
		$languages[$r['short']]['de'] = $r['de'];
	}
	mysql_free_result($rs);
	
	$objecttypes['4'] = 'user';
	$objecttypes['2'] = 'cache';
	$objecttypes['3'] = 'cachedesc';
	$objecttypes['1'] = 'cachelog';
	$objecttypes['6'] = 'picture';
	$objecttypes['8'] = 'cachelist';    // not implemented yet

	/* end now a few dynamically loaded constants */
	
	// temporäre Datei erstellen
	if (!is_dir($zip_basedir . 'ocxml11/' . $sessionid))
		mkdir($zip_basedir . 'ocxml11/' . $sessionid);
	
	$fileid = 1;
	while (file_exists($zip_basedir . 'ocxml11/' . $sessionid . '/' . $sessionid . '-' . $filenr . '-' . $fileid . '.xml'))
		$fileid++;
		
	$xmlfilename = $zip_basedir . 'ocxml11/' . $sessionid . '/' . $sessionid . '-' . $filenr . '-' . $fileid . '.xml';

	$f = fopen($xmlfilename, 'w');
	
	if ($bXmlDecl == '1')
	{
		if ($sCharset == 'iso-8859-1')
			fwrite($f, '<?xml version="1.0" encoding="iso-8859-1" standalone="no" ?>' . "\n");
		else if ($sCharset == 'utf-8')
			fwrite($f, '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>' . "\n");
	}

	if ($bDocType == '1') fwrite($f, '<!DOCTYPE oc11xml PUBLIC "-//Opencaching Network//DTD OCXml V 1.' . ($ocxmlversion % 10) . '//EN" "http://www.opencaching.de/xml/ocxml' . $ocxmlversion . '.dtd">' . "\n");
	if ($bOcXmlTag == '1')
	{
		$rs = sql('SELECT `date_created`, `modified_since` FROM `xmlsession` WHERE `id`=&1', $sessionid);
		$r = sql_fetch_array($rs);
		fwrite($f, '<oc11xml version="1.' . ($ocxmlversion % 10) . '" date="' . date($sDateformat, strtotime($r['date_created'])) . '" since="' . date($sDateformat, strtotime($r['modified_since'])) . '">' . "\n");
		mysql_free_result($rs);
	}

	if ($bAttrlist == '1')
	{
		$rs = sql("SELECT SQL_BUFFER_RESULT `id`, `name`, `icon_large`, `icon_no`, `icon_undef` FROM `cache_attrib`");
		fwrite($f, $t1 . '<attrlist>' . "\n");
		while ($r = sql_fetch_assoc($rs))
		{
			fwrite($f, $t2 . '<attr id="' . $r['id'] . '" icon_large="' . xmlentities($absolute_server_URI . $r['icon_large']) . '" icon_no="' . xmlentities($absolute_server_URI . $r['icon_no']) . '" icon_undef="' . xmlentities($absolute_server_URI . $r['icon_undef']) . '">' . xmlcdata($r['name']) . '</attr>' . "\n");
		}
		fwrite($f, $t1 . '</attrlist>' . "\n");
		sql_free_result($rs);
	}

	$rs = sql('SELECT SQL_BUFFER_RESULT `user`.`user_id` `id`, `user`.`node` `node`, `user`.`uuid` `uuid`, `user`.`username` `username`, `user`.`pmr_flag` `pmr_flag`, `user`.`date_created` `date_created`, `user`.`last_modified` `last_modified` FROM `tmpxml_users`, `user` WHERE `tmpxml_users`.`id`=`user`.`user_id`');
	while ($r = sql_fetch_array($rs))
	{
		fwrite($f, $t1 . '<user>' . "\n");

		fwrite($f, $t2 . '<id id="' . $r['id'] . '" node="' . $r['node'] . '">' . $r['uuid'] . '</id>' . "\n");
		fwrite($f, $t2 . '<username>' . xmlcdata($r['username']) . '</username>' . "\n");
		fwrite($f, $t2 . '<pmr>' . (($r['pmr_flag'] == 0) ? '0' : '1') . '</pmr>' . "\n");
		fwrite($f, $t2 . '<datecreated>' . date($sDateformat, strtotime($r['date_created'])) . '</datecreated>' . "\n");
		fwrite($f, $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n");

		fwrite($f, $t1 . '</user>' . "\n");
	}
	mysql_free_result($rs);

	$rs = sql('SELECT SQL_BUFFER_RESULT `caches`.`cache_id` `id`, `caches`.`uuid` `uuid`, `caches`.`user_id` `user_id`, 
	                                    `user`.`uuid` `useruuid`, `user`.`username` `username`, `caches`.`name` `name`, 
	                                    `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, `caches`.`type` `type`, 
	                                    `caches`.`country` `country`, `caches`.`size` `size`, `caches`.`desc_languages` `desclanguages`,
	                                    `caches`.`difficulty` `difficulty`, `caches`.`terrain` `terrain`, `caches`.`way_length` `way_length`, 
	                                    `caches`.`search_time` `search_time`, `caches`.`wp_gc` `wp_gc`, `caches`.`wp_nc` `wp_nc`,
	                                    /* we deliberatly do not use gc_wp_maintained here */
	                                    `caches`.`wp_oc` `wp_oc`, `caches`.`date_hidden` `date_hidden`, `caches`.`date_created` `date_created`, `caches`.`is_publishdate` `is_publishdate`, 
	                                    `caches`.`last_modified` `last_modified`, `caches`.`status` `status`, `caches`.`node` `node`,
	                                    `caches`.`listing_last_modified` `listing_last_modified`, `cache_status`.`allow_user_view`
	                               FROM `tmpxml_caches`
	                         INNER JOIN `caches` ON `tmpxml_caches`.`id`=`caches`.`cache_id`
	                         INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
	                         INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`');
	while ($r = sql_fetch_array($rs))
	{
		$bAllowView = ($r['allow_user_view'] == 1);

		if ($r['size'] == 8 && $ocxmlversion < 12)
			$r['size'] = 2;   // return as micro in old interface version
	
		fwrite($f, $t1 . '<cache>' . "\n");
		
		fwrite($f, $t2 . '<id id="' . $r['id'] . '" node="' . $r['node'] . '">' . $r['uuid'] . '</id>' . "\n");
		fwrite($f, $t2 . '<userid id="' . $r['user_id'] . '" uuid="' . $r['useruuid'] . '">' . xmlcdata($r['username']) . '</userid>' . "\n");
		fwrite($f, $t2 . '<name>' . xmlcdata(($bAllowView ? $r['name'] : '')) . '</name>' . "\n");
		fwrite($f, $t2 . '<longitude>' . sprintf('%01.5f', ($bAllowView ? $r['longitude'] : 0)) . '</longitude>' . "\n");
		fwrite($f, $t2 . '<latitude>' . sprintf('%01.5f', ($bAllowView ? $r['latitude'] : 0)) . '</latitude>' . "\n");
		fwrite($f, $t2 . '<type id="' . $r['type'] . '" short="' . xmlentities($cachetypes[$r['type']]['short']) . '">' . xmlcdata($cachetypes[$r['type']]['de']) . '</type>' . "\n");
		fwrite($f, $t2 . '<status id="' . $r['status'] . '">' . xmlcdata($cachestatus[$r['status']]['de']) . '</status>' . "\n");
		fwrite($f, $t2 . '<country id="' . $r['country'] . '">' . xmlcdata($counties[$r['country']]['de']) . '</country>' . "\n");
		fwrite($f, $t2 . '<size id="' . $r['size'] . '">' . xmlcdata($cachesizes[$r['size']]['de']) . '</size>' . "\n");
		fwrite($f, $t2 . '<desclanguages>' . $r['desclanguages'] . '</desclanguages>' . "\n");
		fwrite($f, $t2 . '<difficulty>' . sprintf('%01.1f', $r['difficulty'] / 2) . '</difficulty>' . "\n");
		fwrite($f, $t2 . '<terrain>' . sprintf('%01.1f', $r['terrain'] / 2) . '</terrain>' . "\n");
		fwrite($f, $t2 . '<rating waylength="' . $r['way_length'] . '" needtime="' . $r['search_time'] . '" />' . "\n");
		fwrite($f, $t2 . '<waypoints gccom="' . xmlentities($r['wp_gc']) . '" nccom="' . xmlentities($r['wp_nc']) . '" oc="' . xmlentities($r['wp_oc']) . '" />' . "\n");
		fwrite($f, $t2 . '<datehidden>' . date($sDateformat, strtotime($r['date_hidden'])) . '</datehidden>' . "\n");
		if ($ocxmlversion >= 12) $pd = ' ispublishdate="' . $r['is_publishdate'] . '"';
		else $pd = "";
		fwrite($f, $t2 . '<datecreated' . $pd . '>' . date($sDateformat, strtotime($r['date_created'])) . '</datecreated>' . "\n");
		fwrite($f, $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n");
		if ($ocxmlversion >= 14)
			fwrite($f, $t2 . '<listing_lastmodified>' . date($sDateformat, strtotime($r['listing_last_modified'])) . '</listing_lastmodified>' . "\n");

		$rsAttributes = sql("SELECT `cache_attrib`.`id`, `cache_attrib`.`name`
		                       FROM `caches_attributes`
		                 INNER JOIN `cache_attrib` ON `caches_attributes`.`attrib_id`=`cache_attrib`.`id`
		                      WHERE `caches_attributes`.`cache_id`='&1'",
		                    $r['id']);
		fwrite($f, $t2 . '<attributes>' . "\n");
		while ($rAttribute = sql_fetch_assoc($rsAttributes))
		{
			fwrite($f, $t3 . '<attribute id="' . ($rAttribute['id']+0) . '">' . xmlcdata($rAttribute['name']) . '</attribute>' . "\n");
		}
		fwrite($f, $t2 . '</attributes>' . "\n");
		sql_free_result($rsAttributes);

		if ($ocxmlversion >= 13)
		{
			$rsWaypoints = sql("SELECT `coordinates`.`id`, `coordinates`.`subtype` AS `type`,
			                           `coordinates`.`latitude`, `coordinates`.`longitude`,
																 `coordinates`.`description`,
			                           `coordinates_type`.`name` AS `type_name`
			                      FROM `coordinates`
			                INNER JOIN `coordinates_type` ON `coordinates_type`.`id`=`coordinates`.`subtype`
			                     WHERE `cache_id`='&1' AND `type`=1
			                  ORDER BY `coordinates`.`id` ASC", $r['id']);
			fwrite($f, $t2 . '<wpts>' . "\n");
			while ($rWaypoint = sql_fetch_assoc($rsWaypoints))
			{
				fwrite($f, $t3 . '<wpt id="' . ($rWaypoint['id']+0) . '" type="' . ($rWaypoint['type']+0) . '" typename="' . xmlentities($rWaypoint['type_name']) . '" longitude="' . sprintf('%01.5f',$rWaypoint['longitude']) . '" latitude="' . sprintf('%01.5f',$rWaypoint['latitude']) . '">' . xmlcdata($rWaypoint['description']) . '</wpt>' . "\n");
			}
			fwrite($f, $t2 . '</wpts>' . "\n");
			sql_free_result($rsAttributes);
		}

		fwrite($f, $t1 . '</cache>' . "\n");
	}
	mysql_free_result($rs);
	
	$rs = sql('SELECT SQL_BUFFER_RESULT `cache_desc`.`id` `id`, `cache_desc`.`uuid` `uuid`, `cache_desc`.`cache_id` `cache_id`, 
	                                    `cache_desc`.`language` `language`, `cache_desc`.`short_desc` `short_desc`,
	                                    `cache_desc`.`desc` `desc`, `cache_desc`.`desc_html` `desc_html`, `cache_desc`.`hint` `hint`, 
	                                    `cache_desc`.`last_modified` `last_modified`, `caches`.`uuid` `cacheuuid`, `cache_desc`.`node` `node`,
	                                    `cache_status`.`allow_user_view`,
	                                    `caches`.`user_id`, `user`.`username`, `user`.`data_license`
	                               FROM `tmpxml_cachedescs`
	                         INNER JOIN `cache_desc` ON `tmpxml_cachedescs`.`id`=`cache_desc`.`id`
	                         INNER JOIN `caches` ON `caches`.`cache_id`=`cache_desc`.`cache_id`
	                         INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
													 INNER JOIN `user` ON `user`.`user_id`=`caches`.`user_id`');
	while ($r = sql_fetch_array($rs))
	{
		$bAllowView = ($r['allow_user_view'] == 1);
		
		fwrite($f, $t1 . '<cachedesc>' . "\n");
		
		fwrite($f, $t2 . '<id id="' . $r['id'] . '" node="' . $r['node'] . '">' . $r['uuid'] . '</id>' . "\n");
		fwrite($f, $t2 . '<cacheid id="' . $r['cache_id'] . '">' . $r['cacheuuid'] . '</cacheid>' . "\n");

		fwrite($f, $t2 . '<language id="' . $r['language'] . '">' . xmlcdata($languages[$r['language']]['de']) . '</language>' . "\n");
		fwrite($f, $t2 . '<shortdesc>' . xmlcdata(($bAllowView ? $r['short_desc'] : '')) . '</shortdesc>' . "\n");
		
		$desc = $r['desc'];
		if ($r['desc_html'] == 0)
		{
			$desc = mb_ereg_replace('<br />', '', $desc);
			$desc = html_entity_decode($desc, ENT_COMPAT, 'UTF-8');
		}

		$lang = ($sLanguage != "" ? $sLanguage : $r['language']);
		$disclaimer = getLicenseDisclaimer($r['user_id'], $r['username'], $r['data_license'], $r['cache_id'], $lang, true, true);
		if ($bLicense)
			fwrite($f, $t2 . '<license>' . xmlcdata($disclaimer) . '</license>' . "\n");
		else if ($disclaimer != "")
			$desc .= "<p><em>" . $disclaimer . "</em></p>";

		$desc .= get_desc_npas($r['cache_id']);
			
		fwrite($f, $t2 . '<desc html="' . (($r['desc_html'] == 1) ? '1' : '0') . '">' . xmlcdata(($bAllowView ? $desc : '')) . '</desc>' . "\n");
		
		$r['hint'] = mb_ereg_replace('<br />', '', $r['hint']);
		$r['hint'] = html_entity_decode($r['hint'], ENT_COMPAT, 'UTF-8');
		
		fwrite($f, $t2 . '<hint>' . xmlcdata(($bAllowView ? $r['hint'] : '')) . '</hint>' . "\n");
		fwrite($f, $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n");

		fwrite($f, $t1 . '</cachedesc>' . "\n");
	}
	mysql_free_result($rs);

	if ($ocxmlversion >= 14)
		$rating_condition = "AND `cache_logs`.`date`=`cache_rating`.`rating_date`";
	else
		$rating_condition = "";
	$rs = sql('SELECT SQL_BUFFER_RESULT `cache_logs`.`id` `id`, `cache_logs`.`cache_id` `cache_id`, `cache_logs`.`user_id` `user_id`, 
	                                    `cache_logs`.`type` `type`, `cache_logs`.`date` `date`, `cache_logs`.`text` `text`, `cache_logs`.`text_html` `text_html`,
	                                    `cache_logs`.`oc_team_comment`,
	                                    `cache_logs`.`date_created` `date_created`, `cache_logs`.`last_modified` `last_modified`,
	                                    `cache_logs`.`log_last_modified` `log_last_modified`, 
	                                    `cache_logs`.`uuid` `uuid`, `user`.`username` `username`, `caches`.`uuid` `cacheuuid`, 
	                                    `user`.`uuid` `useruuid`, `cache_logs`.`node` `node`, IF(NOT ISNULL(`cache_rating`.`cache_id`) AND `cache_logs`.`type` IN (1,7), 1, 0) AS `recommended`,
	                                    `cache_status`.`allow_user_view`,
	                                    `user`.`data_license`,
	                                    `caches`.`country` AS `language`  /* hack */
	                               FROM `cache_logs` 
	                         INNER JOIN `tmpxml_cachelogs` ON `cache_logs`.`id`=`tmpxml_cachelogs`.`id`
	                         INNER JOIN `user` ON `cache_logs`.`user_id`=`user`.`user_id`
	                         INNER JOIN `caches` ON `caches`.`cache_id`=`cache_logs`.`cache_id`
	                         INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
	                          LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id` ' . $rating_condition);
	while ($r = sql_fetch_array($rs))
	{
		$bAllowView = ($r['allow_user_view'] == 1);
	
		$r['text'] = mb_ereg_replace('<br />', '', $r['text']);
		$r['text'] = html_entity_decode($r['text'], ENT_COMPAT, 'UTF-8');

		// locked/invisible should never be returned here - these logs are deleted before
		// reactivating the cache. Just for the case ... it is safe to return them as 'locked'.
		if ($r['type'] == 14) $r['type'] = 13;

		if ($ocxmlversion >= 13)
			$teamcomment = ' teamcomment="' . $r['oc_team_comment'] . '"';
		else
		{
			$teamcomment = '';
			if ($r['type'] > 8) $r['type'] = 3;
		}
		fwrite($f, $t1 . '<cachelog>' . "\n");
		fwrite($f, $t2 . '<id id="' . $r['id'] . '" node="' . $r['node'] . '">' . $r['uuid'] . '</id>' . "\n");
		fwrite($f, $t2 . '<cacheid id="' . $r['cache_id'] . '">' . $r['cacheuuid'] . '</cacheid>' . "\n");
		fwrite($f, $t2 . '<userid id="' . $r['user_id'] . '" uuid="' . $r['useruuid'] . '">' . xmlcdata($r['username']) . '</userid>' . "\n");
		fwrite($f, $t2 . '<logtype id="' . $r['type'] . '" recommended="' . $r['recommended'] . '"' . $teamcomment . '>' . xmlcdata($logtypes[$r['type']]) . '</logtype>' . "\n");
		fwrite($f, $t2 . '<date>' . date($ocxmlversion >= 13 ? $sDateformat : $sDateshort, strtotime($r['date'])) . '</date>' . "\n");
		fwrite($f, $t2 . '<text html="' . $r['text_html'] . '">' . xmlcdata(($bAllowView ? $r['text'] : '')) . '</text>' . "\n");
		fwrite($f, $t2 . '<datecreated>' . date($sDateformat, strtotime($r['date_created'])) . '</datecreated>' . "\n");
		fwrite($f, $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n");
		if ($ocxmlversion >= 14)
			fwrite($f, $t2 . '<log_lastmodified>' . date($sDateformat, strtotime($r['log_last_modified'])) . '</log_lastmodified>' . "\n");

		if ($bLicense)
		{
			$lang = ($sLanguage != "" ? $sLanguage : $r['language']);
			$disclaimer = getLicenseDisclaimer($r['user_id'], $r['username'], $r['data_license'], $r['cache_id'], $lang, false, true);
			fwrite($f, $t2 . '<license>' . xmlcdata($disclaimer) . '</license>' . "\n");
		}

		fwrite($f, $t1 . '</cachelog>' . "\n");
	}
	mysql_free_result($rs);

	$rs = sql('SELECT SQL_BUFFER_RESULT `pictures`.`id` `id`, `pictures`.`url` `url`, `pictures`.`title` `title`, 
	                                    `pictures`.`object_id` `object_id`, `pictures`.`object_type` `object_type`, 
	                                    `pictures`.`date_created` `date_created`, `pictures`.`uuid` `uuid`, 
	                                    `pictures`.`last_modified` `last_modified`, `pictures`.`display` `display`, 
	                                    `pictures`.`spoiler` `spoiler`, `pictures`.`node` `node`,
	                                    `pictures`.`mappreview`,
	                                    IFNULL(`c1`.`cache_id`,`c2`.`cache_id`) AS `cache_id`,
	                                    IFNULL(`c1`.`country`,`c2`.`country`) AS `language`,  /* hack */
	                                    IFNULL(`cs1`.`allow_user_view`, `cs2`.`allow_user_view`) AS `auv`,
	                                    IFNULL(`u1`.`user_id`,`u2`.`user_id`) AS `user_id`,
	                                    IFNULL(`u1`.`username`,`u2`.`username`) AS `username`,
	                                    IFNULL(`u1`.`data_license`,`u2`.`data_license`) AS `data_license`
	                               FROM `tmpxml_pictures` 
	                         INNER JOIN `pictures` ON `tmpxml_pictures`.`id`=`pictures`.`id` 
	                          LEFT JOIN `caches` AS `c1` ON `pictures`.`object_type`=2 AND `pictures`.`object_id`=`c1`.`cache_id` 
	                          LEFT JOIN `cache_logs` ON `pictures`.`object_type`=1 AND `pictures`.`object_id`=`cache_logs`.`id` 
	                          LEFT JOIN `caches` AS `c2` ON `cache_logs`.`cache_id`=`c2`.`cache_id` 
	                          LEFT JOIN `cache_status` AS `cs1` ON `c1`.`status`=`cs1`.`id` 
	                          LEFT JOIN `cache_status` AS `cs2` ON `c2`.`status`=`cs2`.`id`
	                          LEFT JOIN `user` `u1` ON `u1`.`user_id`=`cache_logs`.`user_id`
	                          LEFT JOIN `user` `u2` ON `u2`.`user_id`=`c1`.`user_id`');
	while ($r = sql_fetch_array($rs))
	{
		$bAllowView = ($r['auv'] == 1);

		fwrite($f, $t1 . '<picture>' . "\n");
		fwrite($f, $t2 . '<id id="' . $r['id'] . '" node="' . $r['node'] . '">' . $r['uuid'] . '</id>' . "\n");
		fwrite($f, $t2 . '<url>' . xmlcdata(($bAllowView ? $r['url'] : '')) . '</url>' . "\n");
		fwrite($f, $t2 . '<title>' . xmlcdata(($bAllowView ? $r['title'] : '')) . '</title>' . "\n");
		fwrite($f, $t2 . '<object id="' . $r['object_id'] . '" type="' . $r['object_type'] . '" typename="' . xmlentities($objecttypes[$r['object_type']]) . '">' . object_id2uuid($r['object_id'], $r['object_type']) . '</object>' . "\n");
		if ($ocxmlversion >= 13)
			fwrite($f, $t2 . '<picattr spoiler="' . $r['spoiler'] . '" display="' . $r['display'] . '" preview="' . $r['mappreview'] . '" />' . "\n");
		else
			fwrite($f, $t2 . '<attributes spoiler="' . $r['spoiler'] . '" display="' . $r['display'] . '" />' . "\n");
		fwrite($f, $t2 . '<datecreated>' . date($sDateformat, strtotime($r['date_created'])) . '</datecreated>' . "\n");
		fwrite($f, $t2 . '<lastmodified>' . date($sDateformat, strtotime($r['last_modified'])) . '</lastmodified>' . "\n");

		if ($bLicense)
		{
			$lang = ($sLanguage != "" ? $sLanguage : $r['language']);
			$disclaimer = getLicenseDisclaimer($r['user_id'], $r['username'], $r['data_license'], $r['cache_id'], $lang, false, true);
			fwrite($f, $t2 . '<license>' . xmlcdata($disclaimer) . '</license>' . "\n");
		}

		fwrite($f, $t1 . '</picture>' . "\n");
	}
	mysql_free_result($rs);

	$rs = sql('SELECT SQL_BUFFER_RESULT `removed_objects`.`id` `id`, `removed_objects`.`localid` `localid`, `removed_objects`.`uuid` `uuid`, 
	                                    `removed_objects`.`type` `type`, `removed_objects`.`removed_date` `removed_date`, `removed_objects`.`node` `node` 
	                               FROM `tmpxml_removedobjects`, `removed_objects` WHERE `removed_objects`.`id`=`tmpxml_removedobjects`.`id`');
	while ($r = sql_fetch_array($rs))
	{
		fwrite($f, $t1 . '<removedobject>' . "\n");
		fwrite($f, $t2 . '<id id="' . $r['id'] . '" node="' . $r['node'] . '" />' . "\n");
		fwrite($f, $t2 . '<object id="' . $r['localid'] . '" type="' . $r['type'] . '" typename="' . xmlentities($objecttypes[$r['type']]) . '">' . $r['uuid'] . '</object>' . "\n");
		fwrite($f, $t2 . '<removeddate>' . date($sDateformat, strtotime($r['removed_date'])) . '</removeddate>' . "\n");
		fwrite($f, $t1 . '</removedobject>' . "\n");
	}
	mysql_free_result($rs);
	
	if ($bOcXmlTag == '1') fwrite($f, '</oc11xml>' . "\n");
	
	fclose($f);
	
	$rel_xmlfile = 'ocxml11/' . $sessionid . '/' . $sessionid . '-' . $filenr . '-' . $fileid . '.xml';
	$rel_zipfile = 'ocxml11/' . $sessionid . '/' . $sessionid . '-' . $filenr . '-' . $fileid;

	// zippen und url-redirect
	if ($ziptype == '0')
	{
		tpl_redirect($zip_wwwdir . $rel_xmlfile);
		exit;
	}
	else if ($ziptype == 'zip')
		$rel_zipfile .= '.zip';
	else if ($ziptype == 'bzip2')
		$rel_zipfile .= '.bz2';
	else if ($ziptype == 'gzip')
		$rel_zipfile .= '.gz';
	else
		die('unknown zip type');

	$call = $safemode_zip . ' --type=' . escapeshellcmd($ziptype) . ' --src=' . escapeshellcmd($rel_xmlfile) . ' --dst=' . escapeshellcmd($rel_zipfile);
	system($call);

	// datei vorhanden?
	if (!file_exists($zip_basedir . $rel_zipfile))
		die('all ok, but zip failed - internal server error');

	tpl_redirect($zip_wwwdir . $rel_zipfile);

	exit;
}

function startXmlSession($sModifiedSince, $bCache, $bCachedesc, $bCachelog, $bUser, $bPicture, $bRemovedObject, $bPictureFromCachelog, $selection, $sAgent)
{
	global $opt;

	// session anlegen
	sql("INSERT INTO `xmlsession` (`last_use`, `modified_since`, `date_created`, `agent`) VALUES (NOW(), '&1', NOW(), '&2')", date('Y-m-d H:i:s', strtotime($sModifiedSince)), $sAgent);
	$sessionid = mysql_insert_id();
	
	$recordcount['caches'] = 0;
	$recordcount['cachedescs'] = 0;
	$recordcount['cachelogs'] = 0;
	$recordcount['users'] = 0;
	$recordcount['pictures'] = 0;
	$recordcount['removedobjects'] = 0;

	if ($selection['type'] == 0)
	{
		// ohne selection
		if ($bCache == 1)
		{
			sql("INSERT INTO xmlsession_data (`session_id`, `object_type`, `object_id`)
			     SELECT &1, 2, `cache_id` FROM `caches` WHERE `last_modified` >= '&2' AND `status`!=5",
			     $sessionid,
			     $sModifiedSince);
			$recordcount['caches'] = mysql_affected_rows();
		}
		
		if ($bCachedesc == 1)
		{
			sql("INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
			     SELECT &1, 3, `cache_desc`.`id` FROM `cache_desc` INNER JOIN `caches` ON `cache_desc`.`cache_id`=`caches`.`cache_id` WHERE `cache_desc`.`last_modified` >= '&2' AND `caches`.`status`!=5",
			     $sessionid,
			     $sModifiedSince);
			$recordcount['cachedescs'] = mysql_affected_rows();
		}

		if ($bCachelog == 1)
		{
			sql("INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
			     SELECT &1, 1, `cache_logs`.`id` FROM `cache_logs` INNER JOIN `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` WHERE `cache_logs`.`last_modified` >= '&2' AND `caches`.`status`!=5",
			     $sessionid,
			     $sModifiedSince);
			$recordcount['cachelogs'] = mysql_affected_rows();
		}

		if ($bUser == 1)
		{
			sql("INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
			     SELECT &1, 4, `user_id` FROM `user` WHERE `last_modified` >= '&2'",
			     $sessionid,
			     $sModifiedSince);
			$recordcount['users'] = mysql_affected_rows();
		}

		if ($bPicture == 1)
		{
			sql("INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
			     SELECT &1, 6, `pictures`.`id` FROM `pictures` INNER JOIN 
			                                        `caches` ON `pictures`.`object_type`=2 AND 
			                                                    `pictures`.`object_id`=`caches`.`cache_id` 
			                                  WHERE `pictures`.`last_modified` >= '&2' AND 
			                                        `caches`.`status`!=5
			     UNION DISTINCT 
			     SELECT &1, 6, `pictures`.`id` FROM `pictures` INNER JOIN 
			                                        `cache_logs` ON `pictures`.`object_type`=1 AND 
			                                                        `pictures`.`object_id`=`cache_logs`.`id` INNER JOIN 
			                                        `caches` ON `cache_logs`.`cache_id`=`caches`.`cache_id` 
			                                  WHERE `pictures`.`last_modified` >= '&2' AND 
			                                        `caches`.`status`!=5",
			     $sessionid,
			     $sModifiedSince);
			$recordcount['pictures'] = mysql_affected_rows();
		}

		if ($bRemovedObject == 1)
		{
			sql("INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
			     SELECT &1, 7, `id` FROM `removed_objects` WHERE `removed_date` >= '&2' AND `type`<>8",
			     $sessionid,
			     $sModifiedSince);
			$recordcount['removedobjects'] = mysql_affected_rows();
		}
	}
	else
	{
		$sqlWhere = '';
		$sqlHaving = '';
	
		if ($selection['type'] == 1)
		{
			sql("CREATE TEMPORARY TABLE `tmpxmlSesssionCaches` (`cache_id` int(11), PRIMARY KEY (`cache_id`)) ENGINE=MEMORY 
			     SELECT DISTINCT `cache_countries`.`cache_id` FROM `caches`, `cache_countries` WHERE `caches`.`cache_id`=`cache_countries`.`cache_id` AND `cache_countries`.`country`='&1' AND `caches`.`status`!=5", $selection['country']);
		}
		else if ($selection['type'] == 2)
		{
			$sql = 'CREATE TEMPORARY TABLE `tmpxmlSesssionCaches` (`cache_id` int(11), `distance` double, KEY (`cache_id`)) ENGINE=MEMORY ';
			$sql .= 'SELECT `cache_coordinates`.`cache_id`, ';
			$sql .= getSqlDistanceFormula($selection['lon'], $selection['lat'], $selection['distance'], 1, 'longitude', 'latitude', 'cache_coordinates') . ' `distance` ';
			$sql .= 'FROM `caches`, `cache_coordinates` WHERE ';
			$sql .= '`cache_coordinates`.`cache_id`=`caches`.`cache_id`';
			$sql .= ' AND `caches`.`status`!=5';
			$sql .= ' AND `cache_coordinates`.`latitude` > ' . getMinLat($selection['lon'], $selection['lat'], $selection['distance']);
			$sql .= ' AND `cache_coordinates`.`latitude` < ' . getMaxLat($selection['lon'], $selection['lat'], $selection['distance']);
			$sql .= ' AND `cache_coordinates`.`longitude` >' . getMinLon($selection['lon'], $selection['lat'], $selection['distance']);
			$sql .= ' AND `cache_coordinates`.`longitude` < ' . getMaxLon($selection['lon'], $selection['lat'], $selection['distance']);
			$sql .= ' HAVING `distance` < ' . ($selection['distance'] + 0);

			sql($sql);
		}
		else if ($selection['type'] == 3)
		{
			sql("CREATE TEMPORARY TABLE `tmpxmlSesssionCaches` (`cache_id` int(11), PRIMARY KEY (`cache_id`)) ENGINE=MEMORY 
			     SELECT `cache_id` FROM `caches` WHERE `cache_id`='&1' AND `status`!=5", $selection['cacheid']+0);
		}
		
		if ($bCache == 1)
		{
			sql("INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`) 
			     SELECT DISTINCT &1, 2, `tmpxmlSesssionCaches`.`cache_id` FROM `tmpxmlSesssionCaches`, `caches`
			     WHERE `tmpxmlSesssionCaches`.`cache_id`=`caches`.`cache_id` AND `caches`.`last_modified` >= '&2'",
			     $sessionid,
			     $sModifiedSince);
			$recordcount['caches'] = mysql_affected_rows();
		}
		
		if ($bCachedesc == 1)
		{
			sql("INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
			     SELECT DISTINCT &1, 3, `cache_desc`.`id` FROM `cache_desc`, `tmpxmlSesssionCaches`
			     WHERE `cache_desc`.`cache_id`=`tmpxmlSesssionCaches`.`cache_id` AND `cache_desc`.`last_modified` >= '&2'",
			     $sessionid,
			     $sModifiedSince);
			$recordcount['cachedescs'] = mysql_affected_rows();
		}
		
		if ($bCachelog == 1)
		{
			sql("INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
			     SELECT DISTINCT &1, 1, `cache_logs`.`id` FROM `cache_logs`, `tmpxmlSesssionCaches`
			     WHERE `cache_logs`.`cache_id`=`tmpxmlSesssionCaches`.`cache_id` AND `cache_logs`.`last_modified` >= '&2'",
			     $sessionid,
			     $sModifiedSince);
			$recordcount['cachelogs'] = mysql_affected_rows();
		}

		if ($bPicture == 1)
		{
			// cachebilder
			sql("INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
			     SELECT DISTINCT &1, 6, `pictures`.`id` FROM `pictures`, `tmpxmlSesssionCaches`
			     WHERE `pictures`.`object_id`=`tmpxmlSesssionCaches`.`cache_id` AND `pictures`.`object_type`=2 AND
			           `pictures`.`last_modified` >= '&2'",
			     $sessionid,
			     $sModifiedSince);
			$recordcount['pictures'] = mysql_affected_rows();
			
			// bilder von logs
			if ($bPictureFromCachelog == 1)
			{
				sql("INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
				     SELECT DISTINCT &1, 6, `pictures`.id FROM `pictures` , `cache_logs`, `tmpxmlSesssionCaches` 
				     WHERE `tmpxmlSesssionCaches`.`cache_id`=`cache_logs`.`cache_id` AND 
				           `pictures`.`object_type`=1 AND `pictures`.`object_id`=`cache_logs`.`id` AND 
				           `pictures`.`last_modified` >= '&2'",
			     $sessionid,
			     $sModifiedSince);
			
				$recordcount['pictures'] += mysql_affected_rows();
			}
		}

		if ($bRemovedObject == 1)
		{
			sql("INSERT INTO `xmlsession_data` (`session_id`, `object_type`, `object_id`)
			     SELECT DISTINCT &1, 7, `id` FROM `removed_objects` WHERE `removed_date` >= '&2'",
			     $sessionid,
			     $sModifiedSince);
			$recordcount['removedobjects'] = mysql_affected_rows();
		}
	}
	
	sql('UPDATE `xmlsession` SET `caches`=&1, `cachedescs`=&2, `cachelogs`=&3, `users`=&4, `pictures`=&5, `removedobjects`=&6 WHERE `id`=&7 LIMIT 1',
	    $recordcount['caches'],
	    $recordcount['cachedescs'],
	    $recordcount['cachelogs'],
	    $recordcount['users'],
	    $recordcount['pictures'],
	    $recordcount['removedobjects'],
	    $sessionid);
	 
	return $sessionid;
}

function outputXmlSessionFile($sessionid, $filenr, $bOcXmlTag, $bDocType, $bXmlDecl, $ziptype)
{
	sql('UPDATE xmlsession SET last_use=NOW() WHERE id=&1', $sessionid);

	/* begin calculate which records to transfer */
	
	$rs = sql('SELECT `users`, `caches`, `cachedescs`, `cachelogs`, `pictures`, `removedobjects` FROM `xmlsession` WHERE `id`=&1 AND `cleaned`=0', $sessionid + 0);
	if (mysql_num_rows($rs) == 0)
		die('invalid sessionid');
	
	$rRecordsCount = sql_fetch_assoc($rs);
	mysql_free_result($rs);

	$startat = ($filenr - 1) * 500;
	if (($startat < 0) || ($startat > $rRecordsCount['users'] + $rRecordsCount['caches'] + $rRecordsCount['cachedescs'] + $rRecordsCount['cachelogs'] + $rRecordsCount['pictures'] + $rRecordsCount['removedobjects'] - 1))
		die('filenr out of range');

	$recordnr[0] = 0;
	$recordnr[1] = $rRecordsCount['users'];
	$recordnr[2] = $recordnr[1] + $rRecordsCount['caches'];
	$recordnr[3] = $recordnr[2] + $rRecordsCount['cachedescs'];
	$recordnr[4] = $recordnr[3] + $rRecordsCount['cachelogs'];
	$recordnr[5] = $recordnr[4] + $rRecordsCount['pictures'];
	$recordnr[6] = $recordnr[5] + $rRecordsCount['removedobjects'];

	if ($recordnr[6] > $startat + 500)
		$endat = $startat + 500;
	else
		$endat = $recordnr[6] - $startat;

//	echo $startat . ' ' . $endat . '<br><br>';
//	echo '<table>';
//	echo '<tr><td>sql-start</td><td>sql-count</td><td>count</td><td>begin</td><td>end</td></tr>';
	for ($i = 0; $i < 6; $i++)
	{
		if (($startat >= $recordnr[$i]) && ($startat + 500 < $recordnr[$i + 1]))
		{
			if ($recordnr[$i + 1] - $startat > 500)
				$limits[$i] = array('start' => $startat - $recordnr[$i], 'count' => 500);
			else
				$limits[$i] = array('start' => $startat - $recordnr[$i], 'count' => $recordnr[$i + 1] - $startat);

			//$limits[$i] = array('start' => 'a', 'count' => 'a');
		}
		else if (($startat >= $recordnr[$i]) && ($startat < $recordnr[$i + 1]))
		{
			$limits[$i] = array('start' => $startat - $recordnr[$i], 'count' => $recordnr[$i + 1] - $startat);
			//$limits[$i] = array('start' => 'b', 'count' => 'b');
		}
		else if (($startat + 500 >= $recordnr[$i]) && ($startat + 500 < $recordnr[$i + 1]))
		{
			if ($startat + 500 < $recordnr[$i + 1])
				$limits[$i] = array('start' => 0, 'count' => 500 - $recordnr[$i] + $startat);
			else
				$limits[$i] = array('start' => 0, 'count' => $recordnr[$i + 1] - $recordnr[$i]);
				
			if ($limits[$i]['count'] < 0) $limits[$i]['count'] = 0;

			//$limits[$i] = array('start' => 'c', 'count' => 'c');
		}
		else if (($startat < $recordnr[$i]) && ($startat + 500 >= $recordnr[$i + 1]))
		{
			$limits[$i] = array('start' => 0, 'count' => $recordnr[$i + 1] - $recordnr[$i]);
			//$limits[$i] = array('start' => 'd', 'count' => 'd');
		}
		else
			$limits[$i] = array('start' => '0', 'count' => '0');
			
//		echo '<tr><td>' . $limits[$i]['start'] . '</td><td>' . $limits[$i]['count'] . '</td><td>' . ($recordnr[$i + 1] - $recordnr[$i]) . '</td><td>' . $recordnr[$i] . '</td><td>' . $recordnr[$i + 1] . '</td></tr>';
	}
//	echo '</table>';
	
//	echo '<a href="ocxml11.php?sessionid=' . $sessionid . '&file=' . ($filenr - 1) . '">Zurück</a><br>';
//	echo '<a href="ocxml11.php?sessionid=' . $sessionid . '&file=' . ($filenr + 1) . '">Vor</a>';

	/* end calculate which records to transfer */

	sql('CREATE TEMPORARY TABLE `tmpxml_users` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`=&1 AND `object_type`=4
	     LIMIT &2, &3', $sessionid, $limits[0]['start'], $limits[0]['count']);
	sql('CREATE TEMPORARY TABLE `tmpxml_caches` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`=&1 AND `object_type`=2
	     LIMIT &2, &3', $sessionid, $limits[1]['start'], $limits[1]['count']);
	sql('CREATE TEMPORARY TABLE `tmpxml_cachedescs` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`=&1 AND `object_type`=3
	     LIMIT &2, &3', $sessionid, $limits[2]['start'], $limits[2]['count']);
	sql('CREATE TEMPORARY TABLE `tmpxml_cachelogs` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`=&1 AND `object_type`=1
	     LIMIT &2, &3', $sessionid, $limits[3]['start'], $limits[3]['count']);
	sql('CREATE TEMPORARY TABLE `tmpxml_pictures` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`=&1 AND `object_type`=6
	     LIMIT &2, &3', $sessionid, $limits[4]['start'], $limits[4]['count']);
	sql('CREATE TEMPORARY TABLE `tmpxml_removedobjects` (`id` int(11), PRIMARY KEY (`id`)) SELECT `object_id` `id` FROM `xmlsession_data` WHERE `session_id`=&1 AND `object_type`=7
	     LIMIT &2, &3', $sessionid, $limits[5]['start'], $limits[5]['count']);

	outputXmlFile($sessionid, $filenr, $bXmlDecl, $bOcXmlTag, $bDocType, $ziptype);
}


/* begin some useful functions */

function xmlcdata($str)
{
	global $bXmlCData;

	if ($bXmlCData == '1')
	{
		$str = output_convert($str);
		$str = mb_ereg_replace(']]>', ']] >', $str);
		return '<![CDATA[' . filterevilchars($str) . ']]>';
	}
	else
		return xmlentities($str);
}

function xmlentities($str)
{
	$from[0] = '&'; $to[0] = '&amp;';
	$from[1] = '<'; $to[1] = '&lt;';
	$from[2] = '>'; $to[2] = '&gt;';
	$from[3] = '"'; $to[3] = '&quot;';
	$from[4] = '\''; $to[4] = '&apos;';

	for ($i = 0; $i <= 4; $i++)
		$str = mb_ereg_replace($from[$i], $to[$i], $str);

	$str = output_convert($str);
	return filterevilchars($str);
}

function filterevilchars($str)
{
	global $sCharset;

	// the same for for ISO-8859-1 and UTF-8
	$str = mb_ereg_replace('[\x{00}-\x{09}\x{0B}\x{0C}\x{0E}-\x{1F}]*', '', $str);

	return $str;
}

function object_id2uuid($objectid, $objecttype)
{
	if ($objecttype == '1')
		return log_id2uuid($objectid);
	elseif ($objecttype == '2')
		return cache_id2uuid($objectid);
	elseif ($objecttype == '4')
		return user_id2uuid($objectid);
}

function cache_id2uuid($id)
{
	global $dblink;
	
	$rs = sql("SELECT `uuid` FROM `caches` WHERE `cache_id`='&1'", $id);
	$r = sql_fetch_array($rs);
	mysql_free_result($rs);
	return $r['uuid'];
}

function log_id2uuid($id)
{
	global $dblink;
	
	$rs = sql("SELECT `uuid` FROM `cache_logs` WHERE `id`='&1'", $id);
	$r = sql_fetch_array($rs);
	mysql_free_result($rs);
	return $r['uuid'];
}

function user_id2uuid($id)
{
	global $dblink;
	
	$rs = sql("SELECT `uuid` FROM `user` WHERE `user_id`='&1'", $id);
	$r = sql_fetch_array($rs);
	mysql_free_result($rs);
	return $r['uuid'];
}

/* end some useful functions */

function unlinkrecursiv($path)
{
	// This loop can be started simultaneously by multiple synchronous XML
	// requests, which both try to delete entries, files and directories.
	// Therefore errors must be gracefully ignored.

	if (mb_substr($path, -1) != '/') $path .= '/';

	$notunlinked = 0;

	$hDir = opendir($path);
	if ($hDir === false)
		++$notunlinked;
	else
	{
		while (false !== ($file = readdir($hDir)))
		{
			if (($file != '.') && ($file != '..'))
			{
				if (is_dir($path . $file))
				{
					if (unlinkrecursiv($path . $file . '/') == false)
						$notunlinked++;
				}
				else
				{
					if ((mb_substr($file, -4) == '.zip') || 
					    (mb_substr($file, -3) == '.gz') || 
					    (mb_substr($file, -4) == '.bz2') || 
					    (mb_substr($file, -4) == '.xml'))
						@unlink($path . $file);
					else
						$notunlinked++;
				}
			}
		}
		closedir($hDir);
	}
	
	if ($notunlinked == 0)
	{
		@rmdir($path);
		return true;
	}
	else
		return false;
}

function output_convert($str)
{
	global $sCharset;

	if ($sCharset == 'iso-8859-1')
	{
		if ($str != null)
			return utf8ToIso88591($str);
		else
			return $str;
	}
	else if ($sCharset == 'utf-8')
		return $str;
}
?>