<?php

namespace Oc\GlobalContext\Subscriber;

use Oc\GlobalContext\GlobalContextFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class GlobalContextSubscriber
 */
class GlobalContextSubscriber implements EventSubscriberInterface
{
    /**
     * @var GlobalContextFactory
     */
    private $contextFactory;

    /**
     * GlobalContextSubscriber constructor.
     *
     * @param GlobalContextFactory $contextFactory
     */
    public function __construct(GlobalContextFactory $contextFactory)
    {
        $this->contextFactory = $contextFactory;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        $globalContext = $this->contextFactory->createFromRequest($request);

        $request->setLocale($globalContext->getLocale());

        $request->attributes->set('global_context', $globalContext);
    }
}
