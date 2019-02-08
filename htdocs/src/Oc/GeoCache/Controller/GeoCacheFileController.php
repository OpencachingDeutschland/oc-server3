<?php

namespace Oc\GeoCache\Controller;

use Oc\GeoCache\Enum\GeoCacheType;
use Oc\GeoCache\Persistence\GeoCache\GeoCacheEntity;
use Oc\GeoCache\Persistence\GeoCache\GeoCacheService;
use Oc\GeoCache\Util;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @return Response
     */
    public function generateQrCode(Request $request): Response
    {
        $waypoint = $request->get('wp');
        $geoCache = $this->geoCacheService->fetchByWaypoint($waypoint);

        if (!$geoCache instanceof GeoCacheEntity) {
            throw new \InvalidArgumentException('the waypoint is not valid!');
        }

        $response = new Response(
            $this->geoCacheUtil->generateQrCodeFromString('https://www.opencaching.de/' . $geoCache->wpOc),
            Response::HTTP_OK,
            [
                'content-type' => 'image/png',
            ]
        );

        if ($request->get('download')) {
            $response->headers->set('content-disposition', 'attachment; filename="' . $waypoint . '.png"');
        }

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/api/geocache/qrCodes/ics")
     * @return Response
     */
    public function generateQrCodeIcs(Request $request): Response
    {
        $waypoint = $request->get('wp');
        $geoCache = $this->geoCacheService->fetchByWaypoint($waypoint);

        if (!$geoCache instanceof GeoCacheEntity && $geoCache->type !== GeoCacheType::EVENT) {
            throw new \InvalidArgumentException('the waypoint is not valid or not an event!');
        }

        $icsString = $this->geoCacheUtil->generateIcsStringFromGeoCache($geoCache);

        if ($request->get('download')) {
            return new Response(
                $icsString,
                Response::HTTP_OK,
                [
                    'content-type' => 'text/calendar; charset=utf-8',
                    'content-disposition' => 'attachment; filename="' . $geoCache->wpOc . '.ics"',
                ]
            );
        }

        return new Response(
            $this->geoCacheUtil->generateQrCodeFromString($icsString),
            Response::HTTP_OK,
            [
                'content-type' => 'image/png',
            ]
        );
    }
}
