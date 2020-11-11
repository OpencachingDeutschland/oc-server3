<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CachesModifiedRepository
{
    const TABLE = 'caches_modified';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCachesModifiedEntity[]
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
     * @return GeoCachesModifiedEntity
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
     * @return GeoCachesModifiedEntity[]
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
     * @return GeoCachesModifiedEntity
     */
    public function create(GeoCachesModifiedEntity $entity)
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
     * @return GeoCachesModifiedEntity
     */
    public function update(GeoCachesModifiedEntity $entity)
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
     * @return GeoCachesModifiedEntity
     */
    public function remove(GeoCachesModifiedEntity $entity)
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
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCachesModifiedEntity $entity)
    {
        return [
            'cache_id' => $entity->cacheId,
            'date_modified' => $entity->dateModified,
            'name' => $entity->name,
            'type' => $entity->type,
            'date_hidden' => $entity->dateHidden,
            'size' => $entity->size,
            'difficulty' => $entity->difficulty,
            'terrain' => $entity->terrain,
            'search_time' => $entity->searchTime,
            'way_length' => $entity->wayLength,
            'wp_gc' => $entity->wpGc,
            'wp_nc' => $entity->wpNc,
            'restored_by' => $entity->restoredBy,
        ];
    }

    /**
     * @return GeoCachesModifiedEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCachesModifiedEntity();
        $entity->cacheId = (int) $data['cache_id'];
        $entity->dateModified = new DateTime($data['date_modified']);
        $entity->name = (string) $data['name'];
        $entity->type = (int) $data['type'];
        $entity->dateHidden = new DateTime($data['date_hidden']);
        $entity->size = (int) $data['size'];
        $entity->difficulty = (int) $data['difficulty'];
        $entity->terrain = (int) $data['terrain'];
        $entity->searchTime = $data['search_time'];
        $entity->wayLength = $data['way_length'];
        $entity->wpGc = (string) $data['wp_gc'];
        $entity->wpNc = (string) $data['wp_nc'];
        $entity->restoredBy = (int) $data['restored_by'];

        return $entity;
    }
}
