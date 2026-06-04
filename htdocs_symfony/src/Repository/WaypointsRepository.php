<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Entity\GeoCacheWaypointsEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Repository for the `coordinates` table — handles additional waypoints (type=1),
 * user personal notes, corrected coordinates, and log passwords (type=2).
 */
class WaypointsRepository extends ServiceEntityRepository
{
    private const TABLE = 'coordinates';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws RecordsNotFoundException
     * @throws Exception
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
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function fetchOneBy(array $where = []): GeoCacheWaypointsEntity
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
     * @throws RecordsNotFoundException
     * @throws Exception
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

        $statement = $queryBuilder->executeQuery();
        $result = $statement->fetchAllAssociative();

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
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */
    public function create(GeoCacheWaypointsEntity $entity): GeoCacheWaypointsEntity
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);
        $this->connection->insert(self::TABLE, $databaseArray);
        $entity->id = (int)$this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function update(GeoCacheWaypointsEntity $entity): GeoCacheWaypointsEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);
        $this->connection->update(self::TABLE, $databaseArray, ['id' => $entity->id]);

        return $entity;
    }

    /**
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function remove(GeoCacheWaypointsEntity $entity): GeoCacheWaypointsEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(self::TABLE, ['id' => $entity->id]);
        $entity->id = 0;

        return $entity;
    }

    public function getDatabaseArrayFromEntity(GeoCacheWaypointsEntity $entity): array
    {
        return [
            'id'            => $entity->id,
            'date_created'  => $entity->dateCreated->format('Y-m-d H:i:s'),
            'last_modified' => $entity->lastModified->format('Y-m-d H:i:s'),
            'type'          => $entity->type,
            'subtype'       => $entity->subtype,
            'latitude'      => $entity->latitude,
            'longitude'     => $entity->longitude,
            'cache_id'      => $entity->cacheId,
            'user_id'       => $entity->userId,
            'log_id'        => $entity->logId,
            'description'   => $entity->description,
            'logpw'         => $entity->logpw,
        ];
    }

    /**
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data): GeoCacheWaypointsEntity
    {
        $entity = new GeoCacheWaypointsEntity();
        $entity->id           = (int)$data['id'];
        $entity->dateCreated  = new \DateTime($data['date_created']);
        $entity->lastModified = new \DateTime($data['last_modified']);
        $entity->type         = (int)$data['type'];
        $entity->subtype      = isset($data['subtype']) ? (int)$data['subtype'] : null;
        $entity->latitude     = (float)$data['latitude'];
        $entity->longitude    = (float)$data['longitude'];
        $entity->cacheId      = (int)$data['cache_id'];
        $entity->userId       = isset($data['user_id']) ? (int)$data['user_id'] : null;
        $entity->logId        = isset($data['log_id']) ? (int)$data['log_id'] : null;
        $entity->description  = $data['description'] ?? null;
        $entity->logpw        = (string)$data['logpw'];

        return $entity;
    }

    // ── Waypoints (type=1, owner-placed) ───────────────────────────────

    /** @throws Exception */
    public function fetchWaypoints(int $cacheId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('co.latitude', 'co.longitude', 'co.description', 'ct.name AS type_name', 'ct.id AS type_id')
            ->from(self::TABLE, 'co')
            ->leftJoin('co', 'coordinates_type', 'ct', 'co.subtype = ct.id')
            ->where('co.cache_id = :cacheId')
            ->andWhere('co.type = 1')
            ->andWhere('co.user_id IS NULL')
            ->orderBy('co.id')
            ->setParameter('cacheId', $cacheId)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** @throws Exception */
    public function fetchWaypointsForEdit(int $cacheId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('id', 'subtype', 'latitude', 'longitude', 'description')
            ->from(self::TABLE)
            ->where('cache_id = :cacheId')
            ->andWhere('type = 1')
            ->andWhere('user_id IS NULL')
            ->orderBy('id')
            ->setParameter('cacheId', $cacheId)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** @throws Exception */
    public function fetchWaypointsByWp(string $wp): array
    {
        return $this->connection->createQueryBuilder()
            ->select('co.latitude', 'co.longitude', 'co.description', 'co.subtype', 'ct.name AS type_name')
            ->from(self::TABLE, 'co')
            ->join('co', 'caches', 'c', 'co.cache_id = c.cache_id')
            ->leftJoin('co', 'coordinates_type', 'ct', 'co.subtype = ct.id')
            ->where('c.wp_oc = :wp')
            ->andWhere('co.type = 1')
            ->andWhere('co.user_id IS NULL')
            ->orderBy('co.id')
            ->setParameter('wp', $wp)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** @throws Exception */
    public function replaceOwnerWaypoints(int $cacheId, array $waypoints, string $now): void
    {
        $this->connection->executeStatement(
            'DELETE FROM coordinates WHERE cache_id = ? AND type = 1 AND user_id IS NULL',
            [$cacheId]
        );
        foreach ($waypoints as $wpt) {
            $this->connection->insert(self::TABLE, [
                'cache_id'      => $cacheId,
                'type'          => 1,
                'subtype'       => $wpt['type'],
                'latitude'      => $wpt['lat'],
                'longitude'     => $wpt['lon'],
                'description'   => $wpt['desc'],
                'date_created'  => $now,
                'last_modified' => $now,
            ]);
        }
    }

    // ── User note / corrected coords (type=2) ─────────────────────────

    /** @throws Exception */
    public function fetchUserNote(int $cacheId, int $userId): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('description', 'latitude', 'longitude', 'logpw')
            ->from(self::TABLE)
            ->where('cache_id = :cacheId')
            ->andWhere('user_id = :userId')
            ->andWhere('type = 2')
            ->orderBy('id', 'DESC')
            ->setMaxResults(1)
            ->setParameters(['cacheId' => $cacheId, 'userId' => $userId])
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }

    /** @throws Exception */
    public function upsertUserNoteText(int $cacheId, int $userId, string $text): array
    {
        $existing = $this->fetchUserNote($cacheId, $userId);
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        if ($text === '') {
            if ($existing) {
                $this->connection->delete(self::TABLE, ['id' => (int)$existing['id']]);
            }
            return ['saved' => false];
        }

        if ($existing) {
            $this->connection->update(self::TABLE, [
                'description' => $text,
                'last_modified' => $now,
            ], ['id' => (int)$existing['id']]);
        } else {
            $this->connection->insert(self::TABLE, [
                'cache_id'      => $cacheId,
                'user_id'       => $userId,
                'type'          => 2,
                'subtype'       => 0,
                'latitude'      => 0,
                'longitude'     => 0,
                'description'   => $text,
                'date_created'  => $now,
                'last_modified' => $now,
            ]);
        }
        return ['saved' => true];
    }

    /** @throws Exception */
    public function upsertUserNoteLogpw(int $cacheId, int $userId, string $logpw): array
    {
        $existing = $this->fetchUserNote($cacheId, $userId);
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        if ($existing) {
            $hasOther = ($existing['description'] !== null && $existing['description'] !== '')
                || (float)$existing['latitude'] !== 0.0
                || (float)$existing['longitude'] !== 0.0;

            if ($logpw === '' && !$hasOther) {
                $this->connection->delete(self::TABLE, ['id' => (int)$existing['id']]);
                return ['saved' => true, 'logpw' => ''];
            }

            $this->connection->update(self::TABLE, [
                'logpw' => $logpw,
                'last_modified' => $now,
            ], ['id' => (int)$existing['id']]);
        } elseif ($logpw !== '') {
            $this->connection->insert(self::TABLE, [
                'cache_id'      => $cacheId,
                'user_id'       => $userId,
                'type'          => 2,
                'subtype'       => 0,
                'latitude'      => 0,
                'longitude'     => 0,
                'description'   => '',
                'logpw'         => $logpw,
                'date_created'  => $now,
                'last_modified' => $now,
            ]);
        }

        return ['saved' => true, 'logpw' => $logpw];
    }

    /** @throws Exception */
    public function upsertUserNoteCoords(int $cacheId, int $userId, float $lat, float $lon): array
    {
        $existing = $this->fetchUserNote($cacheId, $userId);
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        if ($existing) {
            $this->connection->update(self::TABLE, [
                'latitude'      => $lat,
                'longitude'     => $lon,
                'last_modified' => $now,
            ], ['id' => (int)$existing['id']]);
        } else {
            $this->connection->insert(self::TABLE, [
                'cache_id'      => $cacheId,
                'user_id'       => $userId,
                'type'          => 2,
                'subtype'       => 0,
                'latitude'      => $lat,
                'longitude'     => $lon,
                'description'   => '',
                'date_created'  => $now,
                'last_modified' => $now,
            ]);
        }

        return ['saved' => true];
    }
}
