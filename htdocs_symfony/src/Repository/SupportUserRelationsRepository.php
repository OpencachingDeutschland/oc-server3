<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Oc\Entity\SupportUserRelationsEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class SupportUserRelationsRepository
 *
 * @package Oc\Repository
 */
class SupportUserRelationsRepository
{
    const TABLE = 'support_user_relations';

    /** @var Connection */
    private $connection;

    /** @var NodesRepository */
    private $nodesRepository;

    /** @var UserRepository */
    private $userRepository;

    /**
     * @param Connection $connection
     * @param NodesRepository $nodesRepository
     * @param UserRepository $userRepository
     */
    public function __construct(Connection $connection, NodesRepository $nodesRepository, UserRepository $userRepository)
    {
        $this->connection = $connection;
        $this->nodesRepository = $nodesRepository;
        $this->userRepository = $userRepository;
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
     * @return SupportUserRelationsEntity
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = [])
    : SupportUserRelationsEntity {
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
     * @param SupportUserRelationsEntity $entity
     *
     * @return SupportUserRelationsEntity
     * @throws RecordAlreadyExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function create(SupportUserRelationsEntity $entity)
    : SupportUserRelationsEntity {
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
     * @param SupportUserRelationsEntity $entity
     *
     * @return SupportUserRelationsEntity
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function update(SupportUserRelationsEntity $entity)
    : SupportUserRelationsEntity {
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
     * @param SupportUserRelationsEntity $entity
     *
     * @return SupportUserRelationsEntity
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function remove(SupportUserRelationsEntity $entity)
    : SupportUserRelationsEntity {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['id' => $entity->id]
        );

        $entity->id = null;

        return $entity;
    }

    /**
     * @param SupportUserRelationsEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(SupportUserRelationsEntity $entity)
    : array {
        return [
            'id' => $entity->id,
            'oc_user_id' => $entity->ocUserId,
            'node_id' => $entity->nodeId,
            'node_user_id' => $entity->nodeUserId,
            'node_username' => $entity->nodeUsername,
        ];
    }

    /**
     * @param array $data
     *
     * @return SupportUserRelationsEntity
     * @throws \Exception
     */
    public function getEntityFromDatabaseArray(array $data)
    : SupportUserRelationsEntity {
        $entity = new SupportUserRelationsEntity();
        $entity->id = (int) $data['id'];
        $entity->ocUserId = (int) $data['oc_user_id'];
        $entity->nodeId = (int) $data['node_id'];
        $entity->nodeUserId = (string) $data['node_user_id'];
        $entity->nodeUsername = (string) $data['node_username'];
        $entity->node = $this->nodesRepository->fetchOneBy(['id' => $entity->nodeId]);
        $entity->user = $this->userRepository->fetchOneById($entity->ocUserId);

        return $entity;
    }
}
