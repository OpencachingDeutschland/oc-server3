<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class CacheDescRepository
{
    private const TABLE = 'cache_desc';

    public function __construct(
        private Connection $connection,
    ) {}

    /** @throws Exception */
    public function fetchDescription(int $cacheId, string $preferredLang): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('cd.desc', 'cd.hint', 'cd.short_desc', 'cd.desc_html', 'cd.desc_dark_unsafe', 'cd.language')
            ->from(self::TABLE, 'cd')
            ->where('cd.cache_id = :cacheId')
            ->orderBy('cd.language = :preferredLang', 'DESC')
            ->addOrderBy("cd.language = 'EN'", 'DESC')
            ->setMaxResults(1)
            ->setParameters(['cacheId' => $cacheId, 'preferredLang' => $preferredLang])
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }

    /** @throws Exception */
    public function fetchDescriptionForEdit(int $cacheId): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('cache_id = :cacheId')
            ->orderBy('id')
            ->setMaxResults(1)
            ->setParameter('cacheId', $cacheId)
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }

    /** @throws Exception */
    public function insertDescription(int $cacheId, array $data): void
    {
        $data['cache_id'] = $cacheId;
        $this->connection->insert(self::TABLE, $data);
    }

    /** @throws Exception */
    public function updateDescription(int $cacheId, array $data): void
    {
        $this->connection->update(self::TABLE, $data, ['cache_id' => $cacheId]);
    }
}
