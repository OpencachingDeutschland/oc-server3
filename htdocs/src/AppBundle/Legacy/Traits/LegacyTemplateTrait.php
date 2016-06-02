<?php

namespace AppBundle\Legacy\Traits;

trait LegacyTemplateTrait
{
    /**
     * @param int $menu
     *
     * @return void
     */
    protected function setMenu($menu)
    {
        $GLOBALS['tpl']->menuitem = $menu;
    }

    /**
     * @param string $title
     *
     * @return void
     */
    protected function setTitle($title)
    {
        $GLOBALS['tpl']->title = $title;
    }

    /**
     * @param string $url
     *
     * @return void
     */
    protected function setTarget($url)
    {
        $GLOBALS['tpl']->target = $url;
    }
}
