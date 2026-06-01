<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
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
    public function cachesController_index(): Response
    {
        $types = $this->connection->fetchAllAssociative(
            'SELECT ct.id, IFNULL(stt.text, ct.en) AS name
             FROM cache_type ct
             LEFT JOIN sys_trans st ON ct.trans_id = st.id
             LEFT JOIN sys_trans_text stt ON st.id = stt.trans_id AND stt.lang = ?
             ORDER BY ct.ordinal',
            ['EN']
        );

        return $this->render('app/caches/search.html.twig', ['types' => $types]);
    }

    #[Route("/api/caches/search", name: "api_caches_search")]
    public function apiSearchCaches(Request $request): JsonResponse
    {
        $q          = trim($request->query->get('q', ''));
        $type       = (int)$request->query->get('type', 0);
        $minDiff    = (int)round((float)$request->query->get('minDiff', 1.0) * 2);
        $maxDiff    = (int)round((float)$request->query->get('maxDiff', 5.0) * 2);
        $activeOnly = $request->query->get('activeOnly', '1') === '1';
        $ocOnly     = $request->query->get('ocOnly', '0') === '1';
        $lat        = $request->query->get('lat') !== null ? (float)$request->query->get('lat') : null;
        $lon        = $request->query->get('lon') !== null ? (float)$request->query->get('lon') : null;
        $radius     = (float)$request->query->get('radius', 0);

        $user   = $this->security->getUser();
        $userId = $user?->getUserId() ?? 0;

        $qb = $this->connection->createQueryBuilder()
            ->select(
                'c.wp_oc', 'c.name',
                'c.type AS type_id',
                'c.status',
                'c.user_id AS owner_id',
                'c.difficulty / 2 AS difficulty',
                'c.terrain / 2 AS terrain',
                'c.latitude', 'c.longitude',
                'ct.name AS type_name',
                'u.username',
                'c.date_created',
                'EXISTS (SELECT 1 FROM cache_logs cl WHERE cl.cache_id = c.cache_id AND cl.user_id = :userId AND cl.type = 1) AS is_found',
                'EXISTS (SELECT 1 FROM cache_logs cl2 WHERE cl2.cache_id = c.cache_id AND cl2.user_id = :userId AND cl2.type = 2) AS is_dnf',
                'EXISTS (SELECT 1 FROM coordinates co WHERE co.cache_id = c.cache_id AND co.user_id = :userId AND co.type = 2) AS has_pcn',
                '(SELECT co2.description FROM coordinates co2 WHERE co2.cache_id = c.cache_id AND co2.user_id = :userId AND co2.type = 2 ORDER BY co2.id DESC LIMIT 1) AS pcn_text',
                '(SELECT co3.latitude  FROM coordinates co3 WHERE co3.cache_id = c.cache_id AND co3.user_id = :userId AND co3.type = 2 AND co3.latitude  != 0 ORDER BY co3.id DESC LIMIT 1) AS cc_lat',
                '(SELECT co4.longitude FROM coordinates co4 WHERE co4.cache_id = c.cache_id AND co4.user_id = :userId AND co4.type = 2 AND co4.longitude != 0 ORDER BY co4.id DESC LIMIT 1) AS cc_lon',
                'EXISTS (SELECT 1 FROM caches_attributes oca WHERE oca.cache_id = c.cache_id AND oca.attrib_id = 6) AS is_oc_only'
            )
            ->setParameter('userId', $userId)
            ->from('caches', 'c')
            ->innerJoin('c', 'user', 'u', 'c.user_id = u.user_id')
            ->leftJoin('c', 'cache_type', 'ct', 'c.type = ct.id')
            ->andWhere('c.difficulty >= :minDiff AND c.difficulty <= :maxDiff')
            ->setParameter('minDiff', $minDiff)
            ->setParameter('maxDiff', $maxDiff)
            ->orderBy('c.wp_oc', 'ASC')
            ->setMaxResults(1000);

        if ($activeOnly) {
            $qb->andWhere('c.status = 1');
        }
        if ($type > 0) {
            $qb->andWhere('c.type = :type')->setParameter('type', $type);
        }
        if ($ocOnly) {
            $qb->andWhere('EXISTS (SELECT 1 FROM caches_attributes oca2 WHERE oca2.cache_id = c.cache_id AND oca2.attrib_id = 6)');
        }
        if ($q !== '') {
            $qb->andWhere($qb->expr()->or(
                $qb->expr()->eq('c.wp_oc', ':q'),
                $qb->expr()->eq('c.wp_gc', ':q'),
                $qb->expr()->like('c.name', ':qLike'),
                $qb->expr()->like('u.username', ':qLike')
            ))
               ->setParameter('q', $q)
               ->setParameter('qLike', '%' . $q . '%');
        }
        if ($lat !== null && $lon !== null && $radius > 0) {
            $qb->andWhere('(6371 * acos(GREATEST(-1.0, LEAST(1.0, cos(radians(:lat)) * cos(radians(c.latitude)) * cos(radians(c.longitude) - radians(:lon)) + sin(radians(:lat)) * sin(radians(c.latitude)))))) <= :radius')
               ->setParameter('lat', $lat)
               ->setParameter('lon', $lon)
               ->setParameter('radius', $radius);
        }

        $rows = $qb->executeQuery()->fetchAllAssociative();

        $items = array_map(function (array $r) use ($userId): array {
            $name   = (string)$r['name'];
            $status = (int)$r['status'];
            $hasCC  = $r['cc_lat'] !== null && (float)$r['cc_lat'] !== 0.0;
            return [
                'referenceCode' => $r['wp_oc'],
                'name'          => $name,
                'shortName'     => mb_strlen($name) > 25 ? mb_substr($name, 0, 25) . '…' : $name,
                'lat'           => $hasCC ? (float)$r['cc_lat'] : (float)$r['latitude'],
                'lon'           => $hasCC ? (float)$r['cc_lon'] : (float)$r['longitude'],
                'geocacheType'  => ['id' => (int)$r['type_id'], 'name' => (string)($r['type_name'] ?? '')],
                'difficulty'    => (float)$r['difficulty'],
                'terrain'       => (float)$r['terrain'],
                'ownerAlias'    => (string)$r['username'],
                'publishedDate' => substr((string)$r['date_created'], 0, 10),
                'platform'      => 'OC',
                'isFound'       => (bool)(int)$r['is_found'],
                'isOwned'       => $userId > 0 && (int)$r['owner_id'] === $userId,
                'isDNF'         => (bool)(int)$r['is_dnf'],
                'isCached'      => false,
                'isDisabled'    => $status === 2,
                'isArchived'    => $status === 3,
                'hasCC'         => $hasCC,
                'hasPCN'        => (bool)(int)$r['has_pcn'],
                'pcn'           => (string)($r['pcn_text'] ?? ''),
                'isOcOnly'      => (bool)(int)$r['is_oc_only'],
                'isGuessable'   => false,
                'isPartial'     => false,
                'isSelected'    => false,
                'favoritePoints'=> 0,
                'status'        => $status,
            ];
        }, $rows);

        return $this->json(['items' => $items]);
    }

    #[Route("/cache/{wpID}", name: "cache_by_wp_oc_gc")]
    public function detail(string $wpID): Response
    {
        return $this->render('app/caches/detail.html.twig', ['wp' => strtoupper($wpID)]);
    }

    #[Route("/cache/new", name: "cache_new", priority: 1)]
    public function newCache(Request $request): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_security_login');
        }

        $locale = 'EN';

        $types = $this->connection->fetchAllAssociative(
            'SELECT ct.id, IFNULL(stt.text, ct.en) AS name
             FROM cache_type ct
             LEFT JOIN sys_trans st ON ct.trans_id = st.id
             LEFT JOIN sys_trans_text stt ON st.id = stt.trans_id AND stt.lang = ?
             ORDER BY ct.ordinal',
            [$locale]
        );
        $sizes = $this->connection->fetchAllAssociative(
            'SELECT cs.id, IFNULL(stt.text, cs.name) AS name
             FROM cache_size cs
             LEFT JOIN sys_trans st ON cs.trans_id = st.id
             LEFT JOIN sys_trans_text stt ON st.id = stt.trans_id AND stt.lang = ?
             ORDER BY cs.ordinal',
            [$locale]
        );
        $countries = $this->connection->fetchAllAssociative(
            'SELECT c.short, IFNULL(stt.text, c.name) AS name
             FROM countries c
             LEFT JOIN sys_trans st ON c.trans_id = st.id
             LEFT JOIN sys_trans_text stt ON st.id = stt.trans_id AND stt.lang = ?
             ORDER BY name',
            [$locale]
        );
        $languages = $this->connection->fetchAllAssociative(
            'SELECT l.short, IFNULL(stt.text, l.name) AS name
             FROM languages l
             LEFT JOIN sys_trans st ON l.trans_id = st.id
             LEFT JOIN sys_trans_text stt ON st.id = stt.trans_id AND stt.lang = ?
             ORDER BY name',
            [$locale]
        );
        $attrs = $this->connection->fetchAllAssociative(
            'SELECT ca.id, ca.name, ca.icon_undef, ca.icon_large, ca.group_id,
                    ag.name AS group_name
             FROM cache_attrib ca
             JOIN attribute_groups ag ON ca.group_id = ag.id
             WHERE NOT IFNULL(ca.hidden, 0) AND ca.selectable != 0
             ORDER BY ag.category_id, ca.group_id, ca.id'
        );
        $wptTypes = $this->connection->fetchAllAssociative(
            'SELECT id, name FROM coordinates_type ORDER BY id'
        );

        $now = new \DateTimeImmutable();

        // Edit mode: load existing cache data if ?edit={wpID} is present
        $editWp = $request->query->get('edit', '');
        $editCache = null;
        $editDesc = null;
        $editAttribs = [];
        $editNote = null;
        if ($editWp !== '') {
            $editCache = $this->connection->fetchAssociative(
                'SELECT * FROM caches WHERE wp_oc = ?', [strtoupper($editWp)]
            );
            if ($editCache && (int)$editCache['user_id'] === $user->getUserId()) {
                $editDesc = $this->connection->fetchAssociative(
                    'SELECT * FROM cache_desc WHERE cache_id = ? ORDER BY id LIMIT 1',
                    [(int)$editCache['cache_id']]
                );
                $editAttribs = array_column($this->connection->fetchAllAssociative(
                    'SELECT attrib_id FROM caches_attributes WHERE cache_id = ?',
                    [(int)$editCache['cache_id']]
                ), 'attrib_id');
                $editNote = $this->connection->fetchAssociative(
                    'SELECT description, latitude, longitude FROM coordinates
                     WHERE cache_id = ? AND user_id = ? AND type = 2 ORDER BY id DESC LIMIT 1',
                    [(int)$editCache['cache_id'], $user->getUserId()]
                );
                $editWpts = $this->connection->fetchAllAssociative(
                    'SELECT id, subtype, latitude, longitude, description
                     FROM coordinates
                     WHERE cache_id = ? AND type = 1 AND user_id IS NULL
                     ORDER BY id',
                    [(int)$editCache['cache_id']]
                );
            } else {
                $editCache = null; // not owner or not found
            }
        }

        $errors = [];
        $form = [
            'name'           => '',
            'type'           => '',
            'size'           => '',
            'coords'         => '',
            'country'        => $user->country ?? 'DE',
            'difficulty'     => '',
            'terrain'        => '',
            'search_time'    => '',
            'way_length'     => '',
            'wp_gc'          => '',
            'selected_attribs' => [],
            'desc_lang'      => 'DE',
            'short_desc'     => '',
            'desc'           => '',
            'hints'          => '',
            'hidden_date'    => $now->format('Y-m-d'),
            'publish'        => 'now2',
            'activate_date'  => $now->format('Y-m-d'),
            'activate_hour'  => (int)$now->format('H'),
            'log_pw'         => '',
            'cache_note'     => '',
            'user_coords'    => '',
            'waypoints_json' => '[]',
            'tos'            => false,
        ];

        // Prefill coords from ?lat=&lon= query params (e.g. map click)
        $queryLat = $request->query->get('lat', '');
        $queryLon = $request->query->get('lon', '');
        if ($queryLat !== '' && $queryLon !== '' && is_numeric($queryLat) && is_numeric($queryLon)) {
            $form['coords'] = self::decimalToDm((float)$queryLat, (float)$queryLon);
        }

        // Prefill from edit data
        if ($editCache) {
            $form['name']       = $editCache['name'] ?? '';
            $form['type']       = (string)($editCache['type'] ?? '');
            $form['size']       = (string)($editCache['size'] ?? '');
            $form['coords']     = self::decimalToDm(
                (float)($editCache['latitude'] ?? 0), (float)($editCache['longitude'] ?? 0)
            );
            $form['country']    = $editCache['country'] ?? ($user->country ?? 'DE');
            $form['difficulty'] = (string)($editCache['difficulty'] ?? '');
            $form['terrain']    = (string)($editCache['terrain'] ?? '');
            $form['search_time'] = (string)((float)($editCache['search_time'] ?? 0) ?: '');
            $form['way_length']  = (string)((float)($editCache['way_length'] ?? 0) ?: '');
            $form['wp_gc']      = $editCache['wp_gc'] ?? '';
            $form['hidden_date']= substr($editCache['date_hidden'] ?? $now->format('Y-m-d'), 0, 10);
            $form['log_pw']     = $editCache['logpw'] ?? '';
            $form['selected_attribs'] = $editAttribs;
            if ($editDesc) {
                $form['desc_lang']   = $editDesc['language'] ?? 'DE';
                $form['short_desc']  = $editDesc['short_desc'] ?? '';
                $form['desc']        = $editDesc['desc'] ?? '';
                $form['hints']       = $editDesc['hint'] ?? '';
            }
            if ($editNote) {
                $form['cache_note'] = $editNote['description'] ?? '';
                $lat = (float)($editNote['latitude'] ?? 0);
                $lon = (float)($editNote['longitude'] ?? 0);
                if ($lat !== 0.0 || $lon !== 0.0) {
                    $form['user_coords'] = self::decimalToDm($lat, $lon);
                }
            }
            if (!empty($editWpts)) {
                $form['waypoints_json'] = json_encode(array_map(fn($w) => [
                    'id'      => (int)$w['id'],
                    'type'    => (int)$w['subtype'],
                    'coords'  => self::decimalToDm((float)$w['latitude'], (float)$w['longitude']),
                    'desc'    => $w['description'] ?? '',
                ], $editWpts), JSON_HEX_TAG | JSON_HEX_APOS);
            }
            // Edit mode overrides: publish selection is irrelevant for edits;
            // status changes happen via logs, not here.
            $form['publish'] = 'notnow';
        }

        if ($request->isMethod('POST')) {
            $p = $request->request;
            $editId = (int)$p->get('edit_id', 0);
            $isEdit = $editId > 0;
            // Preserve edit mode for template on validation failure
            if ($isEdit && $editCache === null) {
                $editCache = ['cache_id' => $editId]; // minimal stub for template
            }

            $form['name']            = trim((string)$p->get('name', ''));
            $form['type']            = (string)$p->get('type', '');
            $form['size']            = (string)$p->get('size', '');
            $form['coords']          = trim((string)$p->get('coords', ''));
            $form['country']         = (string)$p->get('country', 'DE');
            $form['difficulty']      = (string)$p->get('difficulty', '');
            $form['terrain']         = (string)$p->get('terrain', '');
            $form['search_time']     = trim((string)$p->get('search_time', ''));
            $form['way_length']      = trim((string)$p->get('way_length', ''));
            $form['wp_gc']           = strtoupper(trim((string)$p->get('wp_gc', '')));
            $form['selected_attribs'] = array_filter(
                array_map('intval', explode(';', (string)$p->get('cache_attribs', ''))),
                fn($v) => $v > 0
            );
            $form['desc_lang']       = (string)$p->get('desc_lang', 'DE');
            $form['short_desc']      = trim((string)$p->get('short_desc', ''));
            $form['desc']            = trim((string)$p->get('desc', ''));
            $form['hints']           = trim((string)$p->get('hints', ''));
            $form['hidden_date']     = (string)$p->get('hidden_date', $now->format('Y-m-d'));
            $form['publish']         = (string)$p->get('publish', 'notnow');
            $form['activate_date']   = (string)$p->get('activate_date', $now->format('Y-m-d'));
            $form['activate_hour']   = (int)$p->get('activate_hour', 0);
            $form['log_pw']          = mb_substr(trim((string)$p->get('log_pw', '')), 0, 20);
            $form['cache_note']     = trim((string)$p->get('cache_note', ''));
            $form['user_coords']    = trim((string)$p->get('user_coords', ''));
            $form['waypoints_json'] = trim((string)$p->get('waypoints_json', '[]'));
            $form['tos']             = (bool)$p->get('tos', false);

            // Validate name
            if ($form['name'] === '') {
                $errors['name'] = 'Cache name is required.';
            }

            // Validate type
            $typeId = (int)$form['type'];
            if ($typeId <= 0) {
                $errors['type'] = 'Please select a cache type.';
            }

            // Validate size (auto-set for virtual/webcam/event)
            $sizeId = (int)$form['size'];
            if ($typeId === 4 || $typeId === 5 || $typeId === 6) {
                $sizeId = 7; // no container
                $form['size'] = '7';
            } elseif ($sizeId <= 0) {
                $errors['size'] = 'Please select a cache size.';
            }

            // Validate coordinates
            $lat = null;
            $lon = null;
            $coordsParsed = self::parseCoords($form['coords']);
            if ($coordsParsed === null) {
                $errors['coords'] = 'Valid coordinates required (e.g. N51 02.345 E009 43.210).';
            } else {
                [$lat, $lon] = $coordsParsed;
                $dupWp = $this->connection->fetchOne(
                    'SELECT wp_oc FROM caches WHERE status=1 AND ROUND(longitude,6)=ROUND(?,6) AND ROUND(latitude,6)=ROUND(?,6)'
                    . ($isEdit ? ' AND cache_id != ?' : ''),
                    $isEdit ? [$lon, $lat, $editId] : [$lon, $lat]
                );
                if ($dupWp) {
                    $errors['coords'] = "Another cache ($dupWp) already exists at these coordinates.";
                }
            }

            // Validate difficulty / terrain
            $diff = (int)$form['difficulty'];
            $terr = (int)$form['terrain'];
            if ($typeId === 6) {
                $diff = 2;
                $terr = 2;
            } elseif ($diff < 2 || $diff > 10 || $terr < 2 || $terr > 10) {
                $errors['dt'] = 'Please select both difficulty and terrain ratings.';
            }

            // Validate hidden date
            $hiddenDate = \DateTimeImmutable::createFromFormat('Y-m-d', $form['hidden_date']);
            if (!$hiddenDate) {
                $errors['hidden_date'] = 'Invalid hidden date.';
            }

            // Validate publish / activation (skipped for edit mode)
            if (!$isEdit && !in_array($form['publish'], ['now2', 'later', 'notnow'], true)) {
                $form['publish'] = 'notnow';
            }
            $activationDate = null;
            if ($form['publish'] === 'later') {
                $activationDate = \DateTimeImmutable::createFromFormat('Y-m-d', $form['activate_date']);
                if (!$activationDate || $form['activate_hour'] < 0 || $form['activate_hour'] > 23) {
                    $errors['activate_date'] = 'Invalid activation date/time.';
                }
            }

            // Validate GC waypoint
            if ($form['wp_gc'] !== '' && !preg_match('/^(?:GC|CX)[0-9A-Z]{3,6}$/', $form['wp_gc'])) {
                $errors['wp_gc'] = 'GC waypoint must be in the form GCxxxxx.';
            }

            // Validate optional numeric effort fields
            if ($form['search_time'] !== '' && !is_numeric($form['search_time'])) {
                $errors['search_time'] = 'Search time must be a number (hours).';
            }
            if ($form['way_length'] !== '' && !is_numeric($form['way_length'])) {
                $errors['way_length'] = 'Distance must be a number (km).';
            }

            // Validate user_coords format
            if ($form['user_coords'] !== '' && self::parseCoords($form['user_coords']) === null) {
                $errors['user_coords'] = 'Coordinates must be in DM format (N51 02.345 E009 43.210) or decimal (51.123,9.456).';
            }

            // Parse & validate waypoints JSON
            $waypoints = [];
            $rawWpts = json_decode($form['waypoints_json'], true);
            if (!is_array($rawWpts)) $rawWpts = [];
            foreach ($rawWpts as $i => $w) {
                $wptType  = (int)($w['type'] ?? 0);
                $wptCoords = trim((string)($w['coords'] ?? ''));
                $wptDesc   = trim((string)($w['desc'] ?? ''));
                if ($wptType <= 0) {
                    $errors['waypoints'] = "Waypoint " . ($i + 1) . ": please select a type.";
                    break;
                }
                $parsed = self::parseCoords($wptCoords);
                if ($parsed === null) {
                    $errors['waypoints'] = "Waypoint " . ($i + 1) . ": invalid coordinates format.";
                    break;
                }
                $waypoints[] = [
                    'type'    => $wptType,
                    'lat'     => $parsed[0],
                    'lon'     => $parsed[1],
                    'desc'    => $wptDesc,
                    'orig_id' => isset($w['id']) ? (int)$w['id'] : null,
                ];
            }

            // Validate TOS (only for new caches)
            if (!$isEdit && !$form['tos']) {
                $errors['tos'] = 'You must agree to the Terms of Service.';
            }

            if (empty($errors) && $lat !== null && $lon !== null && $hiddenDate) {
                $nowStr = $now->format('Y-m-d H:i:s');
                $searchTime = $form['search_time'] !== '' ? (float)$form['search_time'] : 0.0;
                $wayLength  = $form['way_length']  !== '' ? (float)$form['way_length']  : 0.0;

                if ($isEdit) {
                    // Verify ownership
                    $existing = $this->connection->fetchAssociative(
                        'SELECT user_id, wp_oc FROM caches WHERE cache_id = ?', [$editId]
                    );
                    if (!$existing || (int)$existing['user_id'] !== $user->getUserId()) {
                        $errors['_general'] = 'Not authorized to edit this cache.';
                    } else {
                        $cacheId = $editId;
                        $wpOc = $existing['wp_oc'];

                        $this->connection->executeStatement(
                            'UPDATE caches SET name=?, longitude=?, latitude=?, type=?,
                             country=?, date_hidden=?, size=?, difficulty=?, terrain=?,
                             logpw=?, search_time=?, way_length=?, wp_gc=?
                             WHERE cache_id=?',
                            [
                                $form['name'], $lon, $lat, $typeId,
                                $form['country'], $hiddenDate->format('Y-m-d'),
                                $sizeId, $diff, $terr,
                                $form['log_pw'], $searchTime, $wayLength, $form['wp_gc'],
                                $cacheId,
                            ]
                        );

                        $this->connection->executeStatement(
                            'UPDATE cache_desc SET language=?, `desc`=?, hint=?, short_desc=?,
                             last_modified=? WHERE cache_id=?',
                            [
                                strtoupper($form['desc_lang']), $form['desc'],
                                $form['hints'], $form['short_desc'],
                                $nowStr, $cacheId,
                            ]
                        );

                        // Attributes: delete and re-insert
                        $this->connection->executeStatement(
                            'DELETE FROM caches_attributes WHERE cache_id=?', [$cacheId]
                        );
                        foreach ($form['selected_attribs'] as $attribId) {
                            $this->connection->executeStatement(
                                'INSERT INTO caches_attributes (cache_id, attrib_id) VALUES (?, ?)',
                                [$cacheId, $attribId]
                            );
                        }

                        // Waypoints: replace all
                        $this->connection->executeStatement(
                            'DELETE FROM coordinates WHERE cache_id=? AND type=1 AND user_id IS NULL', [$cacheId]
                        );
                        foreach ($waypoints as $wpt) {
                            $this->connection->executeStatement(
                                'INSERT INTO coordinates (cache_id, type, subtype, latitude, longitude, description, date_created, last_modified)
                                 VALUES (?, 1, ?, ?, ?, ?, ?, ?)',
                                [$cacheId, $wpt['type'], $wpt['lat'], $wpt['lon'], $wpt['desc'], $nowStr, $nowStr]
                            );
                        }

                        // Personal note / user coords: upsert
                        $coords = self::parseCoords($form['user_coords']);
                        $userLat = $coords ? $coords[0] : 0.0;
                        $userLon = $coords ? $coords[1] : 0.0;
                        $noteExisting = $this->connection->fetchAssociative(
                            'SELECT id FROM coordinates WHERE cache_id=? AND user_id=? AND type=2 ORDER BY id DESC LIMIT 1',
                            [$cacheId, $user->getUserId()]
                        );
                        if ($form['cache_note'] !== '' || ($userLat !== 0.0 && $userLon !== 0.0)) {
                            if ($noteExisting) {
                                $this->connection->executeStatement(
                                    'UPDATE coordinates SET latitude=?, longitude=?, description=?, last_modified=? WHERE id=?',
                                    [$userLat, $userLon, $form['cache_note'], $nowStr, (int)$noteExisting['id']]
                                );
                            } else {
                                $this->connection->executeStatement(
                                    'INSERT INTO coordinates (cache_id, user_id, type, subtype, latitude, longitude, description, date_created, last_modified)
                                     VALUES (?, ?, 2, 0, ?, ?, ?, ?, ?)',
                                    [$cacheId, $user->getUserId(), $userLat, $userLon, $form['cache_note'], $nowStr, $nowStr]
                                );
                            }
                        } elseif ($noteExisting) {
                            // Clear note/coords but preserve logpw in the same row
                            $this->connection->executeStatement(
                                'UPDATE coordinates SET latitude=0, longitude=0, description=\'\', last_modified=? WHERE id=?',
                                [$nowStr, (int)$noteExisting['id']]
                            );
                        }

                        return $this->redirectToRoute('app_cache_by_wp_oc_gc', ['wpID' => $wpOc]);
                    }
                } else {
                    // Determine status
                    if ($form['publish'] === 'now2') {
                        $status = 1;
                        $activationDateStr = $nowStr;
                    } elseif ($form['publish'] === 'later' && $activationDate) {
                        $status = 5;
                        $activationDateStr = $activationDate
                            ->setTime($form['activate_hour'], 0, 0)
                            ->format('Y-m-d H:i:s');
                    } else {
                        $status = 5;
                        $activationDateStr = null;
                    }

                    $this->connection->executeStatement(
                        'INSERT INTO caches
                            (user_id, name, longitude, latitude, type, status, country,
                             date_hidden, date_activate, size, difficulty, terrain,
                             logpw, search_time, way_length, wp_gc, node)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                        [
                            $user->getUserId(),
                            $form['name'],
                            $lon,
                            $lat,
                            $typeId,
                            $status,
                            $form['country'],
                            $hiddenDate->format('Y-m-d'),
                            $activationDateStr,
                            $sizeId,
                            $diff,
                            $terr,
                            $form['log_pw'],
                            $searchTime,
                            $wayLength,
                            $form['wp_gc'],
                            4,
                        ]
                    );
                    $cacheId = (int)$this->connection->lastInsertId();

                    $this->connection->executeStatement(
                        'INSERT INTO cache_desc
                            (cache_id, language, `desc`, desc_html, hint, short_desc,
                             last_modified, desc_htmledit, node)
                         VALUES (?, ?, ?, 0, ?, ?, ?, 0, ?)',
                        [
                            $cacheId,
                            strtoupper($form['desc_lang']),
                            $form['desc'],
                            $form['hints'],
                            $form['short_desc'],
                            $nowStr,
                            4,
                        ]
                    );

                    foreach ($form['selected_attribs'] as $attribId) {
                        $this->connection->executeStatement(
                            'INSERT IGNORE INTO caches_attributes (cache_id, attrib_id) VALUES (?, ?)',
                            [$cacheId, $attribId]
                        );
                    }

                    // Additional waypoints
                    foreach ($waypoints as $wpt) {
                        $this->connection->executeStatement(
                            'INSERT INTO coordinates (cache_id, type, subtype, latitude, longitude, description, date_created, last_modified)
                             VALUES (?, 1, ?, ?, ?, ?, ?, ?)',
                            [$cacheId, $wpt['type'], $wpt['lat'], $wpt['lon'], $wpt['desc'], $nowStr, $nowStr]
                        );
                    }

                    // Save personal cache note and/or user coordinates
                    $userCoordsParsed = self::parseCoords($form['user_coords'] ?? '');
                    $userLat = $userCoordsParsed ? (float)$userCoordsParsed[0] : 0.0;
                    $userLon = $userCoordsParsed ? (float)$userCoordsParsed[1] : 0.0;
                    if ($form['cache_note'] !== '' || ($userLat !== 0.0 && $userLon !== 0.0)) {
                        $this->connection->executeStatement(
                            'INSERT INTO coordinates (cache_id, user_id, type, subtype, latitude, longitude, description, date_created, last_modified)
                             VALUES (?, ?, 2, 0, ?, ?, ?, ?, ?)',
                            [$cacheId, $user->getUserId(), $userLat, $userLon, $form['cache_note'], $nowStr, $nowStr]
                        );
                    }

                    $wpOc = (string)$this->connection->fetchOne(
                        'SELECT wp_oc FROM caches WHERE cache_id = ?',
                        [$cacheId]
                    );

                    return $this->redirectToRoute('app_cache_by_wp_oc_gc', ['wpID' => $wpOc]);
                }
            }
        }

        return $this->render('app/caches/new.html.twig', [
            'types'   => $types,
            'sizes'   => $sizes,
            'countries' => $countries,
            'languages' => $languages,
            'attrs'   => $attrs,
            'wptTypes' => $wptTypes,
            'form'    => $form,
            'errors'  => $errors,
            'is_edit' => $editCache !== null,
            'edit_cache_id' => $editCache ? (int)$editCache['cache_id'] : 0,
        ]);
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
                c.search_time, c.way_length,
                IF(c.logpw != \'\', 1, 0) AS logpw,
                c.logpw AS cache_logpw,
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

        // OC DB status → native status string
        $statusStringMap = [
            1 => 'Active',
            2 => 'Disabled',
        ];
        $statusStr = $statusStringMap[$statusId] ?? 'Archived';

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
                if ($statusId === 2) {
                    $logTypeIds[] = 10; // Ready to search (enable)
                } else {
                    $logTypeIds[] = 11; // Temporarily unavailable (disable)
                }
                $logTypeIds[] = 9; // Archive
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
            'status'           => $statusStr,
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
            'searchTime'  => (float)$cache['search_time'],
            'wayLength'   => (float)$cache['way_length'],
            'wpGc'        => $cache['wp_gc'] ?: '',
            'svgName'     => $cache['svg_name'],
            'descHtml'    => (bool)($desc['desc_html'] ?? true),
            'descDarkUnsafe'   => (bool)($desc['desc_dark_unsafe'] ?? false),
            'needsMaintenance' => (bool)$cache['needs_maintenance'],
            'listingOutdated'  => (bool)$cache['listing_outdated'],
            'myLogpw'          => $noteRow['logpw'] ?? '',
            'cacheLogpw'      => $isOwner ? ($cache['cache_logpw'] ?? '') : '',
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

        $cache = $this->connection->fetchAssociative('SELECT cache_id, logpw, user_id FROM caches WHERE wp_oc = ?', [$wp]);
        if (!$cache) return new JsonResponse(['error' => 'Cache not found'], 404);
        $cacheId = (int)$cache['cache_id'];
        $cacheLogpw = (string)$cache['logpw'];
        $isOwner = (int)$cache['user_id'] === $userId;

        // Status-changing log types (9=Archive, 10=Enable, 11=Disable) require
        // cache ownership.
        if (in_array($type, [9, 10, 11], true) && !$isOwner) {
            return new JsonResponse(['error' => 'Only the cache owner can submit this log type'], 403);
        }

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

        // Status-changing logs update the cache status
        $statusMap = [9 => 3, 10 => 1, 11 => 2];
        if (isset($statusMap[$type])) {
            $this->connection->executeStatement(
                'UPDATE caches SET status=? WHERE cache_id=?',
                [$statusMap[$type], $cacheId]
            );
        }

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

        $cache = $this->connection->fetchAssociative('SELECT logpw, user_id FROM caches WHERE wp_oc = ?', [$wp]);
        if (!$cache) return new JsonResponse(['error' => 'Cache not found'], 404);
        $cacheLogpw = (string)$cache['logpw'];
        $isOwner = (int)$cache['user_id'] === $userId;

        // Status-changing log types require cache ownership
        if (in_array($type, [9, 10, 11], true) && !$isOwner) {
            return new JsonResponse(['error' => 'Only the cache owner can submit this log type'], 403);
        }

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

        // Status-changing logs update the cache status
        $statusMap = [9 => 3, 10 => 1, 11 => 2];
        if (isset($statusMap[$type])) {
            $this->connection->executeStatement(
                'UPDATE caches SET status=? WHERE cache_id=?',
                [$statusMap[$type], $cacheId]
            );
        }

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

    /**
     * Parse a coordinate string in either DM ("N51 02.345 E009 43.210") or
     * decimal ("51.12345,9.456") format. Returns [lat, lon] or null.
     */
    private static function parseCoords(string $input): ?array
    {
        $input = trim($input);
        if ($input === '') return null;

        // Comma-separated decimal: "51.12345, 9.456"
        if (preg_match('/^(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)$/', $input, $m)) {
            $lat = (float)$m[1];
            $lon = (float)$m[2];
            if ($lat >= -90 && $lat <= 90 && $lon >= -180 && $lon <= 180 && $lat != 0) {
                return [$lat, $lon];
            }
        }

        // DM format: "N51 02.345 E009 43.210"
        if (preg_match('/^([NS])\s*(\d+)\s+(\d{2})\.(\d{1,3})\s+([EW])\s*(\d+)\s+(\d{2})\.(\d{1,3})$/', $input, $m)) {
            $latDeg  = (int)$m[2];
            $latMin  = (int)$m[3] + ((int)$m[4]) / pow(10, strlen($m[4]));
            $latSign = ($m[1] === 'S') ? -1 : 1;
            $lat = $latSign * ($latDeg + $latMin / 60);

            $lonDeg  = (int)$m[6];
            $lonMin  = (int)$m[7] + ((int)$m[8]) / pow(10, strlen($m[8]));
            $lonSign = ($m[5] === 'W') ? -1 : 1;
            $lon = $lonSign * ($lonDeg + $lonMin / 60);

            if ($lat >= -90 && $lat <= 90 && $lon >= -180 && $lon <= 180) {
                return [$lat, $lon];
            }
        }

        return null;
    }

    /** Format decimal lat/lon as DM string "N51 02.345 E009 43.210". */
    private static function decimalToDm(float $lat, float $lon): string
    {
        $ns = $lat < 0 ? 'S' : 'N';
        $ew = $lon < 0 ? 'W' : 'E';
        $lat = abs($lat);
        $lon = abs($lon);

        $latDeg = (int)$lat;
        $latMin = ($lat - $latDeg) * 60;
        $lonDeg = (int)$lon;
        $lonMin = ($lon - $lonDeg) * 60;

        return sprintf('%s%02d %02d.%03d %s%03d %02d.%03d',
            $ns, $latDeg, (int)$latMin, (int)round(($latMin - (int)$latMin) * 1000),
            $ew, $lonDeg, (int)$lonMin, (int)round(($lonMin - (int)$lonMin) * 1000));
    }
}
