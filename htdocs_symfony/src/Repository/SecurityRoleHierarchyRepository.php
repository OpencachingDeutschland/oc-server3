<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Entity\SecurityRoleHierarchyEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 *
 */
class SecurityRoleHierarchyRepository
{
    const TABLE = 'security_role_hierarchy';

    /** @var Connection */
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     * @throws RecordsNotFoundException
     * @throws Exception
     */
    public function fetchAll()
    : array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->executeQuery();

        $result = $statement->fetchAllAssociative();

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
     *
     * @return SecurityRoleHierarchyEntity
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function fetchOneBy(array $where = [])
    : SecurityRoleHierarchyEntity {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->setMaxResults(1);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * @param array $where
     *
     * @return array
     * @throws RecordsNotFoundException
     * @throws Exception
     */
    public function fetchBy(array $where = [])
    : array {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAllAssociative();

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
     * @param SecurityRoleHierarchyEntity $entity
     *
     * @return SecurityRoleHierarchyEntity
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */
    public function create(SecurityRoleHierarchyEntity $entity)
    : SecurityRoleHierarchyEntity {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->roleId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @param SecurityRoleHierarchyEntity $entity
     *
     * @return SecurityRoleHierarchyEntity
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function update(SecurityRoleHierarchyEntity $entity)
    : SecurityRoleHierarchyEntity {
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
     * @param SecurityRoleHierarchyEntity $entity
     *
     * @return SecurityRoleHierarchyEntity
     * @throws RecordNotPersistedException
     * @throws Exception
     */
    public function remove(SecurityRoleHierarchyEntity $entity)
    : SecurityRoleHierarchyEntity {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['role_id' => $entity->roleId]
        );

        $entity->roleId = null;

        return $entity;
    }

    /**
     * @param SecurityRoleHierarchyEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(SecurityRoleHierarchyEntity $entity)
    : array {
        return [
            'role_id' => $entity->roleId,
            'sub_role_id' => $entity->subRoleId,
        ];
    }

    /**
     * @param array $data
     *
     * @return SecurityRoleHierarchyEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    : SecurityRoleHierarchyEntity {
        $entity = new SecurityRoleHierarchyEntity();
        $entity->roleId = (int) $data['role_id'];
        $entity->subRoleId = (int) $data['sub_role_id'];

        return $entity;
    }
}
