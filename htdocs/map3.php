<?php
/***************************************************************************
 * for license information see LICENSE.md
 * Author: hxdimpf
 ***************************************************************************/

require __DIR__ . '/lib2/web.inc.php';

$login->verify();

$sMode = $_REQUEST['mode'] ?? '';

if ($sMode === 'live') {
    outputLiveMarkers();
} elseif ($sMode === 'cache') {
    outputCacheDetail();
} else {
    renderPage();
}

// ---------------------------------------------------------------------------
// outputLiveMarkers()
//
// Returns JSON array of OC caches in the given bounding box.
// All caches returned as uniCache-compatible objects with transient WP fields.

function outputLiveMarkers()
{
    global $login, $opt;

    header('Content-Type: application/json; charset=utf-8');

    $lat1 = (float)($_REQUEST['lat1'] ?? 0);
    $lat2 = (float)($_REQUEST['lat2'] ?? 0);
    $lon1 = (float)($_REQUEST['lon1'] ?? 0);
    $lon2 = (float)($_REQUEST['lon2'] ?? 0);

    if ($lat1 >= $lat2 || $lon1 >= $lon2) {
        echo json_encode(['count' => 0, 'items' => []]);
        exit;
    }

    $maxItems = 5000;

    $count = (int)sql_value_slave(
        "SELECT COUNT(*)
         FROM caches
         WHERE latitude  > '&1' AND latitude  < '&2'
           AND longitude > '&3' AND longitude < '&4'
           AND status IN (1, 2)",
        0,
        $lat1, $lat2,
        $lon1, $lon2
    );

    if ($count > $maxItems) {
        echo json_encode(['count' => $count, 'items' => []]);
        exit;
    }

    $userId = (int)($login->userid ?: 0);

    $rs = sql_slave(
        "SELECT
            c.cache_id,
            c.wp_oc         AS referenceCode,
            c.name,
            c.latitude      AS listingLat,
            c.longitude     AS listingLon,
            c.type          AS typeId,
            ct.en           AS typeName,
            c.size          AS sizeId,
            cs.name         AS sizeName,
            c.difficulty / 2 AS difficulty,
            c.terrain    / 2 AS terrain,
            c.status,
            u.username      AS ownerAlias,
            c.user_id       AS ownerCode,
            c.date_created  AS publishedDate,
            IFNULL(sc.toprating, 0) AS favoritePoints,
            IFNULL(sc.found, 0)     AS findCount,
            IF(c.user_id = '&5', 1, 0)                           AS isOwned,
            IF(fl.id IS NOT NULL, 1, 0)                          AS isFound,
            MAX(fl.date)                                         AS foundDate,
            IF(pcn.id IS NOT NULL, 1, 0)                         AS hasPCN,
            IF(pcn.id IS NOT NULL AND pcn.latitude != 0 AND pcn.longitude != 0, 1, 0) AS hasCC,
            pcn.latitude    AS ccLat,
            pcn.longitude   AS ccLon,
            pcn.description AS pcnText
        FROM caches c
        INNER JOIN cache_type ct ON c.type = ct.id
        INNER JOIN cache_size  cs ON c.size = cs.id
        INNER JOIN user         u ON c.user_id = u.user_id
        LEFT  JOIN stat_caches sc ON c.cache_id = sc.cache_id
        LEFT  JOIN cache_logs  fl
               ON fl.cache_id = c.cache_id
              AND fl.user_id  = '&5'
              AND fl.type IN (1, 7)
        LEFT  JOIN coordinates pcn
               ON pcn.cache_id = c.cache_id
              AND pcn.user_id  = '&5'
              AND pcn.type     = 2
        WHERE c.latitude  > '&1' AND c.latitude  < '&2'
          AND c.longitude > '&3' AND c.longitude < '&4'
          AND c.status IN (1, 2)
        GROUP BY c.cache_id
        ORDER BY c.cache_id
        LIMIT &6",
        $lat1, $lat2,
        $lon1, $lon2,
        $userId,
        $maxItems
    );

    $items = [];
    while ($r = sql_fetch_assoc($rs)) {
        $isDisabled = ((int)$r['status'] === 2);
        $shortName  = mb_strlen($r['name']) > 25 ? mb_substr($r['name'], 0, 25) . '…' : $r['name'];
        $hasCC      = (bool)(int)$r['hasCC'];
        $lat        = $hasCC ? (float)$r['ccLat'] : (float)$r['listingLat'];
        $lon        = $hasCC ? (float)$r['ccLon'] : (float)$r['listingLon'];

        $items[] = [
            '_id'            => $r['referenceCode'],
            'referenceCode'  => $r['referenceCode'],
            'platform'       => 'OC',
            'name'           => $r['name'],
            'lat'            => $lat,
            'lon'            => $lon,
            'listingLat'     => (float)$r['listingLat'],
            'listingLon'     => (float)$r['listingLon'],
            'geocacheType'   => [
                'id'   => (int)$r['typeId'],
                'name' => $r['typeName'],
            ],
            'geocacheSize'   => [
                'id'   => (int)$r['sizeId'],
                'name' => $r['sizeName'],
            ],
            'difficulty'     => (float)$r['difficulty'],
            'terrain'        => (float)$r['terrain'],
            'isArchived'     => false,
            'isDisabled'     => $isDisabled,
            'isFound'        => (bool)(int)$r['isFound'],
            'foundDate'      => $r['foundDate'] ? date('Y-m-d', strtotime($r['foundDate'])) : '',
            'isOwned'        => (bool)(int)$r['isOwned'],
            'isOC'           => true,
            'isGC'           => false,
            'isSelected'     => false,
            'ownerAlias'     => $r['ownerAlias'],
            'ownerCode'      => (string)$r['ownerCode'],
            'publishedDate'  => date('Y-m-d', strtotime($r['publishedDate'])),
            'favoritePoints' => (int)$r['favoritePoints'],
            'findCount'      => (int)$r['findCount'],
            'shortName'      => $shortName,
            'hasPCN'         => (bool)(int)$r['hasPCN'],
            'hasCC'          => (bool)(int)$r['hasCC'],
            'pcnText'        => $r['pcnText'] ?? '',
        ];
    }
    sql_free_result($rs);

    echo json_encode(['count' => $count, 'items' => $items]);
    exit;
}

