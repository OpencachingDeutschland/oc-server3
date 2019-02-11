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
     * @return GeoCachesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCachesEntity();
        $entity->cacheId = (int) $data['cache_id'];
        $entity->uuid = (string) $data['uuid'];
        $entity->node = (int) $data['node'];
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->isPublishdate = (int) $data['is_publishdate'];
        $entity->lastModified = new DateTime($data['last_modified']);
        $entity->okapiSyncbase = (string) $data['okapi_syncbase'];
        $entity->listingLastModified = new DateTime($data['listing_last_modified']);
        $entity->metaLastModified = new DateTime($data['meta_last_modified']);
        $entity->userId = (int) $data['user_id'];
        $entity->name = (string) $data['name'];
        $entity->longitude = $data['longitude'];
        $entity->latitude = $data['latitude'];
        $entity->type = (int) $data['type'];
        $entity->status = (int) $data['status'];
        $entity->country = (string) $data['country'];
        $entity->dateHidden = new DateTime($data['date_hidden']);
        $entity->size = (int) $data['size'];
        $entity->difficulty = (int) $data['difficulty'];
        $entity->terrain = (int) $data['terrain'];
        $entity->logpw = (string) $data['logpw'];
        $entity->searchTime = $data['search_time'];
        $entity->wayLength = $data['way_length'];
        $entity->wpGc = (string) $data['wp_gc'];
        $entity->wpGcMaintained = (string) $data['wp_gc_maintained'];
        $entity->wpNc = (string) $data['wp_nc'];
        $entity->wpOc = (string) $data['wp_oc'];
        $entity->descLanguages = (string) $data['desc_languages'];
        $entity->defaultDesclang = (string) $data['default_desclang'];
        $entity->dateActivate = new DateTime($data['date_activate']);
        $entity->needNpaRecalc = (int) $data['need_npa_recalc'];
        $entity->showCachelists = (int) $data['show_cachelists'];
        $entity->protectOldCoords = (int) $data['protect_old_coords'];
        $entity->needsMaintenance = (int) $data['needs_maintenance'];
        $entity->listingOutdated = (int) $data['listing_outdated'];
        $entity->flagsLastModified = new DateTime($data['flags_last_modified']);

        return $entity;
    }
}
