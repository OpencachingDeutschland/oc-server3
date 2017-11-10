<?php

namespace Oc\GeoCache\Controller;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Oc\GeoCache\Persistence\GeoCache\GeoCacheEntity;
use Oc\GeoCache\Persistence\GeoCache\GeoCacheService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QrCodeController extends Controller
{
    /**
     * @var GeoCacheService
     */
    private $geoCacheService;

    public function __construct(GeoCacheService $geoCacheService)
    {
        $this->geoCacheService = $geoCacheService;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/geocache/qrCodes")
     */
    public function generateQrCode(Request $request)
    {
        $geoCache = $this->geoCacheService->fetchByWaypoint($request->get('wp'));

        if (!$geoCache instanceof GeoCacheEntity) {
            throw new \InvalidArgumentException('the waypoint is not valid!');
        }

        $qrCode = new QrCode('https://www.opencaching.de/' . $geoCache->wpOc);
        $qrCode->setSize(300);

        $qrCode
            ->setWriterByName('png')
            ->setMargin(10)
            ->setEncoding('UTF-8')
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0])
            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255])
            ->setLabel('www.opencaching.de', 16, null, LabelAlignment::CENTER)
            ->setLogoPath(__DIR__ . '/../../../../theme/img/logo/oc-logo.png')
            ->setLogoWidth(250)
            ->setValidateResult(false);

        return new Response(
            $qrCode->writeString(), Response::HTTP_OK, ['Content-Type' => $qrCode->getContentType()]
        );
    }
}
