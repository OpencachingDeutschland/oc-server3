<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require_once $opt['rootpath'] . 'lib2/translate.class.php';

define('AUTH_LEVEL_ALL', 0);
define('AUTH_LEVEL_ADMIN', '2');
define('MNU_ROOT', 0);

$menu = new Menu();

class Menu
{
    public $nSelectedItem = 0;
    public $sMenuFilename = '';

    public function __construct()
    {
        global $opt, $login, $build_map_towns_menu;

        $this->sMenuFilename = $opt['rootpath'] . 'cache2/menu-' . $opt['template']['locale'] . '.inc.php';

        if (!file_exists($this->sMenuFilename)) {
            $this->CreateCacheFile();
        }

        // read static menu
        require_once $this->sMenuFilename;

        // add country-dependent town list for small map
        $country = $login->getUserCountry();
        if ($opt['map']['towns']['enable'] &&
            isset($build_map_towns_menu) && $build_map_towns_menu &&   // optimization
            isset($opt['map']['towns'][$country]['enable']) && $opt['map']['towns'][$country]['enable']
        ) {
            $rsTowns = sqlf(
                "
                SELECT
                    IFNULL(`stt`.`text`,`towns`.`name`) AS `name`,
                    `towns`.`name` AS `native_name`,
                    coord_lat, coord_long
                FROM
                    `towns`
                    LEFT JOIN `sys_trans_text` `stt` ON `stt`.`trans_id`=`towns`.`trans_id` AND `stt`.`lang`='&2'
                WHERE `towns`.`country`='&1' AND `towns`.`maplist` > 0
                ORDER BY `name`",
                $country,
                $opt['template']['locale']
            );

            $menu_map = 2001;
            while ($rTown = sql_fetch_assoc($rsTowns)) {
                if (isset($opt['map']['towns'][$country][$rTown['native_name']]['zoom'])) {
                    $zoom = $opt['map']['towns'][$country][$rTown['native_name']]['zoom'];
                } elseif (isset($opt['map']['towns'][$country]['zoom'])) {
                    $zoom = $opt['map']['towns'][$country]['zoom'];
                } else {
                    $zoom = 11;
                }

                if ($zoom > 0) {
                    $menuitem[$menu_map] = [
                        'title' => $rTown['name'],
                        'menustring' => $rTown['name'],
                        'authlevel' => 0,
                        'href' => 'map2.php?mode=normalscreen&lat=' . $rTown['coord_lat'] . '&lon=' . $rTown['coord_long'] . '&zoom=' . $zoom,
                        'visible' => 1,
                        'sublevel' => 1,
                        'parent' => MNU_MAP
                    ];
                    $menuitem[MNU_MAP]['subitems'][] = $menu_map;
                    ++ $menu_map;
                }
            }
        }
    }

    public function CreateCacheFile()
    {
        global $opt, $translate;

        $f = fopen($this->sMenuFilename, 'w');
        fwrite($f, "<?php\n");
        fwrite($f, 'global $menuitem;' . "\n");
        fwrite($f, "\n");

        $rsDefines = sqlf("SELECT `id`, `id_string` FROM `sys_menu`");
        while ($rDefine = sql_fetch_assoc($rsDefines)) {
            fwrite($f, 'if (!defined(\'' . addslashes($rDefine['id_string']) . '\')) define(\'' . addslashes($rDefine['id_string']) . '\', ' . $rDefine['id'] . ");\n");
        }
        sql_free_result($rsDefines);
        fwrite($f, "\n");

        $aMenu = array();
        $nPos = 0;
        $rsSubmenu = sqlf("SELECT `id` FROM `sys_menu` WHERE `parent`=0 ORDER BY `parent` ASC, `position` ASC");
        while ($rSubmenu = sql_fetch_assoc($rsSubmenu)) {
            $aMenu[MNU_ROOT]['subitems'][$nPos] = $rSubmenu['id'];
            $nPos ++;
        }
        sql_free_result($rsSubmenu);
        fwrite($f, "\n");

        $rs = sqlf('SELECT `item`.`id`, `item`.`title`, `item`.`menustring`, `item`.`access`, `item`.`href`, `item`.`visible`, `item`.`parent` AS `parentid`, `item`.`color` AS `color` FROM `sys_menu` AS `item` LEFT JOIN `sys_menu` AS `parentitem` ON `item`.`parent`=`parentitem`.`id`');
        while ($r = sql_fetch_assoc($rs)) {
            $aMenu[$r['id']]['title'] = $translate->t($r['title'], '', basename(__FILE__), __LINE__);
            $aMenu[$r['id']]['menustring'] = $translate->t($r['menustring'], '', basename(__FILE__), __LINE__);
            $aMenu[$r['id']]['authlevel'] = ($r['access'] == 0) ? AUTH_LEVEL_ALL : AUTH_LEVEL_ADMIN;
            if (substr($r['href'], 0, 1) == '!') {
                $aMenu[$r['id']]['href'] = str_replace('%LANG', strtolower($opt['template']['locale']), substr($r['href'], 1));
                $aMenu[$r['id']]['target'] = 'target="_blank"';
            } elseif (strstr($r['href'], '&wiki')) {
                $aMenu[$r['id']]['href'] = $r['href'];
                $aMenu[$r['id']]['target'] = 'target="_blank"';
            } else {
                $aMenu[$r['id']]['href'] = $r['href'];
                $aMenu[$r['id']]['target'] = '';
            }
            $aMenu[$r['id']]['visible'] = $r['visible'];
            $aMenu[$r['id']]['sublevel'] = $this->pGetMenuSublevel($r['id']);

            if ($r['parentid'] != 0) {
                $aMenu[$r['id']]['parent'] = $r['parentid'];
            }
            if ($r['color'] != null) {
                $aMenu[$r['id']]['color'] = $r['color'];
            }

            $nPos = 0;
            $rsSubmenu = sqlf(
                "SELECT `id` FROM `sys_menu` WHERE `parent`='&1' ORDER BY `parent` ASC, `position` ASC",
                $r['id']
            );
            while ($rSubmenu = sql_fetch_assoc($rsSubmenu)) {
                $aMenu[$r['id']]['subitems'][$nPos] = $rSubmenu['id'];
                $nPos ++;
            }
            sql_free_result($rsSubmenu);
        }
        sql_free_result($rs);

        fwrite($f, '$menuitem = unserialize("' . str_replace('"', '\\"', serialize($aMenu)) . '");' . "\n");

        fwrite($f, "?>");
        fclose($f);
    }

