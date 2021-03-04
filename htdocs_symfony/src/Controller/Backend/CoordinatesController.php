<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Oc\Form\CoordinatesFormType;
use Oc\Repository\CoordinatesRepository;
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
    private $coordinatesRepository;

    /**
     * CoordinatesController constructor.
     *
     * @param CoordinatesRepository $coordinatesRepository
     */
    public function __construct(CoordinatesRepository $coordinatesRepository)
    {
        $this->coordinatesRepository = $coordinatesRepository;
    }

    /**
     * @param Request $request
     * @Route("/coordinates", name="coordinates_index")
     *
     * @return Response
     */
    public function index(Request $request)
    : Response {
        $fetchedCoordinates = '';
        $form = $this->createForm(CoordinatesFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();

            $fetchedCoordinates = $this->getCoordinatesForSearchField($inputData['content_coordinates_searchfield']);
        }

        if ($fetchedCoordinates === '') {
            return $this->render(
                'backend/coordinates/index.html.twig', ['coordinatesForm' => $form->createView()]
            );
        } else {
            return $this->render(
                'backend/coordinates/index.html.twig', ['coordinatesForm' => $form->createView(), 'coordinates_by_searchfield' => $fetchedCoordinates]
            );
        }
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
            $c = &$this->coordinatesRepository;
            $c->setLatLon($lat_float, $lon_float);

            $convertedCoordinates['decimal'] = $c->getDecimal()['lat'] . ' ' . $c->getDecimal()['lon'];
            $convertedCoordinates['degreeMinute'] = $c->getDegreeMinutes()['lat'] . ' ' . $c->getDegreeMinutes()['lon'];
            $convertedCoordinates['degreeMinuteSecond'] = $c->getDegreeMinutesSeconds()['lat'] . ' ' . $c->getDegreeMinutesSeconds()['lon'];
            $convertedCoordinates['GK'] = $c->getGK();
            $convertedCoordinates['QTH'] = $c->getQTH();
            $convertedCoordinates['RD'] = $c->getRD();
            $convertedCoordinates['CH1903'] = $c->getSwissGrid()['coord'];
            $convertedCoordinates['UTM'] = $c->getUTM()['zone'] . $c->getUTM()['letter'] . ' ' . $c->getUTM()['east'] . ' ' . $c->getUTM()['north'];
            $convertedCoordinates['W3W_de'] = $c->getW3W('de');
            $convertedCoordinates['W3W_en'] = $c->getW3W();
        }

        return $this->render('backend/coordinates/index.html.twig', ['converted_coordinates' => $convertedCoordinates]);
    }

    /**
     * @param string $searchtext
     *
     * @return array
     */
    public function getCoordinatesForSearchField(string $searchtext)
    : array {
        $searchtext = trim($searchtext);
        $searchtext = preg_replace("/[^0-9.,+\- ]/", "", $searchtext);

        $arr = preg_split('/\s+/', $searchtext);

        $this->coordinatesRepository->setLatLon((float) $arr[0], (float) $arr[1]);

        return $this->coordinatesRepository->getAllCoordinatesFormatsAsArray();
    }
}