// ---------------------------------------------------------------------------
// outputCacheDetail()
//
// Returns JSON with enriched uniCache for a single cache (by ?wp=OCxxxx).
// Returns JSON with enriched uniCache + child waypoints for a single cache.

function outputCacheDetail()
{
    global $login, $opt;

    header('Content-Type: application/json; charset=utf-8');

    $wp = $_REQUEST['wp'] ?? '';
    if (!preg_match('/^OC[A-Z0-9]+$/', $wp)) {
        http_response_code(400);
        echo json_encode(['error' => 'invalid waypoint']);
        exit;
    }

    $userId = (int)($login->userid ?: 0);

    $r = sql_fetch_assoc(sql_slave(
        "SELECT
            c.cache_id,
            c.wp_oc         AS referenceCode,
            c.name,
            c.latitude      AS listingLat,
            c.longitude     AS listingLon,
            c.type          AS typeId,
            ct.en           AS typeName,
            c.size          AS sizeId,
            cs.name         AS sizeName,
            c.difficulty / 2 AS difficulty,
            c.terrain    / 2 AS terrain,
            c.status,
            u.username      AS ownerAlias,
            c.user_id       AS ownerCode,
            c.date_created  AS publishedDate,
            IFNULL(sc.toprating, 0) AS favoritePoints,
            IFNULL(sc.found, 0)     AS findCount,
            IF(c.user_id = '&2', 1, 0)        AS isOwned,
            IF(fl.id IS NOT NULL, 1, 0)        AS isFound,
            fl.date                            AS foundDate,
            IF(pcn.id IS NOT NULL, 1, 0)       AS hasPCN,
            IF(pcn.id IS NOT NULL AND pcn.latitude != 0 AND pcn.longitude != 0, 1, 0) AS hasCC,
            pcn.latitude    AS ccLat,
            pcn.longitude   AS ccLon,
            pcn.description AS pcnText
        FROM caches c
        INNER JOIN cache_type ct ON c.type = ct.id
        INNER JOIN cache_size  cs ON c.size = cs.id
        INNER JOIN user         u ON c.user_id = u.user_id
        LEFT  JOIN stat_caches sc ON c.cache_id = sc.cache_id
        LEFT  JOIN cache_logs  fl
               ON fl.cache_id = c.cache_id
              AND fl.user_id  = '&2'
              AND fl.type IN (1, 7)
        LEFT  JOIN coordinates pcn
               ON pcn.cache_id = c.cache_id
              AND pcn.user_id  = '&2'
              AND pcn.type     = 2
        WHERE c.wp_oc = '&1'
          AND c.status IN (1, 2)
        LIMIT 1",
        $wp,
        $userId
    ));

    if (!$r) {
        http_response_code(404);
        echo json_encode(['error' => 'not found']);
        exit;
    }

    $hasCC = (bool)(int)$r['hasCC'];
    $lat   = $hasCC ? (float)$r['ccLat'] : (float)$r['listingLat'];
    $lon   = $hasCC ? (float)$r['ccLon'] : (float)$r['listingLon'];

    $uniCache = [
        '_id'            => $r['referenceCode'],
        'referenceCode'  => $r['referenceCode'],
        'platform'       => 'OC',
        'name'           => $r['name'],
        'lat'            => $lat,
        'lon'            => $lon,
        'listingLat'     => (float)$r['listingLat'],
        'listingLon'     => (float)$r['listingLon'],
        'geocacheType'   => ['id' => (int)$r['typeId'], 'name' => $r['typeName']],
        'geocacheSize'   => ['id' => (int)$r['sizeId'], 'name' => $r['sizeName']],
        'difficulty'     => (float)$r['difficulty'],
        'terrain'        => (float)$r['terrain'],
        'isArchived'     => false,
        'isDisabled'     => ((int)$r['status'] === 2),
        'isFound'        => (bool)(int)$r['isFound'],
        'foundDate'      => $r['foundDate'] ? date('Y-m-d', strtotime($r['foundDate'])) : '',
        'isOwned'        => (bool)(int)$r['isOwned'],
        'isOC'           => true,
        'isGC'           => false,
        'isSelected'     => false,
        'ownerAlias'     => $r['ownerAlias'],
        'ownerCode'      => (string)$r['ownerCode'],
        'publishedDate'  => date('Y-m-d', strtotime($r['publishedDate'])),
        'favoritePoints' => (int)$r['favoritePoints'],
        'findCount'      => (int)$r['findCount'],
        'shortName'      => mb_strlen($r['name']) > 25 ? mb_substr($r['name'], 0, 25) . '…' : $r['name'],
        'hasPCN'         => (bool)(int)$r['hasPCN'],
        'hasCC'          => (bool)(int)$r['hasCC'],
        'pcnText'        => $r['pcnText'] ?? '',
    ];

    // Child waypoints (type 1 = owner-created waypoints)
    $wpTypeMap = [
        1 => 217,  // Parking
        2 => 219,  // Stage → Physical Stage
        3 => 221,  // Path → Trail Head
        4 => 220,  // Final → Final Location
        5 => 222,  // POI → Point of Interest
    ];
    $wpNameMap = [
        1 => 'Parking',
        2 => 'Stage',
        3 => 'Path',
        4 => 'Final',
        5 => 'Point of Interest',
    ];

    $wpts = [];
    $wrs = sql_slave(
        "SELECT subtype, latitude, longitude, description
         FROM coordinates
         WHERE cache_id = '&1' AND type = 1
         ORDER BY id",
        $r['cache_id']
    );
    while ($w = sql_fetch_assoc($wrs)) {
        $sub = (int)$w['subtype'];
        $wpts[] = [
            'typeId'      => $wpTypeMap[$sub] ?? 0,
            'lat'         => (float)$w['latitude'],
            'lon'         => (float)$w['longitude'],
            'name'        => $wpNameMap[$sub] ?? 'Waypoint',
            'description' => $w['description'] ?? '',
        ];
    }
    sql_free_result($wrs);

    echo json_encode(['uniCache' => $uniCache, 'wpts' => $wpts]);
    exit;
}

