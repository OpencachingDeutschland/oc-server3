<?php
/***************************************************************************
												  ./lang/de/ocstyle/search.inc.php
															-------------------
		begin                : July 25 2004
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

	 set template specific language variables

 ****************************************************************************/

 	$outputformat_notexist = t('The selected output format is unknown!');
 	$error_query_not_found = t('The search operation could not be executed, please reenter the search data.');
 	$safelink = '&nbsp;[<a href="query.php?action=save&queryid={queryid}">' . t('Save') . '</a>]';

 	$caches_newstring = '<b>' . t('NEW') . '</b>&nbsp;';
 	$caches_olddays = 7;

	$caches_oconlystring = '<img src="resource2/ocstyle/images/misc/is_oconly.png" alt="OConly" title="OConly" style="margin:0px; padding:0px" width="64" height="35" />';

 	$bgcolor1 = 'odd';			// even lines
 	$bgcolor2 = 'even';			// odd lines
	$bgcolor_found = "#66FFCC";		// if cache was found by user
	$bgcolor_owner = "#ffffc5";		// if user is owner
	$bgcolor_inactive = "#fafafa";	// if cache is inactive
	
	$string_by = t('by');

 	$logdateformat = 'd.m.Y';
 	$logpics[1] = '<img alt="' . t('Find') . '" border="0" src="images/ok.gif">';
 	$logpics[2] = '<img alt="' . t('Didn\'t find') . '" border="0" src="images/redcross.gif">';
 	$logpics[3] = '<img alt="' . t('Note') . '" border="0" src="images/info.gif">';

 	$diffpics[2] = 'diff-10.gif';
 	$diffpics[3] = 'diff-15.gif';
 	$diffpics[4] = 'diff-20.gif';
 	$diffpics[5] = 'diff-25.gif';
 	$diffpics[6] = 'diff-30.gif';
 	$diffpics[7] = 'diff-35.gif';
 	$diffpics[8] = 'diff-40.gif';
 	$diffpics[9] = 'diff-45.gif';
 	$diffpics[10] = 'diff-50.gif';

 	$terrpics[2] = 'terr-10.gif';
 	$terrpics[3] = 'terr-15.gif';
 	$terrpics[4] = 'terr-20.gif';
 	$terrpics[5] = 'terr-25.gif';
 	$terrpics[6] = 'terr-30.gif';
 	$terrpics[7] = 'terr-35.gif';
 	$terrpics[8] = 'terr-40.gif';
 	$terrpics[9] = 'terr-45.gif';
 	$terrpics[10] = 'terr-50.gif';

 	$terrpics[1] = 'rat-10.gif';
 	$terrpics[2] = 'rat-20.gif';
 	$terrpics[3] = 'rat-30.gif';
 	$terrpics[4] = 'rat-40.gif';
 	$terrpics[5] = 'rat-50.gif';

	$difficulty_text_diff = t("Difficulty: %01.1f of 5.0");
	$difficulty_text_terr = t("Terrain: %01.1f of 5.0");
	$rating_text = t("Rating: {rating}%");
	$not_rated = t('No Rating');

	$error_plz = '<tr><td><span class="errormsg">' . t('The postal code could not be found') . '</span></td></tr>';
	$error_ort = '<tr><td><span class="errormsg">' . t('There does no city exist with this name') . '</span></td></tr>';
	$error_locidnocoords = '<tr><td><span class="errormsg">' . t('There are no Koordinates available for the selected city') . '</span></td></tr>';
	$error_noort = '<tr><td><span class="errormsg">' . t('The entered city is not valid.') . '</span></td></tr>';
	$error_nofulltext = '<tr><td colspan="3"><span class="errormsg">' . t('The entered text is invalid.') . '</span></td></tr>';
	$error_fulltexttoolong = '<tr><td colspan="3"><span class="errormsg">' . t('The entered text contains more than 50 words.') . '</span></td></tr>';

	$gns_countries['GM'] = t('Germany');
	$gns_countries['AU'] = t('Austria');
	$gns_countries['SZ'] = t('Switzerland');

	$default_lang = t('EN');
	$search_all_countries = '<option value="" selected="selected">' . t('All countries') . '</option>';
	$search_all_cachetypes = '<option value="" selected="selected">' . t('All cachetypes') . '</option>';

	$cache_attrib_group = 
	'<div class="attribgroup"><table cellspacing="0">
	     <tr><td bgcolor="{color}" style="line-height:9px;padding-top:2px;margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-top:1px solid gray;"><font size="1">{name}</font></td></tr>
	     <tr><td bgcolor="#F8F8F8" style="margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-bottom:1px solid gray;">{attribs}</td></tr>
	   </table></div>';
	$cache_attrib_jsarray_line = "new Array('{id}', {state}, '{text_long}', '{icon}', '{icon_no}', '{icon_undef}', '{search_default}')";
	$cache_attrib_img_line1 = '<img id="attrimg1_{id}" src="{icon}" onmousedown="switchAttribute({id})" onmouseover="Tip(\'{html_desc}\', TITLE, \'{name}\', TITLEBGCOLOR, \'{color}\', TITLEFONTCOLOR, \'#000000\', BGCOLOR, \'#FFFFFF\', BORDERCOLOR, \'{color}\', CLICKCLOSE, true, DELAY, 0, FADEIN, false, FADEOUT, false, FONTCOLOR, \'#000080\', WIDTH, 500)" onmouseout="UnTip()" />&nbsp;';
	$cache_attrib_img_line2 = '<img id="attrimg2_{id}" src="{icon}" onmousedown="switchAttribute({id})" onmouseover="Tip(\'{html_desc}\', TITLE, \'{name}\', TITLEBGCOLOR, \'{color}\', TITLEFONTCOLOR, \'#000000\', BGCOLOR, \'#FFFFFF\', BORDERCOLOR, \'{color}\', CLICKCLOSE, true, DELAY, 0, FADEIN, false, FADEOUT, false, FONTCOLOR, \'#000080\', WIDTH, 500)" onmouseout="UnTip()" />&nbsp;';

function dateDiff($interval, $dateTimeBegin, $dateTimeEnd)
{
  //Parse about any English textual datetime
  //$dateTimeBegin, $dateTimeEnd

  $dateTimeBegin = strtotime($dateTimeBegin);
  if ($dateTimeBegin === -1)
    return("..begin date Invalid");

  $dateTimeEnd = strtotime($dateTimeEnd);
  if ($dateTimeEnd === -1)
    return("..end date Invalid");

  $dif = $dateTimeEnd - $dateTimeBegin;

  switch($interval)
  {
    case "s"://seconds
      return($dif);

    case "n"://minutes
      return(floor($dif/60)); //60s=1m

    case "h"://hours
      return(floor($dif/3600)); //3600s=1h

    case "d"://days
      return(floor($dif/86400)); //86400s=1d

    case "ww"://Week
      return(floor($dif/604800)); //604800s=1week=1semana

    case "m": //similar result "m" dateDiff Microsoft
      $monthBegin = (date("Y",$dateTimeBegin)*12) + date("n",$dateTimeBegin);
      $monthEnd = (date("Y",$dateTimeEnd)*12) + date("n",$dateTimeEnd);
      $monthDiff = $monthEnd - $monthBegin;
      return($monthDiff);

    case "yyyy": //similar result "yyyy" dateDiff Microsoft
      return(date("Y",$dateTimeEnd) - date("Y",$dateTimeBegin));

    default:
      return(floor($dif/86400)); //86400s=1d
  }
}
?>
