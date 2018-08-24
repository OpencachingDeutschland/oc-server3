<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class OkapiSearchResultsRepository
{
    const TABLE = 'okapi_search_results';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return OkapiSearchResultsEntity[]
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
     * @return OkapiSearchResultsEntity
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
     * @return OkapiSearchResultsEntity[]
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
     * @param OkapiSearchResultsEntity $entity
     * @return OkapiSearchResultsEntity
     */
    public function create(OkapiSearchResultsEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->setId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param OkapiSearchResultsEntity $entity
     * @return OkapiSearchResultsEntity
     */
    public function update(OkapiSearchResultsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['set_id' => $entity->setId]
        );

        return $entity;
    }

    /**
     * @param OkapiSearchResultsEntity $entity
     * @return OkapiSearchResultsEntity
     */
    public function remove(OkapiSearchResultsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['set_id' => $entity->setId]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param OkapiSearchResultsEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(OkapiSearchResultsEntity $entity)
    {
        return [
            'set_id' => $entity->setId,
            'cache_id' => $entity->cacheId,
        ];
    }

    /**
     * @param array $data
     * @return OkapiSearchResultsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new OkapiSearchResultsEntity();
        $entity->setId = (int) $data['set_id'];
        $entity->cacheId = (int) $data['cache_id'];

        return $entity;
    }
}
