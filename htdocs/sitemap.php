<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'sitemap';
	$tpl->menuitem = MNU_SITEMAP;

	$tpl->caching = true;
	$tpl->cache_lifetime = 7*24*60*60;

	$viewall = isset($_REQUEST['viewall']) && $_REQUEST['viewall']=='1' && $login->admin;

	if (!$tpl->is_cached())
	{
		$tpl->assign('sites', getSites($viewall));
	}

	$tpl->display();

function getSites($viewall)
{
	$aItems = array();
	pAppendSites(0, $viewall, 0, $aItems);
	return $aItems;
}

function pAppendSites($parentId, $viewall, $sublevel, &$aItems)
{
	global $opt;

	$rs = sql("SELECT `sys_menu`.`id`, 
	                  IF(`sys_menu`.`title`='', 
	                     IFNULL(`ttMenu`.`text`, `sys_menu`.`menustring`), 
	                     IFNULL(`ttTitle`.`text`, `sys_menu`.`title`)) AS `name`, 
	                  `sys_menu`.`href`, 
	                  `sys_menu`.`sitemap` 
	             FROM `sys_menu` 
	        LEFT JOIN `sys_trans` AS `tTitle` ON `sys_menu`.`title_trans_id`=`tTitle`.`id` AND `sys_menu`.`title`=`tTitle`.`text` 
	        LEFT JOIN `sys_trans_text` AS ttTitle ON `tTitle`.`id`=`ttTitle`.`trans_id` AND `ttTitle`.`lang`='&2' 
	        LEFT JOIN `sys_trans` AS `tMenu` ON `sys_menu`.`menustring_trans_id`=`tMenu`.`id` AND `sys_menu`.`menustring`=`tMenu`.`text` 
	        LEFT JOIN `sys_trans_text` AS `ttMenu` ON `tMenu`.`id`=`ttMenu`.`trans_id` AND `ttMenu`.`lang`='&2' 
	            WHERE `sys_menu`.`access`=0 AND `sys_menu`.`parent`='&1' AND (&3=1 OR `sys_menu`.`sitemap`=1)
	         ORDER BY `sys_menu`.`parent` ASC, `sys_menu`.`position` ASC", $parentId, $opt['template']['locale'], $viewall ? 1 : 0);
	while ($r = sql_fetch_assoc($rs))
	{
		$r['sublevel'] = $sublevel;
		$aItems[] = $r;
		pAppendSites($r['id'], $viewall, $sublevel+1, &$aItems);
	}
	sql_free_result($rs);
}
?>