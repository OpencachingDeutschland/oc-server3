<?php

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Oc\Entity\SecurityRolesEntity;
use Oc\Entity\UserEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

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
     * @return SecurityRolesEntity[]
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
     * @return SecurityRolesEntity
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
     * @return SecurityRolesEntity[]
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


    public function fetchUserRoles(UserEntity $user)
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE, 'sr')
            ->join('sr', 'user_roles', 'ur', 'sr.id = ur.role_id')
            ->where('ur.user_id = :userId')
            ->setParameter(':userId', $user->id)
            ->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records found');
        }

        $records = [];

        foreach ($result as $item) {
            $records[] = $this->getEntityFromDatabaseArray($item);
        }

        return array_map(static function ($role) {return $role->role;}, $records);
    }


    /**
     * @return SecurityRolesEntity
     */
    public function create(SecurityRolesEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->id = (int)$this->connection->lastInsertId();

        return $entity;
    }


    /**
     * @return SecurityRolesEntity
     */
    public function update(SecurityRolesEntity $entity)
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
     * @return SecurityRolesEntity
     */
    public function remove(SecurityRolesEntity $entity)
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
    public function getDatabaseArrayFromEntity(SecurityRolesEntity $entity)
    {
        return [
            'id' => $entity->id,
            'role' => $entity->role,
        ];
    }

    /**
     * @return SecurityRolesEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new SecurityRolesEntity();
        $entity->id = (int)$data['id'];
        $entity->role = (string)$data['role'];
        return $entity;
    }
}
