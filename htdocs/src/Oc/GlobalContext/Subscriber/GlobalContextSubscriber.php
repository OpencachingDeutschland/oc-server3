<?php

namespace Oc\GlobalContext\Subscriber;

use Oc\GlobalContext\GlobalContextFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class GlobalContextSubscriber implements EventSubscriberInterface
{
    /**
     * @var GlobalContextFactory
     */
    private $contextFactory;

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

    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        $globalContext = $this->contextFactory->createFromRequest($request);

        $request->setDefaultLocale($globalContext->getLocale());

        $request->attributes->set('global_context', $globalContext);
    }
}
