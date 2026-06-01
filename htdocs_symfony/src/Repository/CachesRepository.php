<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Entity\GeoCachesEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;

class CachesRepository extends ServiceEntityRepository
{
    private const TABLE = 'caches';

    private Connection $connection;

    private Security $security;

    private CachesAttributesRepository $cachesAttributesRepository;

    private CacheIgnoreRepository $cacheIgnoreRepository;

    private CacheLogsRepository $cacheLogsRepository;

    private CacheRatingRepository $cacheRatingRepository;

    private CacheSizeRepository $cacheSizeRepository;

    private CacheStatusRepository $cacheStatusRepository;

    private CacheTypeRepository $cacheTypeRepository;

    private CacheVisitsRepository $cacheVisitsRepository;

    private CacheWatchesRepository $cacheWatchesRepository;

//    private PicturesRepository $picturesRepository;

    private UserRepository $userRepository;

    public function __construct(
            Connection $connection,
            Security $security,
            CachesAttributesRepository $cachesAttributesRepository,
            CacheIgnoreRepository $cacheIgnoreRepository,
            CacheLogsRepository $cacheLogsRepository,
            CacheRatingRepository $cacheRatingRepository,
            CacheSizeRepository $cacheSizeRepository,
            CacheStatusRepository $cacheStatusRepository,
            CacheTypeRepository $cacheTypeRepository,
            CacheVisitsRepository $cacheVisitsRepository,
            CacheWatchesRepository $cacheWatchesRepository,
//            PicturesRepository $picturesRepository,
            UserRepository $userRepository
    ) {
        $this->connection = $connection;
        $this->security = $security;
        $this->cachesAttributesRepository = $cachesAttributesRepository;
        $this->cacheIgnoreRepository = $cacheIgnoreRepository;
        $this->cacheLogsRepository = $cacheLogsRepository;
        $this->cacheRatingRepository = $cacheRatingRepository;
        $this->cacheSizeRepository = $cacheSizeRepository;
        $this->cacheStatusRepository = $cacheStatusRepository;
        $this->cacheTypeRepository = $cacheTypeRepository;
        $this->cacheVisitsRepository = $cacheVisitsRepository;
        $this->cacheWatchesRepository = $cacheWatchesRepository;
//        $this->picturesRepository = $picturesRepository;
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
            $records[] = $this->getEntityFromDatabaseArray($item);
        }

