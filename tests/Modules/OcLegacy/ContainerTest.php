<?php

namespace OcTest\Modules\OcLegacy;

use Doctrine\DBAL\Connection;
use OcLegacy\Container;
use OcTest\Modules\AbstractModuleTest;

class ContainerTest extends AbstractModuleTest
{
    public function testIfContainerReturnServiceId()
    {
        self::assertInstanceOf(Connection::class, Container::get('app.dbal_connection'));
        // cached container
        self::assertInstanceOf(Connection::class, Container::get('app.dbal_connection'));
    }
}
