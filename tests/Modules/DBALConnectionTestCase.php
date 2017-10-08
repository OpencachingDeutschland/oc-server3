<?php

namespace OcTest\Modules;

use Doctrine\DBAL\Connection;

/**
 * Class DBALConnection
 *
 * @package OcTest\Modules
 */
class DBALConnectionTestCase extends KernelTestCase
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Set up the property $connection and begin transaction.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->connection = $this->container->get('app.dbal_connection');
        $this->connection->setAutoCommit(false);
        $this->connection->connect();
    }

    /**
     * Rollback transaction.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->connection->rollBack();

        parent::tearDown();
    }

    /**
     * Asserts the result of fetchAll and checks that every row is of expected instance.
     *
     * @param array $result
     * @param string $expectedInstance
     *
     * @return void
     */
    public static function assertRepositoryFetchAll(array $result, $expectedInstance)
    {
        self::assertGreaterThan(0, $result);

        foreach ($result as $row) {
            self::assertInstanceOf($expectedInstance, $row);
        }
    }
}
