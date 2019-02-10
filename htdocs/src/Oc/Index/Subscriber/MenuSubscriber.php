<?php

namespace Oc\Index\Subscriber;

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
    public static function getSubscribedEvents(): array
    {
        return [
            MenuEnum::MENU_MAIN => ['onConfigureMenu', 100],
        ];
    }
    
    public function onConfigureMenu(MenuEvent $event): void
    {
        $event->getCurrentItem()->addChild(
            'Startseite',
            [
                'uri' => '/',
            ]
        );

        $event->getCurrentItem()->addChild(
            'Karte',
            [
                'uri' => '/map2.php',
            ]
        );
    }
}
