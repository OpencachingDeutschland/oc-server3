<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class XmlsessionDataRepository
{
    const TABLE = 'xmlsession_data';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return XmlsessionDataEntity[]
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
     * @return XmlsessionDataEntity
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
     * @return XmlsessionDataEntity[]
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
     * @param XmlsessionDataEntity $entity
     * @return XmlsessionDataEntity
     */
    public function create(XmlsessionDataEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->sessionId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param XmlsessionDataEntity $entity
     * @return XmlsessionDataEntity
     */
    public function update(XmlsessionDataEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['session_id' => $entity->sessionId]
        );

        return $entity;
    }

    /**
     * @param XmlsessionDataEntity $entity
     * @return XmlsessionDataEntity
     */
    public function remove(XmlsessionDataEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['session_id' => $entity->sessionId]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @param XmlsessionDataEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(XmlsessionDataEntity $entity)
    {
        return [
            'session_id' => $entity->sessionId,
            'object_type' => $entity->objectType,
            'object_id' => $entity->objectId,
        ];
    }

    /**
     * @param array $data
     * @return XmlsessionDataEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new XmlsessionDataEntity();
        $entity->sessionId = (int) $data['session_id'];
        $entity->objectType = (int) $data['object_type'];
        $entity->objectId = (int) $data['object_id'];

        return $entity;
    }
}
