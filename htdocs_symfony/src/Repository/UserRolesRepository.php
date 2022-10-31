<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
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
    private Connection $connection;

    /** @var SecurityRolesRepository */
    private SecurityRolesRepository $securityRolesRepository;

    /**
     * @param Connection $connection
     * @param SecurityRolesRepository $securityRolesRepository
     */
    public function __construct(Connection $connection, SecurityRolesRepository $securityRolesRepository)
    {
        $this->connection = $connection;
        $this->securityRolesRepository = $securityRolesRepository;
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
     * @return UserRolesEntity
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function fetchOneBy(array $where = [])
    : UserRolesEntity {
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
     * @param UserRolesEntity $entity
     *
     * @return UserRolesEntity
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */
    public function create(UserRolesEntity $entity)
    : UserRolesEntity {
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
     * @throws Exception
     * @throws RecordNotPersistedException
     */
    public function update(UserRolesEntity $entity)
    : UserRolesEntity {
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
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RecordNotPersistedException
     */
    public function remove(UserRolesEntity $entity)
    : UserRolesEntity {
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
     * @param int $userId
     * @param string $role
     *
     * @return bool
     * @throws RecordAlreadyExistsException
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function grantRole(int $userId, string $role)
    : bool {
        try {
            $this->fetchOneBy(['user_id' => $userId, 'role_id' => $this->securityRolesRepository->getIdByRoleName($role)]);
        } catch (\Exception $exception) {
            $entity = new UserRolesEntity($userId, $this->securityRolesRepository->getIdByRoleName($role));
            $this->create($entity);
        }

        return true;
    }

    /**
     * @param int $userId
     * @param string $role
     *
     * @return bool
     */
    public function removeRole(int $userId, string $role)
    : bool {
        try {
            $entity = $this->fetchOneBy(['user_id' => $userId, 'role_id' => $this->securityRolesRepository->getIdByRoleName($role)]);

            $this->remove($entity);
        } catch (\Exception $exception) {
        }

        return true;
    }

    /**
     * @param UserRolesEntity $entity
     *
     * @return array
     */
    public function getDatabaseArrayFromEntity(UserRolesEntity $entity)
    : array {
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
    public function getEntityFromDatabaseArray(array $data)
    : UserRolesEntity {
        $entity = new UserRolesEntity();
        $entity->id = (int) $data['id'];
        $entity->userId = (string) $data['user_id'];
        $entity->roleId = (string) $data['role_id'];

        return $entity;
    }

    /**
     * @param string $role
     *
     * @return string
     *
     * Determine which ROLE of the current user is needed to perform role changes on a user
     */
    public function getNeededRole(string $role)
    : string {
        $neededRole = '';

        if ($role === 'ROLE_TEAM') {
            $neededRole = 'ROLE_SUPER_ADMIN';
        } elseif ($role === 'ROLE_SUPER_ADMIN') {
            $neededRole = 'ROLE_SUPER_DUPER_ADMIN';
        } elseif (str_starts_with($role, 'ROLE_ADMIN')) {
            $neededRole = 'ROLE_SUPER_ADMIN';
        } elseif (str_starts_with($role, 'ROLE_SUPPORT') && (!str_ends_with($role, '_HEAD'))) {
            $neededRole = 'ROLE_SUPPORT_HEAD';
        } elseif (str_starts_with($role, 'ROLE_SOCIAL') && (!str_ends_with($role, '_HEAD'))) {
            $neededRole = 'ROLE_SOCIAL_HEAD';
        } elseif (str_starts_with($role, 'ROLE_DEVELOPER') && (!str_ends_with($role, '_HEAD'))) {
            $neededRole = 'ROLE_DEVELOPER_HEAD';
        } else {
            $neededRole = 'ROLE_ADMIN';
        }

        return $neededRole;
    }

    /**
     * @param string $minimumRoleName
     *
     * @return array
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function getTeamMembersAndRoles(string $minimumRoleName)
    : array {
        $minimumRoleId = $this->securityRolesRepository->getIdByRoleName($minimumRoleName);

        $qb = $this->connection->createQueryBuilder();
        $qb->select('user_roles.user_id', 'security_roles.role', 'user.username')
            ->from('user_roles')
            ->innerJoin('user_roles', 'security_roles', 'security_roles', 'user_roles.role_id = security_roles.id')
            ->innerJoin('user_roles', 'user', 'user', 'user_roles.user_id = user.user_id')
            ->where('user_roles.role_id >= :searchTerm')
            ->setParameters(['searchTerm' => $minimumRoleId])
            ->orderBy('security_roles.role', 'ASC');

        return $qb->executeQuery()->fetchAllAssociative();
    }
}
