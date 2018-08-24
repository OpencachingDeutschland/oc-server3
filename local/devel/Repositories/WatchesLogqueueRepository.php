<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class WatchesLogqueueRepository
{
    const TABLE = 'watches_logqueue';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return WatchesLogqueueEntity[]
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
     * @return WatchesLogqueueEntity
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
     * @return WatchesLogqueueEntity[]
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
     * @param WatchesLogqueueEntity $entity
     * @return WatchesLogqueueEntity
     */
    public function create(WatchesLogqueueEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->logId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param WatchesLogqueueEntity $entity
     * @return WatchesLogqueueEntity
     */
    public function update(WatchesLogqueueEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['log_id' => $entity->logId]
        );

        return $entity;
    }

    /**
     * @param WatchesLogqueueEntity $entity
     * @return WatchesLogqueueEntity
     */
    public function remove(WatchesLogqueueEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['log_id' => $entity->logId]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param WatchesLogqueueEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(WatchesLogqueueEntity $entity)
    {
        return [
            'log_id' => $entity->logId,
            'user_id' => $entity->userId,
        ];
    }

    /**
     * @param array $data
     * @return WatchesLogqueueEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new WatchesLogqueueEntity();
        $entity->logId = (int) $data['log_id'];
        $entity->userId = (int) $data['user_id'];

        return $entity;
    }
}
