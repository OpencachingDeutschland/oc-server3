<?php

namespace OcTest\Modules\Oc\Changelog\Subscriber;

use AppKernel;
use Oc\Changelog\Subscriber\MenuSubscriber;
use OcTest\Modules\TestCase;

class MenuSubscriberTest extends TestCase
{
    public function test_get_subscribed_events_returns_array()
    {
        $menuSubscriber = AppKernel::Container()->get(MenuSubscriber::class);

        $this->assertInternalType('array', $menuSubscriber::getSubscribedEvents());
    }
}
