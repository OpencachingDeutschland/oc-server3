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
        $qrCode->setSize(400);

        $qrCode
            ->setWriterByName('png')
            ->setMargin(10)
            ->setEncoding('UTF-8')
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0])
            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255])
            ->setLabel('www.opencaching.de', 16, null, LabelAlignment::CENTER)
            ->setValidateResult(false);

        $logo = imagecreatefrompng(__DIR__ . '/../../../../theme/frontend/images/logo/oc-logo.png');
        $qrCodeGenerated = imagecreatefromstring($qrCode->writeString());

        imagecopy(
            $qrCodeGenerated,
            $logo,
            150,
            150,
            0,
            0,
            imagesx($logo),
            imagesy($logo)
        );

        header('Content-Type: image/png');
        imagepng($qrCodeGenerated);
        imagedestroy($qrCodeGenerated);
    }
}
