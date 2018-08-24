<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CoordinatesRepository
{
    const TABLE = 'coordinates';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return CoordinatesEntity[]
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
     * @return CoordinatesEntity
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
     * @return CoordinatesEntity[]
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
     * @param CoordinatesEntity $entity
     * @return CoordinatesEntity
     */
    public function create(CoordinatesEntity $entity)
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
     * @param CoordinatesEntity $entity
     * @return CoordinatesEntity
     */
    public function update(CoordinatesEntity $entity)
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
     * @param CoordinatesEntity $entity
     * @return CoordinatesEntity
     */
    public function remove(CoordinatesEntity $entity)
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
     * @param CoordinatesEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(CoordinatesEntity $entity)
    {
        return [
            'id' => $entity->id,
            'date_created' => $entity->dateCreated,
            'last_modified' => $entity->lastModified,
            'type' => $entity->type,
            'subtype' => $entity->subtype,
            'latitude' => $entity->latitude,
            'longitude' => $entity->longitude,
            'cache_id' => $entity->cacheId,
            'user_id' => $entity->userId,
            'log_id' => $entity->logId,
            'description' => $entity->description,
        ];
    }

    /**
     * @param array $data
     * @return CoordinatesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new CoordinatesEntity();
        $entity->id = (int) $data['id'];
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->lastModified = new DateTime($data['last_modified']);
        $entity->type = (int) $data['type'];
        $entity->subtype = (int) $data['subtype'];
        $entity->latitude = $data['latitude'];
        $entity->longitude = $data['longitude'];
        $entity->cacheId = (int) $data['cache_id'];
        $entity->userId = (int) $data['user_id'];
        $entity->logId = (int) $data['log_id'];
        $entity->description = (string) $data['description'];

        return $entity;
    }
}
