<?php
/***************************************************************************
											./lang/de/ocstyle/editdesc.inc.php
															-------------------
		begin                : July 7 2004
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
                                     				                                
	 language vars
	
 ****************************************************************************/

 	$error_wrong_node = t('This description has been created on another Opencaching website. The cache can only be edited there.');

	$desc_not_ok_message = '<br /><br /><p style="margin-top:0px;margin-left:0px;width:550px;background-color:#e5e5e5;border:1px solid black;text-align:left;padding:3px 8px 3px 8px;"><span class="errormsg">' . t('This HTML-Code is invalid.') . '</span><br />%text%</p><br />';
	$show_all_langs_submit = '&nbsp;<input type="submit" name="show_all_langs" value="' . t('Show all') . '" />';

	$error_desc_not_found = t('(internal error) The description is not available.');
	$error_desc_exists = t('(internal error) It already exists a description for this language.');

	$nopictures = t('No pictures available');
	$pictureline = '<td><a href="javascript:SelectFile(\'{link}\');">{title}<br/><img src="{link}" width="180"></a></td>';
	$picturelines = '{lines}';

	$submit = t('Change');
?>