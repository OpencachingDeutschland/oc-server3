<?php
	/***************************************************************************
												./lang/<speach>/expressions.inc.php
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

		language specific expressions

	****************************************************************************/

	global $locale, $opt;

	//only debugging
 	$runtime = t('Runtime: {time} seconds');

	// set Date/Time language
	setlocale(LC_TIME, 'de_DE.utf8');

	//common vars
	$dateformat = $opt['locale'][$locale]['format']['date'];
	$reset = t('Reset');
	$yes = t('Yes');
	$no = t('No');

	//common errors
	$error_pagenotexist = t('The called page does not exist!');
?>