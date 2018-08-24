<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheListsRepository
{
    const TABLE = 'cache_lists';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return GeoCacheListsEntity[]
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
     * @return GeoCacheListsEntity
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
     * @return GeoCacheListsEntity[]
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
     * @param GeoCacheListsEntity $entity
     * @return GeoCacheListsEntity
     */
    public function create(GeoCacheListsEntity $entity)
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
     * @param GeoCacheListsEntity $entity
     * @return GeoCacheListsEntity
     */
    public function update(GeoCacheListsEntity $entity)
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
     * @param GeoCacheListsEntity $entity
     * @return GeoCacheListsEntity
     */
    public function remove(GeoCacheListsEntity $entity)
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
     * @param GeoCacheListsEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(GeoCacheListsEntity $entity)
    {
        return [
            'id' => $entity->id,
            'uuid' => $entity->uuid,
            'node' => $entity->node,
            'user_id' => $entity->userId,
            'date_created' => $entity->dateCreated,
            'last_modified' => $entity->lastModified,
            'last_added' => $entity->lastAdded,
            'last_state_change' => $entity->lastStateChange,
            'name' => $entity->name,
            'is_public' => $entity->isPublic,
            'description' => $entity->description,
            'desc_htmledit' => $entity->descHtmledit,
            'password' => $entity->password,
        ];
    }

    /**
     * @param array $data
     * @return GeoCacheListsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new GeoCacheListsEntity();
        $entity->id = (int) $data['id'];
        $entity->uuid = (string) $data['uuid'];
        $entity->node = (int) $data['node'];
        $entity->userId = (int) $data['user_id'];
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->lastModified = new DateTime($data['last_modified']);
        $entity->lastAdded = new DateTime($data['last_added']);
        $entity->lastStateChange = new DateTime($data['last_state_change']);
        $entity->name = (string) $data['name'];
        $entity->isPublic = (int) $data['is_public'];
        $entity->description = (string) $data['description'];
        $entity->descHtmledit = (int) $data['desc_htmledit'];
        $entity->password = (string) $data['password'];

        return $entity;
    }
}
