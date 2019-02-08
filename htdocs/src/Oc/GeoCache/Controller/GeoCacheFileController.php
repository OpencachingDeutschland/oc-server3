<?php

namespace Oc\GeoCache\Controller;

use Oc\GeoCache\Enum\GeoCacheType;
use Oc\GeoCache\Persistence\GeoCache\GeoCacheEntity;
use Oc\GeoCache\Persistence\GeoCache\GeoCacheService;
use Oc\GeoCache\Util;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GeoCacheFileController extends Controller
{
    /**
     * @var GeoCacheService
     */
    private $geoCacheService;

    /**
     * @var Util
     */
    private $geoCacheUtil;

    public function __construct(GeoCacheService $geoCacheService, Util $geoCacheUtil)
    {
        $this->geoCacheService = $geoCacheService;
        $this->geoCacheUtil = $geoCacheUtil;
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

        header('Content-Type: image/png');

        if ($request->get('download')) {
            header('Content-Disposition: attachment; filename="' . $waypoint . '.png"');
        }

        $this->geoCacheUtil->generateQrCodeFromString('https://www.opencaching.de/' . $geoCache->wpOc);
    }

    /**
     * @param Request $request
     * @Route("/api/geocache/qrCodes/ics")
     */
    public function generateQrCodeIcs(Request $request): void
    {
        $waypoint = $request->get('wp');
        $geoCache = $this->geoCacheService->fetchByWaypoint($waypoint);

        if (!$geoCache instanceof GeoCacheEntity && $geoCache->type !== GeoCacheType::EVENT) {
            throw new \InvalidArgumentException('the waypoint is not valid or not an event!');
        }

        $icsString = $this->geoCacheUtil->generateIcsStringFromGeoCache($geoCache);

        if ($request->get('download')) {
            header('Content-Type: text/calendar; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $geoCache->wpOc . '.ics"');

            echo $icsString;
            die();
        }

        header('Content-Type: image/png');
        $this->geoCacheUtil->generateQrCodeFromString($icsString);
    }
}
