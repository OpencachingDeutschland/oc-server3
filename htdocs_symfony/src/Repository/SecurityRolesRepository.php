<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
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
            $records[] = $item;
        }

        return $records;
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function fetchOneBy(array $where = []): array
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
            $entities[] = $item;
        }

        return $entities;
    }

    /**
     * @throws Exception
     */
    public function fetchUserRoles(array $user): array
    {
        $statement = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE, 'sr')
                ->join('sr', 'user_roles', 'ur', 'sr.id = ur.role_id')
                ->where('ur.user_id = :userId')
                ->setParameter('userId', (int) $user['user_id'], ParameterType::INTEGER)
                ->executeQuery();

        $result = $statement->fetchAllAssociative();

        //        if ($statement->rowCount() === 0) {
        //            throw new RecordsNotFoundException('No records found');
        //        }

        $records = [];

        foreach ($result as $item) {
            $records[] = $item;
        }

        return array_map(static function ($role) {
            return $role['role'];
        }, $records);
    }

    /**
     * @throws Exception
     * @throws RecordAlreadyExistsException
     */

    /**
     * @throws Exception
     * @throws RecordNotPersistedException
     */

    /**
     * @throws Exception
     * @throws RecordNotPersistedException
     */

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function getIdByRoleName(string $roleName): int
    {
        return ($this->fetchOneBy(['role' => $roleName])['id']);
    }

    /**
     * @throws Exception
     * @throws RecordNotFoundException
     */
    public function getRoleNameById(int $roleId): string
    {
        return ($this->fetchOneBy(['id' => $roleId])['role']);
    }


}
