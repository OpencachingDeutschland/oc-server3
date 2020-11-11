<?php

namespace Oc\GeoCache\Persistence\GeoCache;

use DateTime;
use Doctrine\DBAL\Connection;
use Oc\GeoCache\Enum\WaypointType;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GeoCacheRepository
{
    /**
     * Database table name that this repository maintains.
     *
     * @var string
     */
    public const TABLE = 'caches';

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Fetches all GeoCaches.
     *
     * @throws RecordsNotFoundException Thrown when no records are found
     * @return GeoCacheEntity[]
     */
    public function fetchAll(): array
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
     * Fetches a GeoCache by given where clause.
     *
     * @throws RecordNotFoundException Thrown when no record is found
     */
    public function fetchOneBy(array $where = []): ?GeoCacheEntity
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
     * Fetches all GeoCaches by given where clause.
     *
     * @throws RecordsNotFoundException Thrown when no records are found
     * @return GeoCacheEntity[]
     */
    public function fetchBy(array $where = []): array
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

        $geoCaches = [];

        foreach ($result as $item) {
            $geoCaches[] = $this->getEntityFromDatabaseArray($item);
        }

        return $geoCaches;
    }

    /**
     * Fetches a GeoCache by given where clause.
     *
     * @throws RecordNotFoundException Thrown when no record is found
     */
    public function fetchGCWaypoint(string $waypoint): ?GeoCacheEntity
    {
        if (WaypointType::guess($waypoint) !== WaypointType::GC) {
            throw new RecordNotFoundException('Record by given gc waypoint not found');
        }

        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where("IF(wp_gc_maintained = '', wp_gc, wp_gc_maintained) = :waypoint")
            ->setParameter(':waypoint', $waypoint)
            ->setMaxResults(1);

        $statement = $queryBuilder->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record by given gc waypoint not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * Creates a GeoCache in the database.
     *
     * @throws RecordAlreadyExistsException
     */
    public function create(GeoCacheEntity $entity): GeoCacheEntity
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
     * Update a GeoCache in the database.
     *
     * @throws RecordNotPersistedException
     */
    public function update(GeoCacheEntity $entity): GeoCacheEntity
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
     * Removes a GeoCache from the database.
     *
     * @throws RecordNotPersistedException
     */
    public function remove(GeoCacheEntity $entity): GeoCacheEntity
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
     * Maps the given entity to the database array.
     */
    public function getDatabaseArrayFromEntity(GeoCacheEntity $entity): array
    {
        return [
            'cache_id' => $entity->cacheId,
            'uuid' => $entity->uuid,
            'node' => $entity->node,
            'date_created' => $entity->dateCreated->format(DateTime::ATOM),
            'is_publishdate' => $entity->isPublishdate,
            'last_modified' => $entity->lastModified->format(DateTime::ATOM),
            'okapi_syncbase' => $entity->okapiSyncbase,
            'listing_last_modified' => $entity->listingLastModified->format(DateTime::ATOM),
            'meta_last_modified' => $entity->metaLastModified->format(DateTime::ATOM),
            'user_id' => $entity->userId,
            'name' => $entity->name,
            'longitude' => $entity->longitude,
            'latitude' => $entity->latitude,
            'type' => $entity->type,
            'status' => $entity->status,
            'country' => $entity->country,
            'date_hidden' => $entity->dateHidden->format(DateTime::ATOM),
            'size' => $entity->size,
            'difficulty' => $entity->difficulty,
            'terrain' => $entity->terrain,
            'logpw' => $entity->logpw,
            'search_time' => $entity->searchTime,
            'way_length' => $entity->wayLength,
            'wp_gc' => $entity->wpGc,
            'wp_gc_maintained' => $entity->wpGcMaintained,
            'wp_oc' => $entity->wpOc,
            'desc_languages' => $entity->descLanguages,
            'default_desclang' => $entity->defaultDesclang,
            'date_activate' => $entity->dateActivate->format(DateTime::ATOM),
            'need_npa_recalc' => $entity->needNpaRecalc,
            'show_cachelists' => $entity->showCachelists,
            'protect_old_coords' => $entity->protectOldCoords,
            'needs_maintenance' => $entity->needsMaintenance,
            'listing_outdated' => $entity->listingOutdated,
            'flags_last_modified' => $entity->flagsLastModified->format(DateTime::ATOM),
        ];
    }

    /**
     * Prepares database array from properties.
     */
    public function getEntityFromDatabaseArray(array $data): GeoCacheEntity
    {
        $entity = new GeoCacheEntity();
        $entity->cacheId = $data['cache_id'];
        $entity->uuid = $data['uuid'];
        $entity->node = (int) $data['node'];
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->isPublishdate = (bool) $data['is_publishdate'];
        $entity->lastModified = new DateTime($data['last_modified']);
        $entity->okapiSyncbase = (int) $data['okapi_syncbase'];
        $entity->listingLastModified = new DateTime($data['listing_last_modified']);
        $entity->metaLastModified = new DateTime($data['meta_last_modified']);
        $entity->userId = (int) $data['user_id'];
        $entity->name = $data['name'];
        $entity->longitude = (double) $data['longitude'];
        $entity->latitude = (double) $data['latitude'];
        $entity->type = (int) $data['type'];
        $entity->status = (int) $data['status'];
        $entity->country = $data['country'];
        $entity->dateHidden = new DateTime($data['date_hidden']);
        $entity->size = (int) $data['size'];
        $entity->difficulty = (int) $data['difficulty'];
        $entity->terrain = (int) $data['terrain'];
        $entity->logpw = $data['logpw'];
        $entity->searchTime = (float) $data['search_time'];
        $entity->wayLength = (float) $data['way_length'];
        $entity->wpGc = $data['wp_gc'];
        $entity->wpGcMaintained = $data['wp_gc_maintained'];
        $entity->wpOc = $data['wp_oc'];
        $entity->descLanguages = $data['desc_languages'];
        $entity->defaultDesclang = $data['default_desclang'];
        $entity->dateActivate = new DateTime($data['date_activate']);
        $entity->needNpaRecalc = (bool) $data['need_npa_recalc'];
        $entity->showCachelists = (bool) $data['show_cachelists'];
        $entity->protectOldCoords = (bool) $data['protect_old_coords'];
        $entity->needsMaintenance = (bool) $data['needs_maintenance'];
        $entity->listingOutdated = (bool) $data['listing_outdated'];
        $entity->flagsLastModified = new DateTime($data['flags_last_modified']);

        return $entity;
    }
}
