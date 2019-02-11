<?php

namespace Oc\Changelog\Subscriber;

use Oc\Menu\Event\MenuEvent;
use Oc\Menu\MenuEnum;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MenuEnum::MENU_MAIN => ['onConfigureMenu', 70],
        ];
    }
    
    public function onConfigureMenu(MenuEvent $event): void
    {
        $event->getCurrentItem()->addChild(
            'Changelog',
            [
                'route' => 'changelog.index',
            ]
        );
    }
}
