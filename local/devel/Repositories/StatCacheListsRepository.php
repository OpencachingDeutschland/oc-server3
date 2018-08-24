<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class StatCacheListsRepository
{
    const TABLE = 'stat_cache_lists';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return StatCacheListsEntity[]
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
     * @return StatCacheListsEntity
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
     * @return StatCacheListsEntity[]
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
     * @param StatCacheListsEntity $entity
     * @return StatCacheListsEntity
     */
    public function create(StatCacheListsEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->cacheListId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param StatCacheListsEntity $entity
     * @return StatCacheListsEntity
     */
    public function update(StatCacheListsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['cache_list_id' => $entity->cacheListId]
                );

        return $entity;
    }

    /**
     * @param StatCacheListsEntity $entity
     * @return StatCacheListsEntity
     */
    public function remove(StatCacheListsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['cache_list_id' => $entity->cacheListId]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param StatCacheListsEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(StatCacheListsEntity $entity)
    {
        return [
        'cache_list_id' => $entity->cacheListId,
        'entries' => $entity->entries,
        'watchers' => $entity->watchers,
        ];
    }

    /**
     * @param array $data
     * @return StatCacheListsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new StatCacheListsEntity();
        $entity->cacheListId = (int) $data['cache_list_id'];
        $entity->entries = (int) $data['entries'];
        $entity->watchers = (int) $data['watchers'];

        return $entity;
    }
}
