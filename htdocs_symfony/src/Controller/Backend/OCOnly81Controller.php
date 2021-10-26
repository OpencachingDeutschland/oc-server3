<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Oc\Repository\CachesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    private $cachesRepository;

    /**
     * CachesController constructor.
     *
     * @param Connection $connection
     * @param CachesRepository $cachesRepository
     */
    public function __construct(Connection $connection, CachesRepository $cachesRepository)
    {
        $this->connection = $connection;
        $this->cachesRepository = $cachesRepository;
    }

    /**
     * @param Request $request
     * @Route("/oconly81", name="oconly81_index")
     *
     * @return Response
     */
    public function ocOnly81Controller_index(Request $request)
    : Response {
        $userData = $this->ocOnly81_get_user_counts();
        $matrixData = $this->ocOnly81_get_matrixData();

        return $this->render(
            'backend/oconly81/index.html.twig', [
                                                  'ocOnly81_user' => $userData,
                                                  'ocOnly81_matrix' => $matrixData
                                              ]
        );
    }

    /**
     * @return array
     */
    private function ocOnly81_get_matrixData()
    : array
    {
        return [];
    }

    /**
     * @param int $limit
     *
     * @return array
     *
     * OCOnly81-DB-Abfrage auswerten: Anzahl der OCOnly-Funde je Nutzer
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
