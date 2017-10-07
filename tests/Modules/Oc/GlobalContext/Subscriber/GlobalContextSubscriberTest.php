<?php

namespace OcTest\Modules\Oc\GlobalContext\Subscriber;

use Oc\GlobalContext\GlobalContext;
use Oc\GlobalContext\GlobalContextFactory;
use Oc\GlobalContext\Subscriber\GlobalContextSubscriber;
use OcTest\Modules\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class GlobalContextSubscriberTest
 *
 * @package OcTest\Modules\Oc\GlobalContext\Subscriber
 */
class GlobalContextSubscriberTest extends TestCase
{
    /**
     * Tests that subscribed events returns the expected events.
     *
     * @return void
     */
    public function testSubscribedEvents()
    {
        $subscribedEvents = GlobalContextSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(KernelEvents::REQUEST, $subscribedEvents);
        self::assertSame('onKernelRequest', $subscribedEvents[KernelEvents::REQUEST]);
    }

    /**
     * Tests that the onKernelRequest listener initializes and sets the global context to the master request.
     *
     * @return void
     */
    public function testThatOnKernelRequestListenerSetsGlobalContextToMasterRequest()
    {
        $subscriber = $this->getGlobalContextSubscriber();

        $event = $this->createMock(GetResponseEvent::class);
        $event->expects(self::once())
            ->method('isMasterRequest')
            ->willReturn(true);

        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag();

        $event->expects(self::once())
            ->method('getRequest')
            ->willReturn($request);

        $subscriber->onKernelRequest($event);

        self::assertInstanceOf(GlobalContext::class, $request->attributes->get('global_context'));
    }

    /**
     * Tests that the onKernelRequest listener does nothing when the incoming request is not the master request.
     */
    public function testThatOnKernelRequestListenerDoesNothing()
    {
        $subscriber = $this->getGlobalContextSubscriber(false);

        $event = $this->createMock(GetResponseEvent::class);
        $event->expects(self::once())
              ->method('isMasterRequest')
              ->willReturn(false);

        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag();

        $event->expects(self::never())
              ->method('getRequest');

        $subscriber->onKernelRequest($event);

        self::assertFalse($request->attributes->has('global_context'));
    }

    /**
     * Instantiates GlobalContextSubscriber.
     *
     * @param bool $called Flag if the factory method is to be called or not.
     *
     * @return GlobalContextSubscriber
     */
    private function getGlobalContextSubscriber($called = true)
    {
        $globalContext = new GlobalContext('de');

        $factory = $this->createMock(GlobalContextFactory::class);
        $factory->expects($called ? self::once() : self::never())
            ->method('createFromRequest')
            ->willReturn($globalContext);

        return new GlobalContextSubscriber($factory);
    }
}
