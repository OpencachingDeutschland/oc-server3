<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/


/*
 * RFC(2)822 Email Parser
 *
 * By Cal Henderson <cal@iamcal.com>
 * This code is licensed under a Creative Commons Attribution-ShareAlike 2.5 License
 * http://creativecommons.org/licenses/by-sa/2.5/
 *
 * Revision 4
 */

function is_valid_email_address($email)
{
	/*
	 * NO-WS-CTL       =       %d1-8 /         ; US-ASCII control characters
	 *                         %d11 /          ;  that do not include the
	 *                         %d12 /          ;  carriage return, line feed,
	 *                         %d14-31 /       ;  and white space characters
	 *                         %d127
	 * ALPHA          =  %x41-5A / %x61-7A   ; A-Z / a-z
	 * DIGIT          =  %x30-39
	 */

	$no_ws_ctl    = "[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x7f]";
	$alpha        = "[\\x41-\\x5a\\x61-\\x7a]";
	$digit        = "[\\x30-\\x39]";
	$cr        = "\\x0d";
	$lf        = "\\x0a";
	$crlf        = "($cr$lf)";


	/*
	 *
	 *obs-char        =       %d0-9 / %d11 /          ; %d0-127 except CR and
	 *                         %d12 / %d14-127         ;  LF
	 * obs-text        =       *LF *CR *(obs-char *LF *CR)
	 * text            =       %d1-9 /         ; Characters excluding CR and LF
	 *                         %d11 /
	 *                         %d12 /
	 *                         %d14-127 /
	 *                         obs-text
	 * obs-qp          =       "\" (%d0-127)
	 * quoted-pair     =       ("\" text) / obs-qp
	 */

	$obs_char    = "[\\x00-\\x09\\x0b\\x0c\\x0e-\\x7f]";
	$obs_text    = "($lf*$cr*($obs_char$lf*$cr*)*)";
	$text        = "([\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f]|$obs_text)";
	$obs_qp        = "(\\x5c[\\x00-\\x7f])";
	$quoted_pair    = "(\\x5c$text|$obs_qp)";


	/*
	 *
	 * obs-FWS         =       1*WSP *(CRLF 1*WSP)
	 * FWS             =       ([*WSP CRLF] 1*WSP) /   ; Folding white space
	 *                         obs-FWS
	 * ctext           =       NO-WS-CTL /     ; Non white space controls
	 *                         %d33-39 /       ; The rest of the US-ASCII
	 *                         %d42-91 /       ;  characters not including "(",
	 *                         %d93-126        ;  ")", or "\"
	 * ccontent        =       ctext / quoted-pair / comment
	 * comment         =       "(" *([FWS] ccontent) [FWS] ")"
	 * CFWS            =       *([FWS] comment) (([FWS] comment) / FWS)
	 */

	/*
	 * note: we translate ccontent only partially to avoid an infinite loop
	 * instead, we'll recursively strip comments before processing the input
	 */

	$wsp        = "[\\x20\\x09]";
	$obs_fws    = "($wsp+($crlf$wsp+)*)";
	$fws        = "((($wsp*$crlf)?$wsp+)|$obs_fws)";
	$ctext        = "($no_ws_ctl|[\\x21-\\x27\\x2A-\\x5b\\x5d-\\x7e])";
	$ccontent    = "($ctext|$quoted_pair)";
	$comment    = "(\\x28($fws?$ccontent)*$fws?\\x29)";
	$cfws        = "(($fws?$comment)*($fws?$comment|$fws))";
	$cfws        = "$fws*";


	/*
	 *
	 * atext           =       ALPHA / DIGIT / ; Any character except controls,
	 *                         "!" / "#" /     ;  SP, and specials.
	 *                         "$" / "%" /     ;  Used for atoms
	 *                         "&" / "'" /
	 *                         "*" / "+" /
	 *                         "-" / "/" /
	 *                         "=" / "?" /
	 *                         "^" / "_" /
	 *                         "" / "{" /
	 *                         "|" / "}" /
	 *                         "~"
	 * atom            =       [CFWS] 1*atext [CFWS]
	 */
	 
	$atext        = "($alpha|$digit|[\\x21\\x23-\\x27\\x2a\\x2b\\x2d\\x2e\\x3d\\x3f\\x5e\\x5f\\x60\\x7b-\\x7e])";
	$atom        = "($cfws?$atext+$cfws?)";


	/*
	 *
	 * qtext           =       NO-WS-CTL /     ; Non white space controls
	 *                         %d33 /          ; The rest of the US-ASCII
	 *                         %d35-91 /       ;  characters not including "\"
	 *                         %d93-126        ;  or the quote character
	 * qcontent        =       qtext / quoted-pair
	 * quoted-string   =       [CFWS]
	 *                         DQUOTE *([FWS] qcontent) [FWS] DQUOTE
	 *                         [CFWS]
	 * word            =       atom / quoted-string
	 */

	$qtext        = "($no_ws_ctl|[\\x21\\x23-\\x5b\\x5d-\\x7e])";
	$qcontent    = "($qtext|$quoted_pair)";
	$quoted_string    = "($cfws?\\x22($fws?$qcontent)*$fws?\\x22$cfws?)";
	$word        = "($atom|$quoted_string)";


	/*
	 *
	 * obs-local-part  =       word *("." word)
	 * obs-domain      =       atom *("." atom)
	 */

	$obs_local_part    = "($word(\\x2e$word)*)";
	$obs_domain    = "($atom(\\x2e$atom)*)";


	/*
	 *
	 * dot-atom-text   =       1*atext *("." 1*atext)
	 * dot-atom        =       [CFWS] dot-atom-text [CFWS]
	 */

	$dot_atom_text    = "($atext+(\\x2e$atext+)*)";
	$dot_atom    = "($cfws?$dot_atom_text$cfws?)";


	/*
	 *
	 * domain-literal  =       [CFWS] "[" *([FWS] dcontent) [FWS] "]" [CFWS]
	 * dcontent        =       dtext / quoted-pair
	 * dtext           =       NO-WS-CTL /     ; Non white space controls
	 *
	 *                         %d33-90 /       ; The rest of the US-ASCII
	 *                         %d94-126        ;  characters not including "[",
	 *                                         ;  "]", or "\"
	 */

	$dtext        = "($no_ws_ctl|[\\x21-\\x5a\\x5e-\\x7e])";
	$dcontent    = "($dtext|$quoted_pair)";
	$domain_literal    = "($cfws?\\x5b($fws?$dcontent)*$fws?\\x5d$cfws?)";


	/*
	 *
	 * local-part      =       dot-atom / quoted-string / obs-local-part
	 * domain          =       dot-atom / domain-literal / obs-domain
	 * addr-spec       =       local-part "@" domain
	 */

	$local_part    = "($dot_atom|$quoted_string|$obs_local_part)";
	$domain        = "($dot_atom|$domain_literal|$obs_domain)";
	$addr_spec    = "($local_part\\x40$domain)";


	/*
	 * we need to strip comments first (repeat until we can't find any more)
	 */

	$done = 0;

	while(!$done)
	{
		$new = preg_replace("!$comment!", '', $email);
		if (strlen($new) == strlen($email))
		{
			$done = 1;
		}
		$email = $new;
	}


	/*
	 * now match what's left
	 */

	return preg_match("!^$addr_spec$!", $email) ? 1 : 0;
}

