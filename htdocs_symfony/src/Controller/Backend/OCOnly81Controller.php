<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CachesController
 *
 * @package Oc\Controller\Backend
 */
class OCOnly81Controller extends AbstractController
{
    private $connection;

    /**
     * CachesController constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @Route("/oconly81", name="oconly81_index")
     *
     * @return Response
     */
    public function ocOnly81Controller_index()
    : Response {
        $userData = $this->ocOnly81_get_user_counts();
        $matrixData = $this->ocOnly81_get_matrixData();

        return $this->render(
            'backend/oconly81/index.html.twig', [
                                                  'ocOnly81_user' => $userData,
                                                  'ocOnly81_matrix' => $matrixData[0],
                                                  'ocOnly81_dsum' => $matrixData[1],
                                                  'ocOnly81_tsum' => $matrixData[2],
                                                  'ocOnly81_overall_sum' => $matrixData[3]
                                              ]
        );
    }

    /**
     * @return array
     *
     * OCOnly81 Datenbankabfrage: Verteilung der OCOnly-Caches in der 81er Matrix, sowie Summen der einzelnen Zeilen/Spalten erstellen
     */
    private function ocOnly81_get_matrixData()
    : array
    {
        for ($i = 0; $i <= 8; $i ++) {
            for ($j = 0; $j <= 8; $j ++) {
                $matrix[$i][$j] = 0;
                $dsum[$i] = 0;
                $tsum[$i] = 0;
            }
        }

        $qb = $this->connection->createQueryBuilder();
        $qb->select('caches.difficulty', 'caches.terrain', 'COUNT(*) AS count')
            ->from('caches')
            ->innerJoin('caches', 'caches_attributes', 'caches_attributes', 'caches_attributes.cache_id = caches.cache_id AND caches_attributes.attrib_id = 6')
            ->where('caches.status = 1')
            ->groupBy('difficulty', 'terrain');

        $data = $qb->execute()->fetchAll();

        foreach ($data as $item) {
            $matrix[$item['difficulty'] - 2][$item['terrain'] - 2] ++;
        }

        for ($i = 0; $i <= 8; $i ++) {
            $dsum[$i] = $dsum[$i] + array_sum($matrix[$i]);
            $tsum[$i] = $tsum[$i] + array_sum(array_column($matrix, $i));
        }

        return ([$matrix, $dsum, $tsum, array_sum($dsum)]);
    }

    /**
     * @param int $limit
     *
     * @return array
     *
     * OCOnly81 Datenbankabfrage: Anzahl der OCOnly-Funde je Nutzer
     */
    private function ocOnly81_get_user_counts(int $limit = 0)
    : array {
        $result = [];

        $qb = $this->connection->createQueryBuilder();
        //        $qb->select('user.user_id', 'user.username', 'caches.difficulty', 'caches.terrain')
        $qb->select('user.user_id', 'user.username')
            ->from('user')
            ->innerJoin('user', 'cache_logs', 'cache_logs', 'cache_logs.user_id = user.user_id AND cache_logs.type = 1')
            ->innerJoin('user', 'caches', 'caches', 'caches.cache_id = cache_logs.cache_id')
            ->innerJoin('user', 'caches_attributes', 'caches_attributes', 'caches_attributes.cache_id = cache_logs.cache_id AND caches_attributes.attrib_id = 6')
            ->innerJoin('user', 'user_options', 'user_options', 'user_options.user_id = user.user_id')
            ->where('user_options.option_id = 13')
            ->andWhere('user_options.option_value = 1');

        $data = $qb->execute()->fetchAll();

        foreach ($data as $item) {
            if (!array_key_exists($item['user_id'], $result)) {
                $result[$item['user_id']] = [
                    'user_id' => $item['user_id'],
                    'username' => $item['username'],
                    'count' => 1
                ];
            } else {
                $result[$item['user_id']]['count'] ++;
            }
        }

        if ($limit != 0) {
            $result = array_slice($result, $limit);
        }
        array_multisort(array_column($result, 'count'), SORT_DESC, $result);

        return $result;
    }
}
