<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class SearchIndexRepository
{
    const TABLE = 'search_index';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return SearchIndexEntity[]
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
     * @return SearchIndexEntity
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
     * @return SearchIndexEntity[]
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
     * @param SearchIndexEntity $entity
     * @return SearchIndexEntity
     */
    public function create(SearchIndexEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->objectType = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param SearchIndexEntity $entity
     * @return SearchIndexEntity
     */
    public function update(SearchIndexEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['object_type' => $entity->objectType]
        );

        return $entity;
    }

    /**
     * @param SearchIndexEntity $entity
     * @return SearchIndexEntity
     */
    public function remove(SearchIndexEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['object_type' => $entity->objectType]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param SearchIndexEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(SearchIndexEntity $entity)
    {
        return [
            'object_type' => $entity->objectType,
            'cache_id' => $entity->cacheId,
            'hash' => $entity->hash,
            'count' => $entity->count,
        ];
    }

    /**
     * @param array $data
     * @return SearchIndexEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new SearchIndexEntity();
        $entity->objectType = (int) $data['object_type'];
        $entity->cacheId = (int) $data['cache_id'];
        $entity->hash = (int) $data['hash'];
        $entity->count = (int) $data['count'];

        return $entity;
    }
}
