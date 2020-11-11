<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class SysReplSlavesRepository
{
    const TABLE = 'sys_repl_slaves';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return SysReplSlavesEntity[]
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
     * @return SysReplSlavesEntity
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
     * @return SysReplSlavesEntity[]
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
     * @return SysReplSlavesEntity
     */
    public function create(SysReplSlavesEntity $entity)
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
     * @return SysReplSlavesEntity
     */
    public function update(SysReplSlavesEntity $entity)
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
     * @return SysReplSlavesEntity
     */
    public function remove(SysReplSlavesEntity $entity)
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
     * @return []
     */
    public function getDatabaseArrayFromEntity(SysReplSlavesEntity $entity)
    {
        return [
            'id' => $entity->id,
            'server' => $entity->server,
            'active' => $entity->active,
            'weight' => $entity->weight,
            'online' => $entity->online,
            'last_check' => $entity->lastCheck,
            'time_diff' => $entity->timeDiff,
            'current_log_name' => $entity->currentLogName,
            'current_log_pos' => $entity->currentLogPos,
        ];
    }

    /**
     * @return SysReplSlavesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new SysReplSlavesEntity();
        $entity->id = $data['id'];
        $entity->server = (string) $data['server'];
        $entity->active = (int) $data['active'];
        $entity->weight = (int) $data['weight'];
        $entity->online = (int) $data['online'];
        $entity->lastCheck = new DateTime($data['last_check']);
        $entity->timeDiff = (int) $data['time_diff'];
        $entity->currentLogName = (string) $data['current_log_name'];
        $entity->currentLogPos = (int) $data['current_log_pos'];

        return $entity;
    }
}
