<?php
/****************************************************************************
											./lang/de/ocstyle/editdesc.inc.php
															-------------------
		begin                : July 7 2004

		For license information see doc/license.txt
 ****************************************************************************/

/****************************************************************************
	    
   Unicode Reminder メモ
                                     				                                
	 language vars
	
 ****************************************************************************/

 	$error_wrong_node = t('This description has been created on another Opencaching website. The cache can only be edited there.');

	$show_all_langs_submit = '&nbsp;<input type="submit" name="show_all_langs" value="' . t('Show all') . '" class="formbutton" onclick="submitbutton(\'show_all_langs\')" />';

	$error_desc_not_found = t('(internal error) The description is not available.');
	$error_desc_exists = t('(internal error) It already exists a description for this language.');

	$nopictures = t('No pictures available');
	$pictureline = '<td><a href="javascript:SelectFile(\'{link}\');">{title}<br /><img src="{link}" width="180" /></a></td>';
	$picturelines = '{lines}';

	$submit = t('Save');
?>
