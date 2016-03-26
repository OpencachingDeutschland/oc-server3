<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 *
 *  Rot13 hint decoder
 ***************************************************************************/

header('Content-type: text/html; charset=utf-8');

if (isset($_REQUEST['text'])) {
	$text = $_REQUEST['text'];
	$text = str_replace(array('<br>', '<br/>', '<br />'), "\n", $text);
	$text = hint_rot13($text);
	$text = htmlentities($text);
	$text = nl2br($text);
	echo $text;
}


function hint_rot13($in)
{
	$out = "";
	$decode = true;

	for ($i=0; $i<strlen($in); ++$i)
	{
		$c = $in[$i];
		if ($decode && $c == '[') {
			$out .= '['; $decode = false;
		} else if (!$decode && $c == ']') {
			$out .= ']'; $decode = true;
		} else if (!$decode) {
		    $out .= $c;
		} else if ($c >= 'A' && $c <= 'Z') {
			$c = chr(ord($c) + 13);
			if ($c > 'Z') {
				$c = chr(ord($c) - 26);
			}
			$out .= $c;
		} else if ($c >= 'a' && $c <= 'z') {
			$c = chr(ord($c) + 13);
			if ($c > 'z') {
				$c = chr(ord($c) - 26);
			}
			$out .= $c;
		} else {
			$out .= $c;
		}
	}

	return $out;
}
