<?php

namespace Oc\GlobalContext\Subscriber;

use Oc\GlobalContext\GlobalContextFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class GlobalContextSubscriber implements EventSubscriberInterface
{
    /**
     * @var GlobalContextFactory
     */
    private GlobalContextFactory $contextFactory;

    public function __construct(GlobalContextFactory $contextFactory)
    {
        $this->contextFactory = $contextFactory;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $globalContext = $this->contextFactory->createFromRequest($request);

        $request->setDefaultLocale($globalContext->getLocale());

        $request->attributes->set('global_context', $globalContext);
    }
}
