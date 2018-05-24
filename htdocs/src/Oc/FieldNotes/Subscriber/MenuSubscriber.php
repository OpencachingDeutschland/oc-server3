<?php

namespace Oc\FieldNotes\Subscriber;

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
            MenuEnum::MENU_MAIN => ['onConfigureMainMenu', 80],
        ];
    }

    /**
     * @param MenuEvent $event
     */
    public function onConfigureMainMenu(MenuEvent $event)
    {
        $event->getCurrentItem()->addChild(
            'field_notes',
            [
                'label' => 'Field-Notes',
                'route' => 'field_notes.index',
            ]
        );
    }
}
