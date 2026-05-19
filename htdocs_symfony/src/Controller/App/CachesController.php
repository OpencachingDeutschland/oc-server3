<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Form\CachesFormType;
use Oc\Repository\CachesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CachesController extends AbstractController
{
    public function __construct(
        private CachesRepository $cachesRepository,
        private Connection $connection,
        private Security $security,
    ) {}

    #[Route("/caches", name: "caches_index")]
    public function cachesController_index(Request $request): Response
    {
        $fetchedCaches = '';

        $form = $this->createForm(CachesFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();
            $fetchedCaches = $this->cachesRepository->getCachesForSearchField($inputData['content_searchfield']);

            if (empty($fetchedCaches)) {
                if (preg_match('/[a-zA-Z0-9]{3,5}/', $inputData['content_searchfield'])
                    && !str_starts_with($inputData['content_searchfield'], 'OC')
                    && !str_starts_with($inputData['content_searchfield'], 'GC')
                ) {
                    $fetchedCaches = $this->cachesRepository->getCachesForSearchFieldWPOnly($inputData['content_searchfield']);
                }
            }
        }

        return $this->render('app/caches/search.html.twig', [
            'cachesForm' => $form->createView(),
            'caches_by_searchfield' => $fetchedCaches,
        ]);
    }

    #[Route("/cache/{wpID}", name: "cache_by_wp_oc_gc")]
    public function detail(string $wpID): Response
    {
        return $this->render('app/caches/detail.html.twig', ['wp' => strtoupper($wpID)]);
    }

    /**
     * @throws Exception
     */
    #[Route("/api/cache/{wp}", name: "api_cache_detail", methods: ["GET"])]
    public function apiDetail(string $wp): JsonResponse
    {
        $wp = strtoupper($wp);

        $cache = $this->connection->fetchAssociative(
            'SELECT
                c.cache_id, c.wp_oc, c.name,
                c.latitude, c.longitude,
                c.difficulty / 2 AS difficulty,
                c.terrain / 2 AS terrain,
                c.country, c.date_hidden, c.date_created, c.wp_gc,
                c.type AS type_id,
                c.size AS size_id,
                c.status AS status_id,
                IF(c.logpw != \'\', 1, 0) AS logpw,
                c.needs_maintenance, c.listing_outdated,
                ct.en AS type_name, ct.svg_name,
                cs.name AS size_name,
                cst.en AS status_en,
                u.user_id AS owner_id, u.username AS owner_name, u.uuid AS owner_uuid,
                u.date_created AS owner_joined,
                IFNULL(sc.found, 0) AS find_count,
                IFNULL(sc.toprating, 0) AS rating_count,
                co_name.name AS country_name
             FROM caches c
             JOIN cache_type ct    ON c.type    = ct.id
             JOIN cache_size cs    ON c.size    = cs.id
             JOIN cache_status cst ON c.status  = cst.id
             JOIN user u           ON c.user_id = u.user_id
             LEFT JOIN stat_caches sc ON c.cache_id = sc.cache_id
             LEFT JOIN countries co_name ON c.country = co_name.short
             WHERE c.wp_oc = ?',
            [$wp]
        );

        if (!$cache) {
            return new JsonResponse(['error' => 'Cache not found'], 404);
        }

        $cacheId   = (int)$cache['cache_id'];
        $typeId    = (int)$cache['type_id'];
        $statusId  = (int)$cache['status_id'];
        $user      = $this->security->getUser();
        $userId    = $user?->getUserId() ?? 0;
        $userName  = $user?->getUserIdentifier() ?? null;
        $isOwner   = $userId > 0 && (int)$cache['owner_id'] === $userId;
        $isEvent   = $typeId === 6;

        // OC DB type ID → OKAPI type name (matches ocToGCCacheTypes keys)
        $okapiTypes = [
            1  => 'Unknown', 2  => 'Traditional', 3 => 'Multi',
            4  => 'Virtual', 5  => 'Webcam',      6 => 'Event',
            7  => 'Quiz',    8  => 'Math/Physics',9 => 'Moving',
            10 => 'Drive-in',
        ];
        $okapiType = $okapiTypes[$typeId] ?? 'Unknown';

        // OC DB size name → OKAPI size2 token (lowercase keys of ocToGCSizeTypes)
        $okapiSizeMap = [
            'no container' => 'none',
            'nano'         => 'nano',
            'micro'        => 'micro',
            'small'        => 'small',
            'normal'       => 'regular',
            'large'        => 'large',
            'very large'   => 'xlarge',
            'other size'   => 'other',
        ];
        $okapiSize = $okapiSizeMap[strtolower((string)$cache['size_name'])] ?? 'other';

        // OC DB status → OKAPI status string (matches statusMap keys in uniCache.js)
        $okapiStatusMap = [
            1 => 'Available',
            2 => 'Temporarily unavailable',
            3 => 'Archived',
            4 => 'Archived',
            5 => 'Archived',
            6 => 'Archived',
            7 => 'Archived',
        ];
        $okapiStatus = $okapiStatusMap[$statusId] ?? 'Archived';

        // Description — prefer cache country language, else EN, else first available
        $desc = $this->connection->fetchAssociative(
            'SELECT cd.desc, cd.hint, cd.short_desc, cd.desc_html, cd.desc_dark_unsafe, cd.language
             FROM cache_desc cd
             WHERE cd.cache_id = ?
             ORDER BY cd.language = ? DESC, cd.language = \'EN\' DESC
             LIMIT 1',
            [$cacheId, strtoupper($cache['country'] ?: 'DE')]
        );

        // Listing waypoints (additional waypoints placed by owner)
        $waypoints = $this->connection->fetchAllAssociative(
            'SELECT co.latitude, co.longitude, co.description, ct.name AS type_name, ct.id AS type_id
             FROM coordinates co
             LEFT JOIN coordinates_type ct ON co.subtype = ct.id
             WHERE co.cache_id = ? AND co.type = 1 AND co.user_id IS NULL
             ORDER BY co.id',
            [$cacheId]
        );

        // Attributes
        $attributes = $this->connection->fetchAllAssociative(
            'SELECT ca.id, ca.name, ca.icon
             FROM caches_attributes cxa
             JOIN cache_attrib ca ON cxa.attrib_id = ca.id
             WHERE cxa.cache_id = ?
             ORDER BY ca.id',
            [$cacheId]
        );

        // Logs (most recent 30)
        $logs = $this->connection->fetchAllAssociative(
            'SELECT cl.id, cl.uuid, cl.type, lt.en AS type_name,
                    DATE_FORMAT(cl.date, \'%Y-%m-%d\') AS date,
                    cl.text, cl.text_html, cl.user_id,
                    u.username
             FROM cache_logs cl
             JOIN user u       ON cl.user_id = u.user_id
             LEFT JOIN log_types lt ON cl.type = lt.id
             WHERE cl.cache_id = ? AND cl.gdpr_deletion = 0
             ORDER BY cl.date DESC
             LIMIT 30',
            [$cacheId]
        );

        // Map OC log type IDs → OKAPI log type names (for latest_logs in OKAPI shape)
        $okapiLogTypeNames = [
            1  => 'Found it',
            2  => "Didn't find it",
            3  => 'Comment',
            7  => 'Attended',
            8  => 'Will attend',
            9  => 'Archived',
            10 => 'Ready to search',
            11 => 'Temporarily unavailable',
        ];

        // OKAPI latest_logs (slim — type/date pairs)
        $latestLogs = array_map(fn($l) => [
            'type' => $okapiLogTypeNames[(int)$l['type']] ?? (string)$l['type_name'],
            'date' => $l['date'],
            'user' => ['username' => $l['username']],
        ], $logs);

        // User's own logs on this cache — find latest Found / DNF for foundDate inference
        $isFound = false;
        $isNotFound = false;
        if ($userId) {
            foreach ($logs as $l) {
                if ((int)$l['user_id'] !== $userId) continue;
                $t = (int)$l['type'];
                if ($t === 1)      $isFound = true;
                elseif ($t === 2)  $isNotFound = true;
                if ($isFound && $isNotFound) break;
            }
        }

        // User's PCN row (note + corrected coords + remembered log password share
        // one row in `coordinates` type=2)
        $noteRow = $userId ? $this->connection->fetchAssociative(
            'SELECT description, latitude, longitude, logpw FROM coordinates
             WHERE cache_id=? AND user_id=? AND type=2 ORDER BY id DESC LIMIT 1',
            [$cacheId, $userId]
        ) : null;
        $hasUserCoords = $noteRow
            && ((float)$noteRow['latitude'] !== 0.0 || (float)$noteRow['longitude'] !== 0.0);

        // Watch / recommendation status
        $isWatched = false;
        $isRecommended = false;
        if ($userId) {
            $isWatched = (bool)$this->connection->fetchOne(
                'SELECT 1 FROM cache_watches WHERE cache_id=? AND user_id=?',
                [$cacheId, $userId]
            );
            $isRecommended = (bool)$this->connection->fetchOne(
                'SELECT 1 FROM cache_rating WHERE cache_id=? AND user_id=?',
                [$cacheId, $userId]
            );
        }

        // Region from cache_location (adm1 = state-equivalent in OC)
        $region = $this->connection->fetchOne(
            'SELECT adm1 FROM cache_location WHERE cache_id=?',
            [$cacheId]
        ) ?: null;

        // Build OKAPI alt_wpts:
        //   - Owner-placed waypoints (subtype mapping is rough; OKAPI uses string types)
        //   - Plus user-coords entry if user has corrected coordinates
        $altWpts = [];
        foreach ($waypoints as $w) {
            $altWpts[] = [
                'type'        => 'reference',
                'location'    => sprintf('%s|%s', $w['latitude'], $w['longitude']),
                'name'        => $w['type_name'] ?? 'Waypoint',
                'description' => $w['description'] ?? '',
            ];
        }
        if ($hasUserCoords) {
            $altWpts[] = [
                'type'     => 'user-coords',
                'location' => sprintf('%s|%s', $noteRow['latitude'], $noteRow['longitude']),
                'name'     => 'Corrected coordinates',
                'description' => '',
            ];
        }

        // Available log types for current user (OC numeric IDs)
        $logTypeIds = [];
        if ($userId) {
            $logTypeIds = $isEvent ? [7, 8, 3] : [1, 2, 3];
            if ($isOwner) {
                $logTypeIds = array_merge($logTypeIds, [11, 9]);
            }
        }

        // Owner aux info
        $ownerStats = $this->connection->fetchAssociative(
            'SELECT IFNULL(found, 0) AS found, IFNULL(hidden, 0) AS hidden FROM stat_user WHERE user_id=?',
            [(int)$cache['owner_id']]
        ) ?: ['found' => 0, 'hidden' => 0];

        // OKAPI-shaped raw object (consumed by frontend ocToUniCacheWP)
        $oc = [
            'code'             => $cache['wp_oc'],
            'name'             => $cache['name'],
            'location'         => sprintf('%s|%s', $cache['latitude'], $cache['longitude']),
            'status'           => $okapiStatus,
            'type'             => $okapiType,
            'size2'            => $okapiSize,
            'difficulty'       => (float)$cache['difficulty'],
            'terrain'          => (float)$cache['terrain'],
            'date_created'     => $cache['date_created'] ?: $cache['date_hidden'],
            'date_hidden'      => $cache['date_hidden'],
            'country2'         => $cache['country_name'] ?: $cache['country'],
            'country_code'     => $cache['country'],
            'region'           => $region,
            'recommendations'  => (int)$cache['rating_count'],
            'founds'           => (int)$cache['find_count'],
            'req_passwd'       => (bool)$cache['logpw'],
            'is_found'         => $isFound,
            'is_not_found'     => $isNotFound,
            'is_recommended'   => $isRecommended,
            'is_watched'       => $isWatched,
            'my_notes'         => $noteRow['description'] ?? null,
            'owner' => [
                'username'    => $cache['owner_name'],
                'uuid'        => $cache['owner_uuid'],
                'profile_url' => sprintf('/viewprofile.php?userid=%d', (int)$cache['owner_id']),
            ],
            'alt_wpts'         => $altWpts,
            'latest_logs'      => $latestLogs,
            'description'      => $desc['desc'] ?? '',
            'short_description'=> $desc['short_desc'] ?? '',
            'hint2'            => $desc['hint'] ?? '',
            'attr_acodes'      => array_map(fn($a) => (int)$a['id'], $attributes),
        ];

        // Aux: data not in OKAPI shape but needed by the cache detail UI
        $aux = [
            'logs'        => array_map(fn($l) => [
                'id'       => (int)$l['id'],
                'uuid'     => $l['uuid'],
                'type'     => (int)$l['type'],
                'typeName' => $okapiLogTypeNames[(int)$l['type']] ?? (string)$l['type_name'],
                'date'     => $l['date'],
                'username' => $l['username'],
                'text'     => $l['text'],
                'textHtml' => (bool)$l['text_html'],
                'itsMine'  => $userId > 0 && (int)$l['user_id'] === $userId,
            ], $logs),
            'waypoints'   => array_map(fn($w) => [
                'lat'         => (float)$w['latitude'],
                'lon'         => (float)$w['longitude'],
                'typeName'    => $w['type_name'] ?? 'Waypoint',
                'typeId'      => (int)($w['type_id'] ?? 0),
                'description' => $w['description'] ?? '',
            ], $waypoints),
            'attributes'  => array_map(fn($a) => [
                'id'   => (int)$a['id'],
                'name' => $a['name'],
                'icon' => $a['icon'],
            ], $attributes),
            'owner'       => [
                'username'      => $cache['owner_name'],
                'userId'        => (int)$cache['owner_id'],
                'profileUrl'    => sprintf('/viewprofile.php?userid=%d', (int)$cache['owner_id']),
                'findCount'     => (int)($ownerStats['found'] ?? 0),
                'hideCount'     => (int)($ownerStats['hidden'] ?? 0),
                'joinedDate'    => $cache['owner_joined'] ? substr($cache['owner_joined'], 0, 10) : null,
            ],
            'logTypes'    => $logTypeIds,
            'wpGc'        => $cache['wp_gc'] ?: '',
            'svgName'     => $cache['svg_name'],
            'descHtml'    => (bool)($desc['desc_html'] ?? true),
            'descDarkUnsafe'   => (bool)($desc['desc_dark_unsafe'] ?? false),
            'needsMaintenance' => (bool)$cache['needs_maintenance'],
            'listingOutdated'  => (bool)$cache['listing_outdated'],
            'myLogpw'          => $noteRow['logpw'] ?? '',
        ];

        $context = [
            'userId'    => $userId,
            'userName'  => $userName,
            'isOwner'   => $isOwner,
        ];

        return new JsonResponse(['oc' => $oc, 'aux' => $aux, 'context' => $context]);
    }

    #[Route("/api/cache/{wp}/note", name: "api_cache_note_save", methods: ["POST"])]
    public function saveNote(string $wp, Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) return new JsonResponse(['error' => 'Not authenticated'], 401);

        $userId = $user->getUserId();
        $wp = strtoupper($wp);
        $body = json_decode($request->getContent(), true);
        $text = trim((string)($body['text'] ?? ''));

        $cache = $this->connection->fetchAssociative('SELECT cache_id FROM caches WHERE wp_oc = ?', [$wp]);
        if (!$cache) return new JsonResponse(['error' => 'Cache not found'], 404);
        $cacheId = (int)$cache['cache_id'];

        $existing = $this->connection->fetchAssociative(
            'SELECT id FROM coordinates WHERE cache_id=? AND user_id=? AND type=2 ORDER BY id DESC LIMIT 1',
            [$cacheId, $userId]
        );

        if ($text === '') {
            if ($existing) {
                $this->connection->executeStatement('DELETE FROM coordinates WHERE id=?', [(int)$existing['id']]);
            }
            return new JsonResponse(['saved' => false]);
        }

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        if ($existing) {
            $this->connection->executeStatement(
                'UPDATE coordinates SET description=?, last_modified=? WHERE id=?',
                [$text, $now, (int)$existing['id']]
            );
        } else {
            $this->connection->executeStatement(
                'INSERT INTO coordinates (cache_id, user_id, type, subtype, latitude, longitude, description, date_created, last_modified)
                 VALUES (?,?,2,0,0,0,?,?,?)',
                [$cacheId, $userId, $text, $now, $now]
            );
        }
        return new JsonResponse(['saved' => true]);
    }

    #[Route("/api/cache/{wp}/logpw", name: "api_cache_logpw_save", methods: ["POST"])]
    public function saveLogpw(string $wp, Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) return new JsonResponse(['error' => 'Not authenticated'], 401);

        $userId = $user->getUserId();
        $wp = strtoupper($wp);
        $body = json_decode($request->getContent(), true);
        $logpw = substr(trim((string)($body['logpw'] ?? '')), 0, 20);

        $cache = $this->connection->fetchAssociative('SELECT cache_id FROM caches WHERE wp_oc = ?', [$wp]);
        if (!$cache) return new JsonResponse(['error' => 'Cache not found'], 404);
        $cacheId = (int)$cache['cache_id'];

        $existing = $this->connection->fetchAssociative(
            'SELECT id, description, latitude, longitude FROM coordinates
             WHERE cache_id=? AND user_id=? AND type=2 ORDER BY id DESC LIMIT 1',
            [$cacheId, $userId]
        );

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        if ($existing) {
            // If clearing and the row has no other content, delete it.
            $hasOther = ($existing['description'] !== null && $existing['description'] !== '')
                || (float)$existing['latitude'] !== 0.0
                || (float)$existing['longitude'] !== 0.0;
            if ($logpw === '' && !$hasOther) {
                $this->connection->executeStatement('DELETE FROM coordinates WHERE id=?', [(int)$existing['id']]);
                return new JsonResponse(['saved' => true, 'logpw' => '']);
            }
            $this->connection->executeStatement(
                'UPDATE coordinates SET logpw=?, last_modified=? WHERE id=?',
                [$logpw, $now, (int)$existing['id']]
            );
        } elseif ($logpw !== '') {
            $this->connection->executeStatement(
                'INSERT INTO coordinates (cache_id, user_id, type, subtype, latitude, longitude, description, logpw, date_created, last_modified)
                 VALUES (?,?,2,0,0,0,"",?,?,?)',
                [$cacheId, $userId, $logpw, $now, $now]
            );
        }
        return new JsonResponse(['saved' => true, 'logpw' => $logpw]);
    }

    #[Route("/api/cache/{wp}/coords", name: "api_cache_coords_save", methods: ["POST"])]
    public function saveCoords(string $wp, Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) return new JsonResponse(['error' => 'Not authenticated'], 401);

        $userId = $user->getUserId();
        $wp = strtoupper($wp);
        $body = json_decode($request->getContent(), true);
        $lat = (float)($body['lat'] ?? 0);
        $lon = (float)($body['lon'] ?? 0);

        $cache = $this->connection->fetchAssociative('SELECT cache_id FROM caches WHERE wp_oc = ?', [$wp]);
        if (!$cache) return new JsonResponse(['error' => 'Cache not found'], 404);
        $cacheId = (int)$cache['cache_id'];

        $existing = $this->connection->fetchAssociative(
            'SELECT id FROM coordinates WHERE cache_id=? AND user_id=? AND type=2 ORDER BY id DESC LIMIT 1',
            [$cacheId, $userId]
        );

        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        if ($existing) {
            $this->connection->executeStatement(
                'UPDATE coordinates SET latitude=?, longitude=?, last_modified=? WHERE id=?',
                [$lat, $lon, $now, (int)$existing['id']]
            );
        } else {
            $this->connection->executeStatement(
                'INSERT INTO coordinates (cache_id, user_id, type, subtype, latitude, longitude, description, date_created, last_modified)
                 VALUES (?,?,2,0,?,?,"",?,?)',
                [$cacheId, $userId, $lat, $lon, $now, $now]
            );
        }
        return new JsonResponse(['saved' => true]);
    }

    #[Route("/api/cache/{wp}/log", name: "api_cache_log_create", methods: ["POST"])]
    public function createLog(string $wp, Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) return new JsonResponse(['error' => 'Not authenticated'], 401);

        $userId = $user->getUserId();
        $wp = strtoupper($wp);
        $body = json_decode($request->getContent(), true);
        $type = (int)($body['type'] ?? 3);
        $date = (string)($body['date'] ?? date('Y-m-d'));
        $text = trim((string)($body['text'] ?? ''));
        $submittedPw = trim((string)($body['password'] ?? ''));

        $cache = $this->connection->fetchAssociative('SELECT cache_id, logpw FROM caches WHERE wp_oc = ?', [$wp]);
        if (!$cache) return new JsonResponse(['error' => 'Cache not found'], 404);
        $cacheId = (int)$cache['cache_id'];
        $cacheLogpw = (string)$cache['logpw'];

        // Found-type logs (1=Found, 7=Attended) on a cache with a log password
        // must submit a matching password. OC compares case-insensitively.
        if ($cacheLogpw !== '' && in_array($type, [1, 7], true)) {
            if ($submittedPw === '') {
                return new JsonResponse(['error' => 'Log password required for this cache'], 422);
            }
            if (strcasecmp($submittedPw, $cacheLogpw) !== 0) {
                return new JsonResponse(['error' => 'Incorrect log password'], 422);
            }
        }

        // Found (1) / Attended (7) are one-per-user-per-cache. Reject a
        // second one rather than silently letting the user double-log.
        if (in_array($type, [1, 7], true)) {
            $dup = (int)$this->connection->fetchOne(
                'SELECT COUNT(*) FROM cache_logs WHERE cache_id=? AND user_id=? AND type=?',
                [$cacheId, $userId, $type]
            );
            if ($dup > 0) {
                $name = $type === 1 ? 'Found it' : 'Attended';
                return new JsonResponse(['error' => "You already have a $name log on this cache — edit that one instead"], 422);
            }
        }

        // Normalize date to datetime
        if (strlen($date) === 10) $date .= ' 00:00:00';

        // uuid, date_created, entry_last_modified, last_modified, log_last_modified, order_date
        // are filled by trigger `cacheLogsBeforeInsert`. picture has no default — set it explicitly.
        try {
            $this->connection->executeStatement(
                'INSERT INTO cache_logs (node, cache_id, user_id, type, date, text, text_html, text_htmledit, picture)
                 VALUES (4, ?, ?, ?, ?, ?, 0, 0, 0)',
                [$cacheId, $userId, $type, $date, $text]
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'DB insert failed: ' . $e->getMessage()], 500);
        }
        $newId = (int)$this->connection->lastInsertId();

        $row = $this->connection->fetchAssociative(
            'SELECT id, uuid, type, DATE_FORMAT(date, \'%Y-%m-%d\') AS date, text, text_html FROM cache_logs WHERE id=?',
            [$newId]
        );

        return new JsonResponse(['saved' => true, 'log' => $row]);
    }

    #[Route("/api/cache/{wp}/log/{logId}", name: "api_cache_log_update", methods: ["PUT"])]
    public function updateLog(string $wp, int $logId, Request $request): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) return new JsonResponse(['error' => 'Not authenticated'], 401);

        $userId = $user->getUserId();
        $wp = strtoupper($wp);
        $body = json_decode($request->getContent(), true);
        $type = (int)($body['type'] ?? 3);
        $date = (string)($body['date'] ?? date('Y-m-d'));
        $text = trim((string)($body['text'] ?? ''));
        $submittedPw = trim((string)($body['password'] ?? ''));

        $log = $this->connection->fetchAssociative(
            'SELECT id, user_id, cache_id FROM cache_logs WHERE id=?', [$logId]
        );
        if (!$log || (int)$log['user_id'] !== $userId) {
            return new JsonResponse(['error' => 'Not authorized'], 403);
        }
        $cacheId = (int)$log['cache_id'];

        $cache = $this->connection->fetchAssociative('SELECT logpw FROM caches WHERE wp_oc = ?', [$wp]);
        if (!$cache) return new JsonResponse(['error' => 'Cache not found'], 404);
        $cacheLogpw = (string)$cache['logpw'];

        // Reject flipping a log's type to Found/Attended when the user
        // already has another log of that type on this cache.
        if (in_array($type, [1, 7], true)) {
            $dup = (int)$this->connection->fetchOne(
                'SELECT COUNT(*) FROM cache_logs WHERE cache_id=? AND user_id=? AND type=? AND id<>?',
                [$cacheId, $userId, $type, $logId]
            );
            if ($dup > 0) {
                $name = $type === 1 ? 'Found it' : 'Attended';
                return new JsonResponse(['error' => "You already have a $name log on this cache — edit that one instead"], 422);
            }
        }

        // Same gate as createLog: Found/Attended on a password-protected cache
        // must supply a matching password — applies to edits too, otherwise a
        // user could post a Comment then edit it to Found and bypass the check.
        if ($cacheLogpw !== '' && in_array($type, [1, 7], true)) {
            if ($submittedPw === '') {
                return new JsonResponse(['error' => 'Log password required for this cache'], 422);
            }
            if (strcasecmp($submittedPw, $cacheLogpw) !== 0) {
                return new JsonResponse(['error' => 'Incorrect log password'], 422);
            }
        }

        if (strlen($date) === 10) $date .= ' 00:00:00';

        $this->connection->executeStatement(
            'UPDATE cache_logs SET type=?, date=?, text=?, text_html=0 WHERE id=?',
            [$type, $date, $text, $logId]
        );

        return new JsonResponse(['saved' => true]);
    }

    #[Route("/api/cache/{wp}/log/{logId}", name: "api_cache_log_delete", methods: ["DELETE"])]
    public function deleteLog(string $wp, int $logId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) return new JsonResponse(['error' => 'Not authenticated'], 401);

        $userId = $user->getUserId();
        $log = $this->connection->fetchAssociative(
            'SELECT id, user_id FROM cache_logs WHERE id=?', [$logId]
        );
        if (!$log || (int)$log['user_id'] !== $userId) {
            return new JsonResponse(['error' => 'Not authorized'], 403);
        }

        $this->connection->executeStatement('DELETE FROM cache_logs WHERE id=?', [$logId]);
        return new JsonResponse(['deleted' => true]);
    }
}
