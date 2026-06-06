<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class UserRolesRepository
{
    private const TABLE = 'user_roles';

    private Connection $connection;

    private SecurityRolesRepository $securityRolesRepository;

    public function __construct(Connection $connection, SecurityRolesRepository $securityRolesRepository)
    {
        $this->connection = $connection;
        $this->securityRolesRepository = $securityRolesRepository;
    }

    /**
     * @throws RecordsNotFoundException
     * @throws Exception
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
            $records[] = $item;
        }

        return $records;
    }

    /**
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function fetchOneBy(array $where = []): UserRolesEntity
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

        return $result ?: null;
    }

    /**
     * @throws RecordsNotFoundException
     * @throws Exception
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
            $entities[] = $item;
        }

        return $entities;
    }

    /**
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */

    /**
     * @throws Exception
     * @throws RecordNotPersistedException
     */

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws RecordNotPersistedException
     */

    /**
     * @throws RecordAlreadyExistsException
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function grantRole(int $userId, string $role): bool
    {
        try {
            $this->fetchOneBy(['user_id' => $userId, 'role_id' => $this->securityRolesRepository->getIdByRoleName($role)]);
        } catch (\Exception $exception) {
            $entity = ["user_id" => $userId, "role_id" => $this->securityRolesRepository->getIdByRoleName($role)];
            $this->create($entity);
        }

        return true;
    }

    public function removeRole(int $userId, string $role): bool
    {
        try {
            $entity = $this->fetchOneBy(['user_id' => $userId, 'role_id' => $this->securityRolesRepository->getIdByRoleName($role)]);

            $this->remove($entity);
        } catch (\Exception $exception) {
        }

        return true;
    }



    /**
     * Determine which ROLE of the current user is needed to perform role changes on a user
     */
    public function getNeededRole(string $role): string
    {
        $neededRole = '';

        if ($role === 'ROLE_TEAM') {
            $neededRole = 'ROLE_ADMIN';
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
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function getTeamMembersAndRoles(string $minimumRoleName): array
    {
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
