<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'imagebrowser';
	$tpl->popup = true;

	$login->verify();

	$cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid']+0 : 0;

	$rs = sql("SELECT `caches`.`name` FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE `caches`.`user_id`='&1' AND `cache_id`='&2'", $login->userid, $cacheid);
	$r = sql_fetch_assoc($rs);
	sql_free_result($rs);

	if ($r === false)
		$tpl->error(ERROR_NO_ACCESS);

	$tpl->assign('cachename', $r['name']);

	$rsPictures = sql('SELECT `uuid`, `url`, `title` FROM `pictures` WHERE `object_id`=&1 AND `object_type`=2', $cacheid);
	$tpl->assign_rs('pictures', $rsPictures);
	sql_free_result($rsPictures);

	$tpl->assign('thumbwidth', $opt['logic']['pictures']['thumb_max_width']);

	$tpl->display();
?>