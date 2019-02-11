<?php

namespace Oc\Page\Subscriber;

use Oc\Menu\Event\MenuEvent;
use Oc\Menu\MenuEnum;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            MenuEnum::MENU_MAIN => ['onConfigureMenuMain', 50],
        ];
    }
    
    public function onConfigureMenuMain(MenuEvent $event): void
    {
        $event->getCurrentItem()->addChild(
            'tos',
            [
                'label' => 'Nutzungsbedingungen',
                'uri' => '/articles.php?page=impressum#tos',
            ]
        );

        $event->getCurrentItem()->addChild(
            'privacy_policy',
            [
                'label' => 'Datenschutzerklärung',
                'route' => 'page',
                'routeParameters' => [
                    'slug' => 'datenschutzerklaerung',
                ],
            ]
        );

        $event->getCurrentItem()->addChild(
            'imprint',
            [
                'label' => 'Impressum',
                'uri' => '/articles.php?page=impressum',
            ]
        );
    }
}
