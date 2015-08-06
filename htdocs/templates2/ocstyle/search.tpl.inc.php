<?php
/****************************************************************************
	 For license information see doc/license.txt

   Unicode Reminder メモ

	 variable search template contents
 ****************************************************************************/

	// search.php -> $tpl->error
 	$outputformat_notexist = _('The selected output format is unknown!');
 	$error_query_not_found = _('The search operation could not be executed, please reenter the search data.');
	$unknown_searchoption = _('unknown search option');
	$unknown_searchtype = _('unknown search type');

	// search.php -> search.tpl
	$error_plz = '<tr><td colspan="3"><span class="errormsg">' . _('The postal code could not be found') . '</span></td></tr>';
	$error_ort = '<tr><td colspan="3"><span class="errormsg">' . _('There does no city exist with this name') . '</span></td></tr>';
	$error_locidnocoords = '<tr><td colspan="3"><span class="errormsg">' . _('There are no Koordinates available for the selected city') . '</span></td></tr>';
	$error_noort = '<tr><td colspan="3"><span class="errormsg">' . _('The entered city is not valid.') . '</span></td></tr>';
	$error_nofulltext = '<tr><td colspan="3"><span class="errormsg">' . _('The entered text is invalid.') . '</span></td></tr>';
	$error_fulltexttoolong = '<tr><td colspan="3"><span class="errormsg">' . _('The entered text contains more than 50 words.') . '</span></td></tr>';

	// search.php -> selectlocid.tpl
	$locline = '<tr><td width="50px"><p>{nr}.&nbsp;</p></td><td><p><b><a href="search.php?{urlparams}">{locationname}</a>{secondlocationname}</b></p></td></tr>
							<tr><td width="50px">&nbsp;</td><td><p style="margin-bottom:4px">{coords}</p></td></tr>
							<tr><td width="50px">&nbsp;</td><td style="padding-bottom:8px; vertical-align:top"><span class="content-subtitle">{parentlocations}</span></td></tr>';

	$secondlocationname = '&nbsp;<font size="1">({secondlocationname})</font>';
	$no_location_coords = _('no coordinates available');

	// search.html.inc.php -> search.result.caches.tpl
	$caches_newstring = '<b class="newsymbol">&nbsp;' . _('NEW') . '&nbsp;</b>&nbsp;';
	$caches_oconlystring = '<img src="resource2/ocstyle/images/misc/is_oconly.png" alt="OConly" title="OConly" style="margin:0px; padding:0px" width="64" height="35" />';

	$cache_attrib_group =
	'<div class="attribgroup"><table cellspacing="0">
	     <tr><td bgcolor="{color}" style="line-height:9px;padding-top:2px;margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-top:1px solid gray;"><font size="1">{name}</font></td></tr>
	     <tr><td bgcolor="#F8F8F8" style="margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-bottom:1px solid gray;">{attribs}</td></tr>
	   </table></div>';
	$cache_attrib_jsarray_line = "new Array('{id}', {state}, '{text_long}', '{icon}', '{icon_no}', '{icon_undef}', '{search_default}')";
	$cache_attrib_img_line1 = '<img id="attrimg1_{id}" src="{icon}" onmousedown="switchAttribute({id})" onmouseover="Tip(\'{html_desc}\', TITLE, \'{name}\', TITLEBGCOLOR, \'{color}\', TITLEFONTCOLOR, \'#000000\', BGCOLOR, \'#FFFFFF\', BORDERCOLOR, \'{color}\', CLICKCLOSE, true, DELAY, 0, FADEIN, false, FADEOUT, false, FONTCOLOR, \'#000080\', WIDTH, 500)" onmouseout="UnTip()" />&nbsp;';
	$cache_attrib_img_line2 = '<img id="attrimg2_{id}" src="{icon}" onmousedown="switchAttribute({id})" onmouseover="Tip(\'{html_desc}\', TITLE, \'{name}\', TITLEBGCOLOR, \'{color}\', TITLEFONTCOLOR, \'#000000\', BGCOLOR, \'#FFFFFF\', BORDERCOLOR, \'{color}\', CLICKCLOSE, true, DELAY, 0, FADEIN, false, FADEOUT, false, FONTCOLOR, \'#000080\', WIDTH, 500)" onmouseout="UnTip()" />&nbsp;';

	// search.*.inc.php (TXT, KML, LOC, GPX ...)
	$converted_from_html = _('converted from HTML');
	$state_temporarily_na = _('Temporary not available');
	$state_archived = _('Archived');
	$state_locked = _('Locked');
	$cache_note_text = _('Personal cache note');

?>
