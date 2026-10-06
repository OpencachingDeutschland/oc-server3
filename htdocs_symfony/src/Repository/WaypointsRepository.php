<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/**
 * Repository for the `coordinates` table — additional waypoints (type=1),
 * user personal notes, corrected coordinates, and log passwords (type=2).
 */
class WaypointsRepository
{
    private const TABLE = 'coordinates';

    public function __construct(
        private Connection $connection,
    ) {}

    // ── Waypoints (type=1, owner-placed) ───────────────────────────────

    /** @throws Exception */
    public function fetchWaypoints(int $cacheId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('co.latitude', 'co.longitude', 'co.description', 'ct.name AS type_name', 'ct.id AS type_id')
            ->from(self::TABLE, 'co')
            ->leftJoin('co', 'coordinates_type', 'ct', 'co.subtype = ct.id')
            ->where('co.cache_id = :cacheId')
            ->andWhere('co.type = 1')
            ->andWhere('co.user_id IS NULL')
            ->orderBy('co.id')
            ->setParameter('cacheId', $cacheId)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** @throws Exception */
    public function fetchWaypointsForEdit(int $cacheId): array
    {
        return $this->connection->createQueryBuilder()
            ->select('id', 'subtype', 'latitude', 'longitude', 'description')
            ->from(self::TABLE)
            ->where('cache_id = :cacheId')
            ->andWhere('type = 1')
            ->andWhere('user_id IS NULL')
            ->orderBy('id')
            ->setParameter('cacheId', $cacheId)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** @throws Exception */
    public function fetchWaypointsByWp(string $wp): array
    {
        return $this->connection->createQueryBuilder()
            ->select('co.latitude', 'co.longitude', 'co.description', 'co.subtype', 'ct.name AS type_name')
            ->from(self::TABLE, 'co')
            ->join('co', 'caches', 'c', 'co.cache_id = c.cache_id')
            ->leftJoin('co', 'coordinates_type', 'ct', 'co.subtype = ct.id')
            ->where('c.wp_oc = :wp')
            ->andWhere('co.type = 1')
            ->andWhere('co.user_id IS NULL')
            ->orderBy('co.id')
            ->setParameter('wp', $wp)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** @throws Exception */
    public function replaceOwnerWaypoints(int $cacheId, array $waypoints, string $now): void
    {
        $this->connection->executeStatement(
            'DELETE FROM coordinates WHERE cache_id = ? AND type = 1 AND user_id IS NULL',
            [$cacheId]
        );
        foreach ($waypoints as $wpt) {
            $this->connection->insert(self::TABLE, [
                'cache_id'      => $cacheId,
                'type'          => 1,
                'subtype'       => $wpt['type'],
                'latitude'      => $wpt['lat'],
                'longitude'     => $wpt['lon'],
                'description'   => $wpt['desc'],
                'date_created'  => $now,
                'last_modified' => $now,
            ]);
        }
    }

    // ── User note / corrected coords (type=2) ─────────────────────────

    /** @throws Exception */
    public function fetchUserNote(int $cacheId, int $userId): ?array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('description', 'latitude', 'longitude', 'logpw')
            ->from(self::TABLE)
            ->where('cache_id = :cacheId')
            ->andWhere('user_id = :userId')
            ->andWhere('type = 2')
            ->orderBy('id', 'DESC')
            ->setMaxResults(1)
            ->setParameters(['cacheId' => $cacheId, 'userId' => $userId])
            ->executeQuery()
            ->fetchAssociative();

        return $result ?: null;
    }

    /** @throws Exception */
    public function upsertUserNoteText(int $cacheId, int $userId, string $text): array
    {
        $existing = $this->fetchUserNote($cacheId, $userId);
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        if ($text === '') {
            if ($existing) {
                $this->connection->delete(self::TABLE, ['id' => (int)$existing['id']]);
            }
            return ['saved' => false];
        }

        if ($existing) {
            $this->connection->update(self::TABLE, [
                'description' => $text,
                'last_modified' => $now,
            ], ['id' => (int)$existing['id']]);
        } else {
            $this->connection->insert(self::TABLE, [
                'cache_id'      => $cacheId,
                'user_id'       => $userId,
                'type'          => 2,
                'subtype'       => 0,
                'latitude'      => 0,
                'longitude'     => 0,
                'description'   => $text,
                'date_created'  => $now,
                'last_modified' => $now,
            ]);
        }
        return ['saved' => true];
    }

    /** @throws Exception */
    public function upsertUserNoteLogpw(int $cacheId, int $userId, string $logpw): array
    {
        $existing = $this->fetchUserNote($cacheId, $userId);
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        if ($existing) {
            $hasOther = ($existing['description'] !== null && $existing['description'] !== '')
                || (float)$existing['latitude'] !== 0.0
                || (float)$existing['longitude'] !== 0.0;

            if ($logpw === '' && !$hasOther) {
                $this->connection->delete(self::TABLE, ['id' => (int)$existing['id']]);
                return ['saved' => true, 'logpw' => ''];
            }

            $this->connection->update(self::TABLE, [
                'logpw' => $logpw,
                'last_modified' => $now,
            ], ['id' => (int)$existing['id']]);
        } elseif ($logpw !== '') {
            $this->connection->insert(self::TABLE, [
                'cache_id'      => $cacheId,
                'user_id'       => $userId,
                'type'          => 2,
                'subtype'       => 0,
                'latitude'      => 0,
                'longitude'     => 0,
                'description'   => '',
                'logpw'         => $logpw,
                'date_created'  => $now,
                'last_modified' => $now,
            ]);
        }

        return ['saved' => true, 'logpw' => $logpw];
    }

    /** @throws Exception */
    public function upsertUserNoteCoords(int $cacheId, int $userId, float $lat, float $lon): array
    {
        $existing = $this->fetchUserNote($cacheId, $userId);
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        if ($existing) {
            $this->connection->update(self::TABLE, [
                'latitude'      => $lat,
                'longitude'     => $lon,
                'last_modified' => $now,
            ], ['id' => (int)$existing['id']]);
        } else {
            $this->connection->insert(self::TABLE, [
                'cache_id'      => $cacheId,
                'user_id'       => $userId,
                'type'          => 2,
                'subtype'       => 0,
                'latitude'      => $lat,
                'longitude'     => $lon,
                'description'   => '',
                'date_created'  => $now,
                'last_modified' => $now,
            ]);
        }

        return ['saved' => true];
    }
}
