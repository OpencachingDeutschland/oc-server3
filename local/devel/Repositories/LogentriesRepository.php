<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class LogentriesRepository
{
    const TABLE = 'logentries';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return LogentriesEntity[]
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
     * @return LogentriesEntity
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
     * @return LogentriesEntity[]
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
     * @param LogentriesEntity $entity
     * @return LogentriesEntity
     */
    public function create(LogentriesEntity $entity)
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
     * @param LogentriesEntity $entity
     * @return LogentriesEntity
     */
    public function update(LogentriesEntity $entity)
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
     * @param LogentriesEntity $entity
     * @return LogentriesEntity
     */
    public function remove(LogentriesEntity $entity)
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
     * @param LogentriesEntity $entity
     * @return []
     */
    public function getDatabaseArrayFromEntity(LogentriesEntity $entity)
    {
        return [
            'id' => $entity->id,
            'date_created' => $entity->dateCreated,
            'module' => $entity->module,
            'eventid' => $entity->eventid,
            'userid' => $entity->userid,
            'objectid1' => $entity->objectid1,
            'objectid2' => $entity->objectid2,
            'logtext' => $entity->logtext,
            'details' => $entity->details,
        ];
    }

    /**
     * @param array $data
     * @return LogentriesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new LogentriesEntity();
        $entity->id = (int) $data['id'];
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->module = (string) $data['module'];
        $entity->eventid = (int) $data['eventid'];
        $entity->userid = (int) $data['userid'];
        $entity->objectid1 = (int) $data['objectid1'];
        $entity->objectid2 = (int) $data['objectid2'];
        $entity->logtext = (string) $data['logtext'];
        $entity->details = (string) $data['details'];

        return $entity;
    }
}
