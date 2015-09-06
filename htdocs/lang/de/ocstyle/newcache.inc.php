<?php
/****************************************************************************
												  ./lang/de/ocstyle/newcache.inc.php
															-------------------
		begin                : Mon June 14 2004

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 set template specific language variables

 ****************************************************************************/

 $submit = t('Submit cache');
 $default_country = t('EN');
 $default_lang = t('EN');
 $show_all = t('Show all');
 $default_NS = 'N';
 $default_EW = 'E';
 $date_time_format_message = '&nbsp;' . t('Format:&nbsp;DD-MM-YYYY');

 $error_general = "<tr><td class='error' colspan='2'><b>" . t('Some errors occured, please check the marked fields.') . "</b></td></tr>";
 $error_long_not_ok = '<span class="errormsg">' . t('Your chosen coordinated are invalid') . '</span>';
 $error_lat_not_ok = $error_long_not_ok . "<br />";
 $error_duplicate_coords = '<span class="errormsg">' . t('Another cache (<a href="viewcache.php?wp=%1">%1</a>) exists at these coords. Maybe you pressed "submit cache" twice. To publish a cache with identical coords, enter other coords first, then edit the listing and change coords.') . '</span>';
 $time_not_ok_message = '<span class="errormsg">' . t('The entered time is invalid.') . '</span>';
 $way_length_not_ok_message = '<span class="errormsg">' . t('The entered distance is invalid, Format: aa.aaa') . '</span>';
 $date_not_ok_message = '<span class="errormsg">' . t('Invalid date, format:DD-MM-JJJJ') . '</span>';
 $name_not_ok_message = '&nbsp;<span class="errormsg">' . t('Cachename is invalid') . '</span>';
 $tos_not_ok_message = '<br/><span class="errormsg">' . t('The cache can only be adopted if you agree our terms of use.') . '</span>';
 $type_not_ok_message = '&nbsp;<span class="errormsg">' . t('No cache-type is chosen.') . '</span>';
 $size_not_ok_message = '&nbsp;<span class="errormsg">' . t('No cache-size is chosen.') . '</span>';
 $diff_not_ok_message = '&nbsp;<span class="errormsg">' . t('Choose both valuations!') . '</span>';
 $sizemismatch_message = '&nbsp;<span class="errormsg">' . t('For virtual and webcam caches, the cache size has to be -no container-!') . '</span>';
 $safari_not_allowed_message = '<span class="errormsg">' . t('Only virtual caches can be safari caches.') . '</span>';
 $bad_wpgc_message = '<span class="errormsg">' . t('GC waypoint is invalid, must be GCxxxxx') . '</span>';

 $cache_submitted = t('Your cache is successfully added to the database. You will be redirected to the cache page, now.');

 $sel_message = t('Select');

 $cache_attrib_js = "new Array({id}, {selected}, '{img_undef}', '{img_large}')";
 $cache_attrib_pic = '<img id="attr{attrib_id}" src="{attrib_pic}" border="0" onmousedown="toggleAttr({attrib_id})" onmouseover="Tip(\'{html_desc}\', TITLE, \'{name}\', TITLEBGCOLOR, \'{color}\', TITLEFONTCOLOR, \'#000000\', BGCOLOR, \'#FFFFFF\', BORDERCOLOR, \'{color}\', CLICKCLOSE, true, DELAY, 0, FADEIN, false, FADEOUT, false, FONTCOLOR, \'#000080\', WIDTH, 500)" onmouseout="UnTip()" />&nbsp;';

 $cache_attrib_group = 
	'<div class="attribgroup"><table cellspacing="0" style="display:inline;border-spacing:0px;">
	     <tr><td bgcolor="{color}" style="line-height:9px;padding-top:2px;margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-top:1px solid gray;"><font size="1">{name}</font></td></tr>
	     <tr><td bgcolor="#F8F8F8" style="margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-bottom:1px solid gray;">{attribs}</td></tr>
	   </table></div>';
?>