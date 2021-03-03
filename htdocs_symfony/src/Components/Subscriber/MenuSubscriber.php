<?php

declare(strict_types=1);

namespace Oc\Components\Subscriber;

use KevinPapst\AdminLTEBundle\Event\KnpMenuEvent;
use Oc\Entity\UserEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KnpMenuEvent::class => ['onSetupMenu', 100],
        ];
    }

    public function onSetupMenu(KnpMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('MainNavigationMenuItem', [
            'label' => 'MAIN NAVIGATION',
            'childOptions' => $event->getChildOptions()
        ])->setAttribute('class', 'header');

        if ($this->security->isGranted("CAN_VIEW", UserEntity::class)) {
            $userMenu = $menu->addChild('cache', [
                'label' => 'Caches',
                'route' => 'backend_caches_index',
                'childOptions' => $event->getChildOptions(),
            ])->setLabelAttribute('icon', 'fas fa-map-marker-alt');
        }

        if ($this->security->isGranted("CAN_VIEW", UserEntity::class)) {
            $userMenu = $menu->addChild('coordinate', [
                'label' => 'Coordinates',
                'route' => 'backend_coordinates_index',
                'childOptions' => $event->getChildOptions(),
            ])->setLabelAttribute('icon', 'fas fa-map-pin');
        }

        if ($this->security->isGranted("CAN_VIEW", UserEntity::class)) {
            $userMenu = $menu->addChild('user', [
                'label' => 'Users',
                'route' => 'backend_user_index',
                'childOptions' => $event->getChildOptions(),
            ])->setLabelAttribute('icon', 'fas fa-users');
        }

        if ($this->security->isGranted("CAN_VIEW", UserEntity::class)) {
            $userMenu = $menu->addChild('kitchensink', [
                'label' => 'Kitchensink',
                'route' => 'app_kitchensink_index',
                'childOptions' => $event->getChildOptions(),
            ])->setLabelAttribute('icon', 'fab fa-css3');
        }
    }
}
