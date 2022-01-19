<?php

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Entity\SecurityRolesEntity;
use Oc\Entity\UserEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

/**
 * Class SecurityRolesRepository
 */
class SecurityRolesRepository
{
    const TABLE = 'security_roles';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return array
     * @throws RecordsNotFoundException
     */
    public function fetchAll()
    : array
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
     * @return SecurityRolesEntity
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = [])
    : SecurityRolesEntity {
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
     * @param UserEntity $user
     *
     * @return array
     */
    public function fetchUserRoles(UserEntity $user)
    : array {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE, 'sr')
            ->join('sr', 'user_roles', 'ur', 'sr.id = ur.role_id')
            ->where('ur.user_id = :userId')
            ->setParameter(':userId', $user->userId)
            ->execute();

        $result = $statement->fetchAll();

//        if ($statement->rowCount() === 0) {
//            throw new RecordsNotFoundException('No records found');
//        }

        $records = [];

        foreach ($result as $item) {
            $records[] = $this->getEntityFromDatabaseArray($item);
        }

        return array_map(static function($role) {
            return $role->role;
        }, $records);
    }

    /**
     * @param SecurityRolesEntity $entity
     *
     * @return SecurityRolesEntity
     * @throws DBALException
     * @throws RecordAlreadyExistsException
     */
    public function create(SecurityRolesEntity $entity)
    : SecurityRolesEntity {
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
     * @param SecurityRolesEntity $entity
     *
     * @return SecurityRolesEntity
     * @throws DBALException
     * @throws RecordNotPersistedException
     */
    public function update(SecurityRolesEntity $entity)
    : SecurityRolesEntity {
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
     * @param SecurityRolesEntity $entity
     *
     * @return SecurityRolesEntity
     * @throws RecordNotPersistedException
     * @throws DBALException
     * @throws InvalidArgumentException
     */
    public function remove(SecurityRolesEntity $entity)
    : SecurityRolesEntity {
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
     * @param string $roleName
     *
     * @return int
     * @throws RecordNotFoundException
     */
    public function getIdByRoleName(string $roleName)
    : int {
        return ($this->fetchOneBy(['role' => $roleName])->id);
    }

    /**
     * @param int $roleId
     *
     * @return string
     * @throws RecordNotFoundException
     */
    public function getRoleNameById(int $roleId)
    : string {
        return ($this->fetchOneBy(['id' => $roleId])->role);
    }

    /**
     * @param SecurityRolesEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(SecurityRolesEntity $entity)
    : array {
        return [
            'id' => $entity->id,
            'role' => $entity->role,
        ];
    }

    /**
     * @param array $data
     *
     * @return SecurityRolesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    : SecurityRolesEntity {
        $entity = new SecurityRolesEntity();
        $entity->id = (int) $data['id'];
        $entity->role = (string) $data['role'];

        return $entity;
    }
}
