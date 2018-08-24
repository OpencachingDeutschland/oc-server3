<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class OkapiStatsHourlyRepository
{
    const TABLE = 'okapi_stats_hourly';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return OkapiStatsHourlyEntity[]
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
     * @return OkapiStatsHourlyEntity
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
     * @return OkapiStatsHourlyEntity[]
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
     * @param OkapiStatsHourlyEntity $entity
     * @return OkapiStatsHourlyEntity
     */
    public function create(OkapiStatsHourlyEntity $entity)
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
     * @param OkapiStatsHourlyEntity $entity
     * @return OkapiStatsHourlyEntity
     */
    public function update(OkapiStatsHourlyEntity $entity)
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
     * @param OkapiStatsHourlyEntity $entity
     * @return OkapiStatsHourlyEntity
     */
    public function remove(OkapiStatsHourlyEntity $entity)
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
     * @param OkapiStatsHourlyEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(OkapiStatsHourlyEntity $entity)
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
     * @return OkapiStatsHourlyEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new OkapiStatsHourlyEntity();
        $entity->consumerKey = (string) $data['consumer_key'];
        $entity->userId = (int) $data['user_id'];
        $entity->periodStart =  new DateTime($data['period_start']);
        $entity->serviceName = (string) $data['service_name'];
        $entity->totalCalls = (int) $data['total_calls'];
        $entity->httpCalls = (int) $data['http_calls'];
        $entity->totalRuntime = $data['total_runtime'];
        $entity->httpRuntime = $data['http_runtime'];

        return $entity;
    }
}
