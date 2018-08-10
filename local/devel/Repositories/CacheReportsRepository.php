<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheReportsRepository
{
    const TABLE = 'cache_reports';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCacheReportsEntity[]
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
     * @return GeoCacheReportsEntity
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
     * @return GeoCacheReportsEntity[]
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
     * @param GeoCacheReportsEntity $entity
     * @return GeoCacheReportsEntity
     */
    public function create(GeoCacheReportsEntity $entity)
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
     * @param GeoCacheReportsEntity $entity
     * @return GeoCacheReportsEntity
     */
    public function update(GeoCacheReportsEntity $entity)
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
     * @param GeoCacheReportsEntity $entity
     * @return GeoCacheReportsEntity
     */
    public function remove(GeoCacheReportsEntity $entity)
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
     * @param GeoCacheReportsEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCacheReportsEntity $entity)
    {
        return [
        'id' => $entity->id,
        'date_created' => $entity->dateCreated,
        'cacheid' => $entity->cacheid,
        'userid' => $entity->userid,
        'reason' => $entity->reason,
        'note' => $entity->note,
        'status' => $entity->status,
        'adminid' => $entity->adminid,
        'lastmodified' => $entity->lastmodified,
        'comment' => $entity->comment,
        ];
    }

    /**
     * @param array $data
     * @return GeoCacheReportsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCacheReportsEntity();
        $entity->id = $data['id'];
        $entity->dateCreated = $data['date_created'];
        $entity->cacheid = $data['cacheid'];
        $entity->userid = $data['userid'];
        $entity->reason = $data['reason'];
        $entity->note = $data['note'];
        $entity->status = $data['status'];
        $entity->adminid = $data['adminid'];
        $entity->lastmodified = $data['lastmodified'];
        $entity->comment = $data['comment'];

        return $entity;
    }
}
