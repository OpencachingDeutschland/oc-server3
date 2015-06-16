<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/cachelist.class.php');

	$tpl->name = 'mylists';
	$tpl->menuitem = MNU_MYPROFILE_LISTS;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect('login.php?target=' . urlencode($tpl->target));

	$list_name = isset($_REQUEST['list_name']) ? trim($_REQUEST['list_name']) : '';
	$list_public = isset($_REQUEST['list_public']) ? $_REQUEST['list_public'] + 0 : 0;
	$list_caches = isset($_REQUEST['list_caches']) ? strtoupper(trim($_REQUEST['list_caches'])) : '';
	$watch = isset($_REQUEST['watch']);

	if (isset($_REQUEST['create']))
	{
		$list = new cachelist(ID_NEW, $login->userid);
		if (!$list->setName($list_name))
			$tpl->assign('name_error', true);
		else
		{
			$list->setPublic($list_public);
			if ($list->save())
			{
				if ($list_caches != '')
				{
					$result = $list->addCachesByWPs($list_caches);
					$tpl->assign('invalid_waypoints', $result === true ? false : implode(", ", $result));
				}
				if ($watch)
					$list->watch(true);
			}
		}
	}

	if (isset($_REQUEST['edit']))
	{
		$list = new cachelist($_REQUEST['edit'] + 0);
		if ($list->exist() && $list->getUserId() == $login->userid)
		{
			$tpl->assign('edit_list', true);
			$tpl->assign('listid', $list->getId());
			$tpl->assign('caches', $list->getCaches());
			$tpl->assign('watch', $list->isWatchedByMe());
			$list_name = $list->getName();
			$list_public = $list->isPublic();
			$list_caches = '';
		}
	}

	if (isset($_REQUEST['save']) && isset($_REQUEST['listid']))
	{
		$list = new cachelist($_REQUEST['listid'] + 0);
		if ($list->exist() && $list->getUserId() == $login->userid)
		{
			if (!$list->setName($list_name))
			{
				$tpl->assign('name_error', true);
				$tpl->assign('edit_list', true);
				$tpl->assign('listid', $list->getId());
				$tpl->assign('caches', $list->getCaches());
			}
			else
			{
				$list->setPublic($list_public);
				$list->save();
			}
			$list->watch($watch);
			if ($list_caches != '')
			{
				$result = $list->addCachesByWPs($list_caches);
				$tpl->assign('invalid_waypoints', $result === true ? false : implode(", ", $result));
			}
			foreach ($_REQUEST as $key => $value)
				if (substr($key, 0, 7) == 'remove_')
					$list->removeCacheById(substr($key,7));
		}
	}

	if (isset($_REQUEST['delete']))
	{
		sql("DELETE FROM `cache_lists` WHERE `user_id`='&1' AND `id`='&2'",
		    $login->userid, $_REQUEST['delete'] + 0);
		// All dependent deletion and cleanup is done via trigger.
	}

	$tpl->assign('cachelists', cachelist::getMyLists());
	$tpl->assign('show_status', true);
	$tpl->assign('show_user', false);
	$tpl->assign('show_watchers', true);
	$tpl->assign('show_edit', true);
	$tpl->assign('togglewatch', false);

	$tpl->assign('list_name', $list_name);
	$tpl->assign('list_public', $list_public);
	$tpl->assign('list_caches', $list_caches);

	$tpl->display();

?>
