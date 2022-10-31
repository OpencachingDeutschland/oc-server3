<?php

declare(strict_types=1);

namespace Oc\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class OCOnly81Repository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * OCOnly81 Datenbankabfrage: Verteilung der OCOnly-Caches in der 81er Matrix, sowie Summen der einzelnen Zeilen/Spalten erstellen
     *
     * @throws Exception
     */
    public function ocOnly81_get_matrixData(): array
    {
        for ($i = 0; $i <= 8; $i++) {
            for ($j = 0; $j <= 8; $j++) {
                $matrix[$i][$j] = 0;
                $dsum[$i] = 0;
                $tsum[$i] = 0;
            }
        }

        $qb = $this->connection->createQueryBuilder();
        $qb->select('caches.difficulty', 'caches.terrain', 'COUNT(*) AS count')
                ->from('caches')
                ->innerJoin(
                        'caches',
                        'caches_attributes',
                        'caches_attributes',
                        'caches_attributes.cache_id = caches.cache_id AND caches_attributes.attrib_id = 6'
                )
                ->where('caches.status = 1')
                ->groupBy('difficulty', 'terrain');

        $data = $qb->executeQuery()->fetchAllAssociative();

        foreach ($data as $item) {
            $matrix[$item['difficulty'] - 2][$item['terrain'] - 2]++;
        }

        for ($i = 0; $i <= 8; $i++) {
            $dsum[$i] = $dsum[$i] + array_sum($matrix[$i]);
            $tsum[$i] = $tsum[$i] + array_sum(array_column($matrix, $i));
        }

        return ([$matrix, $dsum, $tsum, array_sum($dsum)]);
    }

    /**
     * OCOnly81 Datenbankabfrage: Anzahl der OCOnly-Funde je Nutzer
     *
     * @throws Exception
     */
    public function ocOnly81_get_user_counts(int $limit = 0): array
    {
        $result = [];

        $qb = $this->connection->createQueryBuilder();
        //        $qb->select('user.user_id', 'user.username', 'caches.difficulty', 'caches.terrain')
        $qb->select('user.user_id', 'user.username')
                ->from('user')
                ->innerJoin('user', 'cache_logs', 'cache_logs', 'cache_logs.user_id = user.user_id AND cache_logs.type = 1')
                ->innerJoin('user', 'caches', 'caches', 'caches.cache_id = cache_logs.cache_id')
                ->innerJoin(
                        'user',
                        'caches_attributes',
                        'caches_attributes',
                        'caches_attributes.cache_id = cache_logs.cache_id AND caches_attributes.attrib_id = 6'
                )
                ->innerJoin('user', 'user_options', 'user_options', 'user_options.user_id = user.user_id')
                ->where('user_options.option_id = 13')
                ->andWhere('user_options.option_value = 1');

        $data = $qb->executeQuery()->fetchAllAssociative();

        foreach ($data as $item) {
            if (!array_key_exists($item['user_id'], $result)) {
                $result[$item['user_id']] = [
                        'user_id' => $item['user_id'],
                        'username' => $item['username'],
                        'count' => 1
                ];
            } else {
                $result[$item['user_id']]['count']++;
            }
        }

        if ($limit != 0) {
            $result = array_slice($result, $limit);
        }
        array_multisort(array_column($result, 'count'), SORT_DESC, $result);

        return $result;
    }
}
