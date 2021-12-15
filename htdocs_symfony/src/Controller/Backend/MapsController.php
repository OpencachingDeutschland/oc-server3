<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Oc\Repository\CachesRepository;
use Oc\Repository\Exception\RecordsNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MapsController
 *
 * @package Oc\Controller\Backend
 */
class MapsController extends AbstractController
{
    private $connection;

    private $cachesRepository;

    public function __construct(Connection $connection, CachesRepository $cachesRepository)
    {
        $this->connection = $connection;
        $this->cachesRepository = $cachesRepository;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/maps", name="maps_index")
     */
    public function mapsController_index(Request $request)
    : Response {
        return $this->redirectToRoute('backend_map_coords');
    }

    /**
     * @param string $lat
     * @param string $lon
     * @param bool $centerView
     *
     * @return Response
     * @throws RecordsNotFoundException
     *
     * @Route("/mapS/{lat}+{lon}", name="map_show")
     */
    public function showMap(string $lat = '', string $lon = '', bool $centerView = false)
    : Response {
        $mapWP = [];
        $centerPoint = ['lat' => 0, 'lon' => 0, 'count' => 0];

        $mapWP = $this->cachesRepository->fetchAll();

//        $centerPoint['lat'] += $mapWP->latitude;
//        $centerPoint['lon'] += $mapWP->longitude;
//        $centerPoint['count'] ++;

        if ($centerView && ($centerPoint['count'] > 0)) {
            $mapCenterViewLat = $centerPoint['lat'] / $centerPoint['count'];
            $mapCenterViewLon = $centerPoint['lon'] / $centerPoint['count'];
        }
        if ($lat != '' && $lon != '') {
            $mapCenterViewLat = $lat;
            $mapCenterViewLon = $lon;
        } else {
            $mapCenterViewLat = '48.3585';
            $mapCenterViewLon = '10.8613';
        }

        return $this->render(
            'backend/maps/index.html.twig', [
                                              'mapCenterViewLat' => $mapCenterViewLat,
                                              'mapCenterViewLon' => $mapCenterViewLon,
                                              'mapZoom' => '8',
                                              'mapWP' => $mapWP
                                          ]
        );
    }
}
