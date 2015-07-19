<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	define('AUTH_LEVEL_ALL', 0);
	define('AUTH_LEVEL_ADMIN', '2');

	define('MNU_ROOT', 0);

	global $opt;
	require_once($opt['rootpath'] . 'lib2/translate.class.php');
	$menu = new Menu();

class Menu
{
	var $nSelectedItem = 0;
	var $sMenuFilename = '';

	function Menu()
	{
		global $opt;

		$this->sMenuFilename = $opt['rootpath'] . 'cache2/menu-' . $opt['template']['locale'] . '.inc.php';

		if (!file_exists($this->sMenuFilename))
			$this->CreateCacheFile();

		require_once($this->sMenuFilename);
	}

	function CreateCacheFile()
	{
		global $opt, $translate;

		$f = fopen($this->sMenuFilename, 'w');
		fwrite($f, "<?php\n");
		fwrite($f, 'global $menuitem;' . "\n");
		fwrite($f, "\n");

		$rsDefines = sqlf("SELECT `id`, `id_string` FROM `sys_menu`");
		while ($rDefine = sql_fetch_assoc($rsDefines))
			fwrite($f, 'if (!defined(\'' . addslashes($rDefine['id_string']) . '\')) define(\'' . addslashes($rDefine['id_string']) . '\', ' . $rDefine['id'] . ");\n");
		sql_free_result($rsDefines);
		fwrite($f, "\n");

		$aMenu = array();
		$nPos = 0;
		$rsSubmenu = sqlf("SELECT `id` FROM `sys_menu` WHERE `parent`=0 ORDER BY `parent` ASC, `position` ASC");
		while ($rSubmenu = sql_fetch_assoc($rsSubmenu))
		{
			$aMenu[MNU_ROOT]['subitems'][$nPos] = $rSubmenu['id'];
			$nPos++;
		}
		sql_free_result($rsSubmenu);
		fwrite($f, "\n");

		$rs = sqlf('SELECT `item`.`id`, `item`.`title`, `item`.`menustring`, `item`.`access`, `item`.`href`, `item`.`visible`, `item`.`parent` AS `parentid`, `item`.`color` AS `color` FROM `sys_menu` AS `item` LEFT JOIN `sys_menu` AS `parentitem` ON `item`.`parent`=`parentitem`.`id`');
		while ($r = sql_fetch_assoc($rs))
		{
			$aMenu[$r['id']]['title'] = $translate->t($r['title'], '', basename(__FILE__), __LINE__);
			$aMenu[$r['id']]['menustring'] = $translate->t($r['menustring'], '', basename(__FILE__), __LINE__);
			$aMenu[$r['id']]['authlevel'] = ($r['access']==0) ? AUTH_LEVEL_ALL : AUTH_LEVEL_ADMIN;
			if (substr($r['href'],0,1) == '!')
			{
				$aMenu[$r['id']]['href'] = str_replace('%LANG', strtolower($opt['template']['locale']), substr($r['href'],1));
				$aMenu[$r['id']]['target'] = 'target="_blank"';
			}
			else if (strstr($r['href'],'&wiki'))
			{
				$aMenu[$r['id']]['href'] = $r['href'];
				$aMenu[$r['id']]['target'] = 'target="_blank"';
			}
			else
			{
				$aMenu[$r['id']]['href'] = $r['href'];
				$aMenu[$r['id']]['target'] = '';
			}
			$aMenu[$r['id']]['visible'] = $r['visible'];
			$aMenu[$r['id']]['sublevel'] = $this->pGetMenuSublevel($r['id']);

			if ($r['parentid'] != 0)
				$aMenu[$r['id']]['parent'] = $r['parentid'];
			if ($r['color'] != null)
				$aMenu[$r['id']]['color'] = $r['color'];

			$nPos = 0;
			$rsSubmenu = sqlf("SELECT `id` FROM `sys_menu` WHERE `parent`='&1' ORDER BY `parent` ASC, `position` ASC", $r['id']);
			while ($rSubmenu = sql_fetch_assoc($rsSubmenu))
			{
				$aMenu[$r['id']]['subitems'][$nPos] = $rSubmenu['id'];
				$nPos++;
			}
			sql_free_result($rsSubmenu);
		}
		sql_free_result($rs);

		fwrite($f, '$menuitem = unserialize("' . str_replace('"', '\\"', serialize($aMenu)) . '");' . "\n");
		
		fwrite($f, "?>");
		fclose($f);
	}

