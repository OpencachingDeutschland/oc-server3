<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Oc\Repository\CacheDescRepository;
use Oc\Repository\CacheLogsRepository;
use Oc\Repository\CachesAttributesRepository;
use Oc\Repository\CachesRepository;
use Oc\Repository\CacheSizeRepository;
use Oc\Repository\CacheTypeRepository;
use Oc\Repository\CountriesRepository;
use Oc\Repository\UserRepository;
use Oc\Repository\WaypointsRepository;
use Oc\Security\Auth;
use Oc\Service\UniCacheBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CachesController extends AbstractController
{
    public function __construct(
        private CachesRepository $cachesRepository,
        private CacheDescRepository $cacheDescRepository,
        private CacheLogsRepository $cacheLogsRepository,
        private CachesAttributesRepository $cachesAttributesRepository,
        private CacheTypeRepository $cacheTypeRepository,
        private CacheSizeRepository $cacheSizeRepository,
        private CountriesRepository $countriesRepository,
        private Auth $auth,
        private UniCacheBuilder $uniCacheBuilder,
        private UserRepository $userRepository,
        private WaypointsRepository $waypointsRepository,
    ) {}

    #[Route("/caches", name: "caches_index")]
    public function cachesController_index(): Response
    {
        $types = $this->cacheTypeRepository->fetchLookupTypes('EN');

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

        $user   = $this->auth->getUser();
        $userId = $user["user_id"] ?? 0;

        $rows = $this->cachesRepository->searchCaches([
            'userId'     => $userId,
            'q'          => $q,
            'type'       => $type,
            'minDiff'    => $minDiff,
            'maxDiff'    => $maxDiff,
            'activeOnly' => $activeOnly,
            'ocOnly'     => $ocOnly,
            'lat'        => $lat,
            'lon'        => $lon,
            'radius'     => $radius,
        ]);

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
                'ownerCode'     => (string)$r['username'],
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
        $user = $this->auth->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_security_login');
        }

        $locale = 'EN';

        $types     = $this->cacheTypeRepository->fetchLookupTypes($locale);
        $sizes     = $this->cacheSizeRepository->fetchLookupSizes($locale);
        $countries = $this->countriesRepository->fetchLookupCountries($locale);
        $languages = $this->cachesRepository->fetchLookupLanguages($locale);
        $attrs     = $this->cachesRepository->fetchAllAttributes();
        $wptTypes  = $this->cachesRepository->fetchWaypointTypes();

        $now = new \DateTimeImmutable();

        // Edit mode: load existing cache data if ?edit={wpID} is present
        $editWp = $request->query->get('edit', '');
        $editCache = null;
        $editDesc = null;
        $editAttribs = [];
        $editNote = null;
        if ($editWp !== '') {
            $editCache = $this->cachesRepository->fetchCacheByWpForEdit($editWp);
            if ($editCache && (int)$editCache['user_id'] === $user["user_id"]) {
                $cacheId = (int)$editCache['cache_id'];
                $editDesc    = $this->cacheDescRepository->fetchDescriptionForEdit($cacheId);
                $editAttribs = $this->cachesAttributesRepository->fetchAttribIds($cacheId);
                $editNote    = $this->waypointsRepository->fetchUserNote($cacheId, $user["user_id"]);
                $editWpts    = $this->waypointsRepository->fetchWaypointsForEdit($cacheId);
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
            'country'        => $user['country'] ?? 'DE',
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
            $form['country']    = $editCache['country'] ?? ($user['country'] ?? 'DE');
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
                $dupWp = $this->cachesRepository->checkDuplicateCoords(
                    $lon, $lat, $isEdit ? $editId : null
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
                    $existing = $this->cachesRepository->fetchCacheByWpForEdit($editWp);
                    if (!$existing || (int)$existing['user_id'] !== $user["user_id"]) {
                        $errors['_general'] = 'Not authorized to edit this cache.';
                    } else {
                        $cacheId = $editId;
                        $wpOc = $existing['wp_oc'];

                        $this->cachesRepository->updateCache($cacheId, [
                            'name'       => $form['name'],
                            'longitude'  => $lon,
                            'latitude'   => $lat,
                            'type'       => $typeId,
                            'country'    => $form['country'],
                            'date_hidden' => $hiddenDate->format('Y-m-d'),
                            'size'       => $sizeId,
                            'difficulty' => $diff,
                            'terrain'    => $terr,
                            'logpw'      => $form['log_pw'],
                            'search_time' => $searchTime,
                            'way_length'  => $wayLength,
                            'wp_gc'      => $form['wp_gc'],
                        ]);

                        $this->cacheDescRepository->updateDescription($cacheId, [
                            'language'    => strtoupper($form['desc_lang']),
                            'desc'        => $form['desc'],
                            'hint'        => $form['hints'],
                            'short_desc'  => $form['short_desc'],
                            'last_modified' => $nowStr,
                        ]);

                        $this->cachesAttributesRepository->replaceCacheAttributes(
                            $cacheId, $form['selected_attribs']
                        );

                        $this->waypointsRepository->replaceOwnerWaypoints(
                            $cacheId, $waypoints, $nowStr
                        );

                        // Personal note / user coords: upsert
                        $coords = self::parseCoords($form['user_coords']);
                        $userLat = $coords ? $coords[0] : 0.0;
                        $userLon = $coords ? $coords[1] : 0.0;
                        $noteExisting = $this->waypointsRepository->fetchUserNote($cacheId, $user["user_id"]);

                        if ($form['cache_note'] !== '' || ($userLat !== 0.0 && $userLon !== 0.0)) {
                            if ($noteExisting) {
                                $this->waypointsRepository->upsertUserNoteCoords($cacheId, $user["user_id"], $userLat, $userLon);
                                $this->waypointsRepository->upsertUserNoteText($cacheId, $user["user_id"], $form['cache_note']);
                            } else {
                                $this->waypointsRepository->upsertUserNoteText($cacheId, $user["user_id"], $form['cache_note'] ?: ' ');
                                $this->waypointsRepository->upsertUserNoteCoords($cacheId, $user["user_id"], $userLat, $userLon);
                            }
                        } elseif ($noteExisting) {
                            // Clear note/coords but preserve logpw in the same row
                            $this->waypointsRepository->upsertUserNoteText($cacheId, $user["user_id"], '');
                            // Recreate an empty row so coords can be zeroed
                            if ($form['cache_note'] === '' && $userLat === 0.0 && $userLon === 0.0) {
                                $this->waypointsRepository->upsertUserNoteText($cacheId, $user["user_id"], ' ');
                                $this->waypointsRepository->upsertUserNoteCoords($cacheId, $user["user_id"], 0.0, 0.0);
                            }
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

                    $cacheId = $this->cachesRepository->insertCache([
                        'user_id'       => $user["user_id"],
                        'name'          => $form['name'],
                        'longitude'     => $lon,
                        'latitude'      => $lat,
                        'type'          => $typeId,
                        'status'        => $status,
                        'country'       => $form['country'],
                        'date_hidden'   => $hiddenDate->format('Y-m-d'),
                        'date_activate' => $activationDateStr,
                        'size'          => $sizeId,
                        'difficulty'    => $diff,
                        'terrain'       => $terr,
                        'logpw'         => $form['log_pw'],
                        'search_time'   => $searchTime,
                        'way_length'    => $wayLength,
                        'wp_gc'         => $form['wp_gc'],
                        'node'          => 4,
                    ]);

                    $this->cacheDescRepository->insertDescription($cacheId, [
                        'language'      => strtoupper($form['desc_lang']),
                        'desc'          => $form['desc'],
                        'desc_html'     => 0,
                        'hint'          => $form['hints'],
                        'short_desc'    => $form['short_desc'],
                        'last_modified' => $nowStr,
                        'desc_htmledit' => 0,
                        'node'          => 4,
                    ]);

                    $this->cachesAttributesRepository->replaceCacheAttributes(
                        $cacheId, $form['selected_attribs']
                    );

                    $this->waypointsRepository->replaceOwnerWaypoints(
                        $cacheId, $waypoints, $nowStr
                    );

                    // Save personal cache note and/or user coordinates
                    $userCoordsParsed = self::parseCoords($form['user_coords'] ?? '');
                    $userLat = $userCoordsParsed ? (float)$userCoordsParsed[0] : 0.0;
                    $userLon = $userCoordsParsed ? (float)$userCoordsParsed[1] : 0.0;
                    if ($form['cache_note'] !== '' || ($userLat !== 0.0 && $userLon !== 0.0)) {
                        $noteText = $form['cache_note'] !== '' ? $form['cache_note'] : ' ';
                        $this->waypointsRepository->upsertUserNoteText($cacheId, $user["user_id"], $noteText);
                        if ($userLat !== 0.0 || $userLon !== 0.0) {
                            $this->waypointsRepository->upsertUserNoteCoords($cacheId, $user["user_id"], $userLat, $userLon);
                        }
                    }

                    $wpOc = $this->cachesRepository->getWpOcById($cacheId);

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

        $cacheRow = $this->cachesRepository->fetchDetailByWp($wp);

        if (!$cacheRow) {
            return new JsonResponse(['error' => 'Cache not found'], 404);
        }

        $cacheId  = (int)$cacheRow['cache_id'];
        $user     = $this->auth->getUser();
        $userId   = $user["user_id"] ?? 0;
        $userName = $user["username"] ?? null ?? null;
        $isOwner  = $userId > 0 && (int)$cacheRow['owner_id'] === $userId;

        // Gather all data from repository layer
        $data = [
            'cache'      => $cacheRow,
            'desc'       => $this->cacheDescRepository->fetchDescription(
                $cacheId,
                strtoupper($cacheRow['country'] ?: 'DE')
            ),
            'waypoints'  => $this->waypointsRepository->fetchWaypoints($cacheId),
            'attributes' => $this->cachesAttributesRepository->fetchAttributesWithIcons($cacheId),
            'logs'       => $this->cacheLogsRepository->fetchLogsByCacheId($cacheId),
            'noteRow'    => $userId ? $this->waypointsRepository->fetchUserNote($cacheId, $userId) : null,
            'region'     => $this->cachesRepository->fetchRegion($cacheId),
            'ownerStats' => $this->userRepository->fetchOwnerStats((int)$cacheRow['owner_id']),
        ];

        $context = [
            'userId'        => $userId,
            'userName'      => $userName,
            'isOwner'       => $isOwner,
            'isWatched'     => $userId ? $this->cachesRepository->isWatchedByUser($cacheId, $userId) : false,
            'isRecommended' => $userId ? $this->cachesRepository->isRecommendedByUser($cacheId, $userId) : false,
        ];

        return new JsonResponse($this->uniCacheBuilder->build($data, $context));
    }

    #[Route("/api/cache/{wp}/note", name: "api_cache_note_save", methods: ["POST"])]
    public function saveNote(string $wp, Request $request): JsonResponse
    {
        $user = $this->auth->getUser();
        if (!$user) return new JsonResponse(['error' => 'Not authenticated'], 401);

        $userId = $user["user_id"];
        $wp = strtoupper($wp);
        $body = json_decode($request->getContent(), true);
        $text = trim((string)($body['text'] ?? ''));

        $cacheId = $this->cachesRepository->getCacheIdByWp($wp);
        if (!$cacheId) return new JsonResponse(['error' => 'Cache not found'], 404);

        $result = $this->waypointsRepository->upsertUserNoteText($cacheId, $userId, $text);
        return new JsonResponse($result);
    }

    #[Route("/api/cache/{wp}/logpw", name: "api_cache_logpw_save", methods: ["POST"])]
    public function saveLogpw(string $wp, Request $request): JsonResponse
    {
        $user = $this->auth->getUser();
        if (!$user) return new JsonResponse(['error' => 'Not authenticated'], 401);

        $userId = $user["user_id"];
        $wp = strtoupper($wp);
        $body = json_decode($request->getContent(), true);
        $logpw = substr(trim((string)($body['logpw'] ?? '')), 0, 20);

        $cacheId = $this->cachesRepository->getCacheIdByWp($wp);
        if (!$cacheId) return new JsonResponse(['error' => 'Cache not found'], 404);

        $result = $this->waypointsRepository->upsertUserNoteLogpw($cacheId, $userId, $logpw);
        return new JsonResponse($result);
    }

    #[Route("/api/cache/{wp}/coords", name: "api_cache_coords_save", methods: ["POST"])]
    public function saveCoords(string $wp, Request $request): JsonResponse
    {
        $user = $this->auth->getUser();
        if (!$user) return new JsonResponse(['error' => 'Not authenticated'], 401);

        $userId = $user["user_id"];
        $wp = strtoupper($wp);
        $body = json_decode($request->getContent(), true);
        $lat = (float)($body['lat'] ?? 0);
        $lon = (float)($body['lon'] ?? 0);

        $cacheId = $this->cachesRepository->getCacheIdByWp($wp);
        if (!$cacheId) return new JsonResponse(['error' => 'Cache not found'], 404);

        $result = $this->waypointsRepository->upsertUserNoteCoords($cacheId, $userId, $lat, $lon);
        return new JsonResponse($result);
    }

    #[Route("/api/cache/{wp}/log", name: "api_cache_log_create", methods: ["POST"])]
    public function createLog(string $wp, Request $request): JsonResponse
    {
        $user = $this->auth->getUser();
        if (!$user) return new JsonResponse(['error' => 'Not authenticated'], 401);

        $userId = $user["user_id"];
        $wp = strtoupper($wp);
        $body = json_decode($request->getContent(), true);
        $type = (int)($body['type'] ?? 3);
        $date = (string)($body['date'] ?? date('Y-m-d'));
        $text = trim((string)($body['text'] ?? ''));
        $submittedPw = trim((string)($body['password'] ?? ''));

        $cache = $this->cachesRepository->fetchCacheForLogOp($wp);
        if (!$cache) return new JsonResponse(['error' => 'Cache not found'], 404);
        $cacheId = (int)$cache['cache_id'];
        $cacheLogpw = (string)$cache['logpw'];
        $isOwner = (int)$cache['user_id'] === $userId;

        if (in_array($type, [9, 10, 11], true) && !$isOwner) {
            return new JsonResponse(['error' => 'Only the cache owner can submit this log type'], 403);
        }

        if ($cacheLogpw !== '' && in_array($type, [1, 7], true)) {
            if ($submittedPw === '') {
                return new JsonResponse(['error' => 'Log password required for this cache'], 422);
            }
            if (strcasecmp($submittedPw, $cacheLogpw) !== 0) {
                return new JsonResponse(['error' => 'Incorrect log password'], 422);
            }
        }

        if (in_array($type, [1, 7], true)) {
            $dup = $this->cacheLogsRepository->countDuplicatesByUserAndType($cacheId, $userId, $type, null);
            if ($dup > 0) {
                $name = $type === 1 ? 'Found it' : 'Attended';
                return new JsonResponse(['error' => "You already have a $name log on this cache — edit that one instead"], 422);
            }
        }

        if (strlen($date) === 10) $date .= ' 00:00:00';

        try {
            $newId = $this->cacheLogsRepository->insertLogSimple($cacheId, $userId, $type, $date, $text);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'DB insert failed: ' . $e->getMessage()], 500);
        }

        $statusMap = [9 => 3, 10 => 1, 11 => 2];
        if (isset($statusMap[$type])) {
            $this->cachesRepository->updateCacheStatus($cacheId, $statusMap[$type]);
        }

        $row = $this->cacheLogsRepository->fetchLogForResponse($newId);

        return new JsonResponse(['saved' => true, 'log' => $row]);
    }

    #[Route("/api/cache/{wp}/log/{logId}", name: "api_cache_log_update", methods: ["PUT"])]
    public function updateLog(string $wp, int $logId, Request $request): JsonResponse
    {
        $user = $this->auth->getUser();
        if (!$user) return new JsonResponse(['error' => 'Not authenticated'], 401);

        $userId = $user["user_id"];
        $wp = strtoupper($wp);
        $body = json_decode($request->getContent(), true);
        $type = (int)($body['type'] ?? 3);
        $date = (string)($body['date'] ?? date('Y-m-d'));
        $text = trim((string)($body['text'] ?? ''));
        $submittedPw = trim((string)($body['password'] ?? ''));

        $log = $this->cacheLogsRepository->fetchForAuth($logId);
        if (!$log || (int)$log['user_id'] !== $userId) {
            return new JsonResponse(['error' => 'Not authorized'], 403);
        }
        $cacheId = (int)$log['cache_id'];

        $cache = $this->cachesRepository->fetchCacheForLogOp($wp);
        if (!$cache) return new JsonResponse(['error' => 'Cache not found'], 404);
        $cacheLogpw = (string)$cache['logpw'];
        $isOwner = (int)$cache['user_id'] === $userId;

        if (in_array($type, [9, 10, 11], true) && !$isOwner) {
            return new JsonResponse(['error' => 'Only the cache owner can submit this log type'], 403);
        }

        if (in_array($type, [1, 7], true)) {
            $dup = $this->cacheLogsRepository->countDuplicatesByUserAndType($cacheId, $userId, $type, $logId);
            if ($dup > 0) {
                $name = $type === 1 ? 'Found it' : 'Attended';
                return new JsonResponse(['error' => "You already have a $name log on this cache — edit that one instead"], 422);
            }
        }

        if ($cacheLogpw !== '' && in_array($type, [1, 7], true)) {
            if ($submittedPw === '') {
                return new JsonResponse(['error' => 'Log password required for this cache'], 422);
            }
            if (strcasecmp($submittedPw, $cacheLogpw) !== 0) {
                return new JsonResponse(['error' => 'Incorrect log password'], 422);
            }
        }

        if (strlen($date) === 10) $date .= ' 00:00:00';

        $this->cacheLogsRepository->updateLogSimple($logId, $type, $date, $text);

        $statusMap = [9 => 3, 10 => 1, 11 => 2];
        if (isset($statusMap[$type])) {
            $this->cachesRepository->updateCacheStatus($cacheId, $statusMap[$type]);
        }

        return new JsonResponse(['saved' => true]);
    }

    #[Route("/api/cache/{wp}/log/{logId}", name: "api_cache_log_delete", methods: ["DELETE"])]
    public function deleteLog(string $wp, int $logId): JsonResponse
    {
        $user = $this->auth->getUser();
        if (!$user) return new JsonResponse(['error' => 'Not authenticated'], 401);

        $userId = $user["user_id"];
        $log = $this->cacheLogsRepository->fetchForAuth($logId);
        if (!$log || (int)$log['user_id'] !== $userId) {
            return new JsonResponse(['error' => 'Not authorized'], 403);
        }

        $this->cacheLogsRepository->deleteLogById($logId);
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
