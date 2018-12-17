<?php

namespace OcTest\Modules\Okapi\Integration\Services\Apisrv;

use OcTest\Modules\AbstractModuleTest;
use OcTest\Modules\Okapi\OkapiCredentialsTrait;

class ApiSrvTest extends AbstractModuleTest
{
    use OkapiCredentialsTrait;

    public function testApisrvStatsMethod()
    {
        $client = $this->createOkapiClient();

        $response = $client->get('services/apisrv/stats');

        self::assertCount(4, $response);
        self::assertEquals(1, $response['apps_count']);
    }

    public function testApiSrvInstallationMethod()
    {
        $client = $this->createOkapiClient();

        $response = $client->get('services/apisrv/installation');

        self::assertCount(13, $response);
    }

    public function testApiSrvInstallationsMethod()
    {
        $client = $this->createOkapiClient();

        $response = $client->get('services/apisrv/installations');

        self::assertCount(7, $response);
    }
}