	function clearCache()
	{
		global $opt;

		$dir = $opt['rootpath'] . 'cache2/';
		if ($dh = opendir($dir))
		{
			while (($file = readdir($dh)) !== false)
			{
				if (filetype($dir . $file) == 'file')
				{
					if (preg_match('/^menu-[a-z]{2,2}.inc.php/', $file))
						unlink($dir . $file);
				}
			}
			closedir($dh);
		}
	}

	function pGetMenuSublevel($id)
	{
		$parent = sqlf_value("SELECT `parent` FROM `sys_menu` WHERE `id`='&1'", 0, $id);
		if ($parent != 0)
			return $this->pGetMenuSublevel($parent) + 1;

		return 0;
	}

	function SetSelectItem($item)
	{
		$this->nSelectedItem = $item;
	}

	function GetSelectItem($item)
	{
		return $this->nSelectedItem;
	}

	function GetBreadcrumb()
	{
		global $menuitem;

		$retval = array();
		$retval[] = $menuitem[$this->nSelectedItem];

		$nCurItem = $this->nSelectedItem;

		while ($nCurItem != MNU_ROOT)
		{
			if (isset($menuitem[$nCurItem]['parent']))
			{
				$nCurItem = $menuitem[$nCurItem]['parent'];
				$retval[] = $menuitem[$nCurItem];
			}
			else
				$nCurItem = MNU_ROOT;
		}

		return array_reverse($retval);
	}

	function GetTopMenu()
	{
		global $menuitem, $login;

		$ids = $this->GetSelectedMenuIds();

		$retval = array();
		foreach ($menuitem[MNU_ROOT]['subitems'] AS $item)
		{
			if (($menuitem[$item]['authlevel'] != AUTH_LEVEL_ADMIN || $login->hasAdminPriv()) && ($menuitem[$item]['visible'] == 1 || ($menuitem[$item]['visible'] == 2 && !$login->logged_in())))
			{
				$thisitem = $menuitem[$item];
				$thisitem['selected'] = isset($ids[$item]);
				$retval[] = $thisitem;
			}
		}

		return $retval;
	}

	function GetSubMenu()
	{
		global $menuitem, $login;

		$ids = $this->GetSelectedMenuIds();
		$topmenu = array_pop($ids);
		if (isset($menuitem[$topmenu]['parent']) && $menuitem[$topmenu]['parent'] != MNU_ROOT)
			die('internal error Menu::GetSelectedMenuIds');

		$ids[$topmenu] = $topmenu;

		$retval = array();
		if ($topmenu != MNU_ROOT)
		{
			$this->pAppendSubMenu($topmenu, $ids, $retval);
		}

		return $retval;
	}

	function pAppendSubMenu($menuid, $ids, &$items)
	{
		global $menuitem, $login;

		if (isset($menuitem[$menuid]['subitems']))
		{
			foreach ($menuitem[$menuid]['subitems'] AS $item)
			{
				if (($menuitem[$item]['authlevel'] != AUTH_LEVEL_ADMIN || $login->hasAdminPriv()) && ($menuitem[$item]['visible'] == 1 || ($menuitem[$item]['visible'] == 2 && !$login->logged_in())))
				{
					$thisitem = $menuitem[$item];
					$thisitem['selected'] = isset($ids[$item]);
					$items[] = $thisitem;

					$this->pAppendSubMenu($item, $ids, $items);
				}
			}
		}
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

	function getMenuColor()
	{
		global $menuitem;

		$nCurItem = $this->nSelectedItem;

		while (!isset($menuitem[$nCurItem]['color']) && $nCurItem != MNU_ROOT)
		{
			if (isset($menuitem[$nCurItem]['parent']))
			{
				$nCurItem = $menuitem[$nCurItem]['parent'];
			}
			else
				$nCurItem = MNU_ROOT;
		}
		if (isset($menuitem[$nCurItem]['color']))
			return $menuitem[$nCurItem]['color'];
		else
			return '';
	}

	function GetMenuTitle()
	{
		global $menuitem;

		if (isset($menuitem[$this->nSelectedItem]))
		{
			return isset($menuitem[$this->nSelectedItem]['title']) ? $menuitem[$this->nSelectedItem]['title'] : '';
		}
		else
			return '';
	}
}
?>