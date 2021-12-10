<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Oc\Repository\CachesRepository;
use Oc\Repository\Exception\RecordNotFoundException;
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
     * @param string $wp_oc
     * @param string $lon
     * @param string $lat
     *
     * @return Response
     * @throws RecordNotFoundException
     *
     * @Route("/mapS/{wp_oc}+{lat}+{lon}", name="map_single")
     */
    public function loadMapInfoSingle(string $wp_oc = '', string $lat = '', string $lon = '')
    : Response {
        $mapWP = [];

        if (!empty($wp_oc)) {
            $mapWP = $this->cachesRepository->fetchOneBy(['wp_oc' => $wp_oc]);
        }

        if ($lat != '' && $lon != '') {
            $mapCenterViewLat = $lat;
            $mapCenterViewLon = $lon;
        } else {
            $mapCenterViewLat = '48.3585';
            $mapCenterViewLon = '10.8613';
        }
        return $this->render(
            'backend/maps/index.html.twig', ['mapCenterViewLat' => $mapCenterViewLat, 'mapCenterViewLon' => $mapCenterViewLon, 'mapZoom' => '10', 'mapWP' => [$mapWP]]
        );
    }

    /**
     * @param string $wp_oc
     * @param string $lon
     * @param string $lat
     *
     * @return Response
     * @throws RecordNotFoundException
     *
     * @Route("/mapM", name="map_multiple")
     */
    public function loadMapInfoMultiple(string $wp_oc = '', string $lat = '', string $lon = '')
    : Response {
        $mapCenterViewLat = '';
        $mapCenterViewLon = '';
        $mapWP = [];
        $mapWP2 = [];
        $centerPoint = ['lat' => 0, 'lon' => 0, 'count' => 0];

        if (!empty($wp_oc)) {
            $mapWP = $this->cachesRepository->fetchOneBy(['wp_oc' => $wp_oc]);


            $mapWP2 = $this->cachesRepository->fetchOneBy(['wp_oc' => $wp_oc]);
            //            $mapWP2 = $mapWP;
            $mapWP2->longitude = '6.904883';
            $mapWP2->latitude = '51.999';
            $mapWP2->name = 'Teest';
        }

        if ($lat != '' && $lon != '') {
            $mapCenterViewLat = $lat;
            $mapCenterViewLon = $lon;
        } elseif ($centerPoint['count'] > 0) {
            $mapCenterViewLat = $centerPoint['lat'] / $centerPoint['count'];
            $mapCenterViewLon = $centerPoint['lon'] / $centerPoint['count'];
        } else {
            $mapCenterViewLat = '48.3585';
            $mapCenterViewLon = '10.8613';
        }
        //dd([$mapWP, $mapWP2]);
        //        die();
        return $this->render(
            'backend/maps/index.html.twig', ['mapCenterViewLat' => $mapCenterViewLat, 'mapCenterViewLon' => $mapCenterViewLon, 'mapZoom' => '13', 'mapWP' => [$mapWP, $mapWP2]]
        );
    }
}
