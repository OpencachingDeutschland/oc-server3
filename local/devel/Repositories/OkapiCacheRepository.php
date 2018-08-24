<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class OkapiCacheRepository
{
    const TABLE = 'okapi_cache';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return OkapiCacheEntity[]
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
     * @return OkapiCacheEntity
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
     * @return OkapiCacheEntity[]
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
     * @param OkapiCacheEntity $entity
     * @return OkapiCacheEntity
     */
    public function create(OkapiCacheEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->key = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param OkapiCacheEntity $entity
     * @return OkapiCacheEntity
     */
    public function update(OkapiCacheEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['key' => $entity->key]
        );

        return $entity;
    }

    /**
     * @param OkapiCacheEntity $entity
     * @return OkapiCacheEntity
     */
    public function remove(OkapiCacheEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['key' => $entity->key]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param OkapiCacheEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(OkapiCacheEntity $entity)
    {
        return [
            'key' => $entity->key,
            'score' => $entity->score,
            'expires' => $entity->expires,
            'value' => $entity->value,
        ];
    }

    /**
     * @param array $data
     * @return OkapiCacheEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new OkapiCacheEntity();
        $entity->key = (string) $data['key'];
        $entity->score = $data['score'];
        $entity->expires = new DateTime($data['expires']);
        $entity->value = (string) $data['value'];

        return $entity;
    }
}
