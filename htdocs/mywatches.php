<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/user.class.php');
	$tpl->name = 'mywatches';
	$tpl->menuitem = MNU_MYPROFILE_WATCHES;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect('login.php?target=' . urlencode($tpl->target));

	$action = isset($_REQUEST['action']) ? mb_strtolower($_REQUEST['action']) : '';

	if ($action == 'edit')
	{
		$tpl->menuitem = MNU_MYPROFILE_WATCHES_EDIT;

		$user = new user($login->userid);

		if (isset($_REQUEST['ok']))
		{
			$interval = isset($_REQUEST['interval']) ? $_REQUEST['interval']+0 : 1;
			$hour = isset($_REQUEST['hour']) ? $_REQUEST['hour']+0 : 0;
			$weekday = isset($_REQUEST['weekday']) ? $_REQUEST['weekday']+0 : 1;

			$bError = false;
			if (!$user->setWatchmailMode($interval))
				$bError = true;

			if (!$user->setWatchmailHour($hour))
				$bError = true;

			if (!$user->setWatchmailDay($weekday))
				$bError = true;

			if ($user->save())
				$tpl->assign('saved', true);
			else
				$tpl->assign('error', true);
		}

		$hours = array(); for ($i = 0; $i < 24; $i++) $hours[] = array('value' => $i, 'time' => mktime($i, 0 , 0));
		$weekdays = array(); for ($i = 1; $i <= 7; $i++) $weekdays[] = array('value' => $i, 'time' => mktime(0, 0, 0, 0, $i+5, 2000));

		$tpl->assign('hours', $hours);
		$tpl->assign('weekdays', $weekdays);

		$tpl->assign('interval', $user->getWatchmailMode());
		$tpl->assign('hour', $user->getWatchmailHour());
		$tpl->assign('weekday', $user->getWatchmailDay());
	}
	elseif ($action == 'add')
	{
		$target = isset($_REQUEST['target']) ? $_REQUEST['target'] : '';
		$cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid']+0 : 0;
		if (sql_value("SELECT COUNT(*) FROM `caches` WHERE `cache_id`='&1'", 0, $cacheid) > 0)
			sql("INSERT IGNORE INTO `cache_watches` (`cache_id`, `user_id`) VALUES ('&1', '&2')", $cacheid, $login->userid);

		$tpl->redirect($tpl->checkTarget($target, 'mywatches.php'));
	}
	elseif ($action == 'remove')
	{
		$target = isset($_REQUEST['target']) ? $_REQUEST['target'] : '';
		$cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid']+0 : 0;
		sql("DELETE FROM `cache_watches` WHERE `cache_id`='&1' AND `user_id`='&2'", $cacheid, $login->userid);

		$tpl->redirect($tpl->checkTarget($target, 'mywatches.php'));
	}
	else
	{
		$rs = sql("SELECT `cache_watches`.`cache_id` AS `cacheid`, `caches`.`wp_oc` AS `wp`, `caches`.`name` AS `name`, `stat_caches`.`last_found` AS `lastfound`, `caches`.`type` AS `type`, `caches`.`status` AS `status` FROM `cache_watches` INNER JOIN `caches` ON `cache_watches`.`cache_id`=`caches`.`cache_id` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id` WHERE (`cache_status`.`allow_user_view`=1 OR `caches`.`user_id`='&1') AND `cache_watches`.`user_id`='&1' ORDER BY `caches`.`name`", $login->userid);
		$tpl->assign_rs('watches', $rs);
		sql_free_result($rs);
	}

	$tpl->assign('action', $action);
	$tpl->display();
?>
