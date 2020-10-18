<?php

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Oc\Entity\SecurityRoleHierarchyEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class SecurityRoleHierarchyRepository
{
    const TABLE = 'security_role_hierarchy';

    /** @var Connection */
    private $connection;


    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    /**
     * @return SecurityRoleHierarchyEntity[]
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
     * @return SecurityRoleHierarchyEntity
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
     * @return SecurityRoleHierarchyEntity[]
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
     * @return SecurityRoleHierarchyEntity
     */
    public function create(SecurityRoleHierarchyEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->roleId = (int)$this->connection->lastInsertId();

        return $entity;
    }


    /**
     * @return SecurityRoleHierarchyEntity
     */
    public function update(SecurityRoleHierarchyEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['role_id' => $entity->roleId]
        );

        return $entity;
    }


    /**
     * @return SecurityRoleHierarchyEntity
     */
    public function remove(SecurityRoleHierarchyEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['role_id' => $entity->roleId]
        );

        $entity->cacheId = null;

        return $entity;
    }


    /**
     * @return []
     */
    public function getDatabaseArrayFromEntity(SecurityRoleHierarchyEntity $entity)
    {
        return [
            'role_id' => $entity->roleId,
            'sub_role_id' => $entity->subRoleId,
        ];
    }


    /**
     * @return SecurityRoleHierarchyEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new SecurityRoleHierarchyEntity();
        $entity->roleId = (int)$data['role_id'];
        $entity->subRoleId = (int)$data['sub_role_id'];
        return $entity;
    }
}
