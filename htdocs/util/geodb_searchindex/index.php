#!/usr/local/bin/php -q
<?php
 /***************************************************************************
													./util/geodb_serachindex/index.php
															-------------------
		begin                : Sat September 24 2005

		For license information see doc/license.txt
 ****************************************************************************/

 /***************************************************************************
		
		Unicode Reminder メモ

		Ggf. muss die Location des php-Binaries angepasst werden.
		
		Dieses Script erstellt den Suchindex für Ortsnamen aus den Daten der 
		Opengeodb.
		
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

	sql('DELETE FROM geodb_search');
	
	$rs = sql("SELECT `loc_id`, `text_val` FROM `geodb_textdata` WHERE `text_type`=500100000 AND text_locale IN ('da', 'de', 'en', 'fi', 'fr', 'it', 'nl', 'rm')");
	while ($r = sql_fetch_array($rs))
	{
		$simpletexts = search_text2sort($r['text_val']);
		$simpletextsarray = explode_multi($simpletexts, ' -/,');

		foreach ($simpletextsarray AS $text)
		{
			if ($text != '')
			{
				if (nonalpha($text))
					die($text . "\n");
				
				$simpletext = search_text2simple($text);

				sql("INSERT INTO `geodb_search` (`loc_id`, `sort`, `simple`, `simplehash`) VALUES ('&1', '&2', '&3', '&4')", $r['loc_id'], $text, $simpletext, sprintf("%u", crc32($simpletext)));
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
