<?php
	// Unicode Reminder メモ

	$mailfrom = 'noreply@opencaching.de';
	$mailsubject = '[opencaching.de] Deine Watchlist vom ' . date('d.m.Y');

	$debug = false;
	$debug_mailto = 'abc@xyz.de';
	
	$nologs = 'Keine neuen Logeinträge';
	
	$logowner_text = '{date} {user} hat einen Logeintrag für deinen Cache "{cachename}" gemacht.' . "\n" . 'http://www.opencaching.de/viewcache.php?cacheid={cacheid}' . "\n\n" . '{text}' . "\n\n\n\n";
	$logwatch_text = '{date} {user} hat einen Logeintrag für den Cache "{cachename}" gemacht.' . "\n" . 'http://www.opencaching.de/viewcache.php?cacheid={cacheid}' . "\n\n" . '{text}' . "\n\n\n\n";

	$watchpid = '/var/www/www.opencaching.de/code/htdocs/cache/watch.pid';
?>
