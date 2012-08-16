<?php
 /***************************************************************************
													./util/deletecache/cache.php
															-------------------
		begin                : June 28 2006

		For license information see doc/license.txt
 ****************************************************************************/

 /***************************************************************************
		
		Unicode Reminder メモ

		Script zum vollständigen entfernen von Benutzern.
		Schutz über htpasswd!
		
	***************************************************************************/
	
	$rootpath = '../../';
	header('Content-type: text/html; charset=utf-8');
	require($rootpath . 'lib/common.inc.php');
	
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	
	if ($action == 'delete')
	{
		$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : 0;
		$commit = isset($_REQUEST['commit']) ? $_REQUEST['commit'] : 0;
		
		if ($commit != 1) die('Kein Commit!');
		
		$rsUser = sql("SELECT `user_id` FROM `user` WHERE `username`='&1'", $username);
		if (mysql_num_rows($rsUser) != 1) die(mysql_num_rows($rsUser) . ' Benutzer gefunden');
		$rUser = sql_fetch_array($rsUser);
		$userid = $rUser['user_id'];
		sql_free_result($rsUser);

		if (sqlValue("SELECT COUNT(*) FROM `caches` WHERE `user_id`='" . sql_escape($userid) . "'", 0) > 0)
			die('Es sind noch Caches vorhanden! <a href="../../search.php?searchto=searchbyowner&showresult=1&expert=0&output=HTML&sort=bydistance&f_userowner=0&f_userfound=0&f_inactive=0&f_ignored=0&owner=' . urlencode($username) . '">Suchen</a>');

		// pictures
		$rs = sql("SELECT `pictures`.`id`, `pictures`.`url` FROM `pictures` INNER JOIN `cache_logs` ON `pictures`.`object_type`=1 AND `pictures`.`object_id`=`cache_logs`.`id` WHERE `cache_logs`.`user_id`='&1'", $userid);
		while ($r = sql_fetch_assoc($rs))
		{
			$filename = $r['url'];
			while (mb_strpos($filename, '/') !== false)
				$filename = mb_substr($filename, mb_strpos($filename, '/') + 1);

			if (is_file($picdir . '/' . $filename))
			{
				unlink($picdir . '/' . $filename);
				echo $filename . "<br>";
			}

			sql("DELETE FROM `pictures` WHERE `id`='&1'", $r['id']);
		}

		// statpic
		if (is_file($rootpath . 'images/statpics/statpic' . $userid . '.jpg'))
			unlink($rootpath . 'images/statpics/statpic' . $userid . '.jpg');

		// queries
		sql("DELETE FROM `queries` WHERE `user_id`=&1", $userid);
		
		// watches_notified
		sql("DELETE FROM `watches_notified` WHERE `user_id`=&1", $userid);

		// cache_logs
		$rs = sql("SELECT `id`, `cache_id`, `type` FROM `cache_logs` WHERE `user_id`=&1", $userid);
		while ($r = sql_fetch_assoc($rs))
		{
			sql("DELETE FROM `cache_logs` WHERE `id`=&1", $r['id']);
		}
		
		// user
		sql("DELETE FROM `user` WHERE `user_id`=&1", $userid);

		echo 'Benutzer gelöscht';

		exit;
	}
	else if ($action == 'showuser')
	{
		$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
		
		$rs = sql("SELECT `user`.`user_id`, `user`.`username`, `user`.`email`, `user`.`activation_code`, `user`.`is_active_flag`, `stat_user`.`hidden`, `stat_user`.`found`, `stat_user`.`note`, `stat_user`.`notfound` FROM `user` LEFT JOIN `stat_user` ON `user`.`user_id`=`stat_user`.`user_id` WHERE (`user`.`username`='&1' OR `user`.`email`='&1') LIMIT 1", $username);
		if (mysql_num_rows($rs) != 0)
		{
			$r = sql_fetch_assoc($rs);
			sql_free_result($rs);
?>
<html>
	<body>
		<form action="cache.php" method="get">
			
		</form>
		<table>
<?php
			echo '<tr><td>Name:</td><td><a href="../../viewprofile.php?userid=' . urlencode($r['user_id']) . '">' . htmlspecialchars($r['username'], ENT_COMPAT, 'UTF-8') . '</a></td></tr>';
			echo '<tr><td>EMail:</td><td>' . $r['email'] . '</td></tr>';
			echo '<tr><td>Aktivierungscode:</td><td>' . $r['activation_code'] . '</td></tr>';
			echo '<tr><td>Letzter Login:</td><td>' . sqlValue("SELECT MAX(`last_login`) FROM `sys_sessions` WHERE `user_id`='" . sql_escape($r['user_id']) . "'", '0') . '</td></tr>';
			echo '<tr><td>Aktiv:</td><td>' . $r['is_active_flag'] . '</td></tr>';
			echo '<tr><td>Versteckt:</td><td>' . ($r['hidden']+0) . '</td></tr>';
			echo '<tr><td>Logeinträge:</td><td>' . ($r['found'] + $r['note'] + $r['notfound']) . '</td></tr>';

			echo '<tr>
							<td>&nbsp;</td>
							<td>
								<form action="user.php" method="get">
									<input type="hidden" name="action" value="delete" />
									<input type="hidden" name="username" value="' . $r['username'] . '" />
									<input type="checkbox" id="commit" name="commit" value="1" /><label for="commit">wirklich?</label><br />
									<input type="submit" value="Löschen" />
								</form>
							</td>
						</tr>';
			echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
			echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
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
		<form action="user.php" method="get">
			<input type="hidden" name="action" value="showuser" />
			Benutzername <input type="text" name="username" size="20" />
			<input type="submit" value="Auswählen" />
		</form>
	</body>
</html>
