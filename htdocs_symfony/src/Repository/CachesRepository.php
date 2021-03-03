<?php

namespace Oc\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use Oc\Entity\GeoCachesEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class CachesRepository
 *
 * @package Oc\Repository
 */
class CachesRepository
{
    const TABLE = 'caches';

    /** @var Connection */
    private $connection;

    /**
     * @var CacheSizeRepository
     */
    private $cacheSizeRepository;

    /**
     * @var CacheStatusRepository
     */
    private $cacheStatusRepository;

    /**
     * @var CacheTypeRepository
     */
    private $cacheTypeRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * CachesRepository constructor.
     *
     * @param Connection $connection
     * @param UserRepository $userRepository
     * @param CacheSizeRepository $cacheSizeRepository
     * @param CacheStatusRepository $cacheStatusRepository
     * @param CacheTypeRepository $cacheTypeRepository
     */
    public function __construct(
        Connection $connection,
        UserRepository $userRepository,
        CacheSizeRepository $cacheSizeRepository,
        CacheStatusRepository $cacheStatusRepository,
        CacheTypeRepository $cacheTypeRepository
    ) {
        $this->connection = $connection;
        $this->userRepository = $userRepository;
        $this->cacheSizeRepository = $cacheSizeRepository;
        $this->cacheStatusRepository = $cacheStatusRepository;
        $this->cacheTypeRepository = $cacheTypeRepository;
    }

    /**
     * @return array
     * @throws RecordsNotFoundException
     */
    public function fetchAll()
    : array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->execute();

        $result = $statement->fetchAll();

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
     * @param array $where
     *
     * @return GeoCachesEntity
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = [])
    : GeoCachesEntity {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->setMaxResults(1);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * @param array $where
     *
     * @return array
     * @throws RecordsNotFoundException
     */
    public function fetchBy(array $where = [])
    : array {
        $entities = [];

        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->orWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records with given where clause found');
        } else {
            foreach ($result as $item) {
                $entities[] = $this->getEntityFromDatabaseArray($item);
            }
        }

        return $entities;
    }

    /**
     * @param GeoCachesEntity $entity
     *
     * @return GeoCachesEntity
     * @throws RecordAlreadyExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function create(GeoCachesEntity $entity)
    : GeoCachesEntity {
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
     * @param GeoCachesEntity $entity
     *
     * @return GeoCachesEntity
     * @throws \Doctrine\DBAL\DBALException
     */
    public function update(GeoCachesEntity $entity)
    : GeoCachesEntity {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(self::TABLE, $databaseArray, ['cache_id' => $entity->cacheId]);

        return $entity;
    }

    /**
     * @param GeoCachesEntity $entity
     *
     * @return GeoCachesEntity
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function remove(GeoCachesEntity $entity)
    : GeoCachesEntity {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(self::TABLE, ['cache_id' => $entity->cacheId]);

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param string $wp
     *
     * @return int
     * @throws RecordNotFoundException
     */
    public function getIdByWP(string $wp = '')
    : int {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->setMaxResults(1);

        if ($wp != '') {
            $queryBuilder->where('wp_oc = ' . $queryBuilder->createNamedParameter($wp));
            $queryBuilder->orWhere('wp_gc = ' . $queryBuilder->createNamedParameter($wp));
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        } else {
            return $result['cache_id'];
        }
    }

    /**
     * @param GeoCachesEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(GeoCachesEntity $entity)
    : array {
        return [
            'cache_id' => $entity->cacheId,
            'uuid' => $entity->uuid,
            'node' => $entity->node,
            'date_created' => $entity->dateCreated,
            'is_publishdate' => $entity->isPublishdate,
            'last_modified' => $entity->lastModified,
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
        ];
    }

    /**
     * @param array $data
     *
     * @return GeoCachesEntity
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data)
    : GeoCachesEntity {
        $entity = new GeoCachesEntity();

        $entity->cacheId = (int)$data['cache_id'];
        $entity->uuid = (string)$data['uuid'];
        $entity->node = (int)$data['node'];
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->isPublishdate = (int)$data['is_publishdate'];
        $entity->lastModified = new DateTime($data['last_modified']);
        $entity->okapiSyncbase = (string)$data['okapi_syncbase'];
        $entity->listingLastModified = new DateTime($data['listing_last_modified']);
        $entity->metaLastModified = new DateTime($data['meta_last_modified']);
        $entity->userId = (int)$data['user_id'];
        $entity->name = (string)$data['name'];
        $entity->longitude = $data['longitude'];
        $entity->latitude = $data['latitude'];
        $entity->type = (int)$data['type'];
        $entity->status = (int)$data['status'];
        $entity->country = (string)$data['country'];
        $entity->dateHidden = new DateTime($data['date_hidden']);
        $entity->size = (int)$data['size'];
        $entity->difficulty = (int)$data['difficulty'];
        $entity->terrain = (int)$data['terrain'];
        //        $entity->logpw = (string) $data['logpw'];
        $entity->logpw = ($data['logpw'] == '') ? '' : '1';
        $entity->searchTime = $data['search_time'];
        $entity->wayLength = $data['way_length'];
        $entity->wpGc = (string)$data['wp_gc'];
        $entity->wpGcMaintained = (string)$data['wp_gc_maintained'];
        $entity->wpNc = (string)$data['wp_nc'];
        $entity->wpOc = (string)$data['wp_oc'];
        $entity->descLanguages = (string)$data['desc_languages'];
        $entity->defaultDesclang = (string)$data['default_desclang'];
        $entity->dateActivate = new DateTime($data['date_activate']);
        $entity->needNpaRecalc = (int)$data['need_npa_recalc'];
        $entity->showCachelists = (int)$data['show_cachelists'];
        $entity->protectOldCoords = (int)$data['protect_old_coords'];
        $entity->needsMaintenance = (int)$data['needs_maintenance'];
        $entity->listingOutdated = (int)$data['listing_outdated'];
        $entity->flagsLastModified = new DateTime($data['flags_last_modified']);
        $entity->cacheSize = $this->cacheSizeRepository->fetchOneBy(['id' => $entity->size]);
        $entity->cacheStatus = $this->cacheStatusRepository->fetchOneBy(['id' => $entity->status]);
        $entity->cacheType = $this->cacheTypeRepository->fetchOneBy(['id' => $entity->type]);
        $entity->user = $this->userRepository->fetchOneById($entity->userId);

        return $entity;
    }
}
