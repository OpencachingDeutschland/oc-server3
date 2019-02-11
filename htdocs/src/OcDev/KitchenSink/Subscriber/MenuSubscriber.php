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
            MenuEnum::MENU_MAIN => ['onConfigureMenu', 60],
        ];
    }
    
    public function onConfigureMenu(MenuEvent $event): void
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
