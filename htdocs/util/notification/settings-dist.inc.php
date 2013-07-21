<?php
/***************************************************************************
 *	For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	if (!isset($maildomain)) $maildomain = 'opencaching.de';

	$mailfrom = 'noreply@' . $maildomain;
	$new_cache_subject = '[' . $maildomain . '] Neuer {oconly}Cache: {cachename}';
	$new_oconly_subject = '[' . $maildomain . '] Cache wurde als OConly markiert: {cachename}';

	$debug = false;
	$debug_mailto = 'abc@xyz.de';

	$nologs = 'Keine neuen Logeinträge';

	$notifypid = $rootpath . 'cache/notify.pid';
?>
