<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheLogtypeRepository
{
    const TABLE = 'cache_logtype';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCacheLogtypeEntity[]
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
     * @return GeoCacheLogtypeEntity
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
     * @return GeoCacheLogtypeEntity[]
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
     * @param GeoCacheLogtypeEntity $entity
     * @return GeoCacheLogtypeEntity
     */
    public function create(GeoCacheLogtypeEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->cacheTypeId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param GeoCacheLogtypeEntity $entity
     * @return GeoCacheLogtypeEntity
     */
    public function update(GeoCacheLogtypeEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['cache_type_id' => $entity->cacheTypeId]
        );

        return $entity;
    }

    /**
     * @param GeoCacheLogtypeEntity $entity
     * @return GeoCacheLogtypeEntity
     */
    public function remove(GeoCacheLogtypeEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['cache_type_id' => $entity->cacheTypeId]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param GeoCacheLogtypeEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCacheLogtypeEntity $entity)
    {
        return [
            'cache_type_id' => $entity->cacheTypeId,
            'log_type_id' => $entity->logTypeId,
        ];
    }

    /**
     * @param array $data
     * @return GeoCacheLogtypeEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCacheLogtypeEntity();
        $entity->cacheTypeId = (int) $data['cache_type_id'];
        $entity->logTypeId = (int) $data['log_type_id'];

        return $entity;
    }
}
