<?php

namespace Oc\Page\Subscriber;

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
            MenuEnum::MENU_MAIN => ['onConfigureMenuMain', 50],
        ];
    }

    /**
     * @param MenuEvent $event
     */
    public function onConfigureMenuMain(MenuEvent $event)
    {
        $event->getCurrentItem()->addChild(
            'tos',
            [
                'label' => 'Nutzungsbedingungen',
                'uri' => '/articles.php?page=impressum#tos'
            ]
        );

        $event->getCurrentItem()->addChild(
            'privacy_policy',
            [
                'label' => 'Datenschutzerklärung',
                'route' => 'page',
                'routeParameters' => [
                    'slug' => 'datenschutzerklaerung'
                ],
            ]
        );

        $event->getCurrentItem()->addChild(
            'imprint',
            [
                'label' => 'Impressum',
                'uri' => '/articles.php?page=impressum'
            ]
        );
    }
}
