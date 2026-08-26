<?php

declare(strict_types=1);

namespace Oc\Repository;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CacheLogsRepository
{
    private const TABLE = 'cache_logs';

    private Connection $connection;

    public function __construct(
        Connection $connection,
    ) {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     * @throws RecordsNotFoundException
     * @throws RecordNotFoundException
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
    public function fetchOneBy(array $where = []): GeoCacheLogsEntity
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
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function fetchBy(array $where = []): array
    {
        $queryBuilder = $this->connection->createQueryBuilder()
                ->select('*')
                ->from(self::TABLE)
                ->orderBy('date_created', 'DESC');

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAllAssociative();

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $item;
        }

        return $entities;
    }

    /**
     * @throws Exception
     */
    public function countLogs(int $cacheId): array
    {
        $entities = [
                '1' => 0,
                '2' => 0,
                '3' => 0,
                '7' => 0,
                '8' => 0,
                '9' => 0,
                '10' => 0,
                '11' => 0,
                '13' => 0,
                '14' => 0,
        ];

        $statement = $this->connection->createQueryBuilder()
                ->select('type')
                ->from(self::TABLE)
                ->where('cache_id = :cacheId')
                ->setParameter('cacheId', $cacheId)
                ->executeQuery();

        $result = $statement->fetchAllAssociative();

        foreach ($result as $item) {
            $entities[(int)$item['type']]++;
        }

        return $entities;
    }

    /**
     * @throws Exception
     */
    public function getCountPictures(array $where = []): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
                ->select('COALESCE(SUM(picture), 0)')
                ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAssociative();

        if (count($result) === 0) {
            return 0;
        } else {
            return (int)$result['COALESCE(SUM(picture), 0)'];
        }
    }


    /**
     * @throws RecordAlreadyExistsException
     * @throws Exception
     */

    /**
     * @throws RecordNotPersistedException
     * @throws Exception
     */

    /**
     * @throws Exception
     * @throws RecordNotPersistedException
     */

    /**
     * @throws Exception
     */
    public function checkLogStatus(int $userId, int $cacheId, string $logType): bool
    {
        if (!empty($userId) && !empty($cacheId)) {
            $queryBuilder = $this->connection->createQueryBuilder()
                    ->select('cache_id')
                    ->from(self::TABLE)
                    ->where('cache_id = :cacheId')
                    ->andWhere('user_id = :userId')
                    ->andWhere('type IN (:logType)')
                    ->setParameters(['userId' => $userId, 'cacheId' => $cacheId, 'logType' => $logType])
                    ->setMaxResults(1)
                    ->executeQuery();

            return !($queryBuilder->rowcount() === 0);
        } else {
            return false;
        }
    }


    /**
     * @throws RecordNotFoundException
     * @throws \Exception
     */

    // ── Extended query methods (CachesRepository refactor) ─────────────

    /** @throws Exception */
    public function fetchLogsByCacheId(int $cacheId, int $limit = 30): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'cl.id', 'cl.uuid', 'cl.type', 'lt.en AS type_name',
                "DATE_FORMAT(cl.date, '%Y-%m-%d') AS date",
                'cl.text', 'cl.text_html', 'cl.user_id',
                'u.username'
            )
            ->from(self::TABLE, 'cl')
            ->join('cl', 'user', 'u', 'cl.user_id = u.user_id')
            ->leftJoin('cl', 'log_types', 'lt', 'cl.type = lt.id')
            ->where('cl.cache_id = :cacheId')
            ->andWhere('cl.gdpr_deletion = 0')
            ->orderBy('cl.date', 'DESC')
            ->setMaxResults($limit)
            ->setParameter('cacheId', $cacheId)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** @throws Exception */
    public function fetchForAuth(int $logId): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id', 'user_id', 'cache_id')
            ->from(self::TABLE)
            ->where('id = :logId')
            ->setParameter('logId', $logId)
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }

    /** @throws Exception */
    public function countDuplicatesByUserAndType(int $cacheId, int $userId, int $type, ?int $excludeLogId): int
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from(self::TABLE)
            ->where('cache_id = :cacheId')
            ->andWhere('user_id = :userId')
            ->andWhere('type = :type')
            ->setParameters([
                'cacheId' => $cacheId,
                'userId'  => $userId,
                'type'    => $type,
            ]);

        if ($excludeLogId !== null) {
            $qb->andWhere('id <> :excludeId')
               ->setParameter('excludeId', $excludeLogId);
        }

        return (int)$qb->executeQuery()->fetchOne();
    }

    /** @throws Exception */
    public function insertLogSimple(int $cacheId, int $userId, int $type, string $date, string $text): int
    {
        $this->connection->executeStatement(
            'INSERT INTO cache_logs (node, cache_id, user_id, type, date, text, text_html, text_htmledit, picture, needs_maintenance, listing_outdated)
             VALUES (4, ?, ?, ?, ?, ?, 0, 0, 0, 0, 0)',
            [$cacheId, $userId, $type, $date, $text]
        );
        return (int)$this->connection->lastInsertId();
    }

    /** @throws Exception */
    public function updateLogSimple(int $logId, int $type, string $date, string $text): void
    {
        $this->connection->executeStatement(
            'UPDATE cache_logs SET type = ?, date = ?, text = ?, text_html = 0 WHERE id = ?',
            [$type, $date, $text, $logId]
        );
    }

    /** @throws Exception */
    public function deleteLogById(int $logId): void
    {
        $this->connection->delete(self::TABLE, ['id' => $logId]);
    }

    /** @throws Exception */
    public function fetchLogForResponse(int $logId): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id', 'uuid', 'type', "DATE_FORMAT(date, '%Y-%m-%d') AS date", 'text', 'text_html')
            ->from(self::TABLE)
            ->where('id = :logId')
            ->setParameter('logId', $logId)
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }

    /** @throws Exception */
    public function countTotalLogs(): int
    {
        return (int)$this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from(self::TABLE)
            ->executeQuery()
            ->fetchOne();
    }
}
