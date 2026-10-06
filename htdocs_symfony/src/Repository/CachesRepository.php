<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;
use Oc\Security\Auth;

class CachesRepository
{
    private const TABLE = 'caches';

    private Connection $connection;

    private Auth $auth;

    private CachesAttributesRepository $cachesAttributesRepository;

    private CacheSizeRepository $cacheSizeRepository;

    private CacheStatusRepository $cacheStatusRepository;

    private CacheTypeRepository $cacheTypeRepository;

    private UserRepository $userRepository;

    public function __construct(
            Connection $connection,
            Auth $auth,
            CachesAttributesRepository $cachesAttributesRepository,
            CacheSizeRepository $cacheSizeRepository,
            CacheStatusRepository $cacheStatusRepository,
            CacheTypeRepository $cacheTypeRepository,
            UserRepository $userRepository
    ) {
        $this->connection = $connection;
        $this->auth = $auth;
        $this->cachesAttributesRepository = $cachesAttributesRepository;
        $this->cacheSizeRepository = $cacheSizeRepository;
        $this->cacheStatusRepository = $cacheStatusRepository;
        $this->cacheTypeRepository = $cacheTypeRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     */
    public function fetchAll(): array
    {
        $statement = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->executeQuery();

        $result = $statement->fetchAllAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records found');
        }

        $records = [];

        foreach ($result as $item) {
            $records[] = $item;
        }

        return $records;
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = []): ?array
    {
        $queryBuilder = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->setMaxResults(1);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $result ?: null;
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function fetchBy(array $where = []): array
    {
        $entities = [];

        $queryBuilder = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->orWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAllAssociative();

        if ($statement->rowCount() === 0) {
            //            throw new RecordsNotFoundException('No records with given where clause found');
        } else {
            foreach ($result as $item) {
                $entities[] = $item;
            }
        }

        return $entities;
    }

    /**
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */

    /**
     * @throws RecordNotPersistedException
     * @throws Exception
     */

    /**
     * @throws RecordNotPersistedException
     * @throws Exception
     */

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function getIdByWP(string $wp = ''): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->setMaxResults(1);

        if ($wp != '') {
            $queryBuilder->where('wp_oc = ' . $queryBuilder->createNamedParameter($wp));
            $queryBuilder->orWhere('wp_gc = ' . $queryBuilder->createNamedParameter($wp));
        }

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        } else {
            return (int)$result['cache_id'];
        }
    }


    /**
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws \Exception
     */

    /**
     * @throws RecordNotFoundException
     */
    public function isNew(string $wp): bool
    {
        try {
            if ($this->fetchOneBy(['wp_oc' => $wp])) {
                return false;
            }
        } catch (Exception $exception) {
            return true;
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function getCachesForSearchField(string $searchtext): array
    {
        //      so sieht die SQL-Vorlage aus..
        //        SELECT cache_id, name, wp_oc, user.username
        //        FROM caches
        //        INNER JOIN user ON caches.user_id = user.user_id
        //        WHERE wp_oc         =       "' . $searchtext . '"
        //        OR wp_gc            =       "' . $searchtext . '"
        //        OR caches.name     LIKE    "%' . $searchtext . '%"'
        //        OR user.username   LIKE    "%' . $searchtext . '%"'
        $qb = $this->connection->createQueryBuilder()
                ->select('caches.cache_id', 'caches.name', 'caches.wp_oc', 'caches.wp_gc', 'user.username')
                ->from('caches')
                ->innerJoin('caches', 'user', 'user', 'caches.user_id = user.user_id')
                ->where('caches.wp_oc = :searchTerm')
                ->orWhere('caches.wp_gc = :searchTerm')
                ->orWhere('caches.name LIKE :searchTermLIKE')
                ->orWhere('user.username LIKE :searchTermLIKE')
                ->setParameters(['searchTerm' => $searchtext, 'searchTermLIKE' => '%' . $searchtext . '%'])
                ->orderBy('caches.wp_oc', 'ASC');

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function getCachesForSearchFieldWPOnly(string $searchtext): array
    {
        $qb = $this->connection->createQueryBuilder()
                ->select('caches.cache_id', 'caches.name', 'caches.wp_oc', 'caches.wp_gc', 'user.username')
                ->from('caches')
                ->innerJoin('caches', 'user', 'user', 'caches.user_id = user.user_id')
                ->where('caches.wp_oc = :searchTermOC')
                ->orWhere('caches.wp_gc = :searchTermGC')
                ->setParameters(['searchTermOC' => 'OC' . $searchtext, 'searchTermGC' => 'GC' . $searchtext])
                ->orderBy('caches.wp_oc', 'ASC');

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function getCacheDetailsById(int $id): array
    {
        $fetchedCache = $this->fetchOneBy(['cache_id' => $id]);

        return [$this->getDatabaseArrayFromEntity($fetchedCache)];
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function getCacheDetailsByWayPoint(string $wayPoint): array
    {
        $fetchedCache = $this->fetchOneBy(['wp_oc' => $wayPoint]);

        return $this->getDatabaseArrayFromEntity($fetchedCache);
    }

    public function search_by_cache_wp(string $wpID): array
    {
        $fetchedCache = [];

        try {
            $fetchedCache = $this->getCacheDetailsByWayPoint($wpID);
        } catch (\Exception $e) {
            //  tue was.. (status_not_found = true);
        }

        return $fetchedCache;
    }

    // TODO: slini, Verwendung?

    /**
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function getCacheLogsByWayPoint(string $wayPoint): array
    {
        $fetchedCache = $this->cacheLogsRepository->fetchOneBy(['wp_oc' => $wayPoint]);

        return [$this->cacheLogsRepository->getDatabaseArrayFromEntity($fetchedCache)];
    }


    /**
     * evaluate cache status and provide some information for determining correct icon image name
     *
     * @throws Exception
     */
    public function getCacheiconImagename(array $entity): array
    {
        $user = $this->auth->getUser();
        $loggedInUserId = $user ? (int) $user['user_id'] : 0;
        $result = array();

        $result['type'] = $entity->cacheType->svgName;

        if ($entity->cacheStatus->name === 'Available') {
            $result['status'] = '-active';
        } elseif ($entity->cacheStatus->name === 'Archived') {
            $result['status'] = '-archived';
        } else {
            $result['status'] = '-inactive';
        }

        if ($this->cacheLogsRepository->checkLogStatus($loggedInUserId, $entity->cacheId, '1, 7')) {
            $result['found'] = '-found';
        } elseif ($this->cacheLogsRepository->checkLogStatus($loggedInUserId, $entity->cacheId, '2')) {
            $result['found'] = '-notfound';
        } else {
            $result['found'] = '-untried';
        }

        // set oconly-marker only if cache is active and untried
        if (($result['status'] === '-active') && ($result['found'] === '-untried')) {
            $result['oconly'] = ($this->cachesAttributesRepository->isOCOnly($entity->cacheId) ? '-oconly-border' : '');
        } else {
            $result['oconly'] = '';
        }

        $result['owned'] = ($loggedInUserId === $entity->userId) ? '-owned' : '';

        // iconStandardName = simple icon of cache type without any additional information
        $result['iconStandardName'] = $entity->cacheType->svgName . '-active-untried.svg';

        // iconCurrentName = icon of cache type including additional information like owner, found status, deactivation status of this cache, etc.
        if (!empty($result['owned'])) {
            $result['iconCurrentName'] = $result['type'] . $result['status'] . $result['owned'] . '.svg';
        } else {
            $result['iconCurrentName'] = $result['type'] . $result['status'] . $result['found'] . $result['oconly'] . '.svg';
        }

        return $result;
    }

    /**
     * Fetch the main cache row with all JOINs needed for the API detail endpoint.
     *
     * @throws Exception
     */
    public function fetchDetailByWp(string $wp): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'c.cache_id', 'c.wp_oc', 'c.name',
                'c.latitude', 'c.longitude',
                'c.difficulty / 2 AS difficulty',
                'c.terrain / 2 AS terrain',
                'c.country', 'c.date_hidden', 'c.date_created', 'c.wp_gc',
                'c.type AS type_id',
                'c.size AS size_id',
                'c.status AS status_id',
                'c.search_time', 'c.way_length',
                "IF(c.logpw != '', 1, 0) AS logpw",
                'c.logpw AS cache_logpw',
                'c.needs_maintenance', 'c.listing_outdated',
                'ct.en AS type_name', 'ct.svg_name',
                'cs.name AS size_name',
                'cst.en AS status_en',
                'u.user_id AS owner_id', 'u.username AS owner_name', 'u.uuid AS owner_uuid',
                'u.date_created AS owner_joined',
                'IFNULL(sc.found, 0) AS find_count',
                'IFNULL(sc.toprating, 0) AS rating_count',
                'co_name.name AS country_name'
            )
            ->from('caches', 'c')
            ->join('c', 'cache_type', 'ct', 'c.type = ct.id')
            ->join('c', 'cache_size', 'cs', 'c.size = cs.id')
            ->join('c', 'cache_status', 'cst', 'c.status = cst.id')
            ->join('c', 'user', 'u', 'c.user_id = u.user_id')
            ->leftJoin('c', 'stat_caches', 'sc', 'c.cache_id = sc.cache_id')
            ->leftJoin('c', 'countries', 'co_name', 'c.country = co_name.short')
            ->where('c.wp_oc = :wp')
            ->setParameter('wp', $wp)
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }

    /**
     * Fetch the region (adm1) for a cache from cache_location.
     *
     * @throws Exception
     */
    public function fetchRegion(int $cacheId): ?string
    {
        $result = $this->connection->createQueryBuilder()
            ->select('adm1')
            ->from('cache_location')
            ->where('cache_id = :cacheId')
            ->setParameter('cacheId', $cacheId)
            ->executeQuery()
            ->fetchOne();

        return $result ?: null;
    }

    /**
     * Check whether a user is watching a cache.
     *
     * @throws Exception
     */
    public function isWatchedByUser(int $cacheId, int $userId): bool
    {
        return (bool) $this->connection->createQueryBuilder()
            ->select('cache_id')->from('cache_watches')
            ->where('cache_id = :cid')->andWhere('user_id = :uid')
            ->setParameters(['cid' => $cacheId, 'uid' => $userId])
            ->executeQuery()->fetchOne();
    }

    /** @throws Exception */
    public function isRecommendedByUser(int $cacheId, int $userId): bool
    {
        return (bool) $this->connection->createQueryBuilder()
            ->select('cache_id')->from('cache_rating')
            ->where('cache_id = :cid')->andWhere('user_id = :uid')
            ->setParameters(['cid' => $cacheId, 'uid' => $userId])
            ->executeQuery()->fetchOne();
    }

    // ── Lookup queries (kept for tables without dedicated repos) ──────

    /** @throws Exception */
    public function fetchLookupLanguages(string $lang): array
    {
        return $this->connection->createQueryBuilder()
            ->select('l.short', 'IFNULL(stt.text, l.name) AS name')
            ->from('languages', 'l')
            ->leftJoin('l', 'sys_trans', 'st', 'l.trans_id = st.id')
            ->leftJoin('st', 'sys_trans_text', 'stt', 'st.id = stt.trans_id AND stt.lang = :lang')
            ->orderBy('name')
            ->setParameter('lang', $lang)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** @throws Exception */
    public function fetchAllAttributes(): array
    {
        return $this->connection->createQueryBuilder()
            ->select('ca.id', 'ca.name', 'ca.icon_undef', 'ca.icon_large', 'ca.group_id', 'ag.name AS group_name')
            ->from('cache_attrib', 'ca')
            ->join('ca', 'attribute_groups', 'ag', 'ca.group_id = ag.id')
            ->where('NOT IFNULL(ca.hidden, 0)')
            ->andWhere('ca.selectable != 0')
            ->orderBy('ag.category_id')
            ->addOrderBy('ca.group_id')
            ->addOrderBy('ca.id')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** @throws Exception */
    public function fetchWaypointTypes(): array
    {
        return $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from('coordinates_type')
            ->orderBy('id')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    // ── Cache CRUD ────────────────────────────────────────────────────

    /** @throws Exception */
    public function fetchCacheByWpForEdit(string $wp): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('caches')
            ->where('wp_oc = :wp')
            ->setParameter('wp', strtoupper($wp))
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }

    /** @throws Exception */
    public function getCacheIdByWp(string $wp): ?int
    {
        $result = $this->connection->createQueryBuilder()
            ->select('cache_id')
            ->from('caches')
            ->where('wp_oc = :wp')
            ->setParameter('wp', strtoupper($wp))
            ->executeQuery()
            ->fetchOne();

        return $result ? (int)$result : null;
    }

    /** @throws Exception */
    public function checkDuplicateCoords(float $lon, float $lat, ?int $excludeCacheId): ?string
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('wp_oc')
            ->from('caches')
            ->where('status = 1')
            ->andWhere('ROUND(longitude, 6) = ROUND(:lon, 6)')
            ->andWhere('ROUND(latitude, 6) = ROUND(:lat, 6)')
            ->setParameters(['lon' => $lon, 'lat' => $lat]);

        if ($excludeCacheId !== null) {
            $qb->andWhere('cache_id != :excludeId')
               ->setParameter('excludeId', $excludeCacheId);
        }

        $result = $qb->executeQuery()->fetchOne();
        return $result ? (string)$result : null;
    }

    /** @throws Exception */
    public function insertCache(array $data): int
    {
        $this->connection->insert('caches', $data);
        return (int)$this->connection->lastInsertId();
    }

    /** @throws Exception */
    public function updateCache(int $cacheId, array $data): void
    {
        $this->connection->update('caches', $data, ['cache_id' => $cacheId]);
    }

    /** @throws Exception */
    public function countActiveCaches(): int
    {
        return (int)$this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('caches')
            ->where('status = 1')
            ->executeQuery()
            ->fetchOne();
    }

    // ── Cache Logs helpers (queries caches table) ─────────────────────

    /** @throws Exception */
    public function fetchCacheForLogOp(string $wp): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('cache_id', 'logpw', 'user_id')
            ->from('caches')
            ->where('wp_oc = :wp')
            ->setParameter('wp', strtoupper($wp))
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }

    /** @throws Exception */
    public function updateCacheStatus(int $cacheId, int $status): void
    {
        $this->connection->update('caches', ['status' => $status], ['cache_id' => $cacheId]);
    }

    /** @throws Exception */
    public function countCachesInBounds(float $lat1, float $lat2, float $lon1, float $lon2, int $minDiff, int $maxDiff): int
    {
        return (int)$this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('caches')
            ->where('latitude > :lat1 AND latitude < :lat2')
            ->andWhere('longitude > :lon1 AND longitude < :lon2')
            ->andWhere('status IN (1, 2)')
            ->andWhere('difficulty >= :minDiff AND difficulty <= :maxDiff')
            ->setParameters([
                'lat1' => $lat1, 'lat2' => $lat2,
                'lon1' => $lon1, 'lon2' => $lon2,
                'minDiff' => $minDiff, 'maxDiff' => $maxDiff,
            ])
            ->executeQuery()
            ->fetchOne();
    }

    /** @throws Exception */
    public function fetchCachesInBounds(float $lat1, float $lat2, float $lon1, float $lon2, int $minDiff, int $maxDiff, int $userId, int $maxItems): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'c.cache_id',
                'c.wp_oc         AS referenceCode',
                'c.name',
                'c.latitude      AS listingLat',
                'c.longitude     AS listingLon',
                'c.type          AS typeId',
                'ct.en           AS typeName',
                'c.size          AS sizeId',
                'cs.name         AS sizeName',
                'c.difficulty / 2 AS difficulty',
                'c.terrain    / 2 AS terrain',
                'c.status',
                'u.username      AS ownerAlias',
                'u.username      AS ownerCode',
                'c.user_id       AS userId',
                'c.date_created  AS publishedDate',
                'IFNULL(sc.toprating, 0) AS favoritePoints',
                'IFNULL(sc.found, 0)     AS findCount',
                'IF(c.user_id = :userId, 1, 0) AS isOwned',
                'IF(fl.id IS NOT NULL, 1, 0)  AS isFound',
                'MAX(fl.date) AS foundDate',
                'IF(pcn.id IS NOT NULL, 1, 0) AS hasPCN',
                'IF(pcn.id IS NOT NULL AND pcn.latitude != 0 AND pcn.longitude != 0, 1, 0) AS hasCC',
                'pcn.latitude    AS ccLat',
                'pcn.longitude   AS ccLon',
                'pcn.description AS pcnText',
                'IF(oc_only.cache_id IS NOT NULL, 1, 0) AS isOcOnly'
            )
            ->from('caches', 'c')
            ->innerJoin('c', 'cache_type', 'ct', 'c.type = ct.id')
            ->innerJoin('c', 'cache_size', 'cs', 'c.size = cs.id')
            ->innerJoin('c', 'user', 'u', 'c.user_id = u.user_id')
            ->leftJoin('c', 'stat_caches', 'sc', 'c.cache_id = sc.cache_id')
            ->leftJoin('c', 'caches_attributes', 'oc_only', 'oc_only.cache_id = c.cache_id AND oc_only.attrib_id = 6')
            ->leftJoin('c', 'cache_logs', 'fl', 'fl.cache_id = c.cache_id AND fl.user_id = :userId AND fl.type IN (1, 7)')
            ->leftJoin('c', 'coordinates', 'pcn', 'pcn.cache_id = c.cache_id AND pcn.user_id = :userId AND pcn.type = 2')
            ->where('c.latitude > :lat1 AND c.latitude < :lat2')
            ->andWhere('c.longitude > :lon1 AND c.longitude < :lon2')
            ->andWhere('c.status IN (1, 2)')
            ->andWhere('c.difficulty >= :minDiff AND c.difficulty <= :maxDiff')
            ->groupBy('c.cache_id')
            ->orderBy('c.cache_id')
            ->setMaxResults($maxItems)
            ->setParameters([
                'lat1' => $lat1, 'lat2' => $lat2,
                'lon1' => $lon1, 'lon2' => $lon2,
                'minDiff' => $minDiff, 'maxDiff' => $maxDiff,
                'userId' => $userId,
            ])
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** @throws Exception */
    public function getWpOcById(int $cacheId): string
    {
        return (string)$this->connection->createQueryBuilder()
            ->select('wp_oc')
            ->from('caches')
            ->where('cache_id = :cacheId')
            ->setParameter('cacheId', $cacheId)
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * Dynamic cache search with optional filters. All columns used by apiSearchCaches.
     * @throws Exception
     */
    public function searchCaches(array $criteria): array
    {
        $userId  = (int)($criteria['userId'] ?? 0);
        $q       = trim((string)($criteria['q'] ?? ''));
        $type    = (int)($criteria['type'] ?? 0);
        $minDiff = (int)($criteria['minDiff'] ?? 2);
        $maxDiff = (int)($criteria['maxDiff'] ?? 10);
        $activeOnly = (bool)($criteria['activeOnly'] ?? true);
        $ocOnly = (bool)($criteria['ocOnly'] ?? false);
        $lat    = isset($criteria['lat']) ? (float)$criteria['lat'] : null;
        $lon    = isset($criteria['lon']) ? (float)$criteria['lon'] : null;
        $radius = (float)($criteria['radius'] ?? 0);

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

        return $qb->executeQuery()->fetchAllAssociative();
    }
}