function mb_trim($str)
{
	$bLoop = true;
	while ($bLoop == true)
	{
		$sPos = mb_substr($str, 0, 1);
		
		if ($sPos == ' ' || $sPos == "\r" || $sPos == "\n" || $sPos == "\t" || $sPos == "\x0B" || $sPos == "\0")
			$str = mb_substr($str, 1, mb_strlen($str) - 1);
		else
			$bLoop = false;
	}

	$bLoop = true;
	while ($bLoop == true)
	{
		$sPos = mb_substr($str, -1, 1);
		
		if ($sPos == ' ' || $sPos == "\r" || $sPos == "\n" || $sPos == "\t" || $sPos == "\x0B" || $sPos == "\0")
			$str = mb_substr($str, 0, mb_strlen($str) - 1);
		else
			$bLoop = false;
	}

	return $str;
}

// explode with more than one separator
function explode_multi($str, $sep)
{
	$ret = array();
	$nCurPos = 0;

	while ($nCurPos < mb_strlen($str))
	{
		$nNextSep = mb_strlen($str);
		for ($nSepPos = 0; $nSepPos < mb_strlen($sep); $nSepPos++)
		{
			$nThisPos = mb_strpos($str, mb_substr($sep, $nSepPos, 1), $nCurPos);
			if ($nThisPos !== false)
				if ($nNextSep > $nThisPos)
					$nNextSep = $nThisPos;
		}

		$ret[] = mb_substr($str, $nCurPos, $nNextSep - $nCurPos);

		$nCurPos = $nNextSep + 1;
	}

	return $ret;
}

