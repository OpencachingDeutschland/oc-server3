<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    public function __construct(private readonly Connection $connection) {}

    #[Route("/", name: "index_index")]
    public function index(): Response
    {
        $cacheCount = (int)$this->connection->fetchOne('SELECT COUNT(*) FROM caches WHERE status = 1');
        $logCount   = (int)$this->connection->fetchOne('SELECT COUNT(*) FROM cache_logs');
        $userCount  = (int)$this->connection->fetchOne('SELECT COUNT(*) FROM user WHERE is_active_flag = 1');

        return $this->render('app/index/index.html.twig', [
            'cacheCount' => $cacheCount,
            'logCount'   => $logCount,
            'userCount'  => $userCount,
        ]);
    }
}
