<?php
/****************************************************************************
													    ./lib/caches.inc.php
															--------------------
		begin                : June 24 2004

		For license information see doc/license.txt
 ****************************************************************************/


/****************************************************************************

		Unicode Reminder メモ

  functions and variables for cache-submission related things

 ****************************************************************************/

 // Array with cachetypes, also stored in database - table cache_type
 $cache_types[] = array('id' => '2', 'short' => 'Trad.', 'de' => 'normaler Cache', 'en' => 'Traditional Cache');
 $cache_types[] = array('id' => '10', 'short' => 'Driv.', 'de' => 'Drive-In', 'en' => 'Drive-In');
 $cache_types[] = array('id' => '3', 'short' => 'Multi', 'de' => 'Multicache', 'en' => 'Multicache');
 $cache_types[] = array('id' => '7', 'short' => 'Quiz', 'de' => 'Rätselcache', 'en' => 'Quizcache');
 $cache_types[] = array('id' => '8', 'short' => 'Math', 'de' => 'Mathe-/Physikcache', 'en' => 'Math/Physics-Cache');
 $cache_types[] = array('id' => '9', 'short' => 'Moving', 'de' => 'Beweglicher Cache', 'en' => 'Moving Cache');
 $cache_types[] = array('id' => '4', 'short' => 'Virt.', 'de' => 'virtueller Cache', 'en' => 'virtual Cache');
 $cache_types[] = array('id' => '5', 'short' => 'ICam.', 'de' => 'Webcam Cache', 'en' => 'Webcam Cache');
 $cache_types[] = array('id' => '6', 'short' => 'Event', 'de' => 'Event Cache', 'en' => 'Event Cache');
 $cache_types[] = array('id' => '1', 'short' => 'Other', 'de' => 'unbekannter Cachetyp', 'en' => 'unknown cachetyp');

 // Cachetype-ID selected by default
// $default_cachetype_id = -1;

 // Array with cachestatus, also stored in database - table cache_status
 $cache_status[] = array('id' => '1', 'de' => 'Kann gesucht werden', 'en' => 'Ready for search');
 $cache_status[] = array('id' => '2', 'de' => 'Momentan nicht verfügbar', 'en' => 'Temporary not available');
 $cache_status[] = array('id' => '3', 'de' => 'Archiviert', 'en' => 'Archived');
 $cache_status[] = array('id' => '4', 'de' => 'Von den Approvern entfernt, um geprüft zu werden', 'en' => 'Hidden by approvers to check');
 $cache_status[] = array('id' => '5', 'de' => 'Noch nicht veröffentlicht', 'en' => 'Not yet available');
 $cache_status[] = array('id' => '6', 'de' => 'Gesperrt', 'en' => 'Locked, visible');
 $cache_status[] = array('id' => '7', 'de' => 'Gesperrt, unsichtbar', 'en' => 'Locked, invisible');

 // Sachestatus-ID selected by default
 $default_cachestatus_id = 1;

 // Array with cachesizes, also stored in database - table cache_size
 $cache_size[] = array('id' => '2', 'de' => 'mikro', 'en' => 'micro');
 $cache_size[] = array('id' => '3', 'de' => 'klein', 'en' => 'small');
 $cache_size[] = array('id' => '4', 'de' => 'normal', 'en' => 'normal');
 $cache_size[] = array('id' => '5', 'de' => 'groß', 'en' => 'large');
 $cache_size[] = array('id' => '6', 'de' => 'extrem groß', 'en' => 'very large');
 $cache_size[] = array('id' => '1', 'de' => 'andere Größe', 'en' => 'other size');
 $cache_size[] = array('id' => '7', 'de' => 'kein Behälter', 'en' => 'no container');

 // Sachesize-ID selected by default
// $default_cachesize_id = -1;

 // Array with log_types
 /*
 $log_types[] = array('id' => '1', 'de' => 'Gefunden', 'en' => 'Found');
 $log_types[] = array('id' => '2', 'de' => 'Nicht gefunden', 'en' => 'Not found');
 $log_types[] = array('id' => '3', 'de' => 'Bemerkung', 'en' => 'Note');
 $log_types[] = array('id' => '4', 'de' => 'Gesperrt', 'en' => 'Closed');
 $log_types[] = array('id' => '5', 'de' => 'Freigegeben', 'en' => 'Opened');
 $log_types[] = array('id' => '6', 'de' => 'Entfernt', 'en' => 'Removed');
 */
 $log_types = array();

 // Sachesize-ID selected by default
 $default_logtype_id = 1;

// new: get log_types from database
 get_log_types_from_database();

 function get_log_types_from_database()
 {
	global $dblink;
	global $log_types;

	$resp = sql("SELECT * FROM log_types ORDER BY id");
	while($row = sql_fetch_assoc($resp))
	{
		$log_types[] = $row;
	}
 }

 function log_type_from_id($id, $lang)
 {
	global $log_types;

	foreach ($log_types AS $type)
	{
		if ($type['id'] == $id)
		{
			return $type[$lang];
		}
	}
 }

 function cache_type_from_id($id, $lang)
 {
	global $cache_types;

	foreach ($cache_types AS $cache_type)
	{
		if ($cache_type['id'] == $id)
		{
			return $cache_type[$lang];
		}
	}
 }

 function cache_size_from_id($id, $lang)
 {
	global $cache_size;

	foreach ($cache_size AS $size)
	{
		if ($size['id'] == $id)
		{
			return $size[$lang];
		}
	}
 }

 function cache_status_from_id($id, $lang)
 {
	global $cache_status;

	foreach ($cache_status AS $status)
	{
		if ($status['id'] == $id)
		{
			return $status[$lang];
		}
	}
 }
?>