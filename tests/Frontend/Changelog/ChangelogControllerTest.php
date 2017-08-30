<?php

namespace OcTest\Frontend\Changelog;

use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class ChangelogControllerTest extends WebTestCase
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setUp()
    {
        $this->client = self::createClient();
        $this->container = self::$kernel->getContainer();
        $this->connection = $this->container->get('app.dbal_connection');
        $this->connection->beginTransaction();
    }

    public function test_index_action()
    {
        $controller = $this->container->get('app.controller.changelog_controller');

        $response = $controller->indexAction();
        self::assertInstanceOf(Response::class, $response);
        self::assertContains('Changes in oc-server 3.1', $response->getContent());
    }

    public function tearDown()
    {
        $this->connection->rollBack();
        parent::tearDown();
    }
}
