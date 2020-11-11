<?php

namespace OcLegacy\Template;

trait LegacyTemplateTrait
{
    protected function setMenu(int $menu): void
    {
        $GLOBALS['tpl']->menuitem = $menu;
    }

    protected function setTitle(string $title): void
    {
        $GLOBALS['tpl']->title = $title;
    }

    protected function setTarget(string $url): void
    {
        $GLOBALS['tpl']->target = $url;
    }
}
