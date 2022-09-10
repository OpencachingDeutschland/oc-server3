<?php

declare(strict_types=1);

namespace Oc\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Entity\GeoCacheAdoptionsEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class CacheAdoptionsRepository
 *
 * @package Oc\Repository#
 */
class CacheAdoptionsRepository
{
    const TABLE = 'cache_adoptions';

    /** @var Connection */
    private Connection $connection;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /**
     * CacheAdoptionsRepository constructor.
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
     * @throws RecordNotFoundException
     * @throws RecordsNotFoundException
     * @throws Exception
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
     * @return GeoCacheAdoptionsEntity
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function fetchOneBy(array $where = [])
    : GeoCacheAdoptionsEntity {
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
     * @throws RecordNotFoundException
     * @throws Exception
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
            //            throw new RecordsNotFoundException('No records with given where clause found');
        }

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $this->getEntityFromDatabaseArray($item);
        }

        return $entities;
    }

    /**
     * @param GeoCacheAdoptionsEntity $entity
     *
     * @return GeoCacheAdoptionsEntity
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */
    public function create(GeoCacheAdoptionsEntity $entity)
    : GeoCacheAdoptionsEntity {
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
     * @param GeoCacheAdoptionsEntity $entity
     *
     * @return GeoCacheAdoptionsEntity
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function update(GeoCacheAdoptionsEntity $entity)
    : GeoCacheAdoptionsEntity {
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
     * @param GeoCacheAdoptionsEntity $entity
     *
     * @return GeoCacheAdoptionsEntity
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function remove(GeoCacheAdoptionsEntity $entity)
    : GeoCacheAdoptionsEntity {
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
     * @param GeoCacheAdoptionsEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(GeoCacheAdoptionsEntity $entity)
    : array {
        return [
            'id' => $entity->id,
            'cache_id' => $entity->cacheId,
            'date' => $entity->date,
            'from_user_id' => $entity->fromUserId,
            'to_user_id' => $entity->toUserId,
            'from_user' => $entity->fromUser,
            'to_user' => $entity->toUser,
        ];
    }

    /**
     * @param array $data
     *
     * @return GeoCacheAdoptionsEntity
     * @throws RecordNotFoundException
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data)
    : GeoCacheAdoptionsEntity {
        $entity = new GeoCacheAdoptionsEntity();
        $entity->id = (int) $data['id'];
        $entity->cacheId = (int) $data['cache_id'];
        $entity->date = new DateTime($data['date']);
        $entity->fromUserId = (int) $data['from_user_id'];
        $entity->toUserId = (int) $data['to_user_id'];
        $entity->fromUser = $this->userRepository->fetchOneById($entity->fromUserId);
        $entity->toUser = $this->userRepository->fetchOneById($entity->toUserId);

        return $entity;
    }
}
