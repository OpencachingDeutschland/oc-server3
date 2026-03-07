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
    $skip = max(0, (int)($_REQUEST['skip'] ?? 0));
    $take = min(500, max(1, (int)($_REQUEST['take'] ?? 500)));

    if ($lat1 >= $lat2 || $lon1 >= $lon2) {
        echo json_encode(['items' => []]);
        exit;
    }

    $userId = (int)($login->userid ?: 0);

    $rs = sql_slave(
        "SELECT
            c.cache_id,
            c.wp_oc         AS referenceCode,
            c.name,
            c.latitude      AS lat,
            c.longitude     AS lon,
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
            IF(fl.id IS NOT NULL, 1, 0)                          AS isFound
        FROM caches c
        INNER JOIN cache_type ct ON c.type = ct.id
        INNER JOIN cache_size  cs ON c.size = cs.id
        INNER JOIN user         u ON c.user_id = u.user_id
        LEFT  JOIN stat_caches sc ON c.cache_id = sc.cache_id
        LEFT  JOIN cache_logs  fl
               ON fl.cache_id = c.cache_id
              AND fl.user_id  = '&5'
              AND fl.type IN (1, 7)
        WHERE c.latitude  > '&1' AND c.latitude  < '&2'
          AND c.longitude > '&3' AND c.longitude < '&4'
          AND c.status IN (1, 2)
        GROUP BY c.cache_id
        ORDER BY c.cache_id
        LIMIT &6, &7",
        $lat1, $lat2,
        $lon1, $lon2,
        $userId,
        $skip, $take
    );

    $items = [];
    while ($r = sql_fetch_assoc($rs)) {
        $isDisabled = ((int)$r['status'] === 2);
        $shortName  = mb_substr($r['name'], 0, 7);

        $items[] = [
            '_id'            => $r['referenceCode'],
            'referenceCode'  => $r['referenceCode'],
            'platform'       => 'OC',
            'name'           => $r['name'],
            'lat'            => (float)$r['lat'],
            'lon'            => (float)$r['lon'],
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
        ];
    }
    sql_free_result($rs);

    echo json_encode(['items' => $items]);
    exit;
}

// ---------------------------------------------------------------------------
// outputCacheDetail()
//
// Returns JSON with enriched uniCache for a single cache (by ?wp=OCxxxx).
// Placeholder — extend with hints, attributes, additional waypoints, etc.

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
            c.latitude      AS lat,
            c.longitude     AS lon,
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
            IF(fl.id IS NOT NULL, 1, 0)        AS isFound
        FROM caches c
        INNER JOIN cache_type ct ON c.type = ct.id
        INNER JOIN cache_size  cs ON c.size = cs.id
        INNER JOIN user         u ON c.user_id = u.user_id
        LEFT  JOIN stat_caches sc ON c.cache_id = sc.cache_id
        LEFT  JOIN cache_logs  fl
               ON fl.cache_id = c.cache_id
              AND fl.user_id  = '&2'
              AND fl.type IN (1, 7)
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

    $uniCache = [
        '_id'            => $r['referenceCode'],
        'referenceCode'  => $r['referenceCode'],
        'platform'       => 'OC',
        'name'           => $r['name'],
        'lat'            => (float)$r['lat'],
        'lon'            => (float)$r['lon'],
        'geocacheType'   => ['id' => (int)$r['typeId'], 'name' => $r['typeName']],
        'geocacheSize'   => ['id' => (int)$r['sizeId'], 'name' => $r['sizeName']],
        'difficulty'     => (float)$r['difficulty'],
        'terrain'        => (float)$r['terrain'],
        'isArchived'     => false,
        'isDisabled'     => ((int)$r['status'] === 2),
        'isFound'        => (bool)(int)$r['isFound'],
        'isOwned'        => (bool)(int)$r['isOwned'],
        'isOC'           => true,
        'isGC'           => false,
        'isSelected'     => false,
        'ownerAlias'     => $r['ownerAlias'],
        'ownerCode'      => (string)$r['ownerCode'],
        'publishedDate'  => date('Y-m-d', strtotime($r['publishedDate'])),
        'favoritePoints' => (int)$r['favoritePoints'],
        'findCount'      => (int)$r['findCount'],
        'shortName'      => mb_substr($r['name'], 0, 7),
    ];

    echo json_encode(['uniCache' => $uniCache]);
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
