<?php
 /***************************************************************************
													./util/deletecache/cache.php
															-------------------
		begin                : November 17 2005

		For license information see doc/license.txt
 ****************************************************************************/

 /***************************************************************************

		Unicode Reminder メモ

		Script zum vollständigen entfernen von Caches.
		Schutz über htpasswd!

	***************************************************************************/

	$rootpath = '../../';
	header('Content-type: text/html; charset=utf-8');
	require($rootpath . 'lib/common.inc.php');
	require($rootpath . 'lib/eventhandler.inc.php');

	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

	if ($action == 'delete')
	{
		$cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : 0;
		$commit = isset($_REQUEST['commit']) ? $_REQUEST['commit'] : 0;

		if ($commit != 1) die('Kein Commit!');

		$rs = sql("SELECT COUNT(*) `c` FROM `caches` WHERE `cache_id`='&1'", $cacheid);
		$r = sql_fetch_assoc($rs);
		if ($r['c'] == 0) die('Cache nicht vorhanden');
		mysql_free_result($rs);

		// Bilder
		$rs = sql("SELECT * FROM `pictures` WHERE `object_id`='&1' AND `object_type`=2", $cacheid);
		while ($r = sql_fetch_assoc($rs))
		{
			$logbackup['pictures'][] = $r;

			// Bild löschen und in removed_objects
			sql("DELETE FROM `pictures` WHERE `id`='&1' LIMIT 1", $r['id']);
		}

		// Logeinträge
		$rs = sql("SELECT * FROM `cache_logs` WHERE `cache_id`='&1'", $cacheid);
		while ($r = sql_fetch_assoc($rs))
		{
			// Bilder - Logeinträge
			$rsLogPics = sql("SELECT * FROM `pictures` WHERE `object_id`='&1' AND object_type=1", $r['id']);
			while ($rLogPics = sql_fetch_assoc($rsLogPics))
			{
				$r['pictures'][] = $rLogPics;

				// Bild löschen und in removed_objects
				sql("DELETE FROM `pictures` WHERE `id`='&1' LIMIT 1", $rLogPics['id']);
			}

			$logbackup['logs'][] = $r;

			sql("DELETE FROM `cache_logs` WHERE `id`='&1' LIMIT 1", $r['id']);
		}

		// Aufruf-Records
		$rs = sql("SELECT * FROM `cache_visits` WHERE `cache_id`='&1'", $cacheid);
		while ($r = sql_fetch_assoc($rs))
		{
			$logbackup['visits'][] = $r;
		}
		sql("DELETE FROM `cache_visits` WHERE `cache_id`='&1'", $cacheid);

		// Beschreibungen
		$rs = sql("SELECT * FROM `cache_desc` WHERE `cache_id`='&1'", $cacheid);
		while ($r = sql_fetch_assoc($rs))
		{
			$logbackup['desc'][] = $r;

			sql("DELETE FROM `cache_desc` WHERE `id`='&1' LIMIT 1", $r['id']);
		}

		// Cache
		$rs = sql("SELECT * FROM `caches` WHERE `cache_id`='&1' LIMIT 1", $cacheid);
		while ($r = sql_fetch_assoc($rs))
		{
			$logbackup['caches'][] = $r;

			sql("DELETE FROM `caches` WHERE `cache_id`='&1' LIMIT 1", $r['cache_id']);

			// send event to delete statpic
			event_change_statpic($r['user_id']);
		}

		echo 'Cache gelöscht';

		// logentry($module, $eventid, $userid, $objectid1, $objectid2, $logtext, $details)
		logentry('approving', 4, 0, $cacheid, 0, 'Totaly removed Cache ' . $cacheid, $logbackup);

		exit;
	}
	else if ($action == 'showcache')
	{
		$cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid'] : 0;

		$rs = sql("SELECT `caches`.`cache_id` `cacheid`, `caches`.`name` `name`, `cache_status`.`de` `status`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude` FROM `caches`, `cache_status` WHERE `caches`.`status`=`cache_status`.`id` AND `caches`.`cache_id`='&1' LIMIT 1", $cacheid);
		if (mysql_num_rows($rs) != 0)
		{
			$r = sql_fetch_assoc($rs);
			mysql_free_result($rs);
?>
<html>
	<body>
		<form action="cache.php" method="get">

		</form>
		<table>
<?php
			echo '<tr><td>Name:</td><td><a href="../../viewcache.php?cacheid=' . urlencode($r['cacheid']) . '">' . htmlspecialchars($r['name'], ENT_COMPAT, 'UTF-8') . '</a></td></tr>';
			echo '<tr><td>Status:</td><td>' . htmlspecialchars($r['status'], ENT_COMPAT, 'UTF-8') . '</td></tr>';

			$rsDescs = sql("SELECT COUNT(*) `count` FROM `cache_desc` WHERE `cache_id`='&1'", $r['cacheid']);
			$rDescs = sql_fetch_array($rsDescs);
			mysql_free_result($rsDescs);
			echo '<tr><td>Beschreibungen:</td><td>' . htmlspecialchars($rDescs['count'], ENT_COMPAT, 'UTF-8') . '</td></tr>';

			$rsVisits = sql("SELECT COUNT(*) `count` FROM `cache_visits` WHERE `cache_id`='&1'", $r['cacheid']);
			$rVisits = sql_fetch_array($rsVisits);
			mysql_free_result($rsVisits);
			echo '<tr><td>Aufruf-Records:</td><td>' . htmlspecialchars($rVisits['count'], ENT_COMPAT, 'UTF-8') . '</td></tr>';

			$rsLogs = sql("SELECT COUNT(*) `count` FROM `cache_logs` WHERE `cache_id`='&1'", $r['cacheid']);
			$rLogs = sql_fetch_array($rsLogs);
			mysql_free_result($rsLogs);
			echo '<tr><td>Logeinträge:</td><td>' . htmlspecialchars($rLogs['count'], ENT_COMPAT, 'UTF-8') . '</td></tr>';

			$rsPictures = sql("SELECT COUNT(*) `count` FROM `pictures` WHERE `object_id`='&1' AND object_type=2", $r['cacheid']);
			$rPictures = sql_fetch_array($rsPictures);
			mysql_free_result($rsPictures);
			echo '<tr><td>Bilder:</td><td>' . htmlspecialchars($rPictures['count'], ENT_COMPAT, 'UTF-8') . '</td></tr>';

			echo '<tr>
							<td>&nbsp;</td>
							<td>
								<form action="cache.php" method="get">
									<input type="hidden" name="action" value="delete" />
									<input type="hidden" name="cacheid" value="' . $r['cacheid'] . '" />
									<input type="checkbox" id="commit" name="commit" value="1" /><label for="commit">wirklich?</label><br />
									<input type="submit" value="Löschen" />
								</form>
							</td>
						</tr>';
			echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
			echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';

			// nach alternativem cache suchen
			$rsSecond = sql("SELECT `caches`.`cache_id` `cacheid`, `caches`.`name` `name`, `cache_status`.`de` `status` FROM `caches`, `cache_status` WHERE `caches`.`status`=`cache_status`.`id` AND `caches`.`longitude`='&1' AND `caches`.`latitude`='&2' AND `caches`.`cache_id` != '&3'", $r['longitude'], $r['latitude'], $r['cacheid']);
			while ($rSecond = sql_fetch_assoc($rsSecond))
			{
				echo '<tr><td>Doppellisting:</td><td><a href="../../viewcache.php?cacheid=' . urlencode($rSecond['cacheid']) . '">' . htmlspecialchars($rSecond['name'], ENT_COMPAT, 'UTF-8') . '</a></td><tr>';
				echo '<tr><td>Status:</td><td>' . $rSecond['status'] . '</td><tr>';
			}
			mysql_free_result($rsSecond);
?>
		</table>
	</body>
</html>
<?php
			exit;
		}
	}
?>
<html>
	<body>
		<form action="cache.php" method="get">
			<input type="hidden" name="action" value="showcache" />
			Cacheid <input type="text" name="cacheid" size="8" />
			<input type="submit" value="Auswählen" />
		</form>
	</body>
</html>
