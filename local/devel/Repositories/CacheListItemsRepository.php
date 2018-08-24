<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheListItemsRepository
{
    const TABLE = 'cache_list_items';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCacheListItemsEntity[]
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
     * @return GeoCacheListItemsEntity
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
     * @return GeoCacheListItemsEntity[]
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
     * @param GeoCacheListItemsEntity $entity
     * @return GeoCacheListItemsEntity
     */
    public function create(GeoCacheListItemsEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->cacheListId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param GeoCacheListItemsEntity $entity
     * @return GeoCacheListItemsEntity
     */
    public function update(GeoCacheListItemsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['cache_list_id' => $entity->cacheListId]
        );

        return $entity;
    }

    /**
     * @param GeoCacheListItemsEntity $entity
     * @return GeoCacheListItemsEntity
     */
    public function remove(GeoCacheListItemsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['cache_list_id' => $entity->cacheListId]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param GeoCacheListItemsEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCacheListItemsEntity $entity)
    {
        return [
            'cache_list_id' => $entity->cacheListId,
            'cache_id' => $entity->cacheId,
        ];
    }

    /**
     * @param array $data
     * @return GeoCacheListItemsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCacheListItemsEntity();
        $entity->cacheListId = (int) $data['cache_list_id'];
        $entity->cacheId = (int) $data['cache_id'];

        return $entity;
    }
}
