<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Oc\Repository\CacheLogsRepository;
use Oc\Repository\CachesRepository;
use Oc\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private CachesRepository $cachesRepository,
        private CacheLogsRepository $cacheLogsRepository,
        private UserRepository $userRepository,
    ) {}

    #[Route("/", name: "index_index")]
    public function index(): Response
    {
        $cacheCount = $this->cachesRepository->countActiveCaches();
        $logCount   = $this->cacheLogsRepository->countTotalLogs();
        $userCount  = $this->userRepository->countActiveUsers();

        return $this->render('app/index/index.html.twig', [
            'cacheCount' => $cacheCount,
            'logCount'   => $logCount,
            'userCount'  => $userCount,
        ]);
    }
}
