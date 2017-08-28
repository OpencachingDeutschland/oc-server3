<?php

namespace OcTest\Modules\Oc\Util;

use Oc\Util\DbalConnection;
use OcTest\Modules\AbstractModuleTest;
use Symfony\Component\Yaml\Yaml;

class DbalConnectionTest extends AbstractModuleTest
{
    private function getParameters()
    {
        $parameters = Yaml::parse(file_get_contents(__DIR__ . '/../../../../htdocs/app/config/parameters.yml'));
        return $parameters['parameters'];
    }

    public function testGetConnection()
    {
        $p = $this->getParameters();
        $connection = DbalConnection::createDbalConnection(
            $p['database_host'],
            $p['database_name'],
            $p['database_user'],
            $p['database_password'],
            $p['database_port']
        );
        self::assertInstanceOf('Doctrine\DBAL\Connection', $connection);
    }

    public function testGetQueryBuilder()
    {
        $p = $this->getParameters();
        $connection = DbalConnection::createDbalConnection(
            $p['database_host'],
            $p['database_name'],
            $p['database_user'],
            $p['database_password'],
            $p['database_port']
        );
        self::assertInstanceOf('Doctrine\DBAL\Query\QueryBuilder', $connection->createQueryBuilder());
    }
}
