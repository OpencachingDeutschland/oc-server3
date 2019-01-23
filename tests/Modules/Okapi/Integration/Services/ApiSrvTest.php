<?php

namespace OcTest\Modules\Okapi\Integration\Services\Apisrv;

use OcTest\Modules\AbstractModuleTest;
use OcTest\Modules\Okapi\OkapiCredentialsTrait;

class ApiSrvTest extends AbstractModuleTest
{
    use OkapiCredentialsTrait;

    public function testApisrvStatsMethod()
    {
        $response = $this->createOkapiClient()->get('services/apisrv/stats');

        self::assertCount(4, $response);
        self::assertEquals(1, $response['apps_count']);
    }

    public function testApiSrvInstallationMethod()
    {
        $response = $this->createOkapiClient()->get('services/apisrv/installation');

        self::assertCount(14, $response);
    }

    public function testApiSrvInstallationsMethod()
    {
        $response = $this->createOkapiClient()->get('services/apisrv/installations');

        self::assertCount(7, $response);
    }
}
