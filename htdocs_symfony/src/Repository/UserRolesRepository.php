<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Entity\UserRolesEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class UserRolesRepository
 *
 * @package Oc\Repository
 */
class UserRolesRepository
{
    const TABLE = 'user_roles';

    /** @var Connection */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     * @throws RecordsNotFoundException
     */
    public function fetchAll() : array
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
     *
     * @return UserRolesEntity
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = []) : UserRolesEntity
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
     *
     * @return array
     * @throws RecordsNotFoundException
     */
    public function fetchBy(array $where = []) : array
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
     * @param UserRolesEntity $entity
     *
     * @return UserRolesEntity
     * @throws RecordAlreadyExistsException
     * @throws DBALException
     */
    public function create(UserRolesEntity $entity) : UserRolesEntity
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
     * @param UserRolesEntity $entity
     *
     * @return UserRolesEntity
     * @throws DBALException
     * @throws RecordNotPersistedException
     */
    public function update(UserRolesEntity $entity) : UserRolesEntity
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
     * @param UserRolesEntity $entity
     *
     * @return UserRolesEntity
     * @throws DBALException
     * @throws InvalidArgumentException
     * @throws RecordNotPersistedException
     */
    public function remove(UserRolesEntity $entity) : UserRolesEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['id' => $entity->id]
        );

        $entity->id = null;

        return $entity;
    }

    /**
     * @param UserRolesEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(UserRolesEntity $entity) : array
    {
        return [
            'id' => $entity->id,
            'user_id' => $entity->userId,
            'role_id' => $entity->roleId,
        ];
    }

    /**
     * @param array $data
     *
     * @return UserRolesEntity
     */
    public function getEntityFromDatabaseArray(array $data) : UserRolesEntity
    {
        $entity = new UserRolesEntity();
        $entity->id = (int) $data['id'];
        $entity->userId = (string) $data['user_id'];
        $entity->roleId = (string) $data['role_id'];

        return $entity;
    }
}
