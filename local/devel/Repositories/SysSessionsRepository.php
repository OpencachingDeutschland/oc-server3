<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class SysSessionsRepository
{
    const TABLE = 'sys_sessions';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return SysSessionsEntity[]
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
     * @return SysSessionsEntity
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
     * @return SysSessionsEntity[]
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
     * @param SysSessionsEntity $entity
     * @return SysSessionsEntity
     */
    public function create(SysSessionsEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->uuid = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param SysSessionsEntity $entity
     * @return SysSessionsEntity
     */
    public function update(SysSessionsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['uuid' => $entity->uuid]
        );

        return $entity;
    }

    /**
     * @param SysSessionsEntity $entity
     * @return SysSessionsEntity
     */
    public function remove(SysSessionsEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['uuid' => $entity->uuid]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param SysSessionsEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(SysSessionsEntity $entity)
    {
        return [
            'uuid' => $entity->uuid,
            'user_id' => $entity->userId,
            'permanent' => $entity->permanent,
            'last_login' => $entity->lastLogin,
        ];
    }

    /**
     * @param array $data
     * @return SysSessionsEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new SysSessionsEntity();
        $entity->uuid = (string) $data['uuid'];
        $entity->userId = (int) $data['user_id'];
        $entity->permanent = (int) $data['permanent'];
        $entity->lastLogin = new DateTime($data['last_login']);

        return $entity;
    }
}
