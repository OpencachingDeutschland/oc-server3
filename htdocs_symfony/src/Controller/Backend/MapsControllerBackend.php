<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Repository\CachesRepository;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MapsControllerBackend
 *
 * @package Oc\Controller\Backend
 */
class MapsControllerBackend extends AbstractController
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
     * @throws Exception
     * @throws RecordNotFoundException
     * @Route("/mapS/{lat}+{lon}", name="map_show")
     */
    public function showMap(string $lat = '', string $lon = '', bool $centerView = false)
    : Response {
        $mapCenterViewLat = '48.3585';
        $mapCenterViewLon = '10.8613';
        $mapWP = $this->cachesRepository->fetchAll();

        // Mittelpunkt für die Kartenzentrierung bestimmen
        if ($centerView) {
            $centerPoint = ['lat' => 0, 'lon' => 0, 'count' => 0];

            // Schleife, die alle WP aufsummiert, damit danach der Durchschnitt gebildet werden kann
            foreach ($mapWP as $WP) {
                $centerPoint['lat'] += $WP->latitude;
                $centerPoint['lon'] += $WP->longitude;
                $centerPoint['count'] ++;
            }
            if ($centerPoint['count'] > 0) {
                $mapCenterViewLat = $centerPoint['lat'] / $centerPoint['count'];
                $mapCenterViewLon = $centerPoint['lon'] / $centerPoint['count'];
            }
        } elseif ($lat != '' && $lon != '') {
            $mapCenterViewLat = $lat;
            $mapCenterViewLon = $lon;
        }

        return $this->render(
            'backend/maps/index.html.twig', [
                                              'mapCenterViewLat' => $mapCenterViewLat,
                                              'mapCenterViewLon' => $mapCenterViewLon,
                                              'mapZoom' => '6',
                                              'mapWP' => $mapWP
                                          ]
        );
    }
}
