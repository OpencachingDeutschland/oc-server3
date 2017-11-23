<?php

namespace OcTest\Modules\OcLegacy;

use AppKernel;
use Doctrine\DBAL\Connection;
use OcTest\Modules\AbstractModuleTest;

/**
 * Class ContainerTest
 */
class ContainerTest extends AbstractModuleTest
{
    public function testIfContainerReturnServiceId()
    {
        self::assertInstanceOf(Connection::class, AppKernel::Container()->get(Connection::class));
        // cached container
        self::assertInstanceOf(Connection::class, AppKernel::Container()->get(Connection::class));
    }
}
