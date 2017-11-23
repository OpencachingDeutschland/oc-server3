<?php

namespace Oc\Menu\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MenuEvent extends Event
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ItemInterface
     */
    private $menu;

    /**
     * @var ItemInterface
     */
    private $currentItem;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * ConfigureMenuEvent constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param FactoryInterface $factory
     * @param ItemInterface $menu
     * @param ItemInterface $currentItem
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        FactoryInterface $factory,
        ItemInterface $menu,
        ItemInterface $currentItem
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->factory = $factory;
        $this->menu = $menu;
        $this->currentItem = $currentItem;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return ItemInterface
     */
    public function getCurrentItem()
    {
        return $this->currentItem;
    }

    /**
     * Creates an event object of it self but with given currentItem.
     *
     * @param ItemInterface $currentItem
     * @return self
     */
    public function createSubEvent(ItemInterface $currentItem)
    {
        return new self(
            $this->eventDispatcher,
            $this->factory,
            $this->menu,
            $currentItem
        );
    }
}
