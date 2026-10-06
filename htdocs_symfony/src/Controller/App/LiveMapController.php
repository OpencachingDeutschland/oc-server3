<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Oc\Repository\CachesRepository;
use Oc\Repository\WaypointsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LiveMapController extends AbstractController
{
    public function __construct(
        private CachesRepository $cachesRepository,
        private WaypointsRepository $waypointsRepository
    ) {}

    #[Route('/livemap', name: 'livemap')]
    public function index(): Response
    {
        return $this->render('app/maps/livemap.html.twig', [
            'initLat'  => 52.3759,
            'initLon'  => 9.7320,
            'initZoom' => 13,
        ]);
    }

    #[Route('/api/caches/waypoints', name: 'api_caches_waypoints')]
    public function waypoints(Request $request): JsonResponse
    {
        $wp = (string)$request->query->get('wp', '');
        if (!$wp) {
            return new JsonResponse(['wpts' => []]);
        }

        $rows = $this->waypointsRepository->fetchWaypointsByWp($wp);

        $wpts = array_map(fn($r) => [
            'lat'         => (float)$r['latitude'],
            'lon'         => (float)$r['longitude'],
            'name'        => $r['type_name'] ?? 'Waypoint',
            'description' => $r['description'] ?? '',
            'subtype'     => (int)$r['subtype'],
        ], $rows);

        return new JsonResponse(['wpts' => $wpts]);
    }

    #[Route('/api/geocode/city', name: 'api_geocode_city')]
    public function geocodeCity(Request $request): JsonResponse
    {
        $q = trim((string)$request->query->get('q', ''));
        if (!$q) {
            return new JsonResponse([]);
        }

        $url = 'https://nominatim.openstreetmap.org/search?format=json&limit=10&q=' . urlencode($q);
        $ctx = stream_context_create(['http' => [
            'header'  => "User-Agent: opencaching.de/1.0\r\nAccept: application/json\r\n",
            'timeout' => 5,
        ]]);

        $body = @file_get_contents($url, false, $ctx);
        if ($body === false) {
            return new JsonResponse([]);
        }

        return new JsonResponse(json_decode($body, true) ?? []);
    }
}
