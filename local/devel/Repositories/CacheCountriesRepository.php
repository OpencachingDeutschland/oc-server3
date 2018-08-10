<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheCountriesRepository
{
    const TABLE = 'cache_countries';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCacheCountriesEntity[]
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
     * @return GeoCacheCountriesEntity
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
     * @return GeoCacheCountriesEntity[]
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
     * @param GeoCacheCountriesEntity $entity
     * @return GeoCacheCountriesEntity
     */
    public function create(GeoCacheCountriesEntity $entity)
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
     * @param GeoCacheCountriesEntity $entity
     * @return GeoCacheCountriesEntity
     */
    public function update(GeoCacheCountriesEntity $entity)
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
     * @param GeoCacheCountriesEntity $entity
     * @return GeoCacheCountriesEntity
     */
    public function remove(GeoCacheCountriesEntity $entity)
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
     * @param GeoCacheCountriesEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCacheCountriesEntity $entity)
    {
        return [
        'id' => $entity->id,
        'date_created' => $entity->dateCreated,
        'cache_id' => $entity->cacheId,
        'country' => $entity->country,
        'restored_by' => $entity->restoredBy,
        ];
    }

    /**
     * @param array $data
     * @return GeoCacheCountriesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCacheCountriesEntity();
        $entity->id = (int) $data['id'];
        $entity->dateCreated =  new DateTime($data['date_created']);
        $entity->cacheId = (int) $data['cache_id'];
        $entity->country = (string) $data['country'];
        $entity->restoredBy = (int) $data['restored_by'];

        return $entity;
    }
}
