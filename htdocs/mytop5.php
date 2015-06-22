<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
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
		{
			// will happen if
			//   1. we delete a recommendation
			//   2. we view another recommended cache
			//   3. we return to mytop5 via browser "back" button
			// -> ignore
		}
		else
		{
			$rs = sql("SELECT `caches`.`wp_oc` AS `wp`, `caches`.`name` AS `cachename` FROM `caches` WHERE `caches`.`cache_id`='&1'", $cache_id);
			$deletedItem = sql_fetch_assoc($rs);
			$tpl->assign('deleted', true);
			$tpl->assign('deletedItem', $deletedItem);
			sql_free_result($rs);
			}
	}

	$rs = sql("SELECT `cache_rating`.`cache_id` AS `cacheid`, `cache_rating`.`rating_date`, `caches`.`wp_oc` AS `wp`, `caches`.`name` AS `cachename`, `caches`.`type` AS `type`, `caches`.`status` AS `status`, `ca`.`attrib_id` IS NOT NULL AS `oconly`
	             FROM `cache_rating`
				 INNER JOIN `caches` ON `cache_rating`.`cache_id`=`caches`.`cache_id`
	        LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6
	            WHERE `cache_rating`.`user_id`='&1'
	         ORDER BY `rating_date` DESC", $login->userid);
	$tpl->assign_rs('ratings', $rs);
	sql_free_result($rs);

	$tpl->display();
?>
