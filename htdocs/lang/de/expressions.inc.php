<?php
/****************************************************************************
												./lang/<speach>/expressions.inc.php
																-------------------
			begin                : Mon June 14 2004

		For license information see doc/license.txt
 ****************************************************************************/

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