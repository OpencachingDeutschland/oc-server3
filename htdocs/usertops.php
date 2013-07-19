<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'usertops';
	$tpl->menuitem = MNU_CACHES_USERTOPS;

	$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid']+0 : 0;

	$sUsername = sql_value("SELECT `username` FROM `user` WHERE `user_id`='&1'", null, $userid);
	if ($sUsername == null)
		$tpl->error(ERROR_USER_NOT_EXISTS);

	$tpl->assign('userid', $userid);
	$tpl->assign('username', $sUsername);

	$rs = sql("SELECT `cache_rating`.`cache_id` AS `cacheid`, `caches`.`name` AS `cachename`, `user`.`username` AS `ownername`, `caches`.`type` AS `type`, `caches`.`status` AS `status`, `ca`.`attrib_id` IS NOT NULL AS `oconly`
	             FROM `cache_rating` 
	       INNER JOIN `caches` ON `cache_rating`.`cache_id` = `caches`.`cache_id`
	       INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id`
	       INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id`
	        LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6
	            WHERE `cache_status`.`allow_user_view`=1
	              AND `cache_rating`.`user_id`='&1' 
	         ORDER BY `caches`.`name` ASC", $userid);
	$tpl->assign_rs('ratings', $rs);
	sql_free_result($rs);

	$tpl->display();
?>
