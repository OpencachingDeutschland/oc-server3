<?php
	/***************************************************************************
		For license information see doc/license.txt
		    
		Unicode Reminder メモ
                                     				                                
		loc search output
	****************************************************************************/

	$search_output_file_download = true;
	$content_type_plain = 'application/loc';


function search_output()
{
	global $state_temporarily_na, $state_archived, $state_locked;

	$locHead = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><loc version="1.0" src="opencaching.de">' . "\n";
	
	$locLine = 
'
<waypoint>
	<name id="{waypoint}"><![CDATA[{archivedflag}{name} by {username}]]></name>
	<coord lat="{lat}" lon="{lon}"/>
	<type>Geocache</type>
	<link text="Beschreibung">http://www.opencaching.de/viewcache.php?cacheid={cacheid}</link>
</waypoint>
';

	$locFoot = '</loc>';

	append_output($locHead);
	
	/*
		{waypoint}
		status -> {archivedflag}
		{name}
		{username}
		{lon}
		{lat}
		{cacheid}
	*/

	$rs = sql_slave('
		SELECT SQL_BUFFER_RESULT
			`searchtmp`.`cache_id` `cacheid`,
			`searchtmp`.`longitude`,
			`searchtmp`.`latitude`,
			`caches`.`name`,
			`caches`.`status`,
			`caches`.`wp_oc` `waypoint`,
			`user`.`username` `username`
		FROM
			`searchtmp`,
			`caches`,
			`user`
		WHERE
			`searchtmp`.`cache_id`=`caches`.`cache_id` AND
			`searchtmp`.`user_id`=`user`.`user_id`');

	while ($r = sql_fetch_array($rs))
	{
		$thisline = $locLine;
		
		$lat = sprintf('%01.5f', $r['latitude']);
		$thisline = mb_ereg_replace('{lat}', $lat, $thisline);
		
		$lon = sprintf('%01.5f', $r['longitude']);
		$thisline = mb_ereg_replace('{lon}', $lon, $thisline);

		$thisline = mb_ereg_replace('{waypoint}', $r['waypoint'], $thisline);
		$thisline = mb_ereg_replace('{name}', xmlentities($r['name']), $thisline);
		
		if (($r['status'] == 2) || ($r['status'] == 3) || ($r['status'] == 6))
		{
			if ($r['status'] == 2)
				$thisline = mb_ereg_replace('{archivedflag}', $state_temporarily_na.'!, ', $thisline);
			elseif ($r['status'] == 3)
				$thisline = mb_ereg_replace('{archivedflag}', $state_archived.'!, ', $thisline);
			else
				$thisline = mb_ereg_replace('{archivedflag}', $state_locked.'!, ', $thisline);
		}
		else
			$thisline = mb_ereg_replace('{archivedflag}', '', $thisline);
		
		$thisline = mb_ereg_replace('{username}', xmlentities($r['username']), $thisline);
		$thisline = mb_ereg_replace('{cacheid}', $r['cacheid'], $thisline);

		append_output($thisline);
	}
	mysql_free_result($rs);
	
	append_output($locFoot);
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

		return $str;
	}
	
?>
