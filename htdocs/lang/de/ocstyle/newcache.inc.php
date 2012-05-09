<?php
/***************************************************************************
												  ./lang/de/ocstyle/newcache.inc.php
															-------------------
		begin                : Mon June 14 2004
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

 $submit = t('Submit cache');
 $default_country = t('EN');
 $default_lang = t('EN');
 $show_all = t('Show all');
 $default_NS = 'N';
 $default_EW = 'E';
 $date_time_format_message = '&nbsp;' . t('Format:&nbsp;DD-MM-YYYY');

 $error_general = "<tr><td class='error' colspan='2'><b>" . t('Some errors occured, please check the marked fields.') . "</b></td></tr>";
 $error_coords_not_ok = '<span class="errormsg">' . t('Your chosen coordinated are invalid') . '</span>';
 $time_not_ok_message = '<span class="errormsg">' . t('The entered time is invalid.') . '</span>';
 $way_length_not_ok_message = '<span class="errormsg">' . t('The entered distance is invalid, Format: aa.aaa') . '</span>';
 $date_not_ok_message = '<span class="errormsg">' . t('Invalid date, format:DD-MM-JJJJ') . '</span>';
 $name_not_ok_message = '&nbsp;<span class="errormsg">' . t('Cachename is invalid') . '</span>';
 $tos_not_ok_message = '<br/><span class="errormsg">' . t('The cache can only be adopted if you agree our terms of use.') . '</span>';
 $desc_not_ok_message = '<br/><span class="errormsg">' . t('This HTML-code is invalid. To find out the exact cause of the error, you should use the <a href="htmlprev.php" target="_blank">HTML preview</a>.') . '</span>';
 $type_not_ok_message = '&nbsp;<span class="errormsg">' . t('No cache-type is chosen.') . '</span>';
 $size_not_ok_message = '&nbsp;<span class="errormsg">' . t('No cache-size is chosen.') . '</span>';
 $diff_not_ok_message = '&nbsp;<span class="errormsg">' . t('Choose both valuations!') . '</span>';
 $sizemismatch_message = '&nbsp;<span class="errormsg">' . t('For virtual and webcam caches, the cache size has to be -no container-!') . '</span>';

 $html_desc_errbox = '<br /><br /><p style="margin-top:0px;margin-left:0px;width:550px;background-color:#e5e5e5;border:1px solid black;text-align:left;padding:3px 8px 3px 8px;"><span class="errormsg">' . t('This HTML-Code is invalid.') . '</span><br />%text%</p><br />';

 $cache_submitted = t('Your cache is successfully added to the database. You will be redirected to the cache page, now.');

 $sel_message = t('Select');

 $cache_attrib_js = "new Array({id}, {selected}, '{img_undef}', '{img_large}')";
 $cache_attrib_pic = '<img id="attr{attrib_id}" src="{attrib_pic}" border="0" onmousedown="toggleAttr({attrib_id})" onmouseover="Tip(\'{html_desc}\', TITLE, \'{name}\', TITLEBGCOLOR, \'{color}\', TITLEFONTCOLOR, \'#000000\', BGCOLOR, \'#FFFFFF\', BORDERCOLOR, \'{color}\', CLICKCLOSE, true, DELAY, 0, FADEIN, false, FADEOUT, false, FONTCOLOR, \'#000080\', WIDTH, 500)" onmouseout="UnTip()" />&nbsp;';

 $cache_attrib_group = 
	'<table cellspacing="0" style="display:inline;border-spacing:0px;">
	     <tr><td bgcolor="{color}" style="line-height:9px;padding-top:2px;margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-top:1px solid gray;"><font size="1">{name}</font></td></tr>
	     <tr><td bgcolor="#F8F8F8" style="margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-bottom:1px solid gray;">{attribs}</td></tr>
	   </table>&nbsp;';
?>