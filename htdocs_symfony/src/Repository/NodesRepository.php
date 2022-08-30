<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Entity\NodesEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class NodesRepository
 *
 * @package Oc\Repository
 */
class NodesRepository
{
    const TABLE = 'nodes';

    /** @var Connection */
    private Connection $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     * @throws RecordsNotFoundException
     * @throws Exception
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
     * @return NodesEntity
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchOneBy(array $where = [])
    : NodesEntity {
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
     * @param NodesEntity $entity
     *
     * @return NodesEntity
     * @throws RecordAlreadyExistsException
     * @throws \Doctrine\DBAL\Exception
     */
    public function create(NodesEntity $entity)
    : NodesEntity {
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
     * @param NodesEntity $entity
     *
     * @return NodesEntity
     * @throws \Doctrine\DBAL\Exception
     * @throws RecordNotPersistedException
     */
    public function update(NodesEntity $entity)
    : NodesEntity {
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
     * @param NodesEntity $entity
     *
     * @return NodesEntity
     * @throws \Doctrine\DBAL\Exception
     * @throws InvalidArgumentException
     * @throws RecordNotPersistedException
     */
    public function remove(NodesEntity $entity)
    : NodesEntity {
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
     * @param NodesEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(NodesEntity $entity)
    : array {
        return [
            'id' => $entity->id,
            'name' => $entity->name,
            'url' => $entity->url,
            'waypoint_prefix' => $entity->waypointPrefix,
        ];
    }

    /**
     * @param array $data
     *
     * @return NodesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    : NodesEntity {
        $entity = new NodesEntity();
        $entity->id = (int) $data['id'];
        $entity->name = (string) $data['name'];
        $entity->url = (string) $data['url'];
        $entity->waypointPrefix = (string) $data['waypoint_prefix'];

        return $entity;
    }

    /**
     * @param int $id
     *
     * @return string
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function get_prefix_by_id(int $id)
    : string {
        $data = $this->fetchOneBy(['id' => $id]);
        if (!empty($data)) {
            return $data->waypointPrefix;
        }

        return '';
    }

    /**
     * @param string $prefix
     *
     * @return int
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function get_id_by_prefix(string $prefix)
    : int {
        $data = $this->fetchOneBy(['waypoint_prefix' => $prefix]);
        if (!empty($data)) {
            return $data->id;
        }

        return 0;
    }
}
