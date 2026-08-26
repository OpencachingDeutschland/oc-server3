<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class CachesAttributesRepository
{
    private const TABLE = 'caches_attributes';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
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

//        if ($statement->rowCount() === 0) {
//            throw new RecordsNotFoundException('No records found');
//        }

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
    public function fetchOneBy(array $where = []): GeoCachesAttributesEntity
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

//        if ($statement->rowCount() === 0) {
//            throw new RecordNotFoundException('Record with given where clause not found');
//        }

        return $result ?: null;
    }

    /**
     * @throws RecordsNotFoundException
     * @throws Exception
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

        $statement = $queryBuilder->executeQuery();

        $result = $statement->fetchAllAssociative();

//        if ($statement->rowCount() === 0) {
//            throw new RecordsNotFoundException('No records with given where clause found');
//        }

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
     * @throws RecordNotPersistedException
     * @throws Exception
     */

    /**
     * @throws RecordNotPersistedException
     * @throws Exception
     */

    /**
     * @throws Exception
     */
    public function isOCOnly(int $cacheId) :bool
    {
        if (!empty($cacheId)) {
            $queryBuilder = $this->connection->createQueryBuilder()
                    ->select('cache_id')
                    ->from(self::TABLE)
                    ->where('cache_id = :cacheId')
                    ->andWhere('attrib_id = 6')
                    ->setParameters(['cacheId' => $cacheId])
                    ->setMaxResults(1)
                    ->executeQuery();

            return !($queryBuilder->rowcount() === 0);
        } else {
            return false;
        }
    }



    // ── Extended query methods (CachesRepository refactor) ─────────────

    /** @throws Exception */
    public function fetchAttributesWithIcons(int $cacheId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('ca.id', 'ca.name', 'ca.icon')
            ->from('caches_attributes', 'cxa')
            ->join('cxa', 'cache_attrib', 'ca', 'cxa.attrib_id = ca.id')
            ->where('cxa.cache_id = :cacheId')
            ->orderBy('ca.id')
            ->setParameter('cacheId', $cacheId)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** @throws Exception */
    public function fetchAttribIds(int $cacheId): array
    {
        return array_column(
            $this->connection->createQueryBuilder()
                ->select('attrib_id')
                ->from('caches_attributes')
                ->where('cache_id = :cacheId')
                ->setParameter('cacheId', $cacheId)
                ->executeQuery()
                ->fetchAllAssociative(),
            'attrib_id'
        );
    }

    /** @throws Exception */
    public function replaceCacheAttributes(int $cacheId, array $attribIds): void
    {
        $this->connection->executeStatement(
            'DELETE FROM caches_attributes WHERE cache_id = ?',
            [$cacheId]
        );
        foreach ($attribIds as $attribId) {
            $this->connection->insert('caches_attributes', [
                'cache_id'  => $cacheId,
                'attrib_id' => $attribId,
            ]);
        }
    }
}
