<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class Map2ResultRepository
{
    const TABLE = 'map2_result';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Map2ResultEntity[]
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
     * @return Map2ResultEntity
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
     * @return Map2ResultEntity[]
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
     * @return Map2ResultEntity
     */
    public function create(Map2ResultEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->resultId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @return Map2ResultEntity
     */
    public function update(Map2ResultEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['result_id' => $entity->resultId]
        );

        return $entity;
    }

    /**
     * @return Map2ResultEntity
     */
    public function remove(Map2ResultEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['result_id' => $entity->resultId]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @return []
     */
    public function getDatabaseArrayFromEntity(Map2ResultEntity $entity)
    {
        return [
            'result_id' => $entity->resultId,
            'slave_id' => $entity->slaveId,
            'sqlchecksum' => $entity->sqlchecksum,
            'sqlquery' => $entity->sqlquery,
            'shared_counter' => $entity->sharedCounter,
            'request_counter' => $entity->requestCounter,
            'date_created' => $entity->dateCreated,
            'date_lastqueried' => $entity->dateLastqueried,
        ];
    }

    /**
     * @return Map2ResultEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new Map2ResultEntity();
        $entity->resultId = (int) $data['result_id'];
        $entity->slaveId = (int) $data['slave_id'];
        $entity->sqlchecksum = (int) $data['sqlchecksum'];
        $entity->sqlquery = (string) $data['sqlquery'];
        $entity->sharedCounter = (int) $data['shared_counter'];
        $entity->requestCounter = (int) $data['request_counter'];
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->dateLastqueried = new DateTime($data['date_lastqueried']);

        return $entity;
    }
}
