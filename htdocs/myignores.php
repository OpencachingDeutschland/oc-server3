<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'myignores';
	$tpl->menuitem = MNU_MYPROFILE_IGNORES;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect('login.php?target=mytop5.php');

	$rs = sql("SELECT `cache_ignore`.`cache_id` AS `cacheid`, `caches`.`wp_oc` AS `wp`, `caches`.`name` AS `name`, `caches`.`type` AS `type`, `caches`.`status` AS `status`, `ca`.`attrib_id` IS NOT NULL AS `oconly` FROM `cache_ignore` INNER JOIN `caches` ON `cache_ignore`.`cache_id`=`caches`.`cache_id` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` LEFT JOIN `caches_attributes` `ca` ON `ca`.`cache_id`=`caches`.`cache_id` AND `ca`.`attrib_id`=6 WHERE `cache_ignore`.`user_id`='&1' AND `cache_status`.`allow_user_view`=1 ORDER BY `caches`.`name`", $login->userid);
	$tpl->assign_rs('ignores', $rs);
	sql_free_result($rs);

	$tpl->display();
?>
