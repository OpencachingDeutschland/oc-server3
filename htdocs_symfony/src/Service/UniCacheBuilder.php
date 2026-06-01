<?php

declare(strict_types=1);

namespace Oc\Service;

/**
 * Maps repository data arrays to a uniCacheWP object for the frontend.
 *
 * Replaces the dual transformation pipeline that previously required:
 *   1. Controller assembles an OKAPI-shaped $oc / $aux / $context response
 *   2. Frontend ocToUniCacheWP() + augmentForRender() to cook it into uniCacheWP
 *
 * The builder produces the cooked object directly — the frontend receives
 * a ready-to-use uniCacheWP.
 */
class UniCacheBuilder
{
    // ----------------------------------------------------------------
    // Lookup tables — OC type / size / log-type id → display name

    private const TYPE_NAMES = [
        1  => 'Unknown',
        2  => 'Traditional',
        3  => 'Multi',
        4  => 'Virtual',
        5  => 'Webcam',
        6  => 'Event',
        7  => 'Quiz',
        8  => 'Math/Physics',
        9  => 'Moving',
        10 => 'Drive-in',
    ];

    private const SIZE_IDS = [
        1 => ['id' => 1, 'name' => 'Unknown'],
        2 => ['id' => 2, 'name' => 'Micro'],
        3 => ['id' => 3, 'name' => 'Regular'],
        4 => ['id' => 4, 'name' => 'Large'],
        5 => ['id' => 5, 'name' => 'Virtual'],
        6 => ['id' => 6, 'name' => 'Other'],
        8 => ['id' => 8, 'name' => 'Small'],
    ];

    private const LOG_TYPE_NAMES = [
        1  => 'Found it',
        2  => "Didn't find it",
        3  => 'Comment',
        7  => 'Attended',
        8  => 'Will attend',
        9  => 'Archived',
        10 => 'Ready to search',
        11 => 'Temporarily unavailable',
    ];

    // ----------------------------------------------------------------
    // Public API

