<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CachesRepository
{
    const TABLE = 'caches';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCachesEntity[]
     */
    public function fetchAll()
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
     * @return GeoCachesEntity
     */
    public function fetchOneBy(array $where = [])
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

        $statement = $queryBuilder->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * @param array $where
     * @return GeoCachesEntity[]
     */
    public function fetchBy(array $where = [])
    {
        $queryBuilder = $this->connection->createQueryBuilder()
                     ->select('*')
                     ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records with given where clause found');
        }

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $this->getEntityFromDatabaseArray($item);
        }

        return $entities;
    }

    /**
     * @param GeoCachesEntity $entity
     * @return GeoCachesEntity
     */
    public function create(GeoCachesEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->cacheId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param GeoCachesEntity $entity
     * @return GeoCachesEntity
     */
    public function update(GeoCachesEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['cache_id' => $entity->cacheId]
                );

        return $entity;
    }

    /**
     * @param GeoCachesEntity $entity
     * @return GeoCachesEntity
     */
    public function remove(GeoCachesEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['cache_id' => $entity->cacheId]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param GeoCachesEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCachesEntity $entity)
    {
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
        ];
    }

    /**
     * @param array $data
     * @return GeoCachesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCachesEntity();
        $entity->cacheId = $data['cache_id'];
        $entity->uuid = $data['uuid'];
        $entity->node = $data['node'];
        $entity->dateCreated = $data['date_created'];
        $entity->isPublishdate = $data['is_publishdate'];
        $entity->lastModified = $data['last_modified'];
        $entity->okapiSyncbase = $data['okapi_syncbase'];
        $entity->listingLastModified = $data['listing_last_modified'];
        $entity->metaLastModified = $data['meta_last_modified'];
        $entity->userId = $data['user_id'];
        $entity->name = $data['name'];
        $entity->longitude = $data['longitude'];
        $entity->latitude = $data['latitude'];
        $entity->type = $data['type'];
        $entity->status = $data['status'];
        $entity->country = $data['country'];
        $entity->dateHidden = $data['date_hidden'];
        $entity->size = $data['size'];
        $entity->difficulty = $data['difficulty'];
        $entity->terrain = $data['terrain'];
        $entity->logpw = $data['logpw'];
        $entity->searchTime = $data['search_time'];
        $entity->wayLength = $data['way_length'];
        $entity->wpGc = $data['wp_gc'];
        $entity->wpGcMaintained = $data['wp_gc_maintained'];
        $entity->wpNc = $data['wp_nc'];
        $entity->wpOc = $data['wp_oc'];
        $entity->descLanguages = $data['desc_languages'];
        $entity->defaultDesclang = $data['default_desclang'];
        $entity->dateActivate = $data['date_activate'];
        $entity->needNpaRecalc = $data['need_npa_recalc'];
        $entity->showCachelists = $data['show_cachelists'];
        $entity->protectOldCoords = $data['protect_old_coords'];
        $entity->needsMaintenance = $data['needs_maintenance'];
        $entity->listingOutdated = $data['listing_outdated'];
        $entity->flagsLastModified = $data['flags_last_modified'];

        return $entity;
    }
}
