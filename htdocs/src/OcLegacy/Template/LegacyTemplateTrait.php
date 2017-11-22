<?php

namespace OcLegacy\Template;

trait LegacyTemplateTrait
{
    /**
     * @param int $menu
     */
    protected function setMenu($menu)
    {
        $GLOBALS['tpl']->menuitem = $menu;
    }

    /**
     * @param string $title
     */
    protected function setTitle($title)
    {
        $GLOBALS['tpl']->title = $title;
    }

    /**
     * @param string $url
     */
    protected function setTarget($url)
    {
        $GLOBALS['tpl']->target = $url;
    }
}
