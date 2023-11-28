<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Entity\GeoCachesEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;
use Symfony\Component\Security\Core\Security;

class CachesRepository
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
}
