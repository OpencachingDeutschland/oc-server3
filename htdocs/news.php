<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');

	if ($opt['news']['redirect'] != '')
		$tpl->redirect($opt['news']['redirect']);

	$tpl->name = 'news';
	$tpl->menuitem = MNU_START_NEWS;

	$tpl->caching = true;
	$tpl->cache_lifetime = 3600;

	if (!$tpl->is_cached())
	{
		$rsNews = sql("SELECT `news`.`date_created`, `news_topics`.`name`, `news`.`content` FROM `news` INNER JOIN `news_topics` ON `news`.`topic`=`news_topics`.`id` WHERE `news`.`display`=1 ORDER BY `news`.`date_created` DESC LIMIT 250");
		$tpl->assign_rs('news', $rsNews);
		sql_free_result($rsNews);
	}

	$tpl->display();
?>