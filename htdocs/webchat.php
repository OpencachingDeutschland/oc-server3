<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Displays the Chat/IRC using iframe of freenode.net escaping usernames
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$sUserCountry = $login->getUserCountry();
	
	$tpl->name = 'webchat';
	$tpl->menuitem = MNU_CHAT;

	$tpl->caching = false;
	$tpl->cache_id = $sUserCountry;

	// check loggedin and set username for chat
	/*
	 * OC allows alphanumeric chars in username and:
	 * ". - _ @ ä ü ö Ä Ü Ö = ) ( / \ & * + ~ #"
	 * 
	 * IRC allows alphanumeric chars in nick and:
	 * "_ - \ [ ] { } ^ ` |"
	 * 
	 * so we have to convert the folloing chars before urlencoding it:
	 * ". @ ä ü ö Ä Ü Ö = ) ( / & * + ~ #"
	 */
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
	 * functions
	 */
	function urlEncodeString($string)
	{
		// set arrays with chars/encodings allowed in username see const REGEX_USERNAME
		// ". - _ @ ä ü ö Ä Ü Ö = ) ( / \ & * + ~ #" (ajust if regex is changed)
		$k[] = '.';  $v[] = '%2E';
		$k[] = '-';  $v[] = '%2D';
		$k[] = '_';  $v[] = '%5F';
		$k[] = '@';  $v[] = '%40';
		$k[] = 'ä';  $v[] = '%E4';
		$k[] = 'ü';  $v[] = '%FC';
		$k[] = 'ö';  $v[] = '%F5';
		$k[] = 'Ä';  $v[] = '%C4';
		$k[] = 'Ü';  $v[] = '%DC';
		$k[] = 'Ö';  $v[] = '%D6';
		$k[] = '=';  $v[] = '%3D';
		$k[] = ')';  $v[] = '%29';
		$k[] = '(';  $v[] = '%28';
		$k[] = '/';  $v[] = '%2F';
		$k[] = '\\'; $v[] = '%5C';
		$k[] = '&';  $v[] = '%26';
		$k[] = '*';  $v[] = '%2A';
		$k[] = '+';  $v[] = '%2B';
		$k[] = '~';  $v[] = '%7E';
		$k[] = '#';  $v[] = '%23';
		// used in converting to IRC compatible nicks
		$k[] = '}';  $v[] = '%7D';
		$k[] = '{';  $v[] = '%7B';
		
		// walk through $string and encode string
		$outstring = '';
		for ($i=0;$i<mb_strlen($string);$i++)
		{
			$char = mb_substr($string,$i,1);
			
			// find replacement
			$id = array_search($char,$k);
			if ($id !== false)
				$outstring .= $v[$id];
			else
				$outstring .= $char;
		}
		
		// return
		return $outstring;
	}
	
	function ircConvertString($string)
	{
		// set arrays with chars/replacement allowed OC usernames and not in IRC nickname
		// ". @ ä ü ö Ä Ü Ö = ) ( / & * + ~ #"
		$k[] = '.';  $v[] = '';
		$k[] = '@';  $v[] = '{at}';
		$k[] = 'ä';  $v[] = 'ae';
		$k[] = 'ü';  $v[] = 'ue';
		$k[] = 'ö';  $v[] = 'oe';
		$k[] = 'Ä';  $v[] = 'Ae';
		$k[] = 'Ü';  $v[] = 'Ue';
		$k[] = 'Ö';  $v[] = 'Oe';
		$k[] = '=';  $v[] = '-';
		$k[] = ')';  $v[] = '}';
		$k[] = '(';  $v[] = '{';
		$k[] = '/';  $v[] = '\\';
		$k[] = '&';  $v[] = '';
		$k[] = '*';  $v[] = '';
		$k[] = '+';  $v[] = '';
		$k[] = '~';  $v[] = '-';
		$k[] = '#';  $v[] = '';
		
		// walk through $string and encode string
		$outstring = '';
		for ($i=0;$i<mb_strlen($string);$i++)
		{
			$char = mb_substr($string,$i,1);
			
			// find replacement
			$id = array_search($char,$k);
			if ($id !== false)
				$outstring .= $v[$id];
			else
				$outstring .= $char;
		}
		
		// return
		return $outstring;
	}
?>
