<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class OkapiStatsMonthlyRepository
{
    const TABLE = 'okapi_stats_monthly';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return OkapiStatsMonthlyEntity[]
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
     * @return OkapiStatsMonthlyEntity
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
     * @return OkapiStatsMonthlyEntity[]
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
     * @param OkapiStatsMonthlyEntity $entity
     * @return OkapiStatsMonthlyEntity
     */
    public function create(OkapiStatsMonthlyEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->consumerKey = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param OkapiStatsMonthlyEntity $entity
     * @return OkapiStatsMonthlyEntity
     */
    public function update(OkapiStatsMonthlyEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['consumer_key' => $entity->consumerKey]
        );

        return $entity;
    }

    /**
     * @param OkapiStatsMonthlyEntity $entity
     * @return OkapiStatsMonthlyEntity
     */
    public function remove(OkapiStatsMonthlyEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['consumer_key' => $entity->consumerKey]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param OkapiStatsMonthlyEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(OkapiStatsMonthlyEntity $entity)
    {
        return [
            'consumer_key' => $entity->consumerKey,
            'user_id' => $entity->userId,
            'period_start' => $entity->periodStart,
            'service_name' => $entity->serviceName,
            'total_calls' => $entity->totalCalls,
            'http_calls' => $entity->httpCalls,
            'total_runtime' => $entity->totalRuntime,
            'http_runtime' => $entity->httpRuntime,
        ];
    }

    /**
     * @param array $data
     * @return OkapiStatsMonthlyEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new OkapiStatsMonthlyEntity();
        $entity->consumerKey = (string) $data['consumer_key'];
        $entity->userId = (int) $data['user_id'];
        $entity->periodStart = new DateTime($data['period_start']);
        $entity->serviceName = (string) $data['service_name'];
        $entity->totalCalls = (int) $data['total_calls'];
        $entity->httpCalls = (int) $data['http_calls'];
        $entity->totalRuntime = $data['total_runtime'];
        $entity->httpRuntime = $data['http_runtime'];

        return $entity;
    }
}
