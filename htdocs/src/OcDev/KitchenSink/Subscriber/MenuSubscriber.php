<?php

namespace OcDev\KitchenSink\Subscriber;

use Oc\Menu\Event\MenuEvent;
use Oc\Menu\MenuEnum;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MenuSubscriber
 */
class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            MenuEnum::MENU_MAIN => 'onConfigureMenu',
            MenuEnum::MENU_MAIN . '.kitchensink' => 'onConfigureKitchensinkMenu',
        ];
    }

    /**
     * @param MenuEvent $event
     */
    public function onConfigureKitchensinkMenu(MenuEvent $event)
    {
        $event->getCurrentItem()->addChild('Test1');
        $event->getCurrentItem()->addChild('Test2');
    }

    /**
     * @param MenuEvent $event
     */
    public function onConfigureMenu(MenuEvent $event)
    {
        $kitchensink = $event->getCurrentItem()->addChild(
            'kitchensink',
            [
                'label' => 'Kitchensink',
                'route' => 'kitchensink.index',
            ]
        );

        $subEvent = $event->createSubEvent($kitchensink);

        $event->getEventDispatcher()->dispatch(MenuEnum::MENU_MAIN . '.kitchensink', $subEvent);
    }
}
