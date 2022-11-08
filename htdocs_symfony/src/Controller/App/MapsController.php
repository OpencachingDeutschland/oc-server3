<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Doctrine\DBAL\Exception;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\Repository\MapsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapsController extends AbstractController
{
    private MapsRepository $mapsRepository;

    public function __construct(MapsRepository $mapsRepository)
    {
        $this->mapsRepository = $mapsRepository;
    }

    /**
     * @Route("/maps", name="maps_index")
     */
    public function mapsController_index(Request $request): Response
    {
        return $this->redirectToRoute('app_map_coords');
    }

    /**
     * @throws RecordsNotFoundException
     * @throws Exception
     * @throws RecordNotFoundException
     * @Route("/mapS/{lat}+{lon}", name="map_show")
     */
    public function showMap(string $lat = '', string $lon = '', bool $centerView = false): Response
    {
        $centerPoint = $this->mapsRepository->determineMapCenterPoint($lat, $lon, $centerView);

        return $this->render(
                'app/maps/index.html.twig', [
                        'mapCenterViewLat' => $centerPoint[0],
                        'mapCenterViewLon' => $centerPoint[1],
                        'mapZoom' => '6',
                        'mapWP' => $centerPoint[2]
                ]
        );
    }
}
