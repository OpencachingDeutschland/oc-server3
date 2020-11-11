<?php

namespace OcTest\Modules\Oc\Account\Subscriber;

use Oc\Account\Subscriber\MenuSubscriber;
use OcTest\Modules\TestCase;

class MenuSubscriberTest extends TestCase
{
    public function test_get_subscribed_events_returns_array(): void
    {
        $this->assertInternalType('array', MenuSubscriber::getSubscribedEvents());
    }
}