    /**
     * Build a uniCacheWP object from repository data and user context.
     *
     * @param array $data Repository query results keyed by:
     *   'cache', 'desc', 'waypoints', 'attributes', 'logs',
     *   'noteRow', 'region', 'ownerStats'
     * @param array $context User context:
     *   'userId', 'userName', 'isOwner', 'isWatched', 'isRecommended'
     */
    public function build(array $data, array $context): array
    {
        $cache      = $data['cache'];
        $desc       = $data['desc'];
        $waypoints  = $data['waypoints'];
        $attributes = $data['attributes'];
        $logs       = $data['logs'];
        $noteRow    = $data['noteRow'] ?? null;
        $region     = $data['region'] ?? null;
        $ownerStats = $data['ownerStats'] ?? ['found' => 0, 'hidden' => 0];

        $userId        = (int)($context['userId'] ?? 0);
        $userName      = $context['userName'] ?? null;
        $isOwner       = (bool)($context['isOwner'] ?? false);
        $isWatched     = (bool)($context['isWatched'] ?? false);
        $isRecommended = (bool)($context['isRecommended'] ?? false);

        $cacheId    = (int)$cache['cache_id'];
        $typeId     = (int)$cache['type_id'];
        $statusId   = (int)$cache['status_id'];
        $isEvent    = $typeId === 6;

        // ---- derived booleans ----

        $isFound    = false;
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

        $hasUserCoords = $noteRow
            && ((float)$noteRow['latitude'] !== 0.0 || (float)$noteRow['longitude'] !== 0.0);

        $hasPCN = $noteRow && !empty($noteRow['description']);

        $lat = (float)$cache['latitude'];
        $lon = (float)$cache['longitude'];

        $postedCoords = ['latitude' => $lat, 'longitude' => $lon];
        $correctedCoords = null;
        $effectiveLat = $lat;
        $effectiveLon = $lon;
        if ($hasUserCoords && $noteRow) {
            $correctedCoords = [
                'latitude'  => (float)$noteRow['latitude'],
                'longitude' => (float)$noteRow['longitude'],
            ];
            $effectiveLat = (float)$noteRow['latitude'];
            $effectiveLon = (float)$noteRow['longitude'];
        }

        $isOcOnly = false;
        foreach ($attributes as $a) {
            if ((int)$a['id'] === 6) {
                $isOcOnly = true;
                break;
            }
        }

        $statusStr = match ($statusId) {
            1 => 'Active',
            2 => 'Disabled',
            default => 'Archived',
        };

        $isArchived = $statusStr === 'Archived';
        $isDisabled = $statusStr === 'Disabled';

        // ---- found / dnf dates from own logs ----

        $foundDate = null;
        $dnfDate   = null;
        foreach ($logs as $l) {
            if ((int)$l['user_id'] !== $userId) continue;
            $t = (int)$l['type'];
            if ($t === 1 && !$foundDate) {
                $foundDate = substr($l['date'], 0, 10);
            }
            if ($t === 2 && !$dnfDate) {
                $dnfDate = substr($l['date'], 0, 10);
            }
            if ($foundDate && $dnfDate) break;
        }
        if ($isNotFound && !$dnfDate) {
            $dnfDate = 'DNF';
        }

        // ---- available log types for current user ----

        $logTypeIds = [];
        if ($userId) {
            $logTypeIds = $isEvent ? [7, 8, 3] : [1, 2, 3];
            if ($isOwner) {
                $logTypeIds[] = $statusId === 2 ? 10 : 11;
                $logTypeIds[] = 9;
            }
        }

        // ---- description ----

        $descHtml  = (bool)($desc['desc_html'] ?? true);
        $descDark  = (bool)($desc['desc_dark_unsafe'] ?? false);

        $parts = [];
        if ($desc['short_desc'] ?? '') {
            $parts[] = '<p><b>' . $desc['short_desc'] . '</b></p>';
        }
        if ($desc['desc'] ?? '') {
            $parts[] = $descHtml ? $desc['desc'] : str_replace("\n", '<br>', $desc['desc']);
        }
        $sanitizedDesc = implode("\n", $parts);

        // ---- formatted dates ----

        $placedDateFmt    = isset($cache['date_hidden'])  ? substr($cache['date_hidden'], 0, 10)  : '';
        $publishedDateFmt = isset($cache['date_created']) ? substr($cache['date_created'], 0, 10) : '';
        $foundDateFmt     = $foundDate ?? '';
        $dnfDateFmt       = ($dnfDate && $dnfDate !== 'DNF') ? $dnfDate : '';

        // ---- formatted coords ----

        $postedCoordsFmt    = self::decimalToDm($lat, $lon);
        $correctedCoordsFmt = $correctedCoords
            ? self::decimalToDm($correctedCoords['latitude'], $correctedCoords['longitude'])
            : '';

        // ---- hints ----

        $hints = $desc['hint'] ?? '';

        // ---- owner info ----

        $ownerUsername  = $cache['owner_name'] ?? '';
        $ownerUserId    = (int)$cache['owner_id'];
        $ownerJoined    = isset($cache['owner_joined'])
            ? substr($cache['owner_joined'], 0, 10) : null;

        // ---- log password visibility ----
        // Owner sees the cache's real password; others see their remembered one.

        $logpw = '';
        if ($isOwner) {
            $logpw = $cache['cache_logpw'] ?? '';
        } elseif ($noteRow) {
            $logpw = $noteRow['logpw'] ?? '';
        }

        // ---- assemble the uniCacheWP ----

        $uc = [
            'referenceCode' => $cache['wp_oc'],

            // name
            'name'      => $cache['name'] ?? 'Unnamed',
            'shortName' => self::truncateName($cache['name'] ?? 'Unnamed'),

            // type & size
            'geocacheType' => [
                'id'   => $typeId,
                'name' => self::TYPE_NAMES[$typeId] ?? 'Unknown',
            ],
            'geocacheSize' => self::SIZE_IDS[(int)$cache['size_id']]
                ?? ['id' => (int)$cache['size_id'], 'name' => $cache['size_name'] ?? 'Unknown'],

            // D / T
            'difficulty' => (float)$cache['difficulty'],
            'terrain'    => (float)$cache['terrain'],

            // coords
            'lat'                  => $effectiveLat,
            'lon'                  => $effectiveLon,
            'postedCoordinates'    => $postedCoords,
            'correctedCoordinates' => $correctedCoords,
            'hasCC'                => $hasUserCoords,

            // dates
            'publishedDate' => isset($cache['date_created'])
                ? substr($cache['date_created'], 0, 10) : 'unpublished',

            // owner
            'ownerCode' => $ownerUsername,

            // status
            'status'     => $statusStr,
            'isArchived' => $isArchived,
            'isDisabled' => $isDisabled,

            // user-specific flags
            'isFound'    => $isFound,
            'isDNF'      => !$isFound && $isNotFound,
            'hasPCN'     => $hasPCN,
            'isOwned'    => $isOwner,
            'isOcOnly'   => $isOcOnly,

            // vestigial GCxM fields
            'isCached'    => false,
            'isGuessable' => false,
            'isPartial'   => false,
            'isWatched'   => $isWatched,
            'isFavorited' => $isRecommended,

            // stats
            'pcn'            => $noteRow['description'] ?? null,
            'findCount'      => (int)$cache['find_count'],
            'favoritePoints' => (int)$cache['rating_count'],
            'foundDate'      => $foundDate,
            'dnfDate'        => $dnfDate !== 'DNF' ? $dnfDate : null,

            // location
            'location' => [
                'country'     => $cache['country_name'] ?: ($cache['country'] ?? null),
                'state'       => $region,
                'countryCode' => $cache['country'] ?? null,
            ],

            // timezone
            'ianaTimezoneId' => 'Europe/Berlin',

            // password
            'requiresPasswd' => (bool)$cache['logpw'],

            // ---- Render fields (formerly from augmentForRender + aux) ----

            // displayed text
            'placedDateFmt'     => $placedDateFmt,
            'publishedDateFmt'  => $publishedDateFmt,
            'foundDateFmt'      => $foundDateFmt,
            'dnfDateFmt'        => $dnfDateFmt,
            'postedCoordsFmt'   => $postedCoordsFmt,
            'correctedCoordsFmt' => $correctedCoordsFmt,

            // description
            'sanitizedDescription' => $sanitizedDesc,
            'descDarkUnsafe'    => $descDark,
            'hints'             => $hints,

            // owner profile
            'owner' => [
                'username'      => $ownerUsername,
                'userId'        => $ownerUserId,
                'profileUrl'    => sprintf('/viewprofile.php?userid=%d', $ownerUserId),
                'findCount'     => (int)($ownerStats['found'] ?? 0),
                'hideCount'     => (int)($ownerStats['hidden'] ?? 0),
                'joinedDateFmt' => $ownerJoined,
            ],

            // attributes
            'attributes' => array_map(fn($a) => [
                'id'       => (int)$a['id'],
                'name'     => $a['name'] ?? '',
                'imageUrl' => ($a['icon'] ?? '')
                    ? '/images/attributes/' . $a['icon'] . '.png'
                    : '',
            ], $attributes),

            // additional waypoints
            'additionalWaypoints' => array_map(fn($w) => [
                'location'    => sprintf('%s|%s', $w['latitude'], $w['longitude']),
                'type'        => $w['type_name'] ?? 'Waypoint',
                'type_name'   => $w['type_name'] ?? 'Waypoint',
                'name'        => $w['type_name'] ?? 'Waypoint',
                'description' => $w['description'] ?? '',
            ], $waypoints),

            // logs (full)
            'logs' => array_map(fn($l) => [
                'id'       => (int)$l['id'],
                'uuid'     => $l['uuid'],
                'type'     => (int)$l['type'],
                'typeName' => self::LOG_TYPE_NAMES[(int)$l['type']]
                    ?? (string)($l['type_name'] ?? ''),
                'date'     => $l['date'],
                'username' => $l['username'],
                'text'     => $l['text'] ?? '',
                'textHtml' => (bool)($l['text_html'] ?? false),
                'itsMine'  => $userId > 0 && (int)$l['user_id'] === $userId,
            ], $logs),

            // log type dropdown
            'logTypes' => $logTypeIds,

            // misc
            'wpGc'             => $cache['wp_gc'] ?? '',
            'searchTime'       => (float)$cache['search_time'],
            'needsMaintenance' => (bool)$cache['needs_maintenance'],
            'listingOutdated'   => (bool)$cache['listing_outdated'],
            'logpw' => $logpw,

            // context
            '_context' => [
                'userId'   => $userId,
                'userName' => $userName,
                'isOwner'  => $isOwner,
            ],
            // map.js reads isSelected from uniCacheWP elements
            'isSelected' => false,
        ];

        return $uc;
    }

    // ----------------------------------------------------------------
    // Helpers

    /**
     * Truncate a cache name to max 25 chars for map labels.
     */
    private static function truncateName(string $name, int $maxLen = 25): string
    {
        $name = str_replace("'", '`', $name);
        if (mb_strlen($name) > $maxLen) {
            return mb_substr($name, 0, $maxLen) . ' ...';
        }
        return $name;
    }

    /**
     * Convert decimal lat/lon to Degree-Minutes format.
     * Matches the format produced by the JS coords2Dm() function.
     */
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
