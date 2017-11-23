<?php

namespace Oc\Changelog\Subscriber;

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
        ];
    }

    /**
     * @param MenuEvent $event
     */
    public function onConfigureMenu(MenuEvent $event)
    {
        $event->getCurrentItem()->addChild(
            'Changelog',
            [
                'route' => 'changelog.index',
            ]
        );
    }
}
