<?php 

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class OkapiTileStatusRepository
{
    const TABLE = 'okapi_tile_status';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return OkapiTileStatusEntity[]
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
     * @return OkapiTileStatusEntity
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
     * @return OkapiTileStatusEntity[]
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
     * @param OkapiTileStatusEntity $entity
     * @return OkapiTileStatusEntity
     */
    public function create(OkapiTileStatusEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
                    self::TABLE,
                    $databaseArray
                );

        $entity->z = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param OkapiTileStatusEntity $entity
     * @return OkapiTileStatusEntity
     */
    public function update(OkapiTileStatusEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
                    self::TABLE,
                    $databaseArray,
                    ['z' => $entity->z]
                );

        return $entity;
    }

    /**
     * @param OkapiTileStatusEntity $entity
     * @return OkapiTileStatusEntity
     */
    public function remove(OkapiTileStatusEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                    self::TABLE,
                    ['z' => $entity->z]
                );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param OkapiTileStatusEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(OkapiTileStatusEntity $entity)
    {
        return [
        'z' => $entity->z,
        'x' => $entity->x,
        'y' => $entity->y,
        'status' => $entity->status,
        ];
    }

    /**
     * @param array $data
     * @return OkapiTileStatusEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new OkapiTileStatusEntity();
        $entity->z = $data['z'];
        $entity->x = $data['x'];
        $entity->y = $data['y'];
        $entity->status = $data['status'];

        return $entity;
    }
}
