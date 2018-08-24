<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheLocationRepository
{
    const TABLE = 'cache_location';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCacheLocationEntity[]
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
     * @return GeoCacheLocationEntity
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
     * @return GeoCacheLocationEntity[]
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
     * @param GeoCacheLocationEntity $entity
     * @return GeoCacheLocationEntity
     */
    public function create(GeoCacheLocationEntity $entity)
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
     * @param GeoCacheLocationEntity $entity
     * @return GeoCacheLocationEntity
     */
    public function update(GeoCacheLocationEntity $entity)
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
     * @param GeoCacheLocationEntity $entity
     * @return GeoCacheLocationEntity
     */
    public function remove(GeoCacheLocationEntity $entity)
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
     * @param GeoCacheLocationEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCacheLocationEntity $entity)
    {
        return [
        'cache_id' => $entity->cacheId,
        'last_modified' => $entity->lastModified,
        'adm1' => $entity->adm1,
        'adm2' => $entity->adm2,
        'adm3' => $entity->adm3,
        'adm4' => $entity->adm4,
        'code1' => $entity->code1,
        'code2' => $entity->code2,
        'code3' => $entity->code3,
        'code4' => $entity->code4,
        ];
    }

    /**
     * @param array $data
     * @return GeoCacheLocationEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCacheLocationEntity();
        $entity->cacheId = (int) $data['cache_id'];
        $entity->lastModified =  new DateTime($data['last_modified']);
        $entity->adm1 = (string) $data['adm1'];
        $entity->adm2 = (string) $data['adm2'];
        $entity->adm3 = (string) $data['adm3'];
        $entity->adm4 = (string) $data['adm4'];
        $entity->code1 = (string) $data['code1'];
        $entity->code2 = (string) $data['code2'];
        $entity->code3 = (string) $data['code3'];
        $entity->code4 = (string) $data['code4'];

        return $entity;
    }
}
