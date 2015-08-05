#!/usr/local/bin/php -q
<?php
 /***************************************************************************
		For license information see doc/license.txt
		
		Unicode Reminder メモ

		Dieses Script erstellt den Suchindex für Ortsnamen aus den Daten der 
		GNS-DB.
		
	***************************************************************************/

  $opt['rootpath'] = '../../';
  require_once($opt['rootpath'] . 'lib2/cli.inc.php');
  require_once($opt['rootpath'] . 'lib2/search/search.inc.php');


	$doubleindex['sankt'] = 'st';

	sql('DELETE FROM gns_search');
	
	$rs = sql("SELECT `uni`, `full_name_nd` FROM `gns_locations` WHERE `dsg` LIKE 'PPL%'");
	while ($r = sql_fetch_array($rs))
	{
		$simpletexts = search_text2sort($r['full_name_nd'], true);
		$simpletextsarray = explode_multi($simpletexts, ' -/,');
		  // ^^ This should be obsolete, as search_text2sort() removes all non-a..z chars.

		foreach ($simpletextsarray AS $text)
		{
			if ($text != '')
			{
				if (nonalpha($text))
					die($r['uni'] . ' ' . $text . "\n");  // obsolete for the same reason as above
				
				$simpletext = search_text2simple($text);

				sql("INSERT INTO `gns_search` (`uni_id`, `sort`, `simple`, `simplehash`) VALUES ('&1', '&2', '&3', '&4')", $r['uni'], $text, $simpletext, sprintf("%u", crc32($simpletext)));
				
				if (isset($doubleindex[$text]))
					sql("INSERT INTO `gns_search` (`uni_id`, `sort`, `simple`, `simplehash`) VALUES ('&1', '&2', '&3', '&4')", $r['uni'], $text, $doubleindex[$text], sprintf("%u", crc32($doubleindex[$text])));
			}
		}
	}
	mysql_free_result($rs);


	function nonalpha($str)
	{
		for ($i = 0; $i < mb_strlen($str); $i++)
			if (!((ord(mb_substr($str, $i, 1)) >= ord('a')) && (ord(mb_substr($str, $i, 1)) <= ord('z'))))
				return true;
	
		return false;
	}

?>
