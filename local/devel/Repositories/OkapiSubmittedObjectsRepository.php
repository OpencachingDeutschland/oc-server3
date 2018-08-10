<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class OkapiSubmittedObjectsRepository
{
    const TABLE = 'okapi_submitted_objects';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return OkapiSubmittedObjectsEntity[]
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
     * @return OkapiSubmittedObjectsEntity
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
     * @return OkapiSubmittedObjectsEntity[]
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
     * @param OkapiSubmittedObjectsEntity $entity
     * @return OkapiSubmittedObjectsEntity
     */
    public function create(OkapiSubmittedObjectsEntity $entity)
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
     * @param OkapiSubmittedObjectsEntity $entity
     * @return OkapiSubmittedObjectsEntity
     */
    public function update(OkapiSubmittedObjectsEntity $entity)
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
     * @param OkapiSubmittedObjectsEntity $entity
     * @return OkapiSubmittedObjectsEntity
     */
    public function remove(OkapiSubmittedObjectsEntity $entity)
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
     * @param OkapiSubmittedObjectsEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(OkapiSubmittedObjectsEntity $entity)
    {
        return [
        'object_type' => $entity->objectType,
        'object_id' => $entity->objectId,
        'consumer_key' => $entity->consumerKey,
        ];
    }

    /**
     * @param array $data
     * @return OkapiSubmittedObjectsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new OkapiSubmittedObjectsEntity();
        $entity->objectType = $data['object_type'];
        $entity->objectId = $data['object_id'];
        $entity->consumerKey = $data['consumer_key'];

        return $entity;
    }
}
