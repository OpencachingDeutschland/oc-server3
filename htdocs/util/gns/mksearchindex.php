#!/usr/local/bin/php -q
<?php
 /***************************************************************************
													./util/gns/mksearchindex.php
															-------------------
		begin                : Thu November 1 2005
		copyright            : (C) 2005 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

 /***************************************************************************
		
		Unicode Reminder メモ

		Ggf. muss die Location des php-Binaries angepasst werden.
		
		Dieses Script erstellt den Suchindex für Ortsnamen aus den Daten der 
		GNS-DB.
		
	***************************************************************************/

  $rootpath = '../../';
  require_once($rootpath . 'lib/clicompatbase.inc.php');
  require_once($rootpath . 'lib/search.inc.php');

/* begin db connect */
	db_connect();
	if ($dblink === false)
	{
		echo 'Unable to connect to database';
		exit;
	}
/* end db connect */
  
/* begin search index rebuild */

	$doubleindex['sankt'] = 'st';

	sql('DELETE FROM gns_search');
	
	$rs = sql("SELECT `uni`, `full_name_nd` FROM `gns_locations` WHERE `dsg` LIKE 'PPL%'");
	while ($r = sql_fetch_array($rs))
	{
		$simpletexts = search_text2sort($r['full_name_nd']);
		$simpletextsarray = explode_multi($simpletexts, ' -/,');

		foreach ($simpletextsarray AS $text)
		{
			if ($text != '')
			{
				if (nonalpha($text))
					die($r['uni'] . ' ' . $text . "\n");
				
				$simpletext = search_text2simple($text);

				sql("INSERT INTO `gns_search` (`uni_id`, `sort`, `simple`, `simplehash`) VALUES ('&1', '&2', '&3', '&4')", $r['uni'], $text, $simpletext, sprintf("%u", crc32($simpletext)));
				
				if (isset($doubleindex[$text]))
					sql("INSERT INTO `gns_search` (`uni_id`, `sort`, `simple`, `simplehash`) VALUES ('&1', '&2', '&3', '&4')", $r['uni'], $text, $doubleindex[$text], sprintf("%u", crc32($doubleindex[$text])));
			}
		}
	}
	mysql_free_result($rs);

/* end search index rebuild */

function nonalpha($str)
{
	for ($i = 0; $i < mb_strlen($str); $i++)
		if (!((ord(mb_substr($str, $i, 1)) >= ord('a')) && (ord(mb_substr($str, $i, 1)) <= ord('z'))))
			return true;
	
	return false;
}
?>