// ---------------------------------------------------------------------------
// renderPage() — renders the map3 page via Smarty

function renderPage()
{
    global $tpl, $login;

    // Initial map position from URL params, or user home, or Europe default
    $initLat  = 51.5;
    $initLon  = 10.0;
    $initZoom = 6;

    if (isset($_REQUEST['lat'], $_REQUEST['lon'])) {
        $initLat  = (float)$_REQUEST['lat'];
        $initLon  = (float)$_REQUEST['lon'];
        $initZoom = isset($_REQUEST['zoom']) ? (int)$_REQUEST['zoom'] : 11;
    } elseif ($login->userid > 0) {
        $user = new user($login->userid);
        $userLat = $user->getLatitude();
        $userLon = $user->getLongitude();
        if ($userLat && $userLon) {
            $initLat  = $userLat;
            $initLon  = $userLon;
            $initZoom = 11;
        }
    }

    $tpl->assign('initLat',  $initLat);
    $tpl->assign('initLon',  $initLon);
    $tpl->assign('initZoom', $initZoom);

    $tpl->add_header_javascript("https://unpkg.com/leaflet@1.9.4/dist/leaflet.js");
    $tpl->add_header_javascript("https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js");
    $tpl->add_header_javascript("https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js");

    $tpl->popup = true;
    $tpl->popupmargin = false;
    $tpl->menuitem = MNU_MAP;
    $tpl->name    = 'map3';
    $tpl->display();
}
