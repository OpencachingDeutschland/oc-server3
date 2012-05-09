<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'mytop5';
	$tpl->menuitem = MNU_MYPROFILE_RECOMMENDATIONS;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect('login.php?target=mytop5.php');

	$action = isset($_REQUEST['action']) ? mb_strtolower($_REQUEST['action']) : '';

	if ($action == 'delete')
	{
		$cache_id = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid']+0 : 0;
		sql("DELETE FROM `cache_rating` WHERE `cache_id`='&1' AND `user_id`='&2'", $cache_id, $login->userid);
		if (sql_affected_rows() == 0)
			$tpl->error(ERROR_UNKNOWN);

		$rs = sql("SELECT `caches`.`wp_oc` AS `wp`, `caches`.`name` AS `cachename` FROM `caches` WHERE `caches`.`cache_id`='&1'", $cache_id);
		$deletedItem = sql_fetch_assoc($rs);
		$tpl->assign('deleted', true);
		$tpl->assign('deletedItem', $deletedItem);
		sql_free_result($rs);
	}

	$rs = sql("SELECT `cache_rating`.`cache_id` AS `cacheid`, `caches`.`wp_oc` AS `wp`, `caches`.`name` AS `cachename`, `caches`.`type` AS `type`, `caches`.`status` AS `status`
	             FROM `cache_rating`, `caches`
	            WHERE `cache_rating`.`cache_id`=`caches`.`cache_id`
	              AND `cache_rating`.`user_id`='&1'
	         ORDER BY `caches`.`name` ASC", $login->userid);
	$tpl->assign_rs('ratings', $rs);
	sql_free_result($rs);

	$tpl->display();
?>
