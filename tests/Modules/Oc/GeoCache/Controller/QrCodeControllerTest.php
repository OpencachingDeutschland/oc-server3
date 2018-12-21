<?php

namespace OcTest\Modules\Oc\GeoCache\Controller;

use AppKernel;
use Oc\GeoCache\Controller\QrCodeController;
use OcTest\Modules\TestCase;
use Symfony\Component\HttpFoundation\Request;

class QrCodeControllerTest extends TestCase
{
    public function test_generate_qr_code_throws_unkown_waypoint_exception()
    {
        /** @var QrCodeController $controller */
        $controller = AppKernel::Container()->get(QrCodeController::class);

        $request = new Request(['wp' => 'OC0002']);

        $this->expectException(\InvalidArgumentException::class);

        $controller->generateQrCode($request);
    }
}
