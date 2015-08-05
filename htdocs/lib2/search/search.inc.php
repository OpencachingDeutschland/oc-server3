<?php
/****************************************************************************
		For license information see doc/license.txt

		Unicode Reminder メモ

		functions for the search-engine

 ****************************************************************************/

/* begin conversion rules */

	$search_simplerules[] = array('qu', 'k');
	$search_simplerules[] = array('ts', 'z');
	$search_simplerules[] = array('tz', 'z');
	$search_simplerules[] = array('alp', 'alb');
	$search_simplerules[] = array('y', 'i');
	$search_simplerules[] = array('ai', 'ei');
	$search_simplerules[] = array('ou', 'u');
	$search_simplerules[] = array('th', 't');
	$search_simplerules[] = array('ph', 'f');
	$search_simplerules[] = array('oh', 'o');
	$search_simplerules[] = array('ah', 'a');
	$search_simplerules[] = array('eh', 'e');
	$search_simplerules[] = array('aux', 'o');
	$search_simplerules[] = array('eau', 'o');
	$search_simplerules[] = array('eux', 'oe');
	$search_simplerules[] = array('^ch', 'sch');
	$search_simplerules[] = array('ck', 'k');
	$search_simplerules[] = array('ie', 'i');
	$search_simplerules[] = array('ih', 'i');
	$search_simplerules[] = array('ent', 'end');
	$search_simplerules[] = array('uh', 'u');
	$search_simplerules[] = array('sh', 'sch');
	$search_simplerules[] = array('ver', 'wer');
	$search_simplerules[] = array('dt', 't');
	$search_simplerules[] = array('hard', 'hart');
	$search_simplerules[] = array('egg', 'ek');
	$search_simplerules[] = array('eg', 'ek');
	$search_simplerules[] = array('cr', 'kr');
	$search_simplerules[] = array('ca', 'ka');
	$search_simplerules[] = array('ce', 'ze');
	$search_simplerules[] = array('x', 'ks');
	$search_simplerules[] = array('ve', 'we');
	$search_simplerules[] = array('va', 'wa');

/* end conversion rules */

function search_text2simple($str)
{
	global $search_simplerules;

	$str = search_text2sort($str);

	// regeln anwenden
	foreach ($search_simplerules AS $rule)
	{
		$str = mb_ereg_replace($rule[0], $rule[1], $str);
	}

	// doppelte chars ersetzen
	for ($c = ord('a'); $c <= ord('z'); $c++)
		$str = mb_ereg_replace(chr($c) . chr($c), chr($c), $str);

	return $str;
}

function search_text2sort($str, $gns_syntax=false)
{
	$str = mb_strtolower($str);

	// alles was nicht a-z ist ersetzen
	$str = mb_ereg_replace('0', '', $str);
	$str = mb_ereg_replace('1', '', $str);
	$str = mb_ereg_replace('2', '', $str);
	$str = mb_ereg_replace('3', '', $str);
	$str = mb_ereg_replace('4', '', $str);
	$str = mb_ereg_replace('5', '', $str);
	$str = mb_ereg_replace('6', '', $str);
	$str = mb_ereg_replace('7', '', $str);
	$str = mb_ereg_replace('8', '', $str);
	$str = mb_ereg_replace('9', '', $str);

	// deutsches
	if ($gns_syntax)
	{
		$str = mb_ereg_replace('ä', 'a', $str);
		$str = mb_ereg_replace('ö', 'o', $str);
		$str = mb_ereg_replace('ü', 'u', $str);
		$str = mb_ereg_replace('Ä', 'a', $str);
		$str = mb_ereg_replace('Ö', 'o', $str);
		$str = mb_ereg_replace('Ü', 'u', $str);
	}
	else
	{
		$str = mb_ereg_replace('ä', 'ae', $str);
		$str = mb_ereg_replace('ö', 'oe', $str);
		$str = mb_ereg_replace('ü', 'ue', $str);
		$str = mb_ereg_replace('Ä', 'ae', $str);
		$str = mb_ereg_replace('Ö', 'oe', $str);
		$str = mb_ereg_replace('Ü', 'ue', $str);
	}
	$str = mb_ereg_replace('ß', 'ss', $str);

  // akzente usw.
  $str = mb_ereg_replace('à', 'a', $str);
  $str = mb_ereg_replace('á', 'a', $str);
  $str = mb_ereg_replace('â', 'a', $str);
	$str = mb_ereg_replace('è', 'e', $str);
	$str = mb_ereg_replace('é', 'e', $str);
	$str = mb_ereg_replace('ë', 'e', $str);
	$str = mb_ereg_replace('É', 'e', $str);
	$str = mb_ereg_replace('ô', 'o', $str);
	$str = mb_ereg_replace('ó', 'o', $str);
	$str = mb_ereg_replace('ò', 'o', $str);
	$str = mb_ereg_replace('ê', 'e', $str);
	$str = mb_ereg_replace('ě', 'e', $str);
	$str = mb_ereg_replace('û', 'u', $str);
	$str = mb_ereg_replace('ç', 'c', $str);
	$str = mb_ereg_replace('c', 'c', $str);
	$str = mb_ereg_replace('ć', 'c', $str);
	$str = mb_ereg_replace('î', 'i', $str);
	$str = mb_ereg_replace('ï', 'i', $str);
	$str = mb_ereg_replace('ì', 'i', $str);
	$str = mb_ereg_replace('í', 'i', $str);
	$str = mb_ereg_replace('ł', 'l', $str);
	$str = mb_ereg_replace('š', 's', $str);
	$str = mb_ereg_replace('Š', 's', $str);
	$str = mb_ereg_replace('u', 'u', $str);
	$str = mb_ereg_replace('ý', 'y', $str);
	$str = mb_ereg_replace('ž', 'z', $str);
	$str = mb_ereg_replace('Ž', 'Z', $str);

	$str = mb_ereg_replace('Æ', 'ae', $str);
	$str = mb_ereg_replace('æ', 'ae', $str);
	$str = mb_ereg_replace('œ', 'oe', $str);

	// interpunktion
	$str = mb_ereg_replace('\\?', '', $str);
	$str = mb_ereg_replace('\\)', '', $str);
	$str = mb_ereg_replace('\\(', '', $str);
	$str = mb_ereg_replace('\\.', ' ', $str);
	$str = mb_ereg_replace('´', ' ', $str);
	$str = mb_ereg_replace('`', ' ', $str);
	$str = mb_ereg_replace('\'', ' ', $str);

	// sonstiges
	$str = str_replace('', '', $str);

	// der rest
	$str = mb_ereg_replace('[^a-z]', '', $str);

	return $str;
}

?>