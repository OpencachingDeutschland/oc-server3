<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TwigSsrDemoControllerBackend extends AbstractController
{
    private const PER_PAGE = 25;

    private const SORT_COLUMNS = [
        'code'       => 'c.wp_oc',
        'name'       => 'c.name',
        'difficulty' => 'c.difficulty',
        'terrain'    => 'c.terrain',
        'owner'      => 'u.username',
        'status'     => 'cs.name',
    ];

    public function __construct(private readonly Connection $connection)
    {
    }

    #[Route('/twig-ssr-demo', name: 'twig_ssr_demo_index')]
    public function index(Request $request): Response
    {
        $sort = $request->query->get('sort', 'code');
        $dir  = strtolower($request->query->get('dir', 'asc')) === 'desc' ? 'DESC' : 'ASC';
        $q    = trim($request->query->get('q', ''));
        $page = max(1, (int) $request->query->get('page', 1));

        if (!array_key_exists($sort, self::SORT_COLUMNS)) {
            $sort = 'code';
        }

        $orderBy = self::SORT_COLUMNS[$sort] . ' ' . $dir;

        $params = [];
        $where  = '';
        if ($q !== '') {
            $like   = '%' . $q . '%';
            $where  = 'AND (c.wp_oc LIKE ? OR c.name LIKE ? OR u.username LIKE ? OR cs.name LIKE ?)';
            $params = [$like, $like, $like, $like];
        }

        $baseSql = 'FROM caches c
            INNER JOIN user u          ON c.user_id = u.user_id
            INNER JOIN cache_status cs ON c.status  = cs.id
            WHERE 1=1 ' . $where;

        $total      = (int) $this->connection->fetchOne('SELECT COUNT(*) ' . $baseSql, $params);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * self::PER_PAGE;

        $rows = $this->connection->fetchAllAssociative(
            'SELECT c.wp_oc AS code, c.name AS name,
                c.difficulty / 2.0 AS difficulty,
                c.terrain / 2.0    AS terrain,
                u.username         AS owner,
                cs.name            AS status
            ' . $baseSql . '
            ORDER BY ' . $orderBy . '
            LIMIT ' . self::PER_PAGE . ' OFFSET ' . $offset,
            $params
        );

        foreach ($rows as &$row) {
            $row['difficulty'] = (float) $row['difficulty'];
            $row['terrain']    = (float) $row['terrain'];
        }

        return $this->render('backend/twigSsrDemo/index.html.twig', [
            'rows'       => $rows,
            'sort'       => $sort,
            'dir'        => strtolower($dir),
            'q'          => $q,
            'page'       => $page,
            'totalPages' => $totalPages,
            'total'      => $total,
        ]);
    }
}
