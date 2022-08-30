<?php

declare(strict_types=1);

namespace Oc\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Entity\GeoCacheCoordinatesEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 *
 */
class CacheCoordinatesRepository
{
    const TABLE = 'cache_coordinates';

    /** @var Connection */
    private Connection $connection;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /**
     * CacheCoordinatesRepository constructor.
     *
     * @param Connection $connection
     * @param UserRepository $userRepository
     */
    public function __construct(Connection $connection, UserRepository $userRepository)
    {
        $this->connection = $connection;
        $this->userRepository = $userRepository;
    }

    /**
     * @return array
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchAll()
    : array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->execute();

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
     * @param array $where
     *
     * @return GeoCacheCoordinatesEntity
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchOneBy(array $where = [])
    : GeoCacheCoordinatesEntity {
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

        $result = $statement->fetchAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * @param array $where
     *
     * @return array
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchBy(array $where = [])
    : array {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

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
     * @param GeoCacheCoordinatesEntity $entity
     *
     * @return GeoCacheCoordinatesEntity
     * @throws RecordAlreadyExistsException
     * @throws \Doctrine\DBAL\Exception
     */
    public function create(GeoCacheCoordinatesEntity $entity)
    : GeoCacheCoordinatesEntity {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->id = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param GeoCacheCoordinatesEntity $entity
     *
     * @return GeoCacheCoordinatesEntity
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(GeoCacheCoordinatesEntity $entity)
    : GeoCacheCoordinatesEntity {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['id' => $entity->id]
        );

        return $entity;
    }

    /**
     * @param GeoCacheCoordinatesEntity $entity
     *
     * @return GeoCacheCoordinatesEntity
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\Exception
     * @throws InvalidArgumentException
     */
    public function remove(GeoCacheCoordinatesEntity $entity)
    : GeoCacheCoordinatesEntity {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['id' => $entity->id]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param GeoCacheCoordinatesEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(GeoCacheCoordinatesEntity $entity)
    : array {
        return [
            'id' => $entity->id,
            'date_created' => $entity->dateCreated,
            'cache_id' => $entity->cacheId,
            'longitude' => $entity->longitude,
            'latitude' => $entity->latitude,
            'restored_by' => $entity->restoredBy,
            'user' => $entity->user,
        ];
    }

    /**
     * @param array $data
     *
     * @return GeoCacheCoordinatesEntity
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data)
    : GeoCacheCoordinatesEntity {
        $entity = new GeoCacheCoordinatesEntity();
        $entity->id = (int) $data['id'];
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->cacheId = (int) $data['cache_id'];
        $entity->longitude = $data['longitude'];
        $entity->latitude = $data['latitude'];
        $entity->restoredBy = (int) $data['restored_by'];
        if ($entity->restoredBy != 0) {
            $entity->user = $this->userRepository->fetchOneById($entity->restoredBy);
        }

        return $entity;
    }
}
