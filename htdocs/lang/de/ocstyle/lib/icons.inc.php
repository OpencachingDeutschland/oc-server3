<?php
/***************************************************************************

		$Id: icons.inc.php,v 1.12 2007/10/30 20:23:10 oliver Exp $
		$Date: 2007/10/30 20:23:10 $
		$Revision: 1.12 $
		begin                : Fr Sept 9 2005
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 set template specific variables for icons

 ****************************************************************************/

function icon_log_type($icon_small, $text)
{
	global $stylepath;
	return "<img src='$stylepath/images/$icon_small' width='16' height='16' align='middle' border='0' align='left' alt='$text' title='$text'>";
}

function icon_cache_status($status, $text)
{
	global $stylepath;
	switch($status)
	{
		case 1: $icon = "log/16x16-go.png"; break;
		case 2: $icon = "log/16x16-stop.png"; break;
		case 3: $icon = "log/16x16-trash.png"; break;
		case 4: $icon = "log/16x16-trash.png"; break;
		case 5: $icon = "log/16x16-stop.png"; break;

		default: $icon = "log/16x16-go.png"; break;
	}
	return "<img src='$stylepath/images/$icon' width='16' height='16' align='middle' border='0' align='left' alt='$text' title='$text'>";
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
	return "<img src='$icon' border='0' width='19' height='16' hspace='2' alt='$text' title='$text'>";

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