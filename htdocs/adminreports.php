<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/cache.class.php');

	$tpl->name = 'adminreports';
	$tpl->menuitem = MNU_ADMIN_REPORTS;

	$error = 0;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect_login();

	if (($login->admin & ADMIN_USER) != ADMIN_USER)
		$tpl->error(ERROR_NO_ACCESS);

	$id = isset($_REQUEST['id']) ? $_REQUEST['id']+0 : 0;

	$rid = isset($_REQUEST['rid']) ? $_REQUEST['rid']+0 : 0;
	$cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid']+0 : 0;
	$ownerid = isset($_REQUEST['ownerid']) ? $_REQUEST['ownerid']+0 : 0;
	$reporterid =sql_value("SELECT `userid` FROM `cache_reports` WHERE `id`=&1", 0, $rid); 
	$adminid = sql_value("SELECT `adminid` FROM `cache_reports` WHERE `id`=&1", 0, $rid);
	$age = sql_value("SELECT DATEDIFF(NOW(),`lastmodified`) FROM `cache_reports` WHERE `id`=&1", 0, $rid);

	if (isset($_REQUEST['assign']) && $rid > 0 && 
			($adminid == 0 || ($adminid != $login->userid && $age >= 14)))  
	{
		sql("UPDATE `cache_reports` SET `status`=2, `adminid`=&2 WHERE `id`=&1", $rid, $login->userid);
		$tpl->redirect('adminreports.php?id='.$rid);
	}
	elseif (isset($_REQUEST['contact']) && $ownerid > 0)
	{
		$tpl->redirect('mailto.php?userid=' . urlencode($ownerid));
	}
	elseif (isset($_REQUEST['contact_reporter']) && $reporterid > 0)
	{
		$tpl->redirect('mailto.php?userid=' . urlencode($reporterid));
	}
	elseif (isset($_REQUEST['done']) && $adminid == $login->userid)
	{
		sql("UPDATE `cache_reports` SET `status`=3 WHERE `id`=&1", $rid);
		$tpl->redirect('adminreports.php?id='.$rid);
	}
	elseif (isset($_REQUEST['assign']) && ($adminid == 0 || $adminid != $login->userid))
	{
		$error = 1;
		if ($rid > 0)
		{
			$id = $rid;
		}
		else
		{
			$id = 0;
		}
	}
	elseif (isset($_REQUEST['assign']) && $adminid == $login->userid)
	{
		$error = 2;
		$id = $rid;
	}
	elseif (isset($_REQUEST['statusActive']) ||
		isset($_REQUEST['statusTNA']) ||
		isset($_REQUEST['statusArchived']) ||
		isset($_REQUEST['done'])    ||
		isset($_REQUEST['statusLockedVisible'])    ||
		isset($_REQUEST['statusLockedInvisible']))
	{
		if ($adminid == 0)
		{
			$id = $rid;
			$error = 4;
		}
		elseif ($adminid != $login->userid)
		{
			$id = $rid;
			$error = 3;
		}
	}

	if ($id == 0)
	{
		// no details, show list of reported caches
		$rs = sql("SELECT `cr`.`id`,
				               IF(`cr`.`status`=1,'(*) ', '') AS `new`,
				               `c`.`name`,
				               `u2`.`username` AS `ownernick`,
				               `u`.`username`,
				               IF(LENGTH(`u3`.`username`)>10, CONCAT(LEFT(`u3`.`username`,9),'.'),`u3`.`username`) AS `adminname`,
				               `cr`.`lastmodified`,
				               `cr`.`adminid` IS NOT NULL AND `cr`.`adminid`!=&1 AS otheradmin 
				          FROM `cache_reports` `cr`
				    INNER JOIN `caches` `c` ON `c`.`cache_id` = `cr`.`cacheid`
				    INNER JOIN `user` `u` ON `u`.`user_id`  = `cr`.`userid`
				    INNER JOIN `user` AS `u2` ON `u2`.`user_id`=`c`.`user_id`
				     LEFT JOIN `user` AS `u3` ON `u3`.`user_id`=`cr`.`adminid`
				         WHERE `cr`.`status` < 3 " .
				         //  AND (`cr`.`adminid` IS NULL OR `cr`.`adminid`=&1)
		         "ORDER BY (`cr`.`adminid` IS NULL OR `cr`.`adminid`=&1) DESC,
						            `cr`.`status` ASC, 
												`cr`.`lastmodified` ASC",
			    $login->userid);

		$tpl->assign_rs('reportedcaches', $rs);
		sql_free_result($rs);
		$tpl->assign('list', true);
	}
	else
	{
		// show details of a report
		$rs = sql("SELECT `cr`.`id`, `cr`.`cacheid`, `cr`.`userid`,
				              `u1`.`username` AS `usernick`,
				              IFNULL(`cr`.`adminid`, 0) AS `adminid`,
				              IFNULL(`u2`.`username`, '') AS `adminnick`,
				              IFNULL(`tt2`.`text`, `crr`.`name`) AS `reason`,
				              `cr`.`note`,
				              IFNULL(tt.text, crs.name) AS `status`,
				              `cr`.`date_created`, `cr`.`lastmodified`,
				              `c`.`name` AS `cachename`,
				              `c`.`user_id` AS `ownerid`
				         FROM `cache_reports` AS `cr`
				    LEFT JOIN `cache_report_reasons` AS `crr` ON `cr`.`reason`=`crr`.`id`
			      LEFT JOIN `caches` AS `c` ON `c`.`cache_id`=`cr`.`cacheid`
			      LEFT JOIN `user` AS `u1` ON `u1`.`user_id`=`cr`.`userid`
			      LEFT JOIN `user` AS `u2` ON `u2`.`user_id`=`cr`.`adminid`
			      LEFT JOIN `cache_report_status` AS `crs` ON `cr`.`status`=`crs`.`id`
			      LEFT JOIN `sys_trans_text` AS `tt` ON `crs`.`trans_id`=`tt`.`trans_id` AND `tt`.`lang`='&2'
			      LEFT JOIN `sys_trans_text` AS `tt2` ON `crr`.`trans_id`=`tt2`.`trans_id` AND `tt2`.`lang`='&2'
			          WHERE `cr`.`id`=&1", 
			            $id, $opt['template']['locale']);

		if ($record = sql_fetch_assoc($rs))
		{
			$tpl->assign('id', $record['id']);
			$tpl->assign('cacheid', $record['cacheid']);
			$tpl->assign('userid', $record['userid']);
			$tpl->assign('usernick', $record['usernick']);
			$tpl->assign('adminid', $record['adminid']);
			$tpl->assign('adminnick', $record['adminnick']);
			$tpl->assign('reason', $record['reason']);
			$tpl->assign('note', trim($record['note']));
			$tpl->assign('status', $record['status']);
			$tpl->assign('created', $record['date_created']);
			$tpl->assign('lastmodified', $record['lastmodified']);
			$tpl->assign('cachename', $record['cachename']);
			$tpl->assign('ownerid', $record['ownerid']);
			if (isset($opt['logic']['adminreports']['cachexternal']))
				$tpl->assign('cachexternal', $opt['logic']['adminreports']['cachexternal']);
			else
				$tpl->assign('cachexternal', array());

			if (isset($opt['logic']['adminreports']['external_maintainer']))
			{
				$external_maintainer = @file_get_contents(mb_ereg_replace('%1', $record['cacheid'], $opt['logic']['adminreports']['external_maintainer']['url']));
				if ($external_maintainer)
					$tpl->assign('external_maintainer_msg', mb_ereg_replace('%1', htmlspecialchars($external_maintainer),
					             $opt['logic']['adminreports']['external_maintainer']['msg']));
				else
					$tpl->assign('external_maintainer_msg', false);
			}
		}
		sql_free_result($rs);

		$tpl->assign('list', false);
		$tpl->assign('otheradmin',$record['adminid']>0 && $record['adminid'] != $login->userid);
		$tpl->assign('ownreport',$record['adminid'] == $login->userid);

		$cache = new cache($record['cacheid']);
		$cache->setTplHistoryData($id);
	}

	$tpl->assign('error', $error);	
	$tpl->display();
?>