function getSysConfig($name, $default)
{
	return sql_value("SELECT `value` FROM `sysconfig` WHERE `name`='&1'", $default, $name);
}

function setSysConfig($name, $value)
{
	sql("INSERT INTO `sysconfig` (`name`, `value`) VALUES ('&1', '&2') ON DUPLICATE KEY UPDATE `value`='&2'", $name, $value);
}

function read_file($filename, $maxlength)
{
	$content = '';

	$f = fopen($filename, 'r');
	if ($f === false) return false;

	while ($line = fread($f, 4096))
		$content .= $line;
	fclose($f);

	return $content;
}

// encodes &<>"'
function xmlentities($str)
{
	return htmlspecialchars(xmlfilterevilchars($str), ENT_QUOTES, 'UTF-8');
}

// encodes &<>
// This is ok for XML content text between tags, but not for XML attribute contents.
function text_xmlentities($str)
{
	return htmlspecialchars(xmlfilterevilchars($str), ENT_NOQUOTES, 'UTF-8');
}

function xmlfilterevilchars($str)
{
	// the same for for ISO-8859-1 and UTF-8
	// following 2016-3-1: allowed Tabs (\x{09})
	return mb_ereg_replace('[\x{00}-\x{08}\x{0B}\x{0C}\x{0E}-\x{1F}]*', '', $str);
}


	// decimal longitude to string E/W hhh°mm.mmm
	function help_lonToDegreeStr($lon)
	{
		if ($lon < 0)
		{
			$retval = 'W ';
			$lon = -$lon;
		}
		else
		{
			$retval = 'E ';
		}

		$retval = $retval . sprintf("%03d", floor($lon)) . '° ';
		$lon = $lon - floor($lon);
		$retval = $retval . sprintf("%06.3f", round($lon * 60, 3)) . '\'';

		return $retval;
	}

	// decimal latitude to string N/S hh°mm.mmm
	function help_latToDegreeStr($lat)
	{
		if ($lat < 0)
		{
			$retval = 'S ';
			$lat = -$lat;
		}
		else
		{
			$retval = 'N ';
		}

		$retval = $retval . sprintf("%02d", floor($lat)) . '° ';
		$lat = $lat - floor($lat);
		$retval = $retval . sprintf("%06.3f", round($lat * 60, 3)) . '\'';

		return $retval;
	}


	function escape_javascript($text)
	{
		return str_replace('\'', '\\\'', str_replace('"', '&quot;', $text));
	}


	// perform str_rot13 without renaming parts in []
	function str_rot13_gc($str)
	{
		$delimiter[0][0] = '[';
		$delimiter[0][1] = ']';

		$retval = '';

		while (mb_strlen($retval) < mb_strlen($str))
		{
			$nNextStart = false;
			$sNextEndChar = '';
			foreach ($delimiter AS $del)
			{
				$nThisStart = mb_strpos($str, $del[0], mb_strlen($retval));

				if ($nThisStart !== false)
					if (($nNextStart > $nThisStart) || ($nNextStart === false))
					{
						$nNextStart = $nThisStart;
						$sNextEndChar = $del[1];
					}
			}

			if ($nNextStart === false)
			{
				$retval .= str_rot13(mb_substr($str, mb_strlen($retval), mb_strlen($str) - mb_strlen($retval)));
			}
			else
			{
				// crypted part
				$retval .= str_rot13(mb_substr($str, mb_strlen($retval), $nNextStart - mb_strlen($retval)));

				// uncrypted part
				$nNextEnd = mb_strpos($str, $sNextEndChar, $nNextStart);

				if ($nNextEnd === false)
					$retval .= mb_substr($str, $nNextStart, mb_strlen($str) - mb_strlen($retval));
				else
					$retval .= mb_substr($str, $nNextStart, $nNextEnd - $nNextStart + 1);
			}
		}

		return $retval;
	}


	// format number with 1000s dots
	function number1000($n)
	{
		global $opt;

		if (isset($opt['locale'][$opt['template']['locale']]['format']['dot1000']) &&
		    $opt['locale'][$opt['template']['locale']]['format']['dot1000'] == ',')
			return number_format($n);
		else
			return str_replace(',', '.', number_format($n));
	}
