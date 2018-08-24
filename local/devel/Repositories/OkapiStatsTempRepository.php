<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class OkapiStatsTempRepository
{
    const TABLE = 'okapi_stats_temp';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return OkapiStatsTempEntity[]
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
     * @return OkapiStatsTempEntity
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
     * @return OkapiStatsTempEntity[]
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
     * @param OkapiStatsTempEntity $entity
     * @return OkapiStatsTempEntity
     */
    public function create(OkapiStatsTempEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->datetime = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param OkapiStatsTempEntity $entity
     * @return OkapiStatsTempEntity
     */
    public function update(OkapiStatsTempEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['datetime' => $entity->datetime]
                );

        return $entity;
    }

    /**
     * @param OkapiStatsTempEntity $entity
     * @return OkapiStatsTempEntity
     */
    public function remove(OkapiStatsTempEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['datetime' => $entity->datetime]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param OkapiStatsTempEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(OkapiStatsTempEntity $entity)
    {
        return [
        'datetime' => $entity->datetime,
        'consumer_key' => $entity->consumerKey,
        'user_id' => $entity->userId,
        'service_name' => $entity->serviceName,
        'calltype' => $entity->calltype,
        'runtime' => $entity->runtime,
        ];
    }

    /**
     * @param array $data
     * @return OkapiStatsTempEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new OkapiStatsTempEntity();
        $entity->datetime =  new DateTime($data['datetime']);
        $entity->consumerKey = (string) $data['consumer_key'];
        $entity->userId = (int) $data['user_id'];
        $entity->serviceName = (string) $data['service_name'];
        $entity->calltype = $data['calltype'];
        $entity->runtime = $data['runtime'];

        return $entity;
    }
}
