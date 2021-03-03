<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Oc\Repository\Coordinate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CoordinatesController
 *
 * @package Oc\Controller\Backend
 */
class CoordinatesController extends AbstractController
{
    /**
     * CoordinatesController constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param Request $request
     * @Route("/coordinates", name="coordinates_index")
     *
     * @return Response
     */
    public function index(Request $request)
    : Response {
        return $this->render(
            'backend/coordinates/index.html.twig', []
        );
    }

    /**
     * @param string $lat
     * @param string $lon
     *
     * @return Response
     * @Route("/coordinate/{lat}+{lon}", name="coordinate_by_lat-lon")
     */
    public function convertCoordinates(string $lat, string $lon)
    : Response {
        $convertedCoordinates = [];

        $lat_float = floatval($lat);
        $lon_float = floatval($lon);

        if (is_float($lat_float) && is_float($lon_float)) {
            $coordinate = new Coordinate($lat_float, $lon_float);

            $convertedCoordinates['decimal'] = $coordinate->getDecimal()['lat'] . ' ' . $coordinate->getDecimal()['lon'];
            $convertedCoordinates['decimalMinute'] = $coordinate->getDecimalMinutes()['lat'] . ' ' . $coordinate->getDecimalMinutes()['lon'];
            $convertedCoordinates['decimalMinuteSecond'] = $coordinate->getDecimalMinutesSeconds()['lat'] . ' ' . $coordinate->getDecimalMinutesSeconds()['lon'];
            $convertedCoordinates['GK'] = $coordinate->getGK();
            $convertedCoordinates['QTH'] = $coordinate->getQTH();
            $convertedCoordinates['RD'] = $coordinate->getRD();
            $convertedCoordinates['CH1903'] = $coordinate->getSwissGrid()['coord'];
            $convertedCoordinates['UTM'] = $coordinate->getUTM()['zone'] . $coordinate->getUTM()['letter'] . ' ' . $coordinate->getUTM()['east'] . ' ' . $coordinate->getUTM()['north'];
            $convertedCoordinates['W3W_de'] = $coordinate->getW3W('de');
            $convertedCoordinates['W3W_en'] = $coordinate->getW3W('en');
        }

        return $this->render('backend/coordinates/index.html.twig', ['converted_coordinates' => $convertedCoordinates]);
    }
}
