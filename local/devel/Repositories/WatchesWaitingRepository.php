<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class WatchesWaitingRepository
{
    const TABLE = 'watches_waiting';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return WatchesWaitingEntity[]
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
     * @return WatchesWaitingEntity
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
     * @return WatchesWaitingEntity[]
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
     * @param WatchesWaitingEntity $entity
     * @return WatchesWaitingEntity
     */
    public function create(WatchesWaitingEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->id = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param WatchesWaitingEntity $entity
     * @return WatchesWaitingEntity
     */
    public function update(WatchesWaitingEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['id' => $entity->id]
                );

        return $entity;
    }

    /**
     * @param WatchesWaitingEntity $entity
     * @return WatchesWaitingEntity
     */
    public function remove(WatchesWaitingEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['id' => $entity->id]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param WatchesWaitingEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(WatchesWaitingEntity $entity)
    {
        return [
        'id' => $entity->id,
        'user_id' => $entity->userId,
        'object_id' => $entity->objectId,
        'object_type' => $entity->objectType,
        'date_created' => $entity->dateCreated,
        'watchtext' => $entity->watchtext,
        'watchtype' => $entity->watchtype,
        ];
    }

    /**
     * @param array $data
     * @return WatchesWaitingEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new WatchesWaitingEntity();
        $entity->id = (int) $data['id'];
        $entity->userId = (int) $data['user_id'];
        $entity->objectId = (int) $data['object_id'];
        $entity->objectType = (int) $data['object_type'];
        $entity->dateCreated =  new DateTime($data['date_created']);
        $entity->watchtext = (string) $data['watchtext'];
        $entity->watchtype = (int) $data['watchtype'];

        return $entity;
    }
}
