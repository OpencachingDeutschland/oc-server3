<?php

declare(strict_types=1);

namespace Oc\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class LocaleSubscriber implements EventSubscriberInterface
{
    public const SESSION_KEY = '_locale';
    public const SUPPORTED = ['de', 'en'];

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->hasPreviousSession()) {
            return;
        }

        $locale = $request->getSession()->get(self::SESSION_KEY);
        if (in_array($locale, self::SUPPORTED, true)) {
            $request->setLocale($locale);
        }
    }
}
