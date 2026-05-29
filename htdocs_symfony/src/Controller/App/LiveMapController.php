<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LiveMapController extends AbstractController
{
    public function __construct(
        private Connection $connection,
        private Security $security
    ) {}

    #[Route('/livemap', name: 'livemap')]
    public function index(): Response
    {
        // TODO: read per-user homeLat/homeLon/defaultZoom from a user-settings table
        // once Settings UI exists; fall back to these defaults when unset.
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

        $rows = $this->connection->fetchAllAssociative(
            'SELECT co.latitude, co.longitude, co.description, co.subtype,
                    ct.name AS type_name
             FROM coordinates co
             JOIN caches c ON co.cache_id = c.cache_id
             LEFT JOIN coordinates_type ct ON co.subtype = ct.id
             WHERE c.wp_oc = ? AND co.type = 1 AND co.user_id IS NULL
             ORDER BY co.id',
            [$wp]
        );

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
