<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require 'lib2/web.inc.php';
	require 'lib2/logic/oconly81.inc.php';

	$showall = (@$_REQUEST['showall'] == 1);

	$tpl->name = 'oconly81';
	$tpl->menuitem = MNU_CACHES_OCONLY81;
	$tpl->caching = true;
	$tpl->cache_lifetime = 900;
	$tpl->cache_id = $showall ? 1 : 0;

	$login->verify();

	sql_temp_table('oconly81');
	sql("
		CREATE TEMPORARY TABLE &oconly81 ENGINE=MEMORY
		SELECT DISTINCT `user`.`user_id`, `caches`.`terrain`, `caches`.`difficulty`
		FROM `user`
		INNER JOIN `cache_logs` ON `cache_logs`.`user_id`=`user`.`user_id` AND `cache_logs`.`type`=1
		INNER JOIN `caches` ON `caches`.`cache_id`=`cache_logs`.`cache_id`
		INNER JOIN `caches_attributes` ON `caches_attributes`.`cache_id`=`cache_logs`.`cache_id` AND `caches_attributes`.`attrib_id`=6
		INNER JOIN `user_options` ON `user_options`.`user_id`=`user`.`user_id`
		WHERE `user_options`.`option_id`=13 AND `user_options`.`option_value`='1'");
		// users with 0 OConly founds are filtered out here

	$rs = sql("
		SELECT `user`.`username`, `user`.`user_id`, COUNT(*) AS `count`
		FROM `user`
		INNER JOIN &oconly81 ON &oconly81.`user_id`=`user`.`user_id`
		GROUP BY `user`.`user_id`
		ORDER BY `count` DESC, `username` ASC " .
		($showall ? "" : "LIMIT " . sql_escape($opt['logic']['oconly81']['default_maxusers']+1)) );

	$tpl->assign_rs('users', $rs);
	sql_free_result($rs);
	sql_drop_temp_table('oconly81');

	set_oconly81_tpldata(0);
	$tpl->assign('default_maxusers', $opt['logic']['oconly81']['default_maxusers']);
	$tpl->assign('showall', $showall);

	$tpl->display();
?>