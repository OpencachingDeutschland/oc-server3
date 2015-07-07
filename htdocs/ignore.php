<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');

	$cache_id = isset($_GET['cacheid']) ? $_GET['cacheid']+0 : 0;
	$action = isset($_GET['action']) ? $_GET['action'] : '';
	$target = isset($_GET['target']) ? $_GET['target'] : 'viewcache.php?cacheid=' . $cache_id;

	$login->verify();

	// user valid
	if ($login->userid == 0)
		$tpl->redirect('login.php?target=ignore.php');

	// cache_id valid?
	if (sql_value("SELECT COUNT(*) FROM `caches` WHERE `cache_id`='&1'", 0, $cache_id) == 0)
		$tpl->error(ERROR_CACHE_NOT_EXISTS);

	// action valid
	if (($action != 'addignore') && ($action != 'removeignore'))
		$tpl->error(ERROR_INVALID_OPERATION);

	switch ($action)
	{
		case 'addignore':
			sql("INSERT IGNORE INTO `cache_ignore` (`cache_id`, `user_id`) VALUES ('&1', '&2')", $cache_id, $login->userid);
			break;
		case 'removeignore':
			sql("DELETE FROM `cache_ignore` WHERE `cache_id`='&1' AND `user_id`='&2'", $cache_id, $login->userid);
			break;
	}

	// clear cached map result, so that the change directly appears on the map
	$map_result_id = sql_value("SELECT `result_id` FROM `map2_result`
	                             WHERE INSTR(sqlquery,\"`user_id`='" . sql_escape($login->userid) . "'\")
															 LIMIT 1", 0);
	if ($map_result_id)
	{
		sql("DELETE FROM `map2_result` WHERE `result_id`='&1'", $map_result_id);
		sql("DELETE FROM `map2_data` WHERE `result_id`='&1'", $map_result_id);
	}

	$tpl->redirect($target);
?>