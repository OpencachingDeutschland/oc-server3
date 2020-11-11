<?php

namespace Oc\Account\Subscriber;

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
    public static function getSubscribedEvents()
    {
        return [
//            MenuEnum::MENU_MAIN => ['onConfigureMenuMain', 1],
//            MenuEnum::MENU_MAIN_ACCOUNT => ['onConfigureMenuMainAccount', 0],
        ];
    }
    
    public function onConfigureMenuMain(MenuEvent $event): void
    {
        $accountItem = $event->getCurrentItem()->addChild('account', ['label' => 'Benutzerkonto']);

        $accountEvent = $event->createSubEvent($accountItem);

        $event->getEventDispatcher()->dispatch(
            MenuEnum::MENU_MAIN_ACCOUNT,
            $accountEvent
        );
    }
    
    public function onConfigureMenuMainAccount(MenuEvent $event): void
    {
        $currentItem = $event->getCurrentItem();

        $currentItem->addChild('profile', ['label' => 'Mein Profil']);

        $currentItem->addChild('statistics', ['label' => 'Statistik']);

        $currentItem->addChild('contacts', ['label' => 'Kontakte']);

        $currentItem->addChild('settings', ['label' => 'Einstellungen']);
    }
}
