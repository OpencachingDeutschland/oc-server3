<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheAdoptionRepository
{
    const TABLE = 'cache_adoption';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCacheAdoptionEntity[]
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
     * @return GeoCacheAdoptionEntity
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
     * @return GeoCacheAdoptionEntity[]
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
     * @param GeoCacheAdoptionEntity $entity
     * @return GeoCacheAdoptionEntity
     */
    public function create(GeoCacheAdoptionEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->cacheId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param GeoCacheAdoptionEntity $entity
     * @return GeoCacheAdoptionEntity
     */
    public function update(GeoCacheAdoptionEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['cache_id' => $entity->cacheId]
                );

        return $entity;
    }

    /**
     * @param GeoCacheAdoptionEntity $entity
     * @return GeoCacheAdoptionEntity
     */
    public function remove(GeoCacheAdoptionEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['cache_id' => $entity->cacheId]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param GeoCacheAdoptionEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCacheAdoptionEntity $entity)
    {
        return [
        'cache_id' => $entity->cacheId,
        'user_id' => $entity->userId,
        'date_created' => $entity->dateCreated,
        ];
    }

    /**
     * @param array $data
     * @return GeoCacheAdoptionEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCacheAdoptionEntity();
        $entity->cacheId = $data['cache_id'];
        $entity->userId = $data['user_id'];
        $entity->dateCreated = $data['date_created'];

        return $entity;
    }
}
