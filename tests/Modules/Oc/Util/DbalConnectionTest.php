<?php

namespace OcTest\Modules\Oc\Util;

use Oc\Util\DbalConnection;
use OcTest\Modules\AbstractModuleTest;

class DbalConnectionTest extends AbstractModuleTest
{
    public function testGetConnection()
    {
        $connection = new DbalConnection();
        self::assertInstanceOf('Doctrine\DBAL\Connection', $connection->getConnection());
    }

    public function testGetQueryBuilder()
    {
        $connection = new DbalConnection();
        self::assertInstanceOf('Doctrine\DBAL\Query\QueryBuilder', $connection->getQueryBuilder());
    }
}
