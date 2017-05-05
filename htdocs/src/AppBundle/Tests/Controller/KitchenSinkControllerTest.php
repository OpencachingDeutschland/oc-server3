<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class KitchenSinkControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/kitchensink');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
