<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Displays the Chat/IRC using iframe of freenode.net, escaping usernames
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$sUserCountry = $login->getUserCountry();
	
	$tpl->name = 'webchat';
	$tpl->menuitem = MNU_CHAT;

	$tpl->caching = false;
	$tpl->cache_id = $sUserCountry;

	// check loggedin and set username for chat
	$chatusername = $translate->t('Guest', '', basename(__FILE__), __LINE__) . rand(100,999);
	if ($login->userid != 0)
		$chatusername = urlEncodeString(ircConvertString($login->username));
	
	// prepare iframe-URL
	$chatiframeurl = str_replace('{chatusername}',$chatusername,$opt['chat']['url']);
	
	// assign to template
	$tpl->assign('chatiframeurl',$chatiframeurl);
	$tpl->assign('chatiframewidth',$opt['chat']['width']);
	$tpl->assign('chatiframeheight',$opt['chat']['height']);

	$tpl->display();

	
	/*
	 * OC allows alphanumeric chars in username and
	 *   . - _ @ ä ü ö Ä Ü Ö = ) ( / \ & * + ~ #
	 *
	 * IRC allows alphanumeric chars in nick and:
	 *   _ - \ [ ] { } ^ ` |
	 *
	 * so we have to convert the following chars before urlencoding it:
	 *   . @ ä ü ö Ä Ü Ö = ) ( / & * + ~ #
	 */

	/*
	 * functions
	 */

	function urlEncodeString($string)
	{
		return translateString(
			$string,
			// chars/encodings allowed in username see const REGEX_USERNAME
			//   . - _ @ ä ü ö Ä Ü Ö = ) ( / \ & * + ~ #
			// (ajust if regex is changed)
			array(
				'.' => '%2E',
				'-' => '%2D',
				'_' => '%5F',
				'@' => '%40',
				'ä' => '%E4',
				'ü' => '%FC',
				'ö' => '%F6',
				'Ä' => '%C4',
				'Ü' => '%DC',
				'Ö' => '%D6',
				'=' => '%3D',
				')' => '%29',
				'(' => '%28',
				'/' => '%2F',
				'\\'=> '%5C',
				'&' => '%26',
				'*' => '%2A',
				'+' => '%2B',
				'~' => '%7E',
				'#' => '%23',
				// used in converting to IRC compatible nicks:
				'}' => '%7D',
				'{' => '%7B',
			));
	}

	function ircConvertString($string)
	{
		return translateString(
			$string,
			// chars/replacement allowed OC usernames and not in IRC nickname
			//   . @ ä ü ö Ä Ü Ö = ) ( / & * + ~ #
			// (adjust if additional username chars are allowed)
			array(
				'.' => '',
				'@' => '{at}',
				'ä' => 'ae',
				'ü' => 'ue',
				'ö' => 'oe',
				'Ä' => 'Ae',
				'Ü' => 'Ue',
				'Ö' => 'Oe',
				'=' => '-',
				')' => '}',
				'(' => '{',
				'/' => '\\',
				'&' => '',
				'*' => '',
				'+' => '',
				'~' => '-',
				'#' => '',
				));
	}

	function translateString($string, $translation_table)
	{
		// walk through $string and encode string
		$outstring = '';
		for ($i=0; $i<mb_strlen($string); $i++)
		{
			$char = mb_substr($string,$i,1);
			
			// find replacement
			if (isset($translation_table[$char]))
				$outstring .= $translation_table[$char];
			else
				$outstring .= $char;
		}
		
		// return
		return $outstring;
	}

?>
