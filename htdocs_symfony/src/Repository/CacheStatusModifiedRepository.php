<?php

declare(strict_types=1);

namespace Oc\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Entity\GeoCacheStatusModifiedEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class CacheStatusModifiedRepository
 *
 * @package Oc\Repository
 */
class CacheStatusModifiedRepository
{
    const TABLE = 'cache_status_modified';

    /** @var Connection */
    private Connection $connection;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var CacheStatusRepository */
    private CacheStatusRepository $cacheStatusRepository;

    /**
     * CacheStatusModifiedRepository constructor.
     *
     * @param Connection $connection
     * @param UserRepository $userRepository
     * @param CacheStatusRepository $cacheStatusRepository
     */
    public function __construct(
        Connection $connection,
        UserRepository $userRepository,
        CacheStatusRepository $cacheStatusRepository
    ) {
        $this->connection = $connection;
        $this->userRepository = $userRepository;
        $this->cacheStatusRepository = $cacheStatusRepository;
    }

    /**
     * @return array
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     */
    public function fetchAll()
    : array
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
     * @param array $where
     *
     * @return GeoCacheStatusModifiedEntity
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = [])
    : GeoCacheStatusModifiedEntity {
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
     * @param array $where
     *
     * @return array
     * @throws Exception
     * @throws RecordNotFoundException
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

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAllAssociative();

        if ($statement->rowCount() === 0) {
            // throw new RecordsNotFoundException('No records with given where clause found');
        }

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $this->getEntityFromDatabaseArray($item);
        }

        return $entities;
    }

    /**
     * @param GeoCacheStatusModifiedEntity $entity
     *
     * @return GeoCacheStatusModifiedEntity
     * @throws Exception
     * @throws RecordAlreadyExistsException
     */
    public function create(GeoCacheStatusModifiedEntity $entity)
    : GeoCacheStatusModifiedEntity {
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
     * @param GeoCacheStatusModifiedEntity $entity
     *
     * @return GeoCacheStatusModifiedEntity
     * @throws Exception
     * @throws RecordNotPersistedException
     */
    public function update(GeoCacheStatusModifiedEntity $entity)
    : GeoCacheStatusModifiedEntity {
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
     * @param GeoCacheStatusModifiedEntity $entity
     *
     * @return GeoCacheStatusModifiedEntity
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RecordNotPersistedException
     */
    public function remove(GeoCacheStatusModifiedEntity $entity)
    : GeoCacheStatusModifiedEntity {
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
     * @param GeoCacheStatusModifiedEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(GeoCacheStatusModifiedEntity $entity)
    : array {
        return [
            'cache_id' => $entity->cacheId,
            'date_modified' => $entity->dateModified,
            'old_state' => $entity->oldState,
            'new_state' => $entity->newState,
            'user_id' => $entity->userId,
        ];
    }

    /**
     * @param array $data
     *
     * @return GeoCacheStatusModifiedEntity
     * @throws RecordNotFoundException
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data)
    : GeoCacheStatusModifiedEntity {
        $entity = new GeoCacheStatusModifiedEntity();
        $entity->cacheId = (int) $data['cache_id'];
        $entity->dateModified = new DateTime($data['date_modified']);
        $entity->oldState = (int) $data['old_state'];
        $entity->newState = (int) $data['new_state'];
        $entity->userId = (int) $data['user_id'];
        $entity->user = $this->userRepository->fetchOneById($entity->userId);
        $entity->cacheStatusOld = $this->cacheStatusRepository->fetchOneBy(['id' => $entity->oldState]);
        $entity->cacheStatusNew = $this->cacheStatusRepository->fetchOneBy(['id' => $entity->newState]);

        return $entity;
    }
}