        return $records;
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = []): GeoCachesEntity
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

        return $this->getEntityFromDatabaseArray($result);
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
                $entities[] = $this->getEntityFromDatabaseArray($item);
            }
        }

        return $entities;
    }

    /**
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */
    public function create(GeoCachesEntity $entity): GeoCachesEntity
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                self::TABLE,
                $databaseArray
        );

        $entity->cacheId = (int)$this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function update(GeoCachesEntity $entity): GeoCachesEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(self::TABLE, $databaseArray, ['cache_id' => $entity->cacheId]);

        return $entity;
    }

    /**
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function remove(GeoCachesEntity $entity): GeoCachesEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(self::TABLE, ['cache_id' => $entity->cacheId]);

        $entity->cacheId = 0;

        return $entity;
    }

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

    public function getDatabaseArrayFromEntity(GeoCachesEntity $entity): array
    {
        return [
                'cache_id' => $entity->cacheId,
                'uuid' => $entity->uuid,
                'node' => $entity->node,
                'date_created' => $entity->dateCreated,
                'is_publishdate' => $entity->isPublishdate,
                'last_modified' => date('Y-m-d H:i:s'),
                'okapi_syncbase' => $entity->okapiSyncbase,
                'listing_last_modified' => $entity->listingLastModified,
                'meta_last_modified' => $entity->metaLastModified,
                'user_id' => $entity->userId,
                'name' => $entity->name,
                'longitude' => $entity->longitude,
                'latitude' => $entity->latitude,
                'type' => $entity->type,
                'status' => $entity->status,
                'country' => $entity->country,
                'date_hidden' => $entity->dateHidden,
                'size' => $entity->size,
                'difficulty' => $entity->difficulty,
                'terrain' => $entity->terrain,
                'logpw' => $entity->logpw,
                'search_time' => $entity->searchTime,
                'way_length' => $entity->wayLength,
                'wp_gc' => $entity->wpGc,
                'wp_gc_maintained' => $entity->wpGcMaintained,
                'wp_nc' => $entity->wpNc,
                'wp_oc' => $entity->wpOc,
                'desc_languages' => $entity->descLanguages,
                'default_desclang' => $entity->defaultDesclang,
                'date_activate' => $entity->dateActivate,
                'need_npa_recalc' => $entity->needNpaRecalc,
                'show_cachelists' => $entity->showCachelists,
                'protect_old_coords' => $entity->protectOldCoords,
                'needs_maintenance' => $entity->needsMaintenance,
                'listing_outdated' => $entity->listingOutdated,
                'flags_last_modified' => $entity->flagsLastModified,
                'gdpr_deletion' => $entity->gdprDeletion,
                'cache_size' => $entity->cacheSize,
                'cache_status' => $entity->cacheStatus,
                'cache_type' => $entity->cacheType,
                'user' => $entity->user,
                'rating_count' => $entity->ratingCount,
                'ignore_count' => $entity->ignoreCount,
                'watches_count' => $entity->watchesCount,
                'visits_count' => $entity->visitsCount,
                'cache_logs' => $entity->cacheLogs,
                'logs_count' => $entity->logsCount,
                'picture_count' => $entity->pictureCount,
                'image_Name' => $entity->imageName,
        ];
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data): GeoCachesEntity
    {
        $entity = new GeoCachesEntity();

        $entity->cacheId = (int)$data['cache_id'];
        $entity->uuid = (string)$data['uuid'];
        $entity->node = (int)$data['node'];
        $entity->dateCreated = $data['date_created'];
        $entity->isPublishdate = (int)$data['is_publishdate'];
        $entity->lastModified = date('Y-m-d H:i:s');
        $entity->okapiSyncbase = (string)$data['okapi_syncbase'];
        $entity->listingLastModified = $data['listing_last_modified'];
        $entity->metaLastModified = $data['meta_last_modified'];
        $entity->userId = (int)$data['user_id'];
        $entity->name = (string)$data['name'];
        $entity->longitude = (float)$data['longitude'];
        $entity->latitude = (float)$data['latitude'];
        $entity->type = (int)$data['type'];
        $entity->status = (int)$data['status'];
        $entity->country = (string)$data['country'];
        $entity->dateHidden = $data['date_hidden'];
        $entity->size = (int)$data['size'];
        $entity->difficulty = (int)$data['difficulty'];
        $entity->terrain = (int)$data['terrain'];
        //        $entity->logpw = (string) $data['logpw'];
        $entity->logpw = ($data['logpw'] == '') ? '' : '1';
        $entity->searchTime = (float)$data['search_time'];
        $entity->wayLength = (float)$data['way_length'];
        $entity->wpGc = (string)$data['wp_gc'];
        $entity->wpGcMaintained = (string)$data['wp_gc_maintained'];
        $entity->wpNc = (string)$data['wp_nc'];
        $entity->wpOc = (string)$data['wp_oc'];
        $entity->descLanguages = (string)$data['desc_languages'];
        $entity->defaultDesclang = (string)$data['default_desclang'];
        $entity->dateActivate = $data['date_activate'] ?? '';
        $entity->needNpaRecalc = (int)$data['need_npa_recalc'];
        $entity->showCachelists = (int)$data['show_cachelists'];
        $entity->protectOldCoords = (int)$data['protect_old_coords'];
        $entity->needsMaintenance = (int)$data['needs_maintenance'];
        $entity->listingOutdated = (int)$data['listing_outdated'];
        $entity->flagsLastModified = $data['flags_last_modified'];
        $entity->gdprDeletion = (bool)$data['gdpr_deletion'];
        $entity->cacheSize = $this->cacheSizeRepository->fetchOneBy(['id' => $entity->size]);
        $entity->cacheStatus = $this->cacheStatusRepository->fetchOneBy(['id' => $entity->status]);
        $entity->cacheType = $this->cacheTypeRepository->fetchOneBy(['id' => $entity->type]);
        $entity->user = $this->userRepository->fetchOneById($entity->userId);
        $entity->ratingCount = $this->cacheRatingRepository->countRating(['cache_id' => $entity->cacheId]);
        $entity->ignoreCount = $this->cacheIgnoreRepository->fetchOneByCount(['cache_id' => $entity->cacheId]);
        $entity->watchesCount = $this->cacheWatchesRepository->fetchOneByCount(['cache_id' => $entity->cacheId]);
        $entity->visitsCount = $this->cacheVisitsRepository->getCountVisits(['cache_id' => $entity->cacheId]);
        $entity->cacheLogs = $this->cacheLogsRepository->fetchBy(['cache_id' => $entity->cacheId]);
        $entity->logsCount = $this->cacheLogsRepository->countLogs($entity->cacheId);
        $entity->pictureCount = $this->cacheLogsRepository->getCountPictures(['cache_id' => $entity->cacheId]);
        $entity->imageName = $this->getCacheiconImagename($entity);

        return $entity;
    }

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
    public function getCacheiconImagename(GeoCachesEntity $entity): array
    {
        if ($this->security->getUser() != null) {
            $loggedInUserId = $this->security->getUser()->userId;
        } else {
            $loggedInUserId = 0;
        }
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
        $result = $this->connection->fetchAssociative(
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

        return $result ?: null;
    }

    /**
     * Fetch the cache description, preferring the given language first, then EN.
     *
     * @throws Exception
     */
    public function fetchDescription(int $cacheId, string $preferredLang): ?array
    {
        $result = $this->connection->fetchAssociative(
            'SELECT cd.desc, cd.hint, cd.short_desc, cd.desc_html, cd.desc_dark_unsafe, cd.language
             FROM cache_desc cd
             WHERE cd.cache_id = ?
             ORDER BY cd.language = ? DESC, cd.language = \'EN\' DESC
             LIMIT 1',
            [$cacheId, $preferredLang]
        );

        return $result ?: null;
    }

    /**
     * Fetch listing waypoints (additional waypoints placed by the owner, type=1).
     *
     * @throws Exception
     */
    public function fetchWaypoints(int $cacheId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT co.latitude, co.longitude, co.description, ct.name AS type_name, ct.id AS type_id
             FROM coordinates co
             LEFT JOIN coordinates_type ct ON co.subtype = ct.id
             WHERE co.cache_id = ? AND co.type = 1 AND co.user_id IS NULL
             ORDER BY co.id',
            [$cacheId]
        );
    }

    /**
     * Fetch cache attributes with their icons.
     *
     * @throws Exception
     */
    public function fetchAttributes(int $cacheId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT ca.id, ca.name, ca.icon
             FROM caches_attributes cxa
             JOIN cache_attrib ca ON cxa.attrib_id = ca.id
             WHERE cxa.cache_id = ?
             ORDER BY ca.id',
            [$cacheId]
        );
    }

    /**
     * Fetch most recent logs for a cache with user and log type info.
     *
     * @throws Exception
     */
    public function fetchLogs(int $cacheId, int $limit = 30): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT cl.id, cl.uuid, cl.type, lt.en AS type_name,
                    DATE_FORMAT(cl.date, \'%Y-%m-%d\') AS date,
                    cl.text, cl.text_html, cl.user_id,
                    u.username
             FROM cache_logs cl
             JOIN user u       ON cl.user_id = u.user_id
             LEFT JOIN log_types lt ON cl.type = lt.id
             WHERE cl.cache_id = ? AND cl.gdpr_deletion = 0
             ORDER BY cl.date DESC
             LIMIT ' . (int)$limit,
            [$cacheId]
        );
    }

    /**
     * Fetch the logged-in user's personal cache note row (coordinates type=2).
     * Returns the note text, corrected coordinates, and remembered log password.
     *
     * @throws Exception
     */
    public function fetchUserNote(int $cacheId, int $userId): ?array
    {
        $result = $this->connection->fetchAssociative(
            'SELECT description, latitude, longitude, logpw FROM coordinates
             WHERE cache_id=? AND user_id=? AND type=2 ORDER BY id DESC LIMIT 1',
            [$cacheId, $userId]
        );

        return $result ?: null;
    }

    /**
     * Fetch the region (adm1) for a cache from cache_location.
     *
     * @throws Exception
     */
    public function fetchRegion(int $cacheId): ?string
    {
        $result = $this->connection->fetchOne(
            'SELECT adm1 FROM cache_location WHERE cache_id=?',
            [$cacheId]
        );

        return $result ?: null;
    }

    /**
     * Fetch owner statistics (found / hidden counts) from stat_user.
     *
     * @throws Exception
     */
    public function fetchOwnerStats(int $ownerId): array
    {
        $result = $this->connection->fetchAssociative(
            'SELECT IFNULL(found, 0) AS found, IFNULL(hidden, 0) AS hidden FROM stat_user WHERE user_id=?',
            [$ownerId]
        );

        return $result ?: ['found' => 0, 'hidden' => 0];
    }

    /**
     * Check whether a user is watching a cache.
     *
     * @throws Exception
     */
    public function isWatchedByUser(int $cacheId, int $userId): bool
    {
        return $this->cacheWatchesRepository->fetchOneByCount([
            'cache_id' => $cacheId,
            'user_id'  => $userId,
        ]) > 0;
    }

    /**
     * Check whether a user has recommended a cache.
     *
     * @throws Exception
     */
    public function isRecommendedByUser(int $cacheId, int $userId): bool
    {
        return $this->cacheRatingRepository->getRatingUserCache([
            'cache_id' => $cacheId,
            'user_id'  => $userId,
        ]);
    }
}
