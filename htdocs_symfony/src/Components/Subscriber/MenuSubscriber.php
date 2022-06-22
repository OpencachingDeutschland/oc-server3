<?php

declare(strict_types=1);

namespace Oc\Components\Subscriber;

use KevinPapst\AdminLTEBundle\Event\KnpMenuEvent;
use Oc\Entity\UserEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 *
 */
class MenuSubscriber implements EventSubscriberInterface
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * MenuSubscriber constructor.
     *
     * @param Security $security
     * @param TranslatorInterface $translator
     */
    public function __construct(Security $security, TranslatorInterface $translator)
    {
        $this->security = $security;
        $this->translator = $translator;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents()
    : array
    {
        return [
            KnpMenuEvent::class => ['onSetupMenu', 100],
        ];
    }

    /**
     */
    private function addMenuItem()
    {
    }

    /**
     * @param KnpMenuEvent $event
     */
    public function onSetupMenu(KnpMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('cacheZeug', [
            'label' => 'Cachezeugs',
            'route' => 'backend_caches_index',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-map-marker-alt');

        $menu['cacheZeug']->addChild('cache', [
            'label' => 'Caches',
            'route' => 'backend_caches_index',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-map-marker-alt');

        $menu['cacheZeug']->addChild('coordinate', [
            'label' => 'Coordinates',
            'route' => 'backend_coordinates_index',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-map-pin');

        $menu->addChild('maps', [
            'label' => 'Maps',
            'route' => 'backend_map_show',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-map');

        if ($this->security->isGranted('ROLE_TEAM')) {
            $menu->addChild('kitchensink', [
                'label' => 'Kitchensink',
                'route' => 'app_kitchensink_index',
                'childOptions' => $event->getChildOptions(),
            ])->setLabelAttribute('icon', 'fab fa-css3');
        }

        $menu->addChild('oconly81', [
            'label' => 'OCOnly81',
            'route' => 'backend_oconly81_index',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-question');

        $menu->addChild('roles', [
            'label' => 'Roles',
            'route' => 'backend_roles_index',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-user-shield');

        if ($this->security->isGranted('ROLE_TEAM')) {
            $menu->addChild('support', [
                'label' => 'Support Center',
                'route' => 'backend_support_reported_caches',
                'childOptions' => $event->getChildOptions(),
            ])->setLabelAttribute('icon', 'fas fa-gem');
        }

        if ($this->security->isGranted('ROLE_TEAM')) {
            $menu->addChild('user', [
                'label' => 'Users',
                'route' => 'backend_user_index',
                'childOptions' => $event->getChildOptions(),
            ])->setLabelAttribute('icon', 'fas fa-users');
        }

        $menu->addChild('settings', [
            'label' => $this->translator->trans('Settings'),
            'route' => '',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-cogs');

        $menu->addChild('logout', [
            'label' => 'Logout',
            'route' => 'app_security_logout',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-door-open');
    }
}
