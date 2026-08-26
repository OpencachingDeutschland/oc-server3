<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Repository\Exception\RecordNotFoundException;

class UserRepository
{
    public const TABLE = 'user';

    private Connection $connection;

    private SecurityRolesRepository $securityRolesRepository;

    public function __construct(Connection $connection, SecurityRolesRepository $securityRolesRepository)
    {
        $this->connection = $connection;
        $this->securityRolesRepository = $securityRolesRepository;
    }

    /** @throws Exception */
    public function fetchOneBy(array $where = []): ?array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('*')->from(self::TABLE)->setMaxResults(1);
        foreach ($where as $col => $val) {
            $qb->andWhere($col . ' = ' . $qb->createNamedParameter($val));
        }
        $result = $qb->executeQuery()->fetchAssociative();
        return $result ?: null;
    }

    /** @throws Exception */
    public function fetchOneById(int $id): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('*')->from(self::TABLE)
            ->where('user_id = :id')->setParameter('id', $id)
            ->executeQuery()->fetchAssociative();
        return $result ?: null;
    }

    public function search_by_user_id(int $userID): ?array
    {
        return $this->fetchOneById($userID);
    }

    /** @throws Exception */
    public function searchUsers(string $query, bool $includeEmail): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->from('user', 'u')
            ->leftJoin('u', 'stat_user', 's', 'u.user_id = s.user_id')
            ->orderBy('u.username', 'ASC');

        if ($includeEmail) {
            $qb->select('u.user_id', 'u.username', 'u.email', 'u.date_created',
                'IFNULL(s.found, 0) AS find_count', 'IFNULL(s.hidden, 0) AS hide_count')
               ->where('u.user_id = :exact')->orWhere('u.email = :exact')
               ->orWhere('u.username LIKE :like')->setMaxResults(200);
        } else {
            $qb->select('u.user_id', 'u.username',
                'IFNULL(s.found, 0) AS find_count', 'IFNULL(s.hidden, 0) AS hide_count')
               ->where('u.username LIKE :like')->setMaxResults(20);
        }
        return $qb->setParameters(['exact' => $query, 'like' => '%' . $query . '%'])
            ->executeQuery()->fetchAllAssociative();
    }

    /** @throws Exception */
    public function fetchUserStats(int $userId): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('IFNULL(found, 0) AS findCount', 'IFNULL(hidden, 0) AS hideCount')
            ->from('stat_user')->where('user_id = :id')->setParameter('id', $userId)
            ->executeQuery()->fetchAssociative();
        return $result ?: ['findCount' => 0, 'hideCount' => 0];
    }

    /** @throws Exception */
    public function countActiveUsers(): int
    {
        return (int) $this->connection->createQueryBuilder()
            ->select('COUNT(*)')->from('user')->where('is_active_flag = 1')
            ->executeQuery()->fetchOne();
    }

    /** @throws Exception */
    public function fetchOwnerStats(int $ownerId): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('IFNULL(found, 0) AS found', 'IFNULL(hidden, 0) AS hidden')
            ->from('stat_user')->where('user_id = :ownerId')->setParameter('ownerId', $ownerId)
            ->executeQuery()->fetchAssociative();
        return $result ?: ['found' => 0, 'hidden' => 0];
    }

    public function generateActivationCode(): string
    {
        return bin2hex(random_bytes(16));
    }
}
