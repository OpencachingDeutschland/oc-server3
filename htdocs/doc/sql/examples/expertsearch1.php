<?php

// Unicode Reminder メモ

$opt['rootpath'] = '../../../';
require($opt['rootpath'] . 'lib2/web.inc.php');
sql_enable_debugger();

/*
	(1) Füge alle Einträge die diesem Filter entsprechen der Ergebnisliste hinzu

	Filtertyp: nach Entfernung
	Koordinaten: N 48° 30.000' E 9°30.000'
	Entfernung: 75 km
*/
//sql('CREATE TEMPORARY TABLE result_caches ENGINE=MEMORY SELECT (acos(cos(0.72431) * cos((90-`caches`.`latitude`) * 3.14159 / 180) + sin(0.72431) * sin((90-`caches`.`latitude`) * 3.14159 / 180) * cos((9.50000-`caches`.`longitude`) * 3.14159 / 180)) * 6370) `distance`, `caches`.`cache_id` `cache_id` FROM `caches` WHERE `longitude` > 8.48320014339 AND `longitude` < 10.5167998566 AND `latitude` > 47.8250539957 AND `latitude` < 49.1749460043 HAVING `distance` < 75');
sql('CREATE TEMPORARY TABLE result_caches ENGINE=MEMORY SELECT cache_id FROM caches');
sql('ALTER TABLE result_caches ADD PRIMARY KEY (cache_id)');

/*
	(2) Entferne alle Einträge die diesem Filter entsprechen von der Ergebnisliste

	Filtertyp: nach Finder
	User: Team A
*/
sql('CREATE TEMPORARY TABLE remove_caches ENGINE=MEMORY SELECT DISTINCT result_caches.cache_id cache_id FROM result_caches, cache_logs WHERE result_caches.cache_id=cache_logs.cache_id AND cache_logs.user_id=101254');
sql('ALTER TABLE remove_caches ADD PRIMARY KEY (cache_id)');
sql('DELETE FROM result_caches WHERE cache_id IN (SELECT cache_id FROM remove_caches)');
sql('DROP TABLE remove_caches');

/*
	(3) Entferne alle Einträge die diesem Filter entsprechen von der Ergebnisliste

	Filtertyp: nach Finder
	User: Team B
*/
sql('CREATE TEMPORARY TABLE remove_caches ENGINE=MEMORY SELECT DISTINCT result_caches.cache_id cache_id FROM result_caches, cache_logs WHERE result_caches.cache_id=cache_logs.cache_id AND cache_logs.user_id=101301');
sql('ALTER TABLE remove_caches ADD PRIMARY KEY (cache_id)');
sql('DELETE FROM result_caches WHERE cache_id IN (SELECT cache_id FROM remove_caches)');
sql('DROP TABLE remove_caches');

/*
	(4) Entferne alle Einträge die nicht diesem Filter entsprechen von der Ergebnisliste

	Filtertyp: nach status
	Status: Kann gesucht werden
*/
sql('CREATE TEMPORARY TABLE remove_caches ENGINE=MEMORY SELECT result_caches.cache_id cache_id FROM result_caches, caches WHERE result_caches.cache_id=caches.cache_id AND caches.status!=1');
sql('ALTER TABLE remove_caches ADD PRIMARY KEY (cache_id)');
sql('DELETE FROM result_caches WHERE cache_id IN (SELECT cache_id FROM remove_caches)');
sql('DROP TABLE remove_caches');

/*
	(5) Entferne alle Einträge die nicht diesem Filter entsprechen von der Ergebnisliste

	Filtertyp: nach zeitaufwand
	Zeitaufwand: kleiner 2h
*/
sql('CREATE TEMPORARY TABLE remove_caches ENGINE=MEMORY SELECT result_caches.cache_id cache_id FROM result_caches, caches WHERE result_caches.cache_id=caches.cache_id AND caches.search_time>2');
sql('ALTER TABLE remove_caches ADD PRIMARY KEY (cache_id)');
sql('DELETE FROM result_caches WHERE cache_id IN (SELECT cache_id FROM remove_caches)');
sql('DROP TABLE remove_caches');

$tpl->display();
