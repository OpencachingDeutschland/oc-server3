<?php

namespace Oc\Menu;

use Knp\Menu\FactoryInterface;
use Oc\Menu\Event\MenuEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param FactoryInterface $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem(MenuEnum::MENU_MAIN);

        $this->eventDispatcher->dispatch(
            MenuEnum::MENU_MAIN,
            new MenuEvent($this->eventDispatcher, $this->factory, $menu, $menu)
        );

        return $menu;
    }
}