    public function clearCache()
    {
        global $opt;

        $dir = $opt['rootpath'] . 'cache2/';
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (filetype($dir . $file) == 'file') {
                    if (preg_match('/^menu-[a-z]{2,2}.inc.php/', $file)) {
                        unlink($dir . $file);
                    }
                }
            }
            closedir($dh);
        }
    }

    public function pGetMenuSublevel($id)
    {
        $parent = sqlf_value("SELECT `parent` FROM `sys_menu` WHERE `id`='&1'", 0, $id);
        if ($parent != 0) {
            return $this->pGetMenuSublevel($parent) + 1;
        }

        return 0;
    }

    public function SetSelectItem($item)
    {
        $this->nSelectedItem = $item;
    }

    public function GetSelectItem($item)
    {
        return $this->nSelectedItem;
    }

    public function GetBreadcrumb()
    {
        global $menuitem;

        $retval = array();
        $retval[] = $menuitem[$this->nSelectedItem];

        $nCurItem = $this->nSelectedItem;

        while ($nCurItem != MNU_ROOT) {
            if (isset($menuitem[$nCurItem]['parent'])) {
                $nCurItem = $menuitem[$nCurItem]['parent'];
                $retval[] = $menuitem[$nCurItem];
            } else {
                $nCurItem = MNU_ROOT;
            }
        }

        return array_reverse($retval);
    }

    public function GetTopMenu()
    {
        global $menuitem, $login;

        $ids = $this->GetSelectedMenuIds();

        $retval = array();
        foreach ($menuitem[MNU_ROOT]['subitems'] as $item) {
            if (($menuitem[$item]['authlevel'] != AUTH_LEVEL_ADMIN || $login->hasAdminPriv()) &&
                ($menuitem[$item]['visible'] == 1 || ($menuitem[$item]['visible'] == 2 && !$login->logged_in()))
            ) {
                $thisitem = $menuitem[$item];
                $thisitem['selected'] = isset($ids[$item]);
                $retval[] = $thisitem;
            }
        }

        return $retval;
    }

    public function GetSubMenu()
    {
        global $menuitem, $login;

        $ids = $this->GetSelectedMenuIds();
        $topmenu = array_pop($ids);
        if (isset($menuitem[$topmenu]['parent']) && $menuitem[$topmenu]['parent'] != MNU_ROOT) {
            die('internal error Menu::GetSelectedMenuIds');
        }

        $ids[$topmenu] = $topmenu;

        $retval = array();
        if ($topmenu != MNU_ROOT) {
            $this->pAppendSubMenu($topmenu, $ids, $retval);
        }

        return $retval;
    }

    public function pAppendSubMenu($menuid, $ids, &$items)
    {
        global $menuitem, $login;

        if (isset($menuitem[$menuid]['subitems'])) {
            foreach ($menuitem[$menuid]['subitems'] as $item) {
                if (($menuitem[$item]['authlevel'] != AUTH_LEVEL_ADMIN || $login->hasAdminPriv()) && ($menuitem[$item]['visible'] == 1 || ($menuitem[$item]['visible'] == 2 && !$login->logged_in()))) {
                    $thisitem = $menuitem[$item];
                    $thisitem['selected'] = isset($ids[$item]);
                    $items[] = $thisitem;

                    $this->pAppendSubMenu($item, $ids, $items);
                }
            }
        }
    }

    public function GetSelectedMenuIds()
    {
        global $menuitem;

        $retval = array();
        $retval[$this->nSelectedItem] = $this->nSelectedItem;

        $nCurItem = $this->nSelectedItem;

        while ($nCurItem != MNU_ROOT) {
            if (isset($menuitem[$nCurItem]['parent'])) {
                $nCurItem = $menuitem[$nCurItem]['parent'];
                $retval[$nCurItem] = $nCurItem;
            } else {
                $nCurItem = MNU_ROOT;
            }
        }

        return $retval;
    }

    public function getMenuColor()
    {
        global $menuitem;

        $nCurItem = $this->nSelectedItem;

        while (!isset($menuitem[$nCurItem]['color']) && $nCurItem != MNU_ROOT) {
            if (isset($menuitem[$nCurItem]['parent'])) {
                $nCurItem = $menuitem[$nCurItem]['parent'];
            } else {
                $nCurItem = MNU_ROOT;
            }
        }
        if (isset($menuitem[$nCurItem]['color'])) {
            return $menuitem[$nCurItem]['color'];
        } else {
            return '';
        }
    }

    public function GetMenuTitle()
    {
        global $menuitem;

        if (isset($menuitem[$this->nSelectedItem])) {
            return isset($menuitem[$this->nSelectedItem]['title']) ? $menuitem[$this->nSelectedItem]['title'] : '';
        } else {
            return '';
        }
    }
}
