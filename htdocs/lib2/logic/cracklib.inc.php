<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

/* check if a password is complex enough
 *
 * length min. 6 chars
 * min. 4 different chars
 *
 * if cracklib is available run dictionary check
 * Attention: if you use a too large wordlist,
 *            your users may be unhappy to search
 *            for a strong enough password
 *
 * $pw may not contain one of $addwords[]
 * one of $addwords[] may not contain $pw
 *
 * return value
 * true  ... complex enough
 * false ... not complex enough
 */
function cracklib_checkpw($pw, $addwords)
{
	global $opt;

	// length min. 6 chars
	if (strlen($pw) < 6)
		return false;

	// min. 4 different chars
	$chars = array();
	for ($i = 0; $i < mb_strlen($pw); $i++)
		$chars[mb_substr($pw, $i, 1)] = true;

	if (count($chars) <= 4)
		return false;
	unset($chars);

	// prepare $addwords
	$wordlist = array();
	foreach ($addwords AS $word)
	{
		$word = mb_strtolower($word);
	
		$word = mb_ereg_replace('\\?', ' ', $word);
		$word = mb_ereg_replace('\\)', ' ', $word);
		$word = mb_ereg_replace('\\(', ' ', $word);
		$word = mb_ereg_replace('\\.', ' ', $word);
		$word = mb_ereg_replace('´', ' ', $word);
		$word = mb_ereg_replace('`', ' ', $word);
		$word = mb_ereg_replace('\'', ' ', $word);
		$word = mb_ereg_replace('/', ' ', $word);
		$word = mb_ereg_replace(':', ' ', $word);
		$word = mb_ereg_replace('-', ' ', $word);
		$word = mb_ereg_replace(',', ' ', $word);
		$word = mb_ereg_replace("\r\n", ' ', $word);
		$word = mb_ereg_replace("\n", ' ', $word);
		$word = mb_ereg_replace("\r", ' ', $word);

		$wordlist = array_merge($wordlist, mb_split(' ', $word));
	}
	foreach ($wordlist AS $k => $v)
		if (mb_strlen($v) < 3)
			unset($wordlist[$k]);

	$pw_lc = mb_strtolower($pw);

	// $pw may not contain one of $addwords[]
	foreach ($wordlist AS $v)
		if (mb_stripos($pw_lc, $v) !== false)
			return false;

	// one of $addwords[] may not contain $pw
	foreach ($wordlist AS $v)
		if (mb_stripos($v, $pw_lc) !== false)
			return false;

	// words from pw library are not allowed
	if (sql_value("SELECT COUNT(*) FROM `pw_dict` WHERE `pw`='&1'", 0, $pw))
		return false;

	if ($opt['logic']['cracklib'] == true)
	{
		// load cracklib
		if (!function_exists('crack_check'))
			@dl('crack.so');

		// cracklib loaded?
		if (function_exists('crack_check'))
			if (!crack_check($pw))
				return false;
	}

	return true;
}
?>