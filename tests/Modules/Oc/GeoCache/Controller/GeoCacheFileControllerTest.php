<?php

namespace OcTest\Modules\Oc\GeoCache\Controller;

use AppKernel;
use Oc\GeoCache\Controller\GeoCacheFileController;
use OcTest\Modules\TestCase;
use Symfony\Component\HttpFoundation\Request;

class GeoCacheFileControllerTest extends TestCase
{
    public function test_generate_qr_code_throws_unkown_waypoint_exception()
    {
        /** @var GeoCacheFileController $controller */
        $controller = AppKernel::Container()->get(GeoCacheFileController::class);

        $request = new Request(['wp' => 'OC0002']);

        $this->expectException(\InvalidArgumentException::class);

        $controller->generateQrCode($request);
    }
}
