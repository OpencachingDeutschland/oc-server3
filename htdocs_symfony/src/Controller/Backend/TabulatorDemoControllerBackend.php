<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TabulatorDemoControllerBackend extends AbstractController
{
    public function __construct(private readonly Connection $connection)
    {
    }

    #[Route('/tabulator-demo', name: 'tabulator_demo_index')]
    public function index(): Response
    {
        return $this->render('backend/tabulatorDemo/index.html.twig');
    }

    #[Route('/tabulator-demo/data', name: 'tabulator_demo_data')]
    public function data(): JsonResponse
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT
                c.wp_oc        AS code,
                c.name         AS name,
                c.difficulty / 2.0 AS difficulty,
                c.terrain / 2.0    AS terrain,
                u.username     AS owner,
                cs.name        AS status
            FROM caches c
            INNER JOIN user u         ON c.user_id = u.user_id
            INNER JOIN cache_status cs ON c.status  = cs.id
            LIMIT 200'
        );

        foreach ($rows as &$row) {
            $row['difficulty'] = (float) $row['difficulty'];
            $row['terrain']    = (float) $row['terrain'];
        }

        return $this->json($rows);
    }
}
