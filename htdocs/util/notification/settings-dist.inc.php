<?php
/***************************************************************************
 *	For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	if (!isset($maildomain)) $maildomain = 'opencaching.de';

	$mailfrom = 'noreply@' . $maildomain;
	$mailsubject = '[' . $maildomain . '] Neuer Cache: {cachename}';

	$debug = false;
	$debug_mailto = 'abc@xyz.de';

	$nologs = 'Keine neuen Logeinträge';

	$notifypid = $rootpath . 'cache/notify.pid';
?>
