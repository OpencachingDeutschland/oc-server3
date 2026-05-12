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
        return $this->render('app/maps/livemap.html.twig', [
            'initLat'  => 52.3759,
            'initLon'  => 9.7320,
            'initZoom' => 13,
        ]);
    }

    #[Route('/api/caches/live', name: 'api_caches_live')]
    public function liveMarkers(Request $request): JsonResponse
    {
        $lat1 = (float)$request->query->get('lat1', 0);
        $lat2 = (float)$request->query->get('lat2', 0);
        $lon1 = (float)$request->query->get('lon1', 0);
        $lon2 = (float)$request->query->get('lon2', 0);

        if ($lat1 >= $lat2 || $lon1 >= $lon2) {
            return new JsonResponse(['count' => 0, 'items' => []]);
        }

        $maxItems = 5000;
        $userId   = $this->security->getUser()?->getId() ?? 0;

        $count = (int)$this->connection->fetchOne(
            'SELECT COUNT(*)
             FROM caches c
             WHERE c.latitude > ? AND c.latitude < ?
               AND c.longitude > ? AND c.longitude < ?
               AND c.status IN (1, 2)',
            [$lat1, $lat2, $lon1, $lon2]
        );

        if ($count > $maxItems) {
            return new JsonResponse(['count' => $count, 'items' => []]);
        }

        $rows = $this->connection->fetchAllAssociative(
            'SELECT
                c.wp_oc                                       AS referenceCode,
                c.name,
                c.latitude                                    AS listingLat,
                c.longitude                                   AS listingLon,
                c.type                                        AS typeId,
                ct.en                                         AS typeName,
                c.size                                        AS sizeId,
                cs.name                                       AS sizeName,
                c.difficulty / 2                              AS difficulty,
                c.terrain / 2                                 AS terrain,
                c.status,
                u.username                                    AS ownerAlias,
                c.user_id                                     AS ownerCode,
                c.date_created                                AS publishedDate,
                IFNULL(sc.toprating, 0)                       AS favoritePoints,
                IFNULL(sc.found, 0)                           AS findCount,
                IF(c.user_id = ?, 1, 0)                       AS isOwned,
                IF(fl.id IS NOT NULL, 1, 0)                   AS isFound,
                MAX(fl.date)                                  AS foundDate,
                IF(pcn.id IS NOT NULL, 1, 0)                  AS hasPCN,
                IF(pcn.id IS NOT NULL
                   AND pcn.latitude  != 0
                   AND pcn.longitude != 0, 1, 0)              AS hasCC,
                pcn.latitude                                  AS ccLat,
                pcn.longitude                                 AS ccLon,
                pcn.description                               AS pcnText
             FROM caches c
             INNER JOIN cache_type ct  ON c.type    = ct.id
             INNER JOIN cache_size cs  ON c.size    = cs.id
             INNER JOIN user u         ON c.user_id = u.user_id
             LEFT  JOIN stat_caches sc ON c.cache_id = sc.cache_id
             LEFT  JOIN cache_logs fl  ON fl.cache_id = c.cache_id
                                      AND fl.user_id = ?
                                      AND fl.type IN (1, 7)
             LEFT  JOIN coordinates pcn ON pcn.cache_id = c.cache_id
                                       AND pcn.user_id  = ?
                                       AND pcn.type     = 2
             WHERE c.latitude  > ? AND c.latitude  < ?
               AND c.longitude > ? AND c.longitude < ?
               AND c.status IN (1, 2)
             GROUP BY c.cache_id
             ORDER BY c.cache_id
             LIMIT ?',
            [$userId, $userId, $userId, $lat1, $lat2, $lon1, $lon2, $maxItems]
        );

        $items = [];
        foreach ($rows as $r) {
            $hasCC = (bool)(int)$r['hasCC'];
            $lat   = $hasCC ? (float)$r['ccLat']  : (float)$r['listingLat'];
            $lon   = $hasCC ? (float)$r['ccLon']  : (float)$r['listingLon'];

            $items[] = [
                'referenceCode' => $r['referenceCode'],
                'name'          => $r['name'],
                'lat'           => $lat,
                'lon'           => $lon,
                'listingLat'    => (float)$r['listingLat'],
                'listingLon'    => (float)$r['listingLon'],
                'geocacheType'  => ['id' => (int)$r['typeId'],  'name' => $r['typeName']],
                'geocacheSize'  => ['id' => (int)$r['sizeId'],  'name' => $r['sizeName']],
                'difficulty'    => (float)$r['difficulty'],
                'terrain'       => (float)$r['terrain'],
                'isDisabled'    => ((int)$r['status'] === 2),
                'isFound'       => (bool)(int)$r['isFound'],
                'foundDate'     => $r['foundDate'] ? date('Y-m-d', strtotime($r['foundDate'])) : '',
                'isOwned'       => (bool)(int)$r['isOwned'],
                'ownerAlias'    => $r['ownerAlias'],
                'ownerCode'     => (string)$r['ownerCode'],
                'publishedDate' => date('Y-m-d', strtotime($r['publishedDate'])),
                'favoritePoints'=> (int)$r['favoritePoints'],
                'findCount'     => (int)$r['findCount'],
                'hasPCN'        => (bool)(int)$r['hasPCN'],
                'pcnText'       => $r['pcnText'] ?? '',
            ];
        }

        return new JsonResponse(['count' => $count, 'items' => $items]);
    }

    #[Route('/api/caches/waypoints', name: 'api_caches_waypoints')]
    public function waypoints(Request $request): JsonResponse
    {
        $wp = (string)$request->query->get('wp', '');
        if (!$wp) {
            return new JsonResponse(['wpts' => []]);
        }

        // Map OC coordinates.subtype → icon system type IDs (matches mapIcons.js cacheTypes)
        $subtypeToIconId = [
            1 => 217, // Parking           → Parking
            2 => 219, // Stage/ref point   → Physical Stage
            3 => 221, // Path              → Trail Head
            4 => 220, // Final             → Final Location
            5 => 222, // Point of interest → Point of Interest
        ];

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
            'typeId'      => $subtypeToIconId[(int)$r['subtype']] ?? 219,
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
