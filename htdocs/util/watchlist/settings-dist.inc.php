<?php
	// Unicode Reminder メモ

	if (!isset($maildomain)) $maildomain = 'opencaching.de';

	$mailfrom = 'noreply@' . $maildomain;
	$mailsubject = '[' . $maildomain . '] Deine Watchlist vom ' . date('d.m.Y');

	$debug = false;
	$debug_mailto = 'abc@xyz.de';
	
	$nologs = 'Keine neuen Logeinträge';
	
	$logowner_text = '{date} {user} hat einen Logeintrag für deinen Cache "{cachename}" gemacht: {action}' . "\n" . 'http://opencaching.de/{wp_oc}' . "\n\n" . '{text}' . "\n\n\n\n";
	$logwatch_text = '{date} {user} hat einen Logeintrag für den Cache "{cachename}" gemacht: {action}' . "\n" . 'http://opencaching.de/{wp_oc}' . "\n\n" . '{text}' . "\n\n\n\n";

	$watchpid = $rootpath . 'cache/watch.pid';
?>
