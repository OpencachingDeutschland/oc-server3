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
     * @Route("/api/geocache/qrCodes")
     */
    public function generateQrCode(Request $request): void
    {
        $waypoint = $request->get('wp');
        $geoCache = $this->geoCacheService->fetchByWaypoint($waypoint);

        if (!$geoCache instanceof GeoCacheEntity) {
            throw new \InvalidArgumentException('the waypoint is not valid!');
        }

        $qrCode = new QrCode('https://www.opencaching.de/' . $geoCache->wpOc);
        $qrCode->setSize(400);

        $qrCode->setWriterByName('png');
        $qrCode->setMargin(10);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255]);
        $qrCode->setLabel('www.opencaching.de', 16, null, LabelAlignment::CENTER);
        $qrCode->setValidateResult(false);

        $logo = imagecreatefrompng(__DIR__ . '/../../../../theme/frontend/images/logo/qr-code-oc-logo.png');
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

        if ($request->get('download')) {
            header('Content-Disposition: attachment; filename="' . $waypoint . '.png"');
        }

        imagepng($qrCodeGenerated);
        imagedestroy($qrCodeGenerated);
    }
}
