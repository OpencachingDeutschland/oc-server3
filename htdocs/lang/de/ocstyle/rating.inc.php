<?php
/***************************************************************************
		./lang/de/ocstyle/rating.inc.php
		-------------------
		begin                : July 4 2004
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

	 template replacements:

 ****************************************************************************/

	$rating_tpl =	'<tr>
				<td valign="top">' . t('Recommendations:') . '</td>
				<td valign="top">
					{rating_msg}
					<noscript><br />' . t('A recommendation can only be made within a "found"-log!') . '</noscript>
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>';

	$rating_allowed = '<input type="checkbox" name="rating" value="1" class="checkbox" {chk_sel}/>&nbsp;' . t('This cache is one of my recommendations.');
	$rating_maxreached = t('Alternatively, you can withdraw a <a href="mytop5.php">existing recommendation</a>.');
	$rating_too_few_founds = t('You need additional {anzahl} finds, to make another recommandation.');
	$rating_stat = t('You have given {curr} of {max} possible recommendations.');

?>