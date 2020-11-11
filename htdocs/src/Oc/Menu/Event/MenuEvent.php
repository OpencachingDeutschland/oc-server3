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

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }

    public function getCurrentItem(): ItemInterface
    {
        return $this->currentItem;
    }

    /**
     * Creates an event object of it self but with given currentItem.
     */
    public function createSubEvent(ItemInterface $currentItem): self
    {
        return new self(
            $this->eventDispatcher,
            $this->factory,
            $this->menu,
            $currentItem
        );
    }
}
