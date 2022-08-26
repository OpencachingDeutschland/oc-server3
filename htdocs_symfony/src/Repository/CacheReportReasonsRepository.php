<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Entity\GeoCacheReportReasonsEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 *
 */
class CacheReportReasonsRepository
{
    const TABLE = 'cache_report_reasons';

    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * CacheReportReasonsRepository constructor.
     *
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
     * @return GeoCacheReportReasonsEntity
     * @throws Exception
     * @throws RecordNotFoundException
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchOneBy(array $where = [])
    : GeoCacheReportReasonsEntity {
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
     * @param GeoCacheReportReasonsEntity $entity
     *
     * @return GeoCacheReportReasonsEntity
     * @throws RecordAlreadyExistsException
     * @throws \Doctrine\DBAL\Exception
     */
    public function create(GeoCacheReportReasonsEntity $entity)
    : GeoCacheReportReasonsEntity {
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
     * @param GeoCacheReportReasonsEntity $entity
     *
     * @return GeoCacheReportReasonsEntity
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(GeoCacheReportReasonsEntity $entity)
    : GeoCacheReportReasonsEntity {
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
     * @param GeoCacheReportReasonsEntity $entity
     *
     * @return GeoCacheReportReasonsEntity
     * @throws RecordNotPersistedException
     * @throws \Doctrine\DBAL\Exception
     * @throws InvalidArgumentException
     */
    public function remove(GeoCacheReportReasonsEntity $entity)
    : GeoCacheReportReasonsEntity {
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
     * @param GeoCacheReportReasonsEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(GeoCacheReportReasonsEntity $entity)
    : array {
        return [
            'id' => $entity->id,
            'name' => $entity->name,
            'trans_id' => $entity->transId,
            'order' => $entity->order,
        ];
    }

    /**
     * @param array $data
     *
     * @return GeoCacheReportReasonsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    : GeoCacheReportReasonsEntity {
        $entity = new GeoCacheReportReasonsEntity();
        $entity->id = (int) $data['id'];
        $entity->name = (string) $data['name'];
        $entity->transId = (int) $data['trans_id'];
        $entity->order = (int) $data['order'];

        return $entity;
    }
}
