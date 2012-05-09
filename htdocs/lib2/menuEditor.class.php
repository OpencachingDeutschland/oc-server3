<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

class MenuEditor extends Menu
{
	function GetTopMenu()
	{
		global $menuitem, $login;

		$ids = $this->GetSelectedMenuIds();

		$retval = array();
		foreach ($menuitem[MNU_ROOT]['subitems'] AS $item)
		{
			$thisitem = $menuitem[$item];
			$thisitem['selected'] = isset($ids[$item]);
			$thisitem['href'] = 'menu.php?id=' . $item;
			if ($thisitem['menustring'] == '')
				$thisitem['menustring'] = t('(empty)', '', __FILE__, __LINE__);
			$retval[] = $thisitem;
		}

		$thisitem = array();
		$thisitem['menustring'] = t('New item', '', __FILE__, __LINE__);
		$thisitem['href'] = 'menu.php?action=add&id=0';
		$retval[] = $thisitem;

		return $retval;
	}

	function GetSubMenu()
	{
		global $menuitem, $login;

		$ids = $this->GetSelectedMenuIds();
		$topmenu = array_pop($ids);
		if (isset($menuitem[$topmenu]['parent']) && $menuitem[$topmenu]['parent'] != MNU_ROOT)
			die('internal error MenuEditor::GetSelectedMenuIds');

		$ids[$topmenu] = $topmenu;

		$retval = array();
		if ($topmenu != MNU_ROOT)
		{
			$this->pAppendSubMenu(1, $topmenu, $ids, $retval);
		}

		return $retval;
	}

	function pAppendSubMenu($sublevel, $menuid, $ids, &$items)
	{
		global $menuitem, $login;

		if (isset($menuitem[$menuid]['subitems']))
		{
			foreach ($menuitem[$menuid]['subitems'] AS $item)
			{
				$thisitem = $menuitem[$item];
				$thisitem['selected'] = isset($ids[$item]);
				$thisitem['href'] = 'menu.php?id=' . $item;
				if ($thisitem['menustring'] == '')
					$thisitem['menustring'] = t('(empty)', '', __FILE__, __LINE__);
				$items[] = $thisitem;

				$this->pAppendSubMenu($sublevel+1, $item, $ids, $items);
			}
		}

		$thisitem = array();
		$thisitem['menustring'] = t('New item', '', __FILE__, __LINE__);
		$thisitem['href'] = 'menu.php?action=add&id=' . $menuid;
		$thisitem['sublevel'] = $sublevel;
		$items[] = $thisitem;
	}

	function GetSelectedMenuIds()
	{
		global $menuitem;

		$retval = array();
		$retval[$this->nSelectedItem] = $this->nSelectedItem;

		$nCurItem = $this->nSelectedItem;

		while ($nCurItem != MNU_ROOT)
		{
			if (isset($menuitem[$nCurItem]['parent']))
			{
				$nCurItem = $menuitem[$nCurItem]['parent'];
				$retval[$nCurItem] = $nCurItem;
			}
			else
				$nCurItem = MNU_ROOT;
		}

		return $retval;
	}

	function reorg()
	{
		$nPosition = 1;
		$nLastParent = -1;
		$rs = sqlf("SELECT `id`, `parent` FROM `sys_menu` ORDER BY `parent` ASC, `position` ASC");
		while ($r = sql_fetch_assoc($rs))
		{
			if ($nLastParent != $r['parent'])
			{
				$nPosition = 1;
				$nLastParent = $r['parent'];
			}

			sqlf("UPDATE `sys_menu` SET `position`='&1' WHERE `id`='&2'", $nPosition, $r['id']);
			$nPosition++;
		}
		sql_free_result($rs);
	}
}
?>