<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Oc\Entity\SecurityRolesEntity;
use Oc\Entity\UserEntity;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class SecurityRolesRepository
{
    private const TABLE = 'security_roles';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     * @throws RecordsNotFoundException
     */
    public function fetchAll(): array
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
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = []): SecurityRolesEntity
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

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAssociative();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * @throws Exception
     * @throws RecordsNotFoundException
     */
    public function fetchBy(array $where = []): array
    {
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
     * @throws Exception
     */
    public function fetchUserRoles(UserEntity $user): array
    {
        $statement = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE, 'sr')
                ->join('sr', 'user_roles', 'ur', 'sr.id = ur.role_id')
                ->where('ur.user_id = :userId')
                ->setParameter('userId', $user->userId, ParameterType::INTEGER)
                ->executeQuery();

        $result = $statement->fetchAllAssociative();

        //        if ($statement->rowCount() === 0) {
        //            throw new RecordsNotFoundException('No records found');
        //        }

        $records = [];

        foreach ($result as $item) {
            $records[] = $this->getEntityFromDatabaseArray($item);
        }

        return array_map(static function ($role) {
            return $role->role;
        }, $records);
    }

    /**
     * @throws Exception
     * @throws RecordAlreadyExistsException
     */
    public function create(SecurityRolesEntity $entity): SecurityRolesEntity
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
     * @throws Exception
     * @throws RecordNotPersistedException
     */
    public function update(SecurityRolesEntity $entity): SecurityRolesEntity
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
     * @throws Exception
     * @throws RecordNotPersistedException
     */
    public function remove(SecurityRolesEntity $entity): SecurityRolesEntity
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
                self::TABLE,
                ['id' => $entity->id]
        );

        $entity->id = 0;

        return $entity;
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function getIdByRoleName(string $roleName): int
    {
        return ($this->fetchOneBy(['role' => $roleName])->id);
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function getRoleNameById(int $roleId): string
    {
        return ($this->fetchOneBy(['id' => $roleId])->role);
    }

    public function getDatabaseArrayFromEntity(SecurityRolesEntity $entity): array
    {
        return [
                'id' => $entity->id,
                'role' => $entity->role,
        ];
    }

    public function getEntityFromDatabaseArray(array $data): SecurityRolesEntity
    {
        $entity = new SecurityRolesEntity();
        $entity->id = (int)$data['id'];
        $entity->role = (string)$data['role'];

        return $entity;
    }
}
