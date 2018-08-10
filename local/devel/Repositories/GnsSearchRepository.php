<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class GnsSearchRepository
{
    const TABLE = 'gns_search';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GnsSearchEntity[]
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
     * @return GnsSearchEntity
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
     * @return GnsSearchEntity[]
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
     * @param GnsSearchEntity $entity
     * @return GnsSearchEntity
     */
    public function create(GnsSearchEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->uniId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param GnsSearchEntity $entity
     * @return GnsSearchEntity
     */
    public function update(GnsSearchEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['uni_id' => $entity->uniId]
                );

        return $entity;
    }

    /**
     * @param GnsSearchEntity $entity
     * @return GnsSearchEntity
     */
    public function remove(GnsSearchEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['uni_id' => $entity->uniId]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param GnsSearchEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GnsSearchEntity $entity)
    {
        return [
        'uni_id' => $entity->uniId,
        'sort' => $entity->sort,
        'simple' => $entity->simple,
        'simplehash' => $entity->simplehash,
        ];
    }

    /**
     * @param array $data
     * @return GnsSearchEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GnsSearchEntity();
        $entity->uniId = $data['uni_id'];
        $entity->sort = $data['sort'];
        $entity->simple = $data['simple'];
        $entity->simplehash = $data['simplehash'];

        return $entity;
    }
}
