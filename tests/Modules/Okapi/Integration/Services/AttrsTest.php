<?php

namespace OcTest\Modules\Okapi\Integration\Services\Apisrv;

use OcTest\Modules\AbstractModuleTest;
use OcTest\Modules\Okapi\OkapiCredentialsTrait;

class AttrsTest extends AbstractModuleTest
{
    use OkapiCredentialsTrait;

    public function testAttrsAttributeIndexMethodNeedsConsumerKey(): void
    {
        $client = $this->createOkapiClient();

        $response = $client->get('services/attrs/attribute_index');

        self::assertArrayHasKey('error', $response);
        self::assertContains('(Level 1 Authentication)', $response['error']['developer_message']);
    }

    public function testAttrsAttributeIndexMethodReturnsAttributes(): void
    {
        $client = $this->createOkapiClient();

        $response = $client->get('services/attrs/attribute_index', ['consumer_key' => $this->getConsumerKey()]);

        self::assertCount(77, $response);
    }

    public function testAttrsAttributeMethodNeedsConsumerKey(): void
    {
        $client = $this->createOkapiClient();

        $response = $client->get('services/attrs/attribute');

        self::assertArrayHasKey('error', $response);
        self::assertContains('(Level 1 Authentication)', $response['error']['developer_message']);
    }

    public function testAttrsAttributeMethodReturnsAttribute(): void
    {
        $client = $this->createOkapiClient();

        $response = $client->get(
            'services/attrs/attribute',
            ['consumer_key' => $this->getConsumerKey(), 'acode' => 'A1']
        );

        self::assertArrayHasKey('name', $response);
        self::assertEquals('Listed at Opencaching only', $response['name']);
    }

    public function testAttrsAttributesMethodNeedsConsumerKey(): void
    {
        $client = $this->createOkapiClient();

        $response = $client->get('services/attrs/attributes');

        self::assertArrayHasKey('error', $response);
        self::assertContains('(Level 1 Authentication)', $response['error']['developer_message']);
    }

    public function testAttrsAttributesMethodReturnsAttribute(): void
    {
        $client = $this->createOkapiClient();

        $response = $client->get(
            'services/attrs/attributes',
            ['consumer_key' => $this->getConsumerKey(), 'acodes' => 'A1|A2']
        );

        self::assertCount(2, $response);

        self::assertArrayHasKey('A1', $response);
        self::assertArrayHasKey('A2', $response);

        self::assertEquals('Listed at Opencaching only', $response['A1']['name']);
    }
}
