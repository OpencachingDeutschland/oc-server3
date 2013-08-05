<?php
/****************************************************************************
		begin                : Fr Sept 9 2005

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 set template specific variables for icons

 ****************************************************************************/

function icon_log_type($icon_small, $text)
{
	global $stylepath;
	return "<img src='resource2/ocstyle/images/$icon_small' width='16' height='16' align='middle' border='0' align='left' alt='$text' title='$text' />";
}

function icon_difficulty($what, $difficulty)
{
	global $stylepath;
	global $difficulty_text_diff;
	global $difficulty_text_terr;

	if($what != "diff" && $what != "terr")
		die("Wrong difficulty-identifier!");

	$difficulty = (int)$difficulty;
	if($difficulty < 2 || $difficulty > 10)
		die("Wrong difficulty-value $what: $difficulty");

	$icon = sprintf("$stylepath/images/difficulty/$what-%d%d.gif", (int)$difficulty / 2, ((float)($difficulty / 2) - (int)($difficulty / 2)) * 10);
	$text = sprintf($what == "diff" ? $difficulty_text_diff : $difficulty_text_terr, $difficulty / 2);
	return "<img src='$icon' border='0' width='19' height='16' hspace='2' alt='$text' title='$text' />";

}

function icon_rating($founds, $topratings)
{
	global $stylepath;
	global $rating_text;
	global $not_rated;

	if ($topratings == 0)
		return '';

	$sAltText = $topratings . ' Empfehlungen';

	if ($topratings > 3)
		$nIconsCount = 2;
	else
		$nIconsCount = $topratings;

	$sRetval = '';
	$sRetval .= str_repeat('<img src="images/rating-star.gif" alt="' . $sAltText . '" title="' . $sAltText . '" width="17px" height="16px" />', $nIconsCount);

	if ($topratings > 3)
		$sRetval .= '<img src="images/rating-plus.gif" alt="' . $sAltText . '" title="' . $sAltText . '" width="17px" height="16px" />';

	return '<nobr>' . $sRetval . '</nobr>&nbsp;';
}

?>
